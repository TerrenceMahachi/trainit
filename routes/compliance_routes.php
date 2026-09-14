<?php

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\ComplianceExpiryService;
use App\Models\ComplianceReminderLog;
use App\Models\Database;

global $router;

// GET /admin/compliance - Compliance Radar Dashboard & Expiring Documents
$router->addRoute('GET', '/admin/compliance', function () {
    if (!Auth::check() || (!Auth::isAdmin() && !Auth::isVettingOfficer())) {
        $_SESSION['error'] = 'Access denied. Administrator or Vetting Officer privileges required.';
        header('Location: /login');
        exit;
    }

    $filter = $_GET['filter'] ?? 'actionable'; // 'actionable', 'all', 'expired', 'critical', 'warning'
    $allDocs = ComplianceExpiryService::scanDocuments(null);
    $stats = ComplianceExpiryService::getComplianceStats();

    // Filter documents
    $docs = array_filter($allDocs, function ($d) use ($filter) {
        if ($filter === 'expired') {
            return $d['days_left'] < 0;
        }
        if ($filter === 'critical') {
            return $d['days_left'] >= 0 && $d['days_left'] <= 30;
        }
        if ($filter === 'warning') {
            return $d['days_left'] > 30 && $d['days_left'] <= 60;
        }
        if ($filter === 'actionable') {
            return $d['days_left'] <= 60;
        }
        return true; // 'all'
    });

    // Recent dispatch logs
    $db = Database::sharedPdo();
    $logs = [];
    try {
        $stmt = $db->query("
            SELECT * FROM compliance_reminder_log 
            ORDER BY sent_at DESC LIMIT 15
        ");
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Throwable $e) {
        $logs = [];
    }

    $data = [
        'title'        => 'Compliance Radar & Credential Expiry Tracking',
        'documents'    => $docs,
        'stats'        => $stats,
        'currentFilter'=> $filter,
        'logs'         => $logs,
        'user'         => Auth::user()
    ];

    echo view('compliance.index', compact('data'));
    exit;
});

// POST /admin/compliance/run-scan - Manually trigger compliance scan and reminders
$router->addRoute('POST', '/admin/compliance/run-scan', function () {
    header('Content-Type: application/json');

    if (!Auth::check() || (!Auth::isAdmin() && !Auth::isVettingOfficer())) {
        http_response_code(403);
        echo json_encode(['status' => 0, 'msg' => 'Access denied.']);
        exit;
    }

    $threshold = (int)($_POST['threshold'] ?? 60);
    $cooldown = isset($_POST['force']) && $_POST['force'] == '1' ? 0 : (int)($_POST['cooldown'] ?? 7);

    $result = ComplianceExpiryService::dispatchReminders($threshold, $cooldown);

    echo json_encode([
        'status' => 1,
        'msg'    => "Scan finished. Dispatched {$result['dispatched']} alert(s), skipped {$result['skipped']} throttled item(s).",
        'result' => $result
    ]);
    exit;
});
