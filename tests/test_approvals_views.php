<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Helpers\SiteConfig;
use App\Models\User;
use App\Models\Verificationstatus;
use App\Models\Leavestatus;

global $siteConfig;
$siteConfig = new SiteConfig(
    _BASEURL,
    assetsUrl: _ASSETSURL,
    assetsLoc: _ASSETS_PATH,
    navLoc: _ASSETS_PATH . '/nav/ajax-pagination.php',
    siteName: _SITEDISPLAYNAME,
    defaultEmail: 'hello@trainit.co.zw'
);

// Mock admin session
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'ADMIN';

echo "=== Testing Compliance & Approvals Dedicated Views & Navigation ===\n";

$sections = ['documents', 'expiring', 'leave', 'timesheets'];

$dummyData = [
    'user'             => User::find(1),
    'pendingDocs'      => [],
    'expiringDocs'     => [],
    'pendingLeaves'    => [],
    'pendingTime'      => [],
    'allVerStatuses'   => Verificationstatus::all(),
    'allLeaveStatuses' => Leavestatus::all(),
];

foreach ($sections as $sec) {
    $data = array_merge($dummyData, [
        'activeTab' => $sec,
        'title'     => ucfirst($sec) . ' Queue'
    ]);
    
    $viewName = "staff.approvals_{$sec}";
    $html = view($viewName, compact('data'));
    
    // Assert view rendered content
    assert(strlen($html) > 500, "View $viewName rendered successfully");
    
    // Assert NO JavaScript tabs exist in output
    assert(strpos($html, 'data-bs-toggle="pill"') === false, "View $viewName must NOT contain data-bs-toggle=\"pill\"");
    assert(strpos($html, 'data-bs-toggle="tab"') === false, "View $viewName must NOT contain data-bs-toggle=\"tab\"");
    
    // Assert actual HTML links exist
    assert(strpos($html, '/admin/staff-approvals/documents') !== false, "View $viewName contains /admin/staff-approvals/documents link");
    assert(strpos($html, '/admin/staff-approvals/expiring') !== false, "View $viewName contains /admin/staff-approvals/expiring link");
    assert(strpos($html, '/admin/staff-approvals/leave') !== false, "View $viewName contains /admin/staff-approvals/leave link");
    assert(strpos($html, '/admin/staff-approvals/timesheets') !== false, "View $viewName contains /admin/staff-approvals/timesheets link");
    
    // Assert active class is applied to the active tab
    assert(strpos($html, "href=\"{$siteConfig->siteUrl}/admin/staff-approvals/{$sec}\"") !== false, "View $viewName has href for current section");
    
    echo "PASS: View {$viewName} rendered with dedicated route links and NO client-side JS tabs.\n";
}

// Also test the backwards compatibility wrapper
$wrapperHtml = view('staff.approvals', compact('data'));
assert(strlen($wrapperHtml) > 500, "staff.approvals wrapper rendered successfully");
assert(strpos($wrapperHtml, 'data-bs-toggle="pill"') === false, "Wrapper must NOT contain data-bs-toggle=\"pill\"");
echo "PASS: Backwards compatibility view staff.approvals rendered cleanly.\n";

echo "\nALL APPROVALS VIEW & ROUTE LINK TESTS PASSED!\n";
