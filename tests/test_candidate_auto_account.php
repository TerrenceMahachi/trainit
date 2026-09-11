<?php
ob_start();
require_once __DIR__ . '/../bootstrap.php';

use App\Helpers\SiteConfig;
use App\Helpers\Auth;
use App\Controllers\VacancyController;
use App\Controllers\RosterApplicationController;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Models\Rosterapplication;
use App\Models\User;
use App\Models\Login;

global $siteConfig;
$siteConfig = new SiteConfig(
    _BASEURL,
    assetsUrl: _ASSETSURL,
    assetsLoc: _ASSETS_PATH,
    navLoc: _ASSETS_PATH . '/nav/ajax-pagination.php',
    siteName: _SITEDISPLAYNAME,
    defaultEmail: 'hello@tsigiro.co.zw'
);

ob_end_clean();

echo "=== Testing Candidate Auto-Account Provisioning, Auto-Login & Tracking Dashboard ===\n\n";

// Clear any existing cookies/session
$_COOKIE = [];

// Phase 1: Test Guest Vacancy Application Submission
echo "--- Phase 1: Guest Vacancy Application (Auto-Provision & Auto-Login) ---\n";
$vacancies = Vacancy::where('vacancystatus', 2);
assert(!empty($vacancies), "Published vacancies available for testing");
$v = $vacancies[0];

$uniqueGuestEmail = 'guest.applicant.' . time() . '.' . rand(100, 999) . '@tsigiro-test.zw';
$uniqueFirstName = 'Farai';
$uniqueLastName = 'Chikwanha';

// Set up fake $_FILES for CV upload
$testCvPath = _BASE_PATH . '/public/uploads/cvs/TEST_AUTO_CV.pdf';
if (!file_exists($testCvPath)) {
    @file_put_contents($testCvPath, '%PDF-1.4 Mock CV content for Farai Chikwanha');
}
$_FILES['cv'] = [
    'name'     => 'Farai_Chikwanha_CV.pdf',
    'type'     => 'application/pdf',
    'tmp_name' => $testCvPath,
    'error'    => UPLOAD_ERR_OK,
    'size'     => filesize($testCvPath)
];

$_POST = [
    'first_name'            => $uniqueFirstName,
    'last_name'             => $uniqueLastName,
    'email'                 => $uniqueGuestEmail,
    'phone'                 => '+263 77 999 8888',
    'city'                  => 'Harare',
    'country'               => 'Zimbabwe',
    'years_of_experience'   => '3',
    'highest_qualification' => 'BCom Information Technology',
    'current_employer'      => 'Apex Systems',
    'current_job_title'     => 'Solutions Specialist',
    'expected_salary'       => '$1,500',
    'notice_period_days'    => '30',
    'cover_letter'          => 'Excited to contribute to Tsigiro mission.'
];

ob_start();
(new VacancyController())->apply($v->slug);
$applyOutput = ob_get_clean();

$jsonStart = strpos($applyOutput, '{');
$jsonClean = ($jsonStart !== false) ? substr($applyOutput, $jsonStart) : $applyOutput;
$applyResult = json_decode($jsonClean, true);
assert(is_array($applyResult) && isset($applyResult['status']) && $applyResult['status'] === 1, "Vacancy application succeeded: " . $applyOutput);
assert(!empty($applyResult['application_number']), "Application number generated");
assert(strpos($applyResult['redirect'], '/dashboard') !== false, "Redirect URL points to /dashboard: " . ($applyResult['redirect'] ?? ''));

echo "[1.1] PASS: Guest application submitted successfully. Application Ref: {$applyResult['application_number']}\n";
echo "[1.2] PASS: Redirect points to: {$applyResult['redirect']}\n";

// Verify User and Login records were created
$createdUsers = User::findByQuery("SELECT * FROM user WHERE email = ?", [$uniqueGuestEmail]);
assert(!empty($createdUsers), "User record auto-provisioned for guest");
$createdUser = $createdUsers[0];
assert((int)$createdUser->role === 2, "User role set to 2 (Candidate)");
echo "[1.3] PASS: User record created (ID: {$createdUser->iD}, Name: {$createdUser->name}, Role: {$createdUser->role}).\n";

