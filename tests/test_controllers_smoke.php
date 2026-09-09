<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Helpers\SiteConfig;
use App\Helpers\Auth;
use App\Controllers\ClientController;
use App\Controllers\RequestController;
use App\Controllers\PayrollController;
use App\Controllers\ServiceCatalogueController;
use App\Controllers\StaffController;
use App\Models\Clientorganization;
use App\Models\Servicerequest;

global $siteConfig;
$siteConfig = new SiteConfig(
    _BASEURL,
    assetsUrl: _ASSETSURL,
    assetsLoc: _ASSETS_PATH,
    navLoc: _ASSETS_PATH . '/nav/ajax-pagination.php',
    siteName: _SITEDISPLAYNAME,
    defaultEmail: 'hello@trainit.co.zw'
);

// Authenticate as Admin
Auth::login(1);

echo "=== Testing Controller Smoke & View Rendering Suite ===\n";

// 1. ClientController::index
ob_start();
(new ClientController())->index();
$clientIndex = ob_get_clean();
assert(strlen($clientIndex) > 1000, "ClientController::index renders successfully");
echo "PASS: ClientController::index rendered (" . strlen($clientIndex) . " bytes)\n";

// 2. ClientController::view
$clients = Clientorganization::all();
if (!empty($clients)) {
    $cId = is_object($clients[0]) ? $clients[0]->iD : $clients[0]['iD'];
    ob_start();
    (new ClientController())->view($cId);
    $clientView = ob_get_clean();
    assert(strlen($clientView) > 1000, "ClientController::view renders successfully");
    echo "PASS: ClientController::view($cId) rendered (" . strlen($clientView) . " bytes)\n";
}

// 3. ServiceCatalogueController::index
ob_start();
(new ServiceCatalogueController())->index();
$catIndex = ob_get_clean();
assert(strlen($catIndex) > 1000, "ServiceCatalogueController::index renders");
echo "PASS: ServiceCatalogueController::index rendered (" . strlen($catIndex) . " bytes)\n";

// 4. RequestController::index
ob_start();
(new RequestController())->index();
$reqIndex = ob_get_clean();
assert(strlen($reqIndex) > 1000, "RequestController::index renders");
echo "PASS: RequestController::index rendered (" . strlen($reqIndex) . " bytes)\n";

// 5. RequestController::view
$requests = Servicerequest::all();
if (!empty($requests)) {
    $rId = is_object($requests[0]) ? $requests[0]->iD : $requests[0]['iD'];
    ob_start();
    (new RequestController())->view($rId);
    $reqView = ob_get_clean();
    assert(strlen($reqView) > 1000, "RequestController::view renders");
    echo "PASS: RequestController::view($rId) rendered (" . strlen($reqView) . " bytes)\n";
}

// 6. PayrollController::index
ob_start();
(new PayrollController())->index();
$payIndex = ob_get_clean();
assert(strlen($payIndex) > 1000, "PayrollController::index renders");
echo "PASS: PayrollController::index rendered (" . strlen($payIndex) . " bytes)\n";

echo "\nALL CONTROLLER SMOKE TESTS PASSED!\n";
