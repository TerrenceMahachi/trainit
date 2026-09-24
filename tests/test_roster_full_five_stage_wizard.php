<?php
/**
 * End-to-End Automated Test Suite:
 * Full 5-Stage Wizard Roster Application, Admin Review, and Stage 3 Statutory Onboarding.
 * 
 * Verifies:
 * 1. Guest completes 5-stage wizard with custom password -> auto-provisions user, saves all 5 stages, status = 2.
 * 2. Authenticated user completes 5-stage wizard for Associate track -> saves all 5 stages, status = 2.
 * 3. Admin review console renders complete candidate talent dossier across all 5 stages without gating.
 * 4. Admin submits 100-point scoring matrix -> application admitted to Active Talent Roster (status 5: on_roster).
 * 5. Candidate unlocks and completes Stage 3 Statutory Onboarding.
 * 6. Public and Dashboard routes properly route to the 5-stage wizard.
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\RosterApplicationController;
use App\Helpers\SiteConfig;
use App\Helpers\Auth;
use App\Models\User;
use App\Models\Login;
use App\Models\Rosterapplication;
use App\Models\Apprenticeprofile;
use App\Models\Associateprofile;
use App\Models\Rosterqualification;
use App\Models\Rosterskill;
use App\Models\Rosterworkhistory;
use App\Models\Rosterreferee;
use App\Models\Rosterjudgementresponse;
use App\Models\Rosterdocument;
use App\Models\Rosterassessment;
use App\Models\Rosteronboarding;
use App\Models\Skillitem;

global $siteConfig;
error_reporting(E_ALL & ~E_WARNING);
$siteConfig = new SiteConfig(
    _BASEURL,
    assetsUrl: _ASSETSURL,
    assetsLoc: _ASSETS_PATH,
    navLoc: _ASSETS_PATH . '/nav/ajax-pagination.php',
    siteName: _SITEDISPLAYNAME,
    defaultEmail: 'hello@trainit.co.zw'
);

echo "=== STARTING FULL 5-STAGE WIZARD & REVIEW TEST SUITE ===\n\n";

$controller = new RosterApplicationController();
$pdo = \App\Models\Database::sharedPdo();

// Clean session
Auth::logout();
$_SESSION = [];

// Prepare mock test files
$tmpCv = tempnam(sys_get_temp_dir(), 'test_cv_');
file_put_contents($tmpCv, "%PDF-1.4 Mock CV Curriculum Vitae for Talent Roster");

$tmpTranscript = tempnam(sys_get_temp_dir(), 'test_tr_');
file_put_contents($tmpTranscript, "%PDF-1.4 Mock Academic Transcript Record");

$tmpTax = tempnam(sys_get_temp_dir(), 'test_tax_');
file_put_contents($tmpTax, "%PDF-1.4 Mock ITF263 Tax Clearance Certificate");

// Get a few active skill items
$skillItems = Skillitem::findByQuery("SELECT * FROM skillitem WHERE servicefunction = 1 LIMIT 3");
$skill1 = $skillItems[0] ?? null;
$skill2 = $skillItems[1] ?? null;

// -----------------------------------------------------------------------------
// [Test 1] Guest Candidate: Full 5-Stage Apprentice Wizard Submission
// -----------------------------------------------------------------------------
echo "[Test 1] Testing Guest Candidate Full 5-Stage Apprentice Submission...\n";

$guestEmail = 'apprentice_five_' . bin2hex(random_bytes(4)) . '@example.co.zw';
$guestPass = 'CandidateP@ss2026';

$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

$_POST = [
    'is_submit' => '1',
    'applicationtrack' => 1, // Apprentice
    'primaryfunction' => 1,
    'secondary_functions' => [2],
    // Step 1: Personal & Account
    'legal_name' => 'Tatenda Chidambire',
    'preferred_name' => 'Tatenda',
    'email' => $guestEmail,
    'password' => $guestPass,
    'password_confirmation' => $guestPass,
    'mobile_number' => '+263 77 444 5566',
    'whatsapp_number' => '+263 77 444 5566',
    'date_of_birth' => '2001-05-14',
    'gender' => 1,
    'city' => 'Harare',
    'suburb' => 'Mount Pleasant',
    'zimprovince' => 1,
    'country' => 'Zimbabwe',
    'nationality' => 'Zimbabwean',
    'workrightstatus' => 1,
    // Step 1 Track Branch
    'apprenticestatus' => 1,
    'institution_name' => 'University of Zimbabwe',
    'degree_programme' => 'BSc Computer Science',
    'study_level' => 'Undergraduate Year 3 (WRL Attachment)',
    'student_reg_number' => 'R221045K',
    'expected_completion_date' => '2027-06-30',
    'is_wrl_attachment' => '1',
    'wrl_start_date' => '2026-10-01',
    'wrl_end_date' => '2027-09-30',
    'wrl_duration_months' => 12,
    'wrl_coordinator_name' => 'Dr. K. Gumbo',
    'wrl_coordinator_email' => 'kgumbo@comp.uz.ac.zw',
    'wrl_coordinator_phone' => '+263 77 888 9900',
    'requires_placement_letter' => '1',
    'requires_logbook_visits' => '1',
    'min_stipend_required' => 200,
    'engagementmodel' => 1,
    'worklocationpreference' => 1,
    // Step 2: Qualifications
    'qual_title' => ['Cambridge Advanced Level', 'O-Level Certificate'],
    'qual_type' => [1, 1],
    'qual_body' => [null, null],
    'qual_status' => [1, 1],
    'qual_inst' => ['St Georges College', 'St Georges College'],
    'qual_field' => ['Mathematics, Physics, Computing', 'Sciences & Humanities'],
    'qual_date' => ['2023-11-30', '2021-11-30'],
    // Step 3: Skills Matrix
    'skills' => [
        ($skill1 ? $skill1->iD : 1) => 4,
        ($skill2 ? $skill2->iD : 2) => 3,
    ],
    // Step 4: Work Experience & Referees
    'work_org' => ['TechNovate Solutions'],
    'work_title' => ['Junior Web Intern'],
    'work_sector' => [1],
    'work_basis' => [1],
    'work_start' => ['2025-12-01'],
    'work_end' => ['2026-02-28'],
    'work_deliverables' => ['Assisted in building responsive frontend portals using Bootstrap and PHP'],
    'work_reason' => ['Contract completed'],
    'referee_name' => ['Dr. K. Gumbo', 'Eng. S. Mutasa'],
    'ref_org' => ['University of Zimbabwe', 'TechNovate Solutions'],
    'ref_pos' => ['Senior Lecturer', 'Lead Engineer'],
    'ref_rel' => ['Academic Supervisor', 'Former Internship Supervisor'],
    'ref_email' => ['kgumbo@comp.uz.ac.zw', 'smutasa@technovate.co.zw'],
    'ref_phone' => ['+263 77 888 9900', '+263 71 222 3344'],
    'ref_timing' => [2, 2],
    // Step 5: Situational Judgement & Declaration
    'motivation_narrative' => 'Eager to apply software engineering principles on high-impact client systems.',
    'primary_function_evidence' => 'Engineered a student attendance tracking prototype using PHP, MySQL, and REST APIs.',
    'shared_client_management_plan' => 'Maintain daily scrum logs, time-box assignments to 4-hour sprints, and communicate blocks early.',
    'urgent_friday_deadline_dilemma' => 'Triage error stack trace, inform project manager immediately, produce a regression patch, and document root cause.',
    'e_signature' => 'Tatenda Chidambire',
];

$_FILES = [
    'cv_doc' => [
        'name' => 'Tatenda_Chidambire_CV.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $tmpCv,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($tmpCv),
    ],
    'transcript_doc' => [
        'name' => 'Tatenda_Academic_Transcript.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $tmpTranscript,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($tmpTranscript),
    ],
];

$res1 = $controller->handleSubmission();
if (!isset($res1['status']) || $res1['status'] !== 1) {
    echo "FAILED: handleSubmission returned error: " . json_encode($res1) . "\n";
    exit(1);
}

$appId1 = (int)$res1['application_id'];
$app1 = (new Rosterapplication())->find($appId1);

assert($app1 !== null, "Application #$appId1 should exist");
assert((int)$app1->applicationstatus === 2, "Application should have status = 2 (Submitted)");
assert((int)$app1->applicationtrack === 1, "Track should be 1 (Apprentice)");
assert($app1->e_signature === 'Tatenda Chidambire', "Digital e-signature should be saved");

// Verify candidate user was created and logged in
$createdUser = (new User())->find($app1->user);
assert($createdUser !== null, "User record should have been auto-provisioned");
assert($createdUser->email === $guestEmail, "User email must match guest email");
assert((int)Auth::id() === (int)$createdUser->iD, "Candidate should be automatically logged in");

// Verify all 5 stages in DB
$appProfile = Apprenticeprofile::findByQuery("SELECT * FROM apprenticeprofile WHERE rosterapplication = ?", [$appId1]);
assert(!empty($appProfile), "Apprentice profile record must exist");
assert($appProfile[0]->degree_programme === 'BSc Computer Science', "Degree programme must match");

$quals1 = Rosterqualification::findByQuery("SELECT * FROM rosterqualification WHERE rosterapplication = ?", [$appId1]);
assert(count($quals1) === 2, "Expected 2 qualification records, found " . count($quals1));

$skills1 = Rosterskill::findByQuery("SELECT * FROM rosterskill WHERE rosterapplication = ?", [$appId1]);
assert(count($skills1) >= 2, "Expected at least 2 skill ratings, found " . count($skills1));

$work1 = Rosterworkhistory::findByQuery("SELECT * FROM rosterworkhistory WHERE rosterapplication = ?", [$appId1]);
assert(count($work1) === 1, "Expected 1 work history record, found " . count($work1));

$refs1 = Rosterreferee::findByQuery("SELECT * FROM rosterreferee WHERE rosterapplication = ?", [$appId1]);
assert(count($refs1) === 2, "Expected 2 referees, found " . count($refs1));

$judge1 = Rosterjudgementresponse::findByQuery("SELECT * FROM rosterjudgementresponse WHERE rosterapplication = ?", [$appId1]);
assert(!empty($judge1), "Situational judgement response must exist");
assert(!empty($judge1[0]->primary_function_evidence), "Primary function evidence must be saved");

$docs1 = Rosterdocument::findByQuery("SELECT * FROM rosterdocument WHERE rosterapplication = ?", [$appId1]);
assert(!empty($docs1), "Roster document records must exist (CV / Transcripts)");

echo " PASSED: Guest Apprentice completed full 5 stages -> Account provisioned (User #{$createdUser->iD}), Application #$appId1 submitted with all 5 stages saved.\n";

// -----------------------------------------------------------------------------
// [Test 2] Authenticated Candidate: Full 5-Stage Associate Wizard Submission
// -----------------------------------------------------------------------------
echo "\n[Test 2] Testing Authenticated Candidate Full 5-Stage Associate Submission...\n";

// Create authenticated user
$assocUser = new User();
$assocUser->name = 'Tendai Mukaro';
$assocUser->email = 'assoc_test_' . bin2hex(random_bytes(4)) . '@tsigiro.co.zw';
$assocUser->role = 2;
$assocUser->reg_by = 1;
$assocUser->save();

$assocLogin = new Login();
$assocLogin->user = $assocUser->iD;
$assocLogin->password = password_hash('AssociateP@ss2026', PASSWORD_BCRYPT);
$assocLogin->reg_by = 1;
$assocLogin->save();

Auth::login($assocUser->iD);

$_POST = [
    'is_submit' => '1',
    'applicationtrack' => 2, // Associate
    'primaryfunction' => 1,
    'secondary_functions' => [3],
    // Step 1: Personal
    'legal_name' => 'Tendai Mukaro',
    'preferred_name' => 'Tendai',
    'email' => $assocUser->email,
    'mobile_number' => '+263 71 555 7788',
    'city' => 'Bulawayo',
    'suburb' => 'Suburbs',
    'zimprovince' => 2,
    'country' => 'Zimbabwe',
    'nationality' => 'Zimbabwean',
    'workrightstatus' => 1,
    // Step 1 Track Branch (Associate Profile)
    'employmentstatus' => 2, // Independent Consultant
    'years_experience' => '12',
    'donor_experience_years' => '8',
    'donors_worked_with' => ['USAID', 'EU', 'FCDO'],
    'largest_budget_handled' => 'USD 1,200,000',
    'largest_team_supervised' => 15,
    'day_rate_expectation' => 350.00,
    'capacity_days_per_month' => '10-15 days',
    'notice_period' => '2 weeks',
    'invoiceentitytype' => 2, // Registered Private Business Corporation (PBC)
    'has_tax_clearance_itf263' => '1',
    'zimra_bp_number' => 'BP200192837',
    'cv_bid_consent' => 'yes',
    // Step 2: Qualifications
    'qual_title' => ['MSc Information Systems Management', 'BSc Computer Science'],
    'qual_type' => [2, 1],
    'qual_body' => [null, null],
    'qual_status' => [1, 1],
    'qual_inst' => ['University of Liverpool', 'National University of Science and Technology'],
    'qual_field' => ['Enterprise Systems Architecture', 'Computer Science'],
    'qual_date' => ['2018-07-15', '2012-06-30'],
    // Step 3: Skills Matrix
    'skills' => [
        ($skill1 ? $skill1->iD : 1) => 5,
        ($skill2 ? $skill2->iD : 2) => 5,
    ],
    // Step 4: Work Experience & Referees
    'work_org' => ['Apex Digital Consulting', 'Standard Chartered Bank'],
    'work_title' => ['Principal Solution Architect', 'Senior Systems Analyst'],
    'work_sector' => [2, 1],
    'work_basis' => [2, 1],
    'work_start' => ['2019-01-01', '2013-03-01'],
    'work_end' => [null, '2018-12-31'],
    'work_current' => ['1', null],
    'work_deliverables' => [
        'Designed scalable cloud architectures and audited enterprise core banking integrations.',
        'Maintained high-throughput transaction processing pipelines with 99.99% SLA.'
    ],
    'work_reason' => ['Ongoing consultancy', 'Career advancement'],
    'referee_name' => ['Farai Chitiyo', 'Chipo Nyamupfukudza'],
    'ref_org' => ['Apex Digital Consulting', 'Standard Chartered Bank'],
    'ref_pos' => ['Managing Director', 'Head of Enterprise Engineering'],
    'ref_rel' => ['Consulting Partner', 'Direct Supervisor'],
    'ref_email' => ['fchitiyo@apexdigital.co.zw', 'cnyamupfukudza@sc.com'],
    'ref_phone' => ['+263 77 333 4455', '+263 77 999 1122'],
    'ref_timing' => [1, 1],
    // Step 5: Situational Judgement & Declaration
    'motivation_narrative' => 'Seeking to provide technical leadership and mentor rising apprentices across national digital infrastructure projects.',
    'primary_function_evidence' => 'Architected and led the deployment of a 15-node distributed API gateway handling 10M daily transactions.',
    'shared_client_management_plan' => 'Establish bi-weekly stakeholder steers, strict milestone tracking via Jira, and proactive risk escalations.',
    'urgent_friday_deadline_dilemma' => 'Initiate emergency incident room, isolate defect to rollback branch, issue hotfix within 45 mins, and conduct post-mortem on Monday.',
    'e_signature' => 'Tendai Mukaro',
];

$_FILES = [
    'cv_doc' => [
        'name' => 'Tendai_Mukaro_Principal_CV.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $tmpCv,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($tmpCv),
    ],
    'tax_clearance_doc' => [
        'name' => 'ITF263_Apex_Tax_Clearance.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $tmpTax,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($tmpTax),
    ],
];

$res2 = $controller->handleSubmission();
if (!isset($res2['status']) || $res2['status'] !== 1) {
    echo "FAILED: handleSubmission for Associate returned error: " . json_encode($res2) . "\n";
    exit(1);
}

$appId2 = (int)$res2['application_id'];
$app2 = (new Rosterapplication())->find($appId2);

assert($app2 !== null, "Application #$appId2 must exist");
assert((int)$app2->applicationstatus === 2, "Status must be 2 (Submitted)");
assert((int)$app2->applicationtrack === 2, "Track must be 2 (Associate)");
assert((int)$app2->user === (int)$assocUser->iD, "Must link to authenticated user");

// Verify associate profile & documents
$assocProf = Associateprofile::findByQuery("SELECT * FROM associateprofile WHERE rosterapplication = ?", [$appId2]);
assert(!empty($assocProf), "Associate profile must exist");
assert((float)$assocProf[0]->day_rate_expectation === 350.00, "Day rate must match");

$assocDocs = Rosterdocument::findByQuery("SELECT * FROM rosterdocument WHERE rosterapplication = ?", [$appId2]);
assert(!empty($assocDocs), "Associate documents (CV, Tax Clearance) must be saved");

echo " PASSED: Authenticated Associate completed full 5 stages -> Application #$appId2 submitted.\n";

// -----------------------------------------------------------------------------
// [Test 3] Admin Review Console: Complete Talent Dossier Un-gating
// -----------------------------------------------------------------------------
echo "\n[Test 3] Verifying Admin Review Console & Complete Talent Dossier Display...\n";

// Authenticate as Admin
$admin = (new User())->find(1);
Auth::login($admin->iD);

ob_start();
$reviewHtml = $controller->adminReviewConsole($appId2);
$bufferedReview = ob_get_clean();
$fullReviewHtml = $reviewHtml . $bufferedReview;

// Verify that the dossier is UN-GATED and contains all stages
assert(strpos($fullReviewHtml, 'Stages 2–5: Candidate Talent Dossier') !== false, "Dossier title must be present");
assert(strpos($fullReviewHtml, 'Stage 2: Qualifications &amp; Certifications') !== false, "Stage 2 qualifications section must be present");
assert(strpos($fullReviewHtml, 'Stage 3: Self-Assessed Competencies') !== false, "Stage 3 skills matrix must be present");
assert(strpos($fullReviewHtml, 'Stage 4: Work Experience &amp; Deliverables') !== false, "Stage 4 work history must be present");
assert(strpos($fullReviewHtml, 'Stage 4: Referees &amp; Academic Supervisors') !== false, "Stage 4 referees must be present");
assert(strpos($fullReviewHtml, 'Stage 5: Situational Judgement &amp; Evidence Narratives') !== false, "Stage 5 judgement section must be active & styled");
assert(strpos($fullReviewHtml, 'Digital Declaration &amp; E-Signature') !== false, "Stage 5 e-signature must be present");

// Verify that deprecated shortlist gate & text are completely absent
assert(strpos($fullReviewHtml, 'Dossier Unlocked Upon Shortlisting') === false, "Deprecated shortlist lock must not appear");
assert(strpos($fullReviewHtml, 'Pending Shortlisting') === false, "Deprecated Pending Shortlisting badge must not appear");
assert(strpos($fullReviewHtml, 'btn_shortlist_action') === false, "Deprecated shortlist button must not appear");

// Verify candidate content is rendered
assert(strpos($fullReviewHtml, 'Tendai Mukaro') !== false, "Candidate name must appear in review");
assert(strpos($fullReviewHtml, 'Apex Digital Consulting') !== false, "Work history must appear in review");
assert(strpos($fullReviewHtml, 'Farai Chitiyo') !== false, "Referee must appear in review");
assert(strpos($fullReviewHtml, '15-node distributed API gateway') !== false, "Judgement narrative must appear in review");

echo " PASSED: Admin review console displays unified 5-stage candidate talent dossier without gating.\n";

// -----------------------------------------------------------------------------
// [Test 4] Admin 100-Point Scoring Matrix & Admission to Active Talent Roster
// -----------------------------------------------------------------------------
echo "\n[Test 4] Submitting 100-Point Vetting Assessment & Admitting Candidate to Active Roster...\n";

$_POST = [
    'rosterapplication' => $appId2,
    'eligibility_gate_passed' => '1',
    'score_technical_depth' => 28,      // max 30
    'score_work_experience' => 23,      // max 25
    'score_problem_solving' => 18,      // max 20
    'score_communication' => 14,        // max 15
    'score_cultural_alignment' => 9,    // max 10
    'vettingrecommendation' => 1,       // 1: Recommend Admission
    'status_transition' => 5,           // 5: on_roster (Active Talent Roster)
    'review_notes' => 'Exceptional technical depth, verified reference from Apex Digital, high delivery maturity. Admitted to Associate Talent Roster.',
];

$assessRes = $controller->handleAssessmentSubmit();
if (!isset($assessRes['status']) || $assessRes['status'] !== 1) {
    echo "FAILED: handleAssessmentSubmit failed: " . json_encode($assessRes) . "\n";
    exit(1);
}

assert((int)$assessRes['total_score'] === 92, "Total score should equal 28+23+18+14+9 = 92");
assert((int)$assessRes['new_status_id'] === 5, "New status ID must be 5 (on_roster)");

// Verify database records
$app2Updated = (new Rosterapplication())->find($appId2);
assert((int)$app2Updated->applicationstatus === 5, "Application status must be updated to 5 (on_roster)");

$assessment = Rosterassessment::findByQuery("SELECT * FROM rosterassessment WHERE rosterapplication = ?", [$appId2]);
assert(!empty($assessment), "Rosterassessment record must exist");
assert((float)$assessment[0]->total_score == 92, "Assessment score must be 92");
assert((int)$assessment[0]->eligibility_gate_passed === 1, "Eligibility gate must be passed");

echo " PASSED: Assessment scored (92/100) and candidate successfully admitted to Active Talent Roster (Status = 5: on_roster).\n";

// -----------------------------------------------------------------------------
// [Test 5] Stage 3 Statutory Onboarding Access and Completion
// -----------------------------------------------------------------------------
echo "\n[Test 5] Verifying Candidate Unlocks and Completes Stage 3 Statutory Onboarding...\n";

// Switch back to Candidate session
Auth::login($assocUser->iD);

// 1. Candidate views application detail
ob_start();
$detailHtml = $controller->showApplicationDetail($appId2);
$bufferedDetail = ob_get_clean();
$fullDetailHtml = $detailHtml . $bufferedDetail;

assert(strpos($fullDetailHtml, 'Stage 3 Statutory Onboarding Unlocked!') !== false, "Stage 3 callout must be shown to candidate");
assert(strpos($fullDetailHtml, 'dashboard/application/onboarding?id=' . $appId2) !== false, "Link to Stage 3 onboarding must be present");

// 2. Candidate views onboarding form
ob_start();
$onboardHtml = $controller->showOnboardingForm($appId2);
$bufferedOnboard = ob_get_clean();
$fullOnboardHtml = $onboardHtml . $bufferedOnboard;

assert(strpos($fullOnboardHtml, 'Stage 3: Statutory Onboarding') !== false, "Stage 3 onboarding view must render");

// 3. Candidate submits Stage 3 Statutory Onboarding details
$tmpIdCopy = tempnam(sys_get_temp_dir(), 'test_id_');
file_put_contents($tmpIdCopy, "%PDF-1.4 Mock National ID Document");

$_POST = [
    'rosterapplication' => $appId2,
    'national_id_number' => '63-9876543-A-09',
    'street_address' => '42 Robert Mugabe Way',
    'city' => 'Bulawayo',
    'bank_name' => 'Stanbic Bank Zimbabwe',
    'bank_branch' => 'Bulawayo Main',
    'bank_branch_code' => '03100',
    'bank_account_number' => '9140001234567',
    'bank_account_name' => 'Tendai Mukaro',
    'bank_account_type' => 'Current (USD Nostro)',
    'emergency_contact_name' => 'Sipho Mukaro',
    'emergency_contact_phone' => '+263 71 888 4433',
    'emergency_contact_relationship' => 'Spouse',
    'signed_code_of_conduct' => '1',
    'signed_ip_agreement' => '1',
    'statutory_consent_timestamp' => date('Y-m-d H:i:s'),
];

$_FILES = [
    'national_id_doc' => [
        'name' => 'Tendai_National_ID.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $tmpIdCopy,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($tmpIdCopy),
    ],
];

$onboardRes = $controller->handleOnboardingSubmit();
assert(isset($onboardRes['status']) && $onboardRes['status'] === 1, "Onboarding submission should succeed");

$onboardingRec = Rosteronboarding::findByQuery("SELECT * FROM rosteronboarding WHERE rosterapplication = ?", [$appId2]);
assert(!empty($onboardingRec), "Rosteronboarding record must be saved in database");
assert($onboardingRec[0]->national_id_number === '63-9876543-A-09', "National ID must match");
assert($onboardingRec[0]->account_number === '9140001234567', "Bank account must match");

echo " PASSED: Candidate completed Stage 3 Statutory Onboarding with banking & statutory ID verification.\n";

// -----------------------------------------------------------------------------
// [Test 6] Routing Verification: Public and Dashboard Endpoints Route to 5-Stage Wizard
// -----------------------------------------------------------------------------
echo "\n[Test 6] Verifying Routes Direct to 5-Stage Wizard...\n";

// Test unauthenticated showApplyForm
Auth::logout();
$_SESSION = [];

ob_start();
$apprForm = $controller->showApplyForm('apprentice');
$bufAppr = ob_get_clean();
$fullAppr = $apprForm . $bufAppr;

assert(strpos($fullAppr, 'Apply as Apprentice') !== false, "Apprentice wizard heading must appear");
assert(strpos($fullAppr, '1. Identity') !== false, "Step 1 Identity tab must appear");
assert(strpos($fullAppr, '2. Education') !== false, "Step 2 Education tab must appear");
assert(strpos($fullAppr, '3. Skills') !== false, "Step 3 Skills tab must appear");
assert(strpos($fullAppr, '4. Experience') !== false, "Step 4 Experience tab must appear");
assert(strpos($fullAppr, '5. Consent') !== false, "Step 5 Consent tab must appear");
assert(strpos($fullAppr, 'Create Account Password') !== false, "Password intake for guest must appear on Step 1");

ob_start();
$assocForm = $controller->showApplyForm('associate');
$bufAssoc = ob_get_clean();
$fullAssoc = $assocForm . $bufAssoc;

assert(strpos($fullAssoc, 'Apply as Associate') !== false, "Associate wizard heading must appear");
assert(strpos($fullAssoc, '1. Identity') !== false, "Step 1 tab must appear on Associate form");
assert(strpos($fullAssoc, '5. Consent') !== false, "Step 5 tab must appear on Associate form");

echo " PASSED: Routes consistently render the full 5-stage application wizard.\n";

// Clean up temporary test files
@unlink($tmpCv);
@unlink($tmpTranscript);
@unlink($tmpTax);
@unlink($tmpIdCopy);

echo "\n========================================================================\n";
echo ">>> ALL 6 FULL 5-STAGE WIZARD & REVIEW TEST SUITES PASSED 100%! <<<\n";
echo "========================================================================\n";
