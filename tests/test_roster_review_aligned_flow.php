<?php
/**
 * Automated Verification Test Suite:
 * Align Admin Roster Review Console with Express Intake & Verified Dossier Process
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\RosterApplicationController;
use App\Helpers\SiteConfig;
use App\Helpers\Auth;
use App\Models\User;
use App\Models\Rosterapplication;
use App\Models\Apprenticeprofile;
use App\Models\Associateprofile;
use App\Models\Rosterqualification;
use App\Models\Rosterskill;
use App\Models\Rosterworkhistory;
use App\Models\Rosterreferee;
use App\Models\Rosterdocument;
use App\Models\Rosterassessment;
use App\Router;

global $siteConfig;
$siteConfig = new SiteConfig(
    _BASEURL,
    assetsUrl: _ASSETSURL,
    assetsLoc: _ASSETS_PATH,
    navLoc: _ASSETS_PATH . '/nav/ajax-pagination.php',
    siteName: _SITEDISPLAYNAME,
    defaultEmail: 'hello@trainit.co.zw'
);

echo "=== STARTING ADMIN REVIEW & APPLICATION INTAKE ALIGNMENT SUITE ===" . PHP_EOL;

// 1. Verify Route Resolution for /opportunities/apply and /opportunities/apply/ via Subprocess
echo PHP_EOL . "[Test 1] Verifying /opportunities/apply and /opportunities/apply/ routes..." . PHP_EOL;

$cmd1 = 'php -r "' .
    'require \'bootstrap.php\'; ' .
    '\$siteConfig = new \\App\\Helpers\\SiteConfig(_BASEURL, _ASSETSURL, _ASSETS_PATH, _ASSETS_PATH . \'/nav/ajax-pagination.php\', _SITEDISPLAYNAME, \'hello@trainit.co.zw\'); ' .
    '\$router = new \\App\\Router(); ' .
    'foreach (glob(_ROUTES_PATH . \'/*.php\') as \$f) include \$f; ' .
    '\$_SERVER[\'REQUEST_METHOD\'] = \'GET\'; ' .
    '\$_SERVER[\'REQUEST_URI\'] = \'/opportunities/apply\'; ' .
    '\$router->matchRoute();' .
    '"';

$out1 = shell_exec($cmd1);
if (strpos($out1, 'Choose Your Application Track') !== false &&
    strpos($out1, '/opportunities/apply/apprentice') !== false &&
    strpos($out1, '/opportunities/apply/associate') !== false) {
    echo " PASSED: /opportunities/apply renders Track Selector with Apprentice & Associate links." . PHP_EOL;
} else {
    echo " FAILED: /opportunities/apply output mismatch: " . substr((string)$out1, 0, 200) . PHP_EOL;
    exit(1);
}

$cmd2 = 'php -r "' .
    'require \'bootstrap.php\'; ' .
    '\$siteConfig = new \\App\\Helpers\\SiteConfig(_BASEURL, _ASSETSURL, _ASSETS_PATH, _ASSETS_PATH . \'/nav/ajax-pagination.php\', _SITEDISPLAYNAME, \'hello@trainit.co.zw\'); ' .
    '\$router = new \\App\\Router(); ' .
    'foreach (glob(_ROUTES_PATH . \'/*.php\') as \$f) include \$f; ' .
    '\$_SERVER[\'REQUEST_METHOD\'] = \'GET\'; ' .
    '\$_SERVER[\'REQUEST_URI\'] = \'/opportunities/apply/\'; ' .
    '\$router->matchRoute();' .
    '"';

$out2 = shell_exec($cmd2);
if (strpos($out2, 'Choose Your Application Track') !== false) {
    echo " PASSED: /opportunities/apply/ (trailing slash) also resolves to Track Selector." . PHP_EOL;
} else {
    echo " FAILED: /opportunities/apply/ output mismatch." . PHP_EOL;
    exit(1);
}

// Authenticate as Admin for Review Console Tests
$adminUsers = User::findByQuery("SELECT * FROM user WHERE role = 1 AND status = 1 LIMIT 1");
$adminUser = !empty($adminUsers) ? $adminUsers[0] : (new User())->find(1);
Auth::login($adminUser->iD);

$controller = new RosterApplicationController();

// 2. Test Rendering Review Console for Application #11 (Legacy Record)
echo PHP_EOL . "[Test 2] Verifying Admin Review Console for Application #11 (Legacy Record)..." . PHP_EOL;
ob_start();
echo $controller->adminReviewConsole(11);
$review11Html = ob_get_clean();

if (strpos($review11Html, 'Stage 1: Submitted Application Details') === false) {
    echo " FAILED: Missing Stage 1 header on #11." . PHP_EOL;
    exit(1);
}
if (strpos($review11Html, 'Stages 2–5: Verified Talent Dossier') === false) {
    echo " FAILED: Missing Stages 2-5 Verified Talent Dossier on #11." . PHP_EOL;
    exit(1);
}
if (strpos($review11Html, 'Historical Legacy Questionnaire Responses') === false) {
    echo " FAILED: Expected legacy archive accordion on #11." . PHP_EOL;
    exit(1);
}
if (strpos($review11Html, '1. Technical Fit &amp; Academic Baseline') === false ||
    strpos($review11Html, '2. CV &amp; Practical Track Record') === false ||
    strpos($review11Html, '3. Competencies &amp; Vetting Assessment') === false ||
    strpos($review11Html, '4. Availability &amp; Delivery Logistics') === false ||
    strpos($review11Html, '5. Motivation &amp; Professional Alignment') === false) {
    echo " FAILED: 100-point scoring dimensions not properly aligned." . PHP_EOL;
    exit(1);
}
echo " PASSED: Application #11 renders aligned Stage 1 details, verified dossier, legacy archive, and 5 scoring dimensions." . PHP_EOL;

// 3. Create Fresh Express Application (Status 2: Submitted) and Verify Review Console
echo PHP_EOL . "[Test 3] Verifying Admin Review Console for fresh Stage 1 Express Applicant..." . PHP_EOL;
$freshEmail = 'express_align_' . time() . '@example.co.zw';
$testUser = new User();
$testUser->name = 'Kudzai Alignment Tester';
$testUser->email = $freshEmail;
$testUser->role = 4; // Candidate
$testUser->status = 1;
$testUser->save();

$freshApp = new Rosterapplication();
$freshApp->user = $testUser->iD;
$freshApp->applicationtrack = 1; // Apprentice
$freshApp->applicationstatus = 2; // Submitted
$freshApp->primaryfunction = 1;
$freshApp->legal_name = 'Kudzai Alignment Tester';
$freshApp->email = $freshEmail;
$freshApp->mobile_number = '+263773334455';
$freshApp->city = 'Harare';
$freshApp->zimprovince = 1;
$freshApp->reg_by = $testUser->iD;
$freshApp->save();

$freshProfile = new Apprenticeprofile();
$freshProfile->rosterapplication = $freshApp->iD;
$freshProfile->institution_name = 'Harare Institute of Technology';
$freshProfile->degree_programme = 'BTech Software Engineering';
$freshProfile->study_level = 'Seeking Attachment (Year 3/Part 3)';
$freshProfile->wrl_start_date = '2026-11-01';
$freshProfile->wrl_duration_months = 12;
$freshProfile->reg_by = $testUser->iD;
$freshProfile->save();

// Dummy CV document
$freshCv = new Rosterdocument();
$freshCv->rosterapplication = $freshApp->iD;
$freshCv->documenttype = 1; // CV_RESUME
$freshCv->file_path = 'uploads/test_cv.pdf';
$freshCv->original_name = 'kudzai_tester_cv.pdf';
$freshCv->file_size_kb = 128;
$freshCv->reg_by = $testUser->iD;
$freshCv->save();

ob_start();
echo $controller->adminReviewConsole($freshApp->iD);
$freshHtml = ob_get_clean();

if (strpos($freshHtml, 'Harare Institute of Technology') === false ||
    strpos($freshHtml, 'BTech Software Engineering') === false) {
    echo " FAILED: Academic details not displayed on Stage 1 review." . PHP_EOL;
    exit(1);
}
if (strpos($freshHtml, 'kudzai_tester_cv.pdf') === false) {
    echo " FAILED: CV document not displayed on review." . PHP_EOL;
    exit(1);
}
if (strpos($freshHtml, 'Initial Express Application (Pending Shortlisting)') === false ||
    strpos($freshHtml, 'Shortlist &amp; Send Dossier Invitation Link') === false) {
    echo " FAILED: Shortlist call-to-action missing for fresh applicant." . PHP_EOL;
    exit(1);
}
if (strpos($freshHtml, 'Dossier Unlocked Upon Shortlisting') === false) {
    echo " FAILED: Gated dossier placeholder not displayed for fresh applicant." . PHP_EOL;
    exit(1);
}
if (strpos($freshHtml, 'Historical Legacy Questionnaire Responses') !== false) {
    echo " FAILED: Legacy questionnaire archive should NOT be displayed on fresh applicant." . PHP_EOL;
    exit(1);
}
echo " PASSED: Fresh Express Applicant displays contact, academic details, CV, shortlist CTA, and clean gated dossier." . PHP_EOL;

// 4. Test Complete Dossier Applicant (Status 4: Interview / Verification)
echo PHP_EOL . "[Test 4] Verifying Admin Review Console for Completed Verified Dossier (Status 4)..." . PHP_EOL;
// Add Qualifications, Skills, Work History, Referees, and E-Signature
$q = new Rosterqualification();
$q->rosterapplication = $freshApp->iD;
$q->qualificationtype = 1;
$q->title = 'HIT Software Development Certificate';
$q->institution_name = 'HIT';
$q->field_of_study = 'Computer Software';
$q->date_obtained = '2025-06-01';
$q->reg_by = $testUser->iD;
$q->save();

$sk = new Rosterskill();
$sk->rosterapplication = $freshApp->iD;
$sk->servicefunction = 1;
$sk->skillitem = 1;
$sk->proficiencylevel = 4; // Expert
$sk->reg_by = $testUser->iD;
$sk->save();

$wh = new Rosterworkhistory();
$wh->rosterapplication = $freshApp->iD;
$wh->organization_name = 'Econet Wireless';
$wh->position_title = 'Software Intern';
$wh->start_date = '2025-01-01';
$wh->end_date = '2025-06-30';
$wh->key_deliverables = 'Developed automated customer billing reconciliation scripts.';
$wh->reg_by = $testUser->iD;
$wh->save();

$ref = new Rosterreferee();
$ref->rosterapplication = $freshApp->iD;
$ref->referee_name = 'Eng. T. Mutasa';
$ref->organization = 'Econet Wireless';
$ref->position = 'Engineering Team Lead';
$ref->relationship = 'Direct Supervisor';
$ref->email = 'tmutasa@example.com';
$ref->phone = '+263771002003';
$ref->reg_by = $testUser->iD;
$ref->save();

$freshApp->applicationstatus = 4; // Interview / Verification (Dossier Complete)
$freshApp->e_signature = 'Kudzai Alignment Tester';
$freshApp->consent_timestamp = date('Y-m-d H:i:s');
$freshApp->consent_ip_address = '127.0.0.1';
$freshApp->update();

ob_start();
echo $controller->adminReviewConsole($freshApp->iD);
$dossierHtml = ob_get_clean();

if (strpos($dossierHtml, 'HIT Software Development Certificate') === false) {
    echo " FAILED: Verified qualification not displayed in review console." . PHP_EOL;
    exit(1);
}
if (strpos($dossierHtml, 'Econet Wireless') === false ||
    strpos($dossierHtml, 'Software Intern') === false ||
    strpos($dossierHtml, 'Developed automated customer billing') === false) {
    echo " FAILED: Work deliverables not displayed in review console." . PHP_EOL;
    exit(1);
}
if (strpos($dossierHtml, 'Eng. T. Mutasa') === false ||
    strpos($dossierHtml, 'Engineering Team Lead') === false ||
    strpos($dossierHtml, 'tmutasa@example.com') === false) {
    echo " FAILED: Verified referee not displayed in review console." . PHP_EOL;
    exit(1);
}
if (strpos($dossierHtml, 'Digitally signed by') === false) {
    echo " FAILED: Digital e-signature not displayed in review console." . PHP_EOL;
    exit(1);
}
if (strpos($dossierHtml, 'Verified Talent Dossier Completed') === false) {
    echo " FAILED: Dossier completed banner not displayed." . PHP_EOL;
    exit(1);
}
echo " PASSED: Completed Verified Dossier displays all qualifications, skills, deliverables, referees, and e-signature." . PHP_EOL;

// 5. Test Saving Assessment and Status Transition to On Roster (Status 5)
echo PHP_EOL . "[Test 5] Testing Assessment Scoring & Elevation to On Roster..." . PHP_EOL;
$_POST = [
    'rosterapplication' => $freshApp->iD,
    'eligibility_gate_passed' => 1,
    'technical_fit_score' => 28,
    'evidence_score' => 18,
    'judgement_score' => 22,
    'availability_score' => 14,
    'motivation_score' => 9,
    'vettingrecommendation' => 1, // Recommend for Roster
    'new_applicationstatus' => 5, // On Roster
    'interview_notes' => 'Exceptional technical depth and attachment availability verified.',
];

$assessmentRes = $controller->handleAssessmentSubmit();
if ($assessmentRes['status'] === 1 && (float)$assessmentRes['total_score'] === 91.0) {
    echo " PASSED: Assessment saved successfully with total score: {$assessmentRes['total_score']} / 100." . PHP_EOL;
} else {
    echo " FAILED: Assessment save returned: " . json_encode($assessmentRes) . PHP_EOL;
    exit(1);
}

$elevatedApp = (new Rosterapplication())->find($freshApp->iD);
if ((int)$elevatedApp->applicationstatus === 5) {
    echo " PASSED: Application status updated to 5 (On Roster - Active)." . PHP_EOL;
} else {
    echo " FAILED: Expected status 5, got: {$elevatedApp->applicationstatus}" . PHP_EOL;
    exit(1);
}

echo PHP_EOL . "=== ALL 5 ADMIN REVIEW & APPLICATION INTAKE ALIGNMENT TESTS PASSED! ===" . PHP_EOL;
