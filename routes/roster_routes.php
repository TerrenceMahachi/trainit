<?php

use App\Controllers\RosterApplicationController;
use App\Models\Skillitem;
use App\Models\Proficiencylevel;
use App\Helpers\Auth;

global $router;

// 1. Simplified Public Express Intake (Zero friction, initiated on Opportunities page)
$router->addRoute('GET', '/opportunities/apply/apprentice', function () {
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    echo (new RosterApplicationController())->showExpressForm('apprentice', $appId);
    exit;
});

$router->addRoute('GET', '/opportunities/apply/associate', function () {
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    echo (new RosterApplicationController())->showExpressForm('associate', $appId);
    exit;
});

$router->addRoute('POST', '/opportunities/apply/express', function () {
    $res = (new RosterApplicationController())->handleExpressSubmit();
    if (is_array($res)) {
        header('Content-Type: application/json');
        echo json_encode($res);
    }
    exit;
});

// Stage 2: Credentials & Supporting Documents
$router->addRoute('GET', '/roster/apply/credentials', function () {
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo (new RosterApplicationController())->showCredentialsForm($appId);
    exit;
});

$router->addRoute('POST', '/roster/apply/credentials', function () {
    (new RosterApplicationController())->handleCredentialsSubmit();
    exit;
});

// Stage 3: Skills & Competency Matrix
$router->addRoute('GET', '/roster/apply/skills', function () {
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo (new RosterApplicationController())->showSkillsForm($appId);
    exit;
});

$router->addRoute('POST', '/roster/apply/skills', function () {
    (new RosterApplicationController())->handleSkillsSubmit();
    exit;
});

// Stage 4: Practical Experience & Referees
$router->addRoute('GET', '/roster/apply/experience', function () {
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo (new RosterApplicationController())->showExperienceForm($appId);
    exit;
});

$router->addRoute('POST', '/roster/apply/experience', function () {
    (new RosterApplicationController())->handleExperienceSubmit();
    exit;
});

// Stage 5: Review & Digital Declaration
$router->addRoute('GET', '/roster/apply/review', function () {
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo (new RosterApplicationController())->showReviewForm($appId);
    exit;
});

$router->addRoute('POST', '/roster/apply/submit', function () {
    (new RosterApplicationController())->handleFinalSubmit();
    exit;
});

// Stage 6: Candidate Onboarding Status Tracker
$router->addRoute('GET', '/roster/application/status', function () {
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo (new RosterApplicationController())->showStatusView($appId);
    exit;
});

// Legacy / Direct Link Redirections to Express Intake
$router->addRoute('GET', '/apply/apprentice', function () {
    global $siteConfig;
    header("Location: " . $siteConfig->siteUrl . "/opportunities/apply/apprentice");
    exit;
});

$router->addRoute('GET', '/apply/associate', function () {
    global $siteConfig;
    header("Location: " . $siteConfig->siteUrl . "/opportunities/apply/associate");
    exit;
});

// 2. Wizard Application Form (Apprentice & Associate)
$router->addRoute('GET', '/dashboard/apply', function () {
    global $siteConfig;
    if (!Auth::check()) {
        header("Location: " . $siteConfig->siteUrl . "/login");
        exit;
    }
    // Render path chooser or default to apprentice
    $data = ['title' => 'Choose Application Track'];
    echo view('roster.choose_track', compact('data'));
    exit;
});

$router->addRoute('GET', '/dashboard/apply/apprentice', function () {
    global $siteConfig;
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    header("Location: " . $siteConfig->siteUrl . "/opportunities/apply/apprentice" . ($appId ? '?id=' . $appId : ''));
    exit;
});

$router->addRoute('GET', '/dashboard/apply/associate', function () {
    global $siteConfig;
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    header("Location: " . $siteConfig->siteUrl . "/opportunities/apply/associate" . ($appId ? '?id=' . $appId : ''));
    exit;
});

// 3. Application Submission & Save Draft
$router->addRoute('POST', '/dashboard/apply/save', function () {
    $result = (new RosterApplicationController())->handleSubmission();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
});

// 4. View Application Detail & Timeline
$router->addRoute('GET', '/dashboard/application', function () {
    global $siteConfig;
    if (!Auth::check()) {
        header("Location: " . $siteConfig->siteUrl . "/login");
        exit;
    }
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo (new RosterApplicationController())->showApplicationDetail($appId);
    exit;
});

// 5. Stage 3 Statutory Onboarding
$router->addRoute('GET', '/dashboard/application/onboarding', function () {
    global $siteConfig;
    if (!Auth::check()) {
        header("Location: " . $siteConfig->siteUrl . "/login");
        exit;
    }
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo (new RosterApplicationController())->showOnboardingForm($appId);
    exit;
});

$router->addRoute('POST', '/dashboard/application/onboarding', function () {
    $result = (new RosterApplicationController())->handleOnboardingSubmit();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
});

// 6. Admin / Vetting Officer Console
$router->addRoute('GET', '/admin/roster', function () {
    global $siteConfig;
    if (!Auth::check() || (!Auth::isAdmin() && !Auth::isVettingOfficer())) {
        header("Location: " . $siteConfig->siteUrl . "/dashboard");
        exit;
    }
    echo (new RosterApplicationController())->adminPipeline();
    exit;
});

$router->addRoute('POST', '/get-admin-roster-records', function () {
    if (!Auth::check() || (!Auth::isAdmin() && !Auth::isVettingOfficer())) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 0, 'msg' => 'Unauthorized']);
        exit;
    }
    $result = (new RosterApplicationController())->getAdminRosterRecords();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
});

$router->addRoute('GET', '/admin/roster/review', function () {
    global $siteConfig;
    if (!Auth::check() || (!Auth::isAdmin() && !Auth::isVettingOfficer())) {
        header("Location: " . $siteConfig->siteUrl . "/dashboard");
        exit;
    }
    $appId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    echo (new RosterApplicationController())->adminReviewConsole($appId);
    exit;
});

$router->addRoute('POST', '/admin/roster/assessment', function () {
    if (!Auth::check() || (!Auth::isAdmin() && !Auth::isVettingOfficer())) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 0, 'msg' => 'Unauthorized']);
        exit;
    }
    $result = (new RosterApplicationController())->handleAssessmentSubmit();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
});

// 7. Dynamic API to get skill items for selected functions
$router->addRoute('GET', '/api/service-functions/skills', function () {
    $funcIds = isset($_GET['functions']) ? explode(',', $_GET['functions']) : [];
    $cleanIds = array_filter(array_map('intval', $funcIds));

    $skills = [];
    if (!empty($cleanIds)) {
        $in = implode(',', $cleanIds);
        $skillItems = Skillitem::findByQuery("SELECT * FROM skillitem WHERE servicefunction IN ({$in}) ORDER BY sort_order ASC");
        foreach ($skillItems as $item) {
            $fn = $item->servicefunction();
            $skills[] = [
                'id' => $item->iD,
                'function_id' => $item->servicefunction,
                'function_name' => $fn ? $fn->name : '',
                'code' => $item->code,
                'name' => $item->name,
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($skills);
    exit;
});
