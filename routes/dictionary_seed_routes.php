<?php

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\ReferenceDataSeeder;

global $router, $siteConfig;

// Seed reference dictionaries via web interface (Admin only)
$seedHandler = function () use ($router, $siteConfig) {
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }

    if (!Auth::check()) {
        header("Location: " . $siteConfig->siteUrl . "/login");
        exit;
    }

    $currentUser = Auth::user();
    if (!$currentUser || (int)$currentUser->role !== 1) {
        http_response_code(403);
        echo "403 Forbidden: Administrator role required to seed reference dictionaries.";
        exit;
    }

    // Check CSRF for POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Csrf::check()) {
        http_response_code(403);
        $_SESSION['flash_error'] = 'Security validation failed (invalid CSRF token). Please try again.';
        header("Location: " . $siteConfig->siteUrl . "/dashboard");
        exit;
    }

    $res = ReferenceDataSeeder::seedAll();

    if ($res['tables_seeded_count'] > 0) {
        $msg = "Successfully seeded {$res['tables_seeded_count']} reference tables with {$res['total_records_inserted']} standard entries.";
    } else {
        $msg = "All reference dictionary tables are already populated ({$res['already_seeded_count']} verified).";
    }

    $_SESSION['flash_success'] = $msg;

    $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => $msg,
            'result' => $res
        ]);
        exit;
    }

    header("Location: " . $siteConfig->siteUrl . "/dashboard");
    exit;
};

$router->addRoute('POST', '/admin/seed-dictionaries', $seedHandler);
$router->addRoute('GET', '/admin/seed-dictionaries', $seedHandler);
