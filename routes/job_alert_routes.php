<?php

use App\Helpers\Auth;
use App\Helpers\JobAlertService;
use App\Models\CandidateJobAlert;
use App\Models\Database;

global $router, $siteConfig;

// POST /opportunities/alerts/subscribe - Candidate Job Alert Subscription
$router->addRoute('POST', '/opportunities/alerts/subscribe', function () {
    header('Content-Type: application/json');

    $res = JobAlertService::subscribe([
        'email'           => $_POST['email'] ?? '',
        'name'            => $_POST['name'] ?? '',
        'keywords'        => $_POST['keywords'] ?? '',
        'department'      => $_POST['department'] ?? null,
        'engagementbasis' => $_POST['engagementbasis'] ?? null,
        'user_id'         => Auth::check() ? Auth::id() : null,
    ]);

    echo json_encode($res);
    exit;
});

// GET /opportunities/alerts/unsubscribe/:token - 1-Click Unsubscribe
$router->addRoute('GET', '/opportunities/alerts/unsubscribe/:token', function ($token) use ($siteConfig) {
    $unsubscribed = JobAlertService::unsubscribe($token);

    $title = $unsubscribed ? 'Alert Unsubscribed' : 'Unsubscribe Error';
    $message = $unsubscribed 
        ? 'You have been successfully unsubscribed from this job alert. You will no longer receive automated notifications for this search.'
        : 'Invalid or expired unsubscribe link. Your email may have already been removed.';

    $data = [
        'title'        => $title,
        'success'      => $unsubscribed,
        'message'      => $message,
        'opportunitiesUrl' => $siteConfig->siteUrl . '/opportunities',
    ];

    echo view('vacancies.unsubscribed', compact('data'));
    exit;
});

// GET /admin/candidate-alerts - Admin overview of active alert preferences
$router->addRoute('GET', '/admin/candidate-alerts', function () use ($siteConfig) {
    if (!Auth::check() || (!Auth::isAdmin() && !Auth::isVettingOfficer())) {
        $_SESSION['error'] = 'Access denied. Administrator or Vetting Officer privileges required.';
        header('Location: /login');
        exit;
    }

    new CandidateJobAlert();
    $db = Database::sharedPdo();
    $stmt = $db->query("
        SELECT cja.*, d.name AS dept_name, eb.name AS basis_name
        FROM candidate_job_alert cja
        LEFT JOIN department d ON cja.department = d.iD
        LEFT JOIN engagementbasis eb ON cja.engagementbasis = eb.iD
        ORDER BY cja.reg_date DESC
    ");
    $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        'title'  => 'Candidate Vacancy Alert Subscriptions',
        'alerts' => $alerts,
        'user'   => Auth::user(),
    ];

    echo view('vacancies.admin_alerts', compact('data'));
    exit;
});