$createdLogins = Login::findByQuery("SELECT * FROM user_login WHERE user = ?", [$createdUser->iD]);
assert(!empty($createdLogins), "Login record created for user");
assert((int)$createdLogins[0]->status === 1, "Login status is active");
echo "[1.4] PASS: Login credentials provisioned with active status.\n";

// Verify candidate is immediately logged in
assert(Auth::check(), "Candidate is immediately authenticated after applying");
assert((int)Auth::id() === (int)$createdUser->iD, "Authenticated user ID matches provisioned user ID");
echo "[1.5] PASS: Candidate auto-logged in via Auth session (User ID: " . Auth::id() . ").\n";

// Verify VacancyApplication record links to user
$vacApps = VacancyApplication::findByQuery("SELECT * FROM vacancy_application WHERE email = ?", [$uniqueGuestEmail]);
assert(!empty($vacApps), "VacancyApplication record stored");
$vacApp = $vacApps[0];
assert((int)$vacApp->user === (int)$createdUser->iD, "VacancyApplication foreign key 'user' matches created user ID");
assert((int)$vacApp->application_status === 1, "VacancyApplication status is 1 (Submitted)");
echo "[1.6] PASS: VacancyApplication foreign key user correctly linked.\n";

// Phase 2: Test Vacancy Detail Page When Already Applied
echo "\n--- Phase 2: Duplicate Application Guard on Vacancy View ---\n";
ob_start();
(new VacancyController())->view($v->slug);
$viewOutput = ob_get_clean();

assert(strpos($viewOutput, 'Application Already Received') !== false, "Show view detected existing application for logged-in user");
assert(strpos($viewOutput, $vacApp->application_number) !== false, "Show view rendered candidate's application reference number");
assert(strpos($viewOutput, 'Track Application on Dashboard') !== false, "Show view provided direct dashboard tracking CTA");
echo "[2.1] PASS: Vacancy page detected candidate has already applied and rendered tracking card.\n";

// Phase 3: Test Candidate Portal Dashboard Rendering
echo "\n--- Phase 3: Candidate Portal Dashboard Rendering (/dashboard) ---\n";
$dashboardData = [
    'title' => 'Candidate Dashboard',
    'user'  => $createdUser,
];

ob_start();
echo view('dashboard.client', ['data' => $dashboardData]);
$dashboardHtml = ob_get_clean();

assert(strlen($dashboardHtml) > 500, "Client dashboard rendered successfully");
assert(strpos($dashboardHtml, $vacApp->application_number) !== false, "Dashboard renders Vacancy Application reference number");
assert(strpos($dashboardHtml, htmlspecialchars($v->title)) !== false, "Dashboard renders Job Vacancy title: {$v->title}");
assert(strpos($dashboardHtml, 'Application Received') !== false, "Dashboard renders status badge 'Application Received'");
assert(strpos($dashboardHtml, 'Job Vacancy Applications') !== false, "Dashboard section header 'Job Vacancy Applications' rendered");
echo "[3.1] PASS: Candidate dashboard renders vacancy application card with status and metadata.\n";

// Phase 4: Test Apprentice / Associate Auto-Provision & Tracking
echo "\n--- Phase 4: Roster Express Intake Auto-Provision & Dashboard Multi-Tracking ---\n";
// Clear session for a new guest applicant
$_COOKIE = [];
$guestRosterEmail = 'guest.apprentice.' . time() . '.' . rand(100, 999) . '@tsigiro-test.zw';
$rosterName = 'Chipo Moyo';

