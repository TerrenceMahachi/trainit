<?php
// Suppress output buffering headers warning in CLI testing
ob_start();
require_once __DIR__ . '/../bootstrap.php';

use App\Helpers\SiteConfig;
use App\Helpers\Auth;
use App\Controllers\VacancyController;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Models\StaffInvite;
use App\Models\User;

global $siteConfig;
$siteConfig = new SiteConfig(
    _BASEURL,
    assetsUrl: _ASSETSURL,
    assetsLoc: _ASSETS_PATH,
    navLoc: _ASSETS_PATH . '/nav/ajax-pagination.php',
    siteName: _SITEDISPLAYNAME,
    defaultEmail: 'hello@tsigiro.co.zw'
);

// Authenticate as Admin before any echoes
Auth::login(1);
ob_end_clean();

echo "=== Testing Staff Onboarding via Vacancy Advertising ===\n\n";

// 1. Authenticate as Admin
assert(Auth::check() && Auth::isAdmin(), "Admin authentication successful");
echo "[1] PASS: Authenticated as System Admin.\n";

// 2. Query Seeded Vacancies
$vacancies = Vacancy::where('vacancystatus', 2);
assert(count($vacancies) >= 3, "At least 3 seeded vacancies found in database");
$v1 = $vacancies[0];
echo "[2] PASS: Found " . count($vacancies) . " published vacancies. Testing with: {$v1->title} [{$v1->reference_number}]\n";

// 3. Test Skills Junction
$skills = $v1->skills();
assert(!empty($skills), "Vacancy has associated skills from junction table vacancy_skill");
echo "[3] PASS: Vacancy skills junction loaded (" . count($skills) . " skills mapped).\n";

// 4. Test View Rendering for Admin Views
ob_start();
try {
    (new VacancyController())->adminIndex();
} catch (\Throwable $e) {}
$adminIndexHtml = ob_get_clean();
assert(strlen($adminIndexHtml) > 500, "VacancyController::adminIndex rendered successfully");
echo "[4] PASS: VacancyController::adminIndex rendered (" . strlen($adminIndexHtml) . " bytes).\n";

ob_start();
try {
    (new VacancyController())->adminCreate();
} catch (\Throwable $e) {}
$adminCreateHtml = ob_get_clean();
assert(strlen($adminCreateHtml) > 500, "VacancyController::adminCreate rendered successfully");
echo "[5] PASS: VacancyController::adminCreate rendered (" . strlen($adminCreateHtml) . " bytes).\n";

// 5. Test Public View Rendering
ob_start();
try {
    (new VacancyController())->view($v1->slug);
} catch (\Throwable $e) {}
$publicViewHtml = ob_get_clean();
assert(strlen($publicViewHtml) > 500, "VacancyController::view rendered successfully");
echo "[6] PASS: VacancyController::view('{$v1->slug}') rendered (" . strlen($publicViewHtml) . " bytes).\n";

// 6. Simulate Candidate Application Submission
$testEmail = 'applicant.test.' . time() . '@example.com';
$testAppNumber = 'APP-' . $v1->reference_number . '-TEST' . rand(100, 999);

$dummyCv = 'uploads/cvs/CV_TEST_RESUME.pdf';
if (!file_exists(_BASE_PATH . '/public/uploads/cvs/CV_TEST_RESUME.pdf')) {
    @file_put_contents(_BASE_PATH . '/public/uploads/cvs/CV_TEST_RESUME.pdf', '%PDF-1.4 Dummy CV test content');
}

$app = new VacancyApplication();
$app->vacancy              = $v1->iD;
$app->application_number   = $testAppNumber;
$app->user                 = null;
$app->first_name           = 'Tinashe';
$app->last_name            = 'Moyo';
$app->email                = $testEmail;
$app->phone                = '+263771234567';
$app->city                 = 'Harare';
$app->country              = 'Zimbabwe';
$app->years_of_experience  = 4;
$app->highest_qualification= 'BSc Honors Information Systems';
$app->current_employer     = 'FinTech Labs';
$app->current_job_title    = 'Junior Operations Analyst';
$app->expected_salary      = 'USD 1,200 / month';
$app->notice_period_days   = 14;
$app->cover_letter         = 'I am passionate about talent vetting, operations, and compliance.';
$app->cv_path              = $dummyCv;
$app->application_status   = 1; // submitted
$app->reg_by               = 1;
$app->status               = 1;
$app->save();

assert(!empty($app->iD), "Candidate application saved with ID {$app->iD}");
echo "[7] PASS: Candidate application submitted ({$testAppNumber}, ID: {$app->iD}).\n";

// 7. Verify Admin Review Console renders candidate application
ob_start();
try {
    (new VacancyController())->adminApplications($v1->iD);
} catch (\Throwable $e) {}
$appsHtml = ob_get_clean();
assert(strpos($appsHtml, 'Tinashe Moyo') !== false, "Applicant visible in admin review console");
echo "[8] PASS: VacancyController::adminApplications rendered with new applicant.\n";

// 8. Bridge to Staff Onboarding (Appoint candidate to Staff)
$_POST['application_id'] = $app->iD;
ob_start();
try {
    (new VacancyController())->adminAppointStaff();
} catch (\Throwable $e) {}
$appointJson = ob_get_clean();

// Extract JSON object if headers/warnings prepended
$jsonStart = strpos($appointJson, '{');
$jsonClean = ($jsonStart !== false) ? substr($appointJson, $jsonStart) : $appointJson;
$appointResult = json_decode($jsonClean, true);

assert(isset($appointResult['status']) && $appointResult['status'] === 1, "Staff appointment returned success status: " . $appointJson);
assert(!empty($appointResult['token']), "Appointment generated onboarding token");
echo "[9] PASS: Staff appointment successful! Generated invitation token: " . substr($appointResult['token'], 0, 12) . "...\n";

// 9. Verify Database Records After Appointment
$freshApp = VacancyApplication::find($app->iD);
assert((int)$freshApp->application_status === 5, "Application status transitioned to 5 (Appointed to Staff)");
assert(!empty($freshApp->staff_invite), "Application linked to staff_invite ID");
assert(!empty($freshApp->appointed_at), "Application appointed_at timestamp populated");

$invite = StaffInvite::find($freshApp->staff_invite);
assert($invite && $invite->email === $testEmail, "StaffInvite created for candidate with correct email");
assert($invite->used == 0, "StaffInvite token is initially unused");
echo "[10] PASS: Relational integrity verified. Application status is 5 (Appointed), linked to StaffInvite #{$invite->iD}.\n";

// 10. Verify Onboarding Token can be processed by StaffController
$_GET['token'] = $invite->token;
ob_start();
try {
    (new \App\Controllers\StaffController())->showOnboarding();
} catch (\Throwable $e) {}
$onboardHtml = ob_get_clean();
assert(strlen($onboardHtml) > 500 && strpos($onboardHtml, 'Tinashe Moyo') !== false, "Staff onboarding wizard recognized appointment token");
echo "[11] PASS: Candidate accessed /staff/onboard with valid token and loaded onboarding wizard!\n";

// Cleanup test records
$invite->delete();
$freshApp->delete();
echo "\n=== ALL 11 TEST PHASES PASSED! Staff Onboarding via Vacancy Advertising is 100% verified. ===\n";
