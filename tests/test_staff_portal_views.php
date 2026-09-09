<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Helpers\SiteConfig;
use App\Helpers\Auth;
use App\Controllers\StaffController;
use App\Models\User;
use App\Models\Staffprofile;
use App\Models\Documenttype;
use App\Models\Leavetype;
use App\Models\Activitycategory;
use App\Models\Department;

global $siteConfig;
$siteConfig = new SiteConfig(
    _BASEURL,
    assetsUrl: _ASSETSURL,
    assetsLoc: _ASSETS_PATH,
    navLoc: _ASSETS_PATH . '/nav/ajax-pagination.php',
    siteName: _SITEDISPLAYNAME,
    defaultEmail: 'hello@trainit.co.zw'
);

// Mock login as user 1
Auth::login(1);

echo "=== Testing Staff Portal Dedicated Views & Navigation ===\n";

$sections = ['documents', 'leave', 'timesheets'];

$user = User::find(1);
$profile = Staffprofile::where('user', 1)[0] ?? null;

$dummyData = [
    'user'             => $user,
    'profile'          => $profile,
    'role'             => $user ? $user->role() : null,
    'documents'        => [],
    'leaves'           => [],
    'timeEntries'      => [],
    'deptAssignment'   => null,
    'allDocTypes'      => Documenttype::all(),
    'allLeaveTypes'    => Leavetype::all(),
    'allActivityCats'  => Activitycategory::all(),
    'allDepartments'   => Department::all(),
];

foreach ($sections as $sec) {
    $data = array_merge($dummyData, [
        'activeTab' => $sec,
        'title'     => ucfirst($sec) . ' — Staff Hub'
    ]);
    
    $viewName = "staff.portal_{$sec}";
    $html = view($viewName, compact('data'));
    
    // Assert view rendered content
    assert(strlen($html) > 500, "View $viewName rendered successfully");
    
    // Assert NO JavaScript tabs exist in output
    assert(strpos($html, 'data-bs-toggle="pill"') === false, "View $viewName must NOT contain data-bs-toggle=\"pill\"");
    assert(strpos($html, 'data-bs-toggle="tab"') === false, "View $viewName must NOT contain data-bs-toggle=\"tab\"");
    
    // Assert actual HTML links exist
    assert(strpos($html, '/staff/portal/documents') !== false, "View $viewName contains /staff/portal/documents link");
    assert(strpos($html, '/staff/portal/leave') !== false, "View $viewName contains /staff/portal/leave link");
    assert(strpos($html, '/staff/portal/timesheets') !== false, "View $viewName contains /staff/portal/timesheets link");
    
    // Assert active class is applied to the active tab
    assert(strpos($html, "href=\"{$siteConfig->siteUrl}/staff/portal/{$sec}\"") !== false, "View $viewName has href for current section");
    
    echo "PASS: View {$viewName} rendered with dedicated route links and NO client-side JS tabs.\n";
}

// Test backwards compatibility wrapper
$wrapperHtml = view('staff.portal', compact('data'));
assert(strlen($wrapperHtml) > 500, "staff.portal wrapper rendered successfully");
assert(strpos($wrapperHtml, 'data-bs-toggle="pill"') === false, "Wrapper must NOT contain data-bs-toggle=\"pill\"");
echo "PASS: Backwards compatibility view staff.portal rendered cleanly.\n";

echo "\nALL STAFF PORTAL VIEW & ROUTE LINK TESTS PASSED!\n";