$_POST = [
    'track_code'         => 'apprentice',
    'email'              => $guestRosterEmail,
    'legal_name'         => $rosterName,
    'preferred_name'     => 'Chipo',
    'mobile_number'      => '+263 77 111 2222',
    'city'               => 'Bulawayo',
    'institution_name'   => 'NUST Zimbabwe',
    'degree_programme'   => 'BSc Computer Science',
    'study_level'        => 'Seeking Attachment (Year 3/Part 3)',
    'wrl_start_date'     => date('Y-m-d'),
    'wrl_duration_months'=> '12',
];
$_FILES['cv_doc'] = [
    'name'     => 'Chipo_Moyo_CV.pdf',
    'type'     => 'application/pdf',
    'tmp_name' => $testCvPath,
    'error'    => UPLOAD_ERR_OK,
    'size'     => filesize($testCvPath)
];
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

$rosterRes = (new RosterApplicationController())->handleExpressSubmit();
assert(is_array($rosterRes) && isset($rosterRes['status']) && $rosterRes['status'] === 1, "Roster express submission succeeded");
assert(Auth::check(), "Roster applicant is immediately logged in");

$rosterUser = User::findByQuery("SELECT * FROM user WHERE email = ?", [$guestRosterEmail])[0];
assert((int)Auth::id() === (int)$rosterUser->iD, "Authenticated user ID matches newly created roster user");
echo "[4.1] PASS: Guest Apprentice application submitted and user auto-provisioned & logged in.\n";

// Now simulate this candidate also applying for a vacancy to test multi-profile dashboard
$multiVacApp = new VacancyApplication();
$multiVacApp->vacancy = $v->iD;
$multiVacApp->application_number = 'APP-' . $v->reference_number . '-MULTI' . rand(100, 999);
$multiVacApp->user = $rosterUser->iD;
$multiVacApp->first_name = 'Chipo';
$multiVacApp->last_name = 'Moyo';
$multiVacApp->email = $guestRosterEmail;
$multiVacApp->phone = '+263 77 111 2222';
$multiVacApp->city = 'Bulawayo';
$multiVacApp->country = 'Zimbabwe';
$multiVacApp->cv_path = 'uploads/cvs/TEST_AUTO_CV.pdf';
$multiVacApp->application_status = 3; // Interview Scheduled
$multiVacApp->interview_at = date('Y-m-d H:i:s', strtotime('+3 days 10:00:00'));
$multiVacApp->save();

ob_start();
echo view('dashboard.client', ['data' => ['title' => 'Dashboard', 'user' => $rosterUser]]);
$multiDashboardHtml = ob_get_clean();

assert(strpos($multiDashboardHtml, 'Apprentice Profiles') !== false, "Dashboard renders Apprentice Profile section");
assert(strpos($multiDashboardHtml, 'Job Vacancy Applications') !== false, "Dashboard renders Job Vacancy section");
assert(strpos($multiDashboardHtml, 'Interview Scheduled') !== false, "Dashboard displays interview schedule alert banner");
echo "[4.2] PASS: Multi-tracking dashboard simultaneously displays Apprentice Profile and Vacancy Application with interview alert!\n";

// Cleanup test records
$vacApp->delete();
foreach ($createdLogins as $cl) $cl->delete();
$createdUser->delete();

$multiVacApp->delete();
$db = new \App\Models\Database();
$rosterUserApps = Rosterapplication::findByQuery("SELECT * FROM rosterapplication WHERE user = ?", [$rosterUser->iD]);
foreach ($rosterUserApps as $ra) {
    $db->query("DELETE FROM apprenticeprofile WHERE rosterapplication = ?", [$ra->iD]);
    $db->query("DELETE FROM rosterdocument WHERE rosterapplication = ?", [$ra->iD]);
    $db->query("DELETE FROM rosterstatusevent WHERE rosterapplication = ?", [$ra->iD]);
    $ra->delete();
}
$db->query("DELETE FROM user_login WHERE user = ?", [$rosterUser->iD]);
$rosterUser->delete();

echo "\n=== ALL 4 AUTOMATED TEST PHASES PASSED! Auto-account provisioning, auto-login, and candidate tracking dashboard are 100% verified. ===\n";
