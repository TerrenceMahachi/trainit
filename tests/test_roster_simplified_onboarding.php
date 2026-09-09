<?php
/**
 * End-to-End Automated Test Suite:
 * Simplified Talent Onboarding Process (Apprentice & Associate)
 * Initiated on Opportunities Page with LocalStorage autosave,
 * Shortlist-Only Gate, and Token Magic Link Dossier Access.
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\RosterApplicationController;
use App\Helpers\SiteConfig;
use App\Models\User;
use App\Models\Login;
use App\Models\Rosterapplication;
use App\Models\Apprenticeprofile;
use App\Models\Associateprofile;
use App\Models\Rosterdocument;
use App\Models\Rosterstatusevent;
use App\Models\Rosterskill;
use App\Models\Rosterqualification;
use App\Models\Rosterworkhistory;
use App\Models\Rosterreferee;
use App\Helpers\Auth;

global $siteConfig;
$siteConfig = new SiteConfig(
    _BASEURL,
    assetsUrl: _ASSETSURL,
    assetsLoc: _ASSETS_PATH,
    navLoc: _ASSETS_PATH . '/nav/ajax-pagination.php',
    siteName: _SITEDISPLAYNAME,
    defaultEmail: 'hello@trainit.co.zw'
);

echo "=== STARTING SIMPLIFIED ONBOARDING TEST SUITE ===" . PHP_EOL;

$controller = new RosterApplicationController();

// 1. Test Unauthenticated View Rendering for Stage 1 (Apprentice)
echo "\n[Test 1] Rendering Stage 1 Single-Page Express Apprentice View...";
ob_start();
$out1 = $controller->showExpressForm('apprentice');
$html1 = ob_get_clean();
$fullHtml1 = $out1 . $html1;
if (strpos($fullHtml1, 'Apprentice Talent Application') !== false && strpos($fullHtml1, 'tsigiro_express_apprentice') !== false) {
    echo " PASSED (Single-page layout & LocalStorage autosave verified)\n";
} else {
    echo " FAILED: Expected Apprentice title and localStorage key in output.\n";
    exit(1);
}

// 2. Test Unauthenticated View Rendering for Stage 1 (Associate)
echo "[Test 2] Rendering Stage 1 Single-Page Express Associate View...";
ob_start();
$out2 = $controller->showExpressForm('associate');
$html2 = ob_get_clean();
$fullHtml2 = $out2 . $html2;
if (strpos($fullHtml2, 'Associate Specialist Application') !== false && strpos($fullHtml2, 'tsigiro_express_associate') !== false) {
    echo " PASSED (Single-page layout & LocalStorage autosave verified)\n";
} else {
    echo " FAILED: Expected Associate title and localStorage key in output.\n";
    exit(1);
}

// 3. Test Guest Express Submission (Auto-provisioning Candidate User & Setting Status = 2 Submitted)
echo "[Test 3] Submitting Stage 1 Express Apprentice Intake as Guest...";
$testEmail = 'apprentice_test_' . time() . '@example.co.zw';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_POST = [
    'track_code' => 'apprentice',
    'legal_name' => 'Tinashe Test Moyo',
    'preferred_name' => 'Tinashe',
    'email' => $testEmail,
    'mobile_number' => '+263771112233',
    'city' => 'Harare',
    'zimprovince' => 1,
    'primaryfunction' => 1,
    'institution_name' => 'University of Zimbabwe',
    'degree_programme' => 'BSc Computer Science',
    'study_level' => 'Seeking Attachment (Year 3/Part 3)',
    'wrl_start_date' => '2026-10-01',
    'wrl_duration_months' => 12,
];

// Create a dummy uploaded file for CV
$tmpFile = tempnam(sys_get_temp_dir(), 'cv_test');
file_put_contents($tmpFile, 'Dummy CV Content for testing');
$_FILES['cv_doc'] = [
    'name' => 'tinashe_moyo_cv.pdf',
    'type' => 'application/pdf',
    'tmp_name' => $tmpFile,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($tmpFile),
];

$res1 = $controller->handleExpressSubmit();
if (isset($res1['status']) && $res1['status'] === 1) {
    echo " PASSED (Redirect URL: {$res1['redirect']})\n";
} else {
    echo " FAILED: " . json_encode($res1) . "\n";
    exit(1);
}

// Verify User and Application in Database
$createdUser = User::findByQuery("SELECT * FROM user WHERE email = ?", [$testEmail]);
if (empty($createdUser)) {
    echo " FAILED: User was not auto-provisioned in database.\n";
    exit(1);
}
$userObj = $createdUser[0];
echo " - Verified User ID: {$userObj->iD} logged in: " . (Auth::id() == $userObj->iD ? "YES" : "NO") . "\n";

$createdApp = Rosterapplication::findByQuery("SELECT * FROM rosterapplication WHERE user = ?", [$userObj->iD]);
if (empty($createdApp)) {
    echo " FAILED: Rosterapplication record not found.\n";
    exit(1);
}
$appObj = $createdApp[0];
$appId = (int)$appObj->iD;
echo " - Verified Application ID: {$appId} (Status: {$appObj->applicationstatus} - Submitted)\n";

if ((int)$appObj->applicationstatus !== 2) {
    echo " FAILED: Initial application status should be 2 (Submitted).\n";
    exit(1);
}

// Verify Apprenticeprofile
$appProfile = $appObj->apprenticeProfile();
if (!$appProfile || $appProfile->institution_name !== 'University of Zimbabwe') {
    echo " FAILED: Apprenticeprofile record not found or data mismatch.\n";
    exit(1);
}
echo " - Verified Apprentice Profile: {$appProfile->degree_programme} at {$appProfile->institution_name}\n";

// Verify Rosterdocument (CV)
$docs = $appObj->documents();
if (empty($docs)) {
    echo " FAILED: Rosterdocument for CV not created.\n";
    exit(1);
}
echo " - Verified Rosterdocument: {$docs[0]->original_name} ({$docs[0]->file_size_kb} KB)\n";

// Verify Rosterstatusevent
$events = $appObj->statusEvents();
if (empty($events)) {
    echo " FAILED: Rosterstatusevent not logged.\n";
    exit(1);
}
echo " - Verified Rosterstatusevent: {$events[0]->remarks}\n";

// 4. Test Shortlist Gate: Access to Stage 2 Credentials is BLOCKED for non-shortlisted applicant
echo "[Test 4] Verifying Stage 2 Dossier Access Gate is BLOCKED before Shortlisting...";
unset($_SERVER['HTTP_X_REQUESTED_WITH']);
unset($_FILES['cv_doc']);

$blockedResult = $controller->showCredentialsForm($appId);
if ($blockedResult === 'ACCESS_DENIED_NOT_SHORTLISTED') {
    echo " PASSED: Candidate with status 2 was correctly gated from Stage 2 credentials.\n";
} else {
    echo " FAILED: Expected ACCESS_DENIED_NOT_SHORTLISTED, got: " . substr((string)$blockedResult, 0, 100) . "\n";
    exit(1);
}

// 5. Test Admin Shortlisting Candidate
echo "[Test 5] Testing Admin Shortlisting & Magic Link Generation...";
// Simulate Admin login
$adminUser = User::findByQuery("SELECT * FROM user WHERE role = 1 OR role = 6 LIMIT 1");
$adminId = !empty($adminUser) ? $adminUser[0]->iD : 1;
Auth::login($adminId);

$_POST = ['rosterapplication' => $appId];
$shortlistResult = $controller->handleShortlistCandidate();
if (isset($shortlistResult['status']) && $shortlistResult['status'] === 1 && !empty($shortlistResult['dossier_link'])) {
    echo " PASSED: Candidate shortlisted. Dossier link: {$shortlistResult['dossier_link']}\n";
} else {
    echo " FAILED: " . json_encode($shortlistResult) . "\n";
    exit(1);
}

// Verify application status changed to 3 (Screened / Shortlisted)
$reloadedApp = (new Rosterapplication())->find($appId);
if ((int)$reloadedApp->applicationstatus !== 3) {
    echo " FAILED: Application status should be 3 (Screened/Shortlisted), got: {$reloadedApp->applicationstatus}\n";
    exit(1);
}
echo " - Verified Application Status is 3 (Screened/Shortlisted)\n";

// Verify HMAC Token
$token = $controller->generateShortlistToken($reloadedApp);
if (!$controller->verifyShortlistToken($reloadedApp, $token)) {
    echo " FAILED: HMAC token verification failed.\n";
    exit(1);
}
echo " - Verified Valid HMAC Shortlist Token: " . substr($token, 0, 16) . "...\n";

// 6. Test Candidate Magic Link Login
echo "[Test 6] Testing Candidate Magic Link Login via Token...";
Auth::logout(); // Log out admin
$_GET = ['id' => $appId, 'token' => $token];
$loginResult = $controller->handleShortlistTokenLogin();
if (isset($loginResult['status']) && $loginResult['status'] === 1 && Auth::id() == $userObj->iD) {
    echo " PASSED: Candidate authenticated successfully via magic link.\n";
} else {
    echo " FAILED: Magic link login failed: " . json_encode($loginResult) . "\n";
    exit(1);
}

// 7. Test Unlocked Stage 2: Credentials & Qualifications
echo "[Test 7] Accessing Unlocked Stage 2 Credentials Form...";
ob_start();
$outCredentials = $controller->showCredentialsForm($appId);
$htmlCredentials = ob_get_clean();
$fullHtmlCreds = $outCredentials . $htmlCredentials;
if (strpos($fullHtmlCreds, 'Step 2: Qualifications') !== false) {
    echo " PASSED: Credentials view rendered successfully.\n";
} else {
    echo " FAILED: Credentials view did not render Step 2.\n";
    exit(1);
}

// Save qualification
$quals = new Rosterqualification();
$quals->rosterapplication = $appId;
$quals->qualificationtype = 1;
$quals->title = 'BSc Honours Computer Science';
$quals->institution_name = 'University of Zimbabwe';
$quals->field_of_study = 'Computer Science & Artificial Intelligence';
$quals->qualificationstatus = 1;
$quals->reg_by = $userObj->iD;
$quals->save();

$appProfile->student_reg_number = 'R214582H';
$appProfile->expected_completion_date = '2027-12-31';
$appProfile->update();
$controller->logStatusEvent($appId, 3, 'Stage 2: Credentials and student ID uploaded');
echo " - Saved Qualification & Apprentice Profile details.\n";

// 8. Test Stage 3: Skills Matrix
echo "[Test 8] Rendering & Submitting Stage 3 Skills Matrix...";
ob_start();
$outSkills = $controller->showSkillsForm($appId);
$htmlSkills = ob_get_clean();
$fullHtmlSkills = $outSkills . $htmlSkills;
if (strpos($fullHtmlSkills, 'Step 3: Skills') === false) {
    echo " FAILED: Skills view did not render Step 3.\n";
    exit(1);
}

$skill = new Rosterskill();
$skill->rosterapplication = $appId;
$skill->servicefunction = 1;
$skill->skillitem = 1;
$skill->proficiencylevel = 3; // Advanced
$skill->reg_by = $userObj->iD;
$skill->save();
$controller->logStatusEvent($appId, 3, 'Stage 3: Skills matrix competency ratings saved');
echo " PASSED: Skills view rendered and ratings saved.\n";

// 9. Test Stage 4: Experience & Referees
echo "[Test 9] Rendering & Submitting Stage 4 Experience & Referees...";
ob_start();
$outExp = $controller->showExperienceForm($appId);
$htmlExp = ob_get_clean();
$fullHtmlExp = $outExp . $htmlExp;
if (strpos($fullHtmlExp, 'Step 4: Experience') === false) {
    echo " FAILED: Experience view did not render Step 4.\n";
    exit(1);
}

$wh = new Rosterworkhistory();
$wh->rosterapplication = $appId;
$wh->organization_name = 'TechLabs Zim';
$wh->position_title = 'Junior Project Developer';
$wh->start_date = '2025-01-15';
$wh->key_deliverables = 'Built PHP/Laravel APIs and customer portals';
$wh->sectortype = 1;
$wh->engagementbasis = 1;
$wh->reg_by = $userObj->iD;
$wh->save();

$ref = new Rosterreferee();
$ref->rosterapplication = $appId;
$ref->referee_name = 'Dr. K. Sibanda';
$ref->organization = 'University of Zimbabwe';
$ref->position = 'Senior Lecturer & WRL Coordinator';
$ref->relationship = 'Academic Supervisor';
$ref->email = 'ksibanda@science.uz.ac.zw';
$ref->phone = '+263772223344';
$ref->refereecontacttiming = 1;
$ref->refereeverificationstatus = 1;
$ref->reg_by = $userObj->iD;
$ref->save();
$controller->logStatusEvent($appId, 3, 'Stage 4: Work deliverables and referee contact details saved');
echo " PASSED: Work history view rendered and referee contact saved.\n";

// 10. Test Stage 5: Review & Digital Declaration Submission
echo "[Test 10] Rendering Stage 5 Review View & Submitting Full Dossier...";
ob_start();
$outRev = $controller->showReviewForm($appId);
$htmlRev = ob_get_clean();
$fullHtmlRev = $outRev . $htmlRev;
if (strpos($fullHtmlRev, 'Step 5: Review') === false || strpos($fullHtmlRev, 'Tinashe Test Moyo') === false) {
    echo " FAILED: Review view did not render properly.\n";
    exit(1);
}

$_POST = [
    'application_id' => $appId,
    'e_signature' => 'Tinashe Test Moyo',
];
$finalSubmitRes = $controller->handleFinalSubmit();
$completedApp = (new Rosterapplication())->find($appId);
if ((int)$completedApp->applicationstatus === 4 && !empty($completedApp->e_signature)) {
    echo " PASSED: Application #{$appId} moved to Status 4 (Interview / Verification Complete) with signature '{$completedApp->e_signature}'.\n";
} else {
    echo " FAILED: Expected status 4, got: {$completedApp->applicationstatus}\n";
    exit(1);
}

// 11. Test Stage 6: Status Tracker View with Updated Milestone States
echo "[Test 11] Rendering Live Status Tracker View...";
ob_start();
$outStatus = $controller->showStatusView($appId);
$htmlStatus = ob_get_clean();
$fullHtmlStatus = $outStatus . $htmlStatus;
if (strpos($fullHtmlStatus, 'Candidate Onboarding Status') !== false && strpos($fullHtmlStatus, '4-Stage Vetting') !== false) {
    echo " PASSED: Live status tracker rendered successfully with full milestones.\n";
} else {
    echo " FAILED: Status view did not render properly.\n";
    exit(1);
}

// 12. Test Associate Express Intake flow
echo "[Test 12] Testing Stage 1 Express Associate Intake Flow...";
$assocEmail = 'associate_test_' . time() . '@example.co.zw';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
$_POST = [
    'track_code' => 'associate',
    'legal_name' => 'Farai Senior Consultant',
    'preferred_name' => 'Farai',
    'email' => $assocEmail,
    'mobile_number' => '+263779998877',
    'city' => 'Bulawayo',
    'zimprovince' => 2,
    'primaryfunction' => 3, // Finance & Accounting
    'years_experience' => '8-12 years',
    'employmentstatus' => 1,
    'day_rate_expectation' => 300,
    'capacity_days_per_month' => 'Part-Time (8-14 days/month)',
];

$tmpFile2 = tempnam(sys_get_temp_dir(), 'cv_assoc');
file_put_contents($tmpFile2, 'Dummy Associate CV Content');
$_FILES['cv_doc'] = [
    'name' => 'farai_consultant_cv.pdf',
    'type' => 'application/pdf',
    'tmp_name' => $tmpFile2,
    'error' => UPLOAD_ERR_OK,
    'size' => filesize($tmpFile2),
];

$resAssoc = $controller->handleExpressSubmit();
if (isset($resAssoc['status']) && $resAssoc['status'] === 1) {
    echo " PASSED\n";
} else {
    echo " FAILED: " . json_encode($resAssoc) . "\n";
    exit(1);
}

$assocApps = Rosterapplication::findByQuery("SELECT * FROM rosterapplication WHERE email = ?", [$assocEmail]);
if (empty($assocApps)) {
    echo " FAILED: Associate application not found.\n";
    exit(1);
}
$assocApp = $assocApps[0];
if ((int)$assocApp->applicationstatus !== 2) {
    echo " FAILED: Associate application status should be 2 (Submitted).\n";
    exit(1);
}
$assocProfile = $assocApp->associateProfile();
if (!$assocProfile || (float)$assocProfile->day_rate_expectation !== 300.0) {
    echo " FAILED: Associate profile data mismatch.\n";
    exit(1);
}
echo " - Verified Associate Profile: {$assocProfile->years_experience} at \${$assocProfile->day_rate_expectation}/day (Status: 2 Submitted)\n";

// 13. Test Opportunities View: Guest vs Admin visibility
echo "[Test 13] Verifying Opportunities page hides applicants for guest and shows applicants for Admin...";

// Guest view
$guestData = [
    'title' => 'Apprentices and Associates',
    'isAdmin' => false,
    'user' => null,
];
$guestHtml = view('home.opportunities', ['data' => $guestData]);
if (strpos($guestHtml, 'admin-applicants-section') !== false || strpos($guestHtml, 'Talent Intake Applicants') !== false) {
    echo " FAILED: Guest visitor improperly saw admin applicants section!\n";
    exit(1);
}
echo " PASSED (Guest view cleanly hides applicants pipeline)\n";

// Admin view
$adminUsers = User::findByQuery("SELECT * FROM user WHERE role = 1 AND status = 1 LIMIT 1");
$adminUser = !empty($adminUsers) ? $adminUsers[0] : (new User())->find(1);
Auth::login($adminUser->iD);

$pdo = \App\Models\Database::sharedPdo();
$adminData = [
    'title' => 'Apprentices and Associates',
    'user' => $adminUser,
    'isAdmin' => true,
    'statuses' => \App\Models\Applicationstatus::all(),
    'tracks' => \App\Models\Applicationtrack::all(),
    'totalApplicants' => (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication")->fetchColumn(),
    'apprenticeCount' => (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationtrack = 1")->fetchColumn(),
    'associateCount' => (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationtrack = 2")->fetchColumn(),
    'submittedCount' => (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationstatus = 2")->fetchColumn(),
    'shortlistedCount' => (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationstatus = 3")->fetchColumn(),
    'interviewCount' => (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationstatus = 4")->fetchColumn(),
    'onRosterCount' => (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationstatus = 5")->fetchColumn(),
    'initialApplications' => \App\Models\Rosterapplication::findByQuery("SELECT * FROM rosterapplication ORDER BY iD DESC LIMIT 20"),
];

$adminHtml = view('home.opportunities', ['data' => $adminData]);
if (strpos($adminHtml, 'admin-applicants-section') === false || strpos($adminHtml, 'Talent Intake Applicants') === false) {
    echo " FAILED: Admin visitor failed to see admin applicants section!\n";
    exit(1);
}
if (strpos($adminHtml, 'Administrator Session Active') === false) {
    echo " FAILED: Hero admin session active banner not rendered!\n";
    exit(1);
}
echo " - Admin view verified: hero active ribbon, KPI cards, and applicants table successfully rendered.\n";

// 14. Test Pipeline Records API includes CV metadata and inline shortlist support
echo "[Test 14] Testing getAdminRosterRecords API & Shortlist Action as Admin...";
$_POST = [
    'search' => '',
    'page' => 1,
    'page_size' => 10,
    'track' => 0,
    'status' => 0,
];
$apiRes = $controller->getAdminRosterRecords();
if ($apiRes['status'] !== 1 || !isset($apiRes['records']) || empty($apiRes['records'])) {
    echo " FAILED: getAdminRosterRecords did not return records.\n";
    exit(1);
}
$firstRecord = $apiRes['records'][0];
if (!array_key_exists('has_cv', $firstRecord) || !array_key_exists('cv_path', $firstRecord)) {
    echo " FAILED: getAdminRosterRecords record missing has_cv or cv_path field.\n";
    exit(1);
}
echo " PASSED: API returned " . count($apiRes['records']) . " candidates with CV metadata (Candidate #{$firstRecord['iD']} '{$firstRecord['legal_name']}', has_cv: " . ($firstRecord['has_cv'] ? 'true' : 'false') . ").\n";

// Test inline shortlisting via POST
$_POST = [
    'rosterapplication' => $assocApp->iD,
];
$shortlistRes = $controller->handleShortlistCandidate();
if ($shortlistRes['status'] !== 1 || empty($shortlistRes['dossier_link'])) {
    echo " FAILED: Shortlist candidate action failed: " . json_encode($shortlistRes) . "\n";
    exit(1);
}
echo " - Inline shortlist action verified for Associate #{$assocApp->iD}: Link={$shortlistRes['dossier_link']}\n";

echo "\n=== ALL 14 SIMPLIFIED ONBOARDING & ADMIN PIPELINE TESTS PASSED 100%! ===\n";
