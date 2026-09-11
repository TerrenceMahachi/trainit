<?php
/**
 * Test Suite: Client Portal Self-Service Work Request & Delivery Workflow
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;
use App\Models\Role;
use App\Models\Clientorganization;
use App\Models\Clientserviceplan;
use App\Models\Clientmembership;
use App\Models\Clientmemberrole;
use App\Models\Serviceoffering;
use App\Models\Prioritylevel;
use App\Models\Servicerequest;
use App\Models\Servicerequeststatus;
use App\Models\Servicerequeststatusevent;
use App\Models\Servicerequestclosure;
use App\Models\Requestmessage;
use App\Models\Messagevisibility;
use App\Controllers\ClientController;
use App\Controllers\RequestController;
use App\Helpers\Auth;

echo "========================================================\n";
echo "TEST SUITE: Client Portal Work Request & Delivery Workflow\n";
echo "========================================================\n\n";

$passed = 0;
$failed = 0;

function assertCondition($expr, $desc) {
    global $passed, $failed;
    if ($expr) {
        echo "  [PASS] $desc\n";
        $passed++;
    } else {
        echo "  [FAIL] $desc\n";
        $failed++;
    }
}

// 1. Setup / Resolve Test Client Organization & User
echo "1. Initializing Test Data...\n";
$testEmail = 'client_portal_test_' . time() . '@example.com';
$clientUser = User::create([
    'name' => 'Acme Tech Client Lead',
    'email' => $testEmail,
    'role' => 3, // Client User
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
$userId = is_object($clientUser) ? $clientUser->iD : $clientUser['iD'];
assertCondition($userId > 0, "Created client test user with ID: $userId");

// Authenticate as client user
Auth::login($userId);


$clientOrg = Clientorganization::create([
    'legal_name' => 'Acme Innovations Zimbabwe Ltd',
    'trading_name' => 'Acme Tech Solutions',
    'registration_number' => 'REG-' . rand(10000, 99999),
    'tax_number' => 'BP-' . rand(10000, 99999),
    'billing_email' => 'accounts@acmetech.co.zw',
    'address' => '100 Samora Machel Ave',
    'city' => 'Harare',
    'country' => 'Zimbabwe',
    'primary_phone' => '+263771122334',
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => $userId,
]);
$orgId = is_object($clientOrg) ? $clientOrg->iD : $clientOrg['iD'];
assertCondition($orgId > 0, "Created client organization with ID: $orgId");

// Assign Client Membership
$memberRoles = Clientmemberrole::all();
$roleId = !empty($memberRoles) ? (is_object($memberRoles[0]) ? $memberRoles[0]->iD : $memberRoles[0]['iD']) : 1;
$membership = Clientmembership::create([
    'clientorganization' => $orgId,
    'user' => $userId,
    'clientmemberrole' => $roleId,
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => $userId,
]);
assertCondition(!empty($membership), "Associated client user to organization via clientmembership");

// Create Active Retainer Plan
$offerings = Serviceoffering::all();
$offeringId = !empty($offerings) ? (is_object($offerings[0]) ? $offerings[0]->iD : $offerings[0]['iD']) : 1;
$plan = Clientserviceplan::create([
    'clientorganization' => $orgId,
    'serviceoffering' => $offeringId,
    'plan_name' => 'Full-Stack Agile Squad Retainer',
    'currency' => 'USD',
    'monthly_fee' => 1800.00,
    'included_hours' => 80.0,
    'associate_rate' => 35.0,
    'apprentice_rate' => 18.0,
    'billing_cycle_day' => 1,
    'service_manager' => 1,
    'billing_owner' => 1,
    'excesspolicy' => 1,
    'start_date' => date('Y-m-d'),
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
$planId = is_object($plan) ? $plan->iD : $plan['iD'];
assertCondition($planId > 0, "Created active retainer plan with ID: $planId (80 hrs @ $1,800/mo)");

// Authenticate as client user
$_COOKIE['user'] = $userId;

echo "\n2. Testing Client Controller Data Resolution...\n";
$clientCtrl = new ClientController();
$resolvedClient = $clientCtrl->getClientForUser($userId);
assertCondition(
    $resolvedClient && (is_object($resolvedClient) ? $resolvedClient->iD : $resolvedClient['iD']) == $orgId,
    "getClientForUser correctly resolved organization ID $orgId for user ID $userId"
);

echo "\n3. Testing Service Request Submission...\n";
$reqCtrl = new RequestController();
$_POST = [
    'client_id' => $orgId,
    'service_plan_id' => $planId,
    'priority_id' => 1, // High / Urgent
    'title' => 'Implement Automated Invoicing System API',
    'description' => "Detailed Work Brief Requirements:\n1. Build REST API for client subscriptions\n2. Integrate webhook callbacks for payment verification\n3. Export monthly statement PDF deliverables.",
    'desired_due_date' => date('Y-m-d', strtotime('+7 days')),
];

$submitRes = $reqCtrl->submitRequestAction();
assertCondition(
    is_array($submitRes) && ($submitRes['status'] ?? 0) === 1,
    "submitRequestAction executed successfully with ticket: " . ($submitRes['request_number'] ?? 'N/A')
);

$reqId = $submitRes['request_id'] ?? 0;
$createdReq = Servicerequest::find($reqId);
assertCondition($createdReq !== null, "Servicerequest record exists in database with ID $reqId");

// Check initial status event
$statusEvents = Servicerequeststatusevent::where('servicerequest', $reqId);
assertCondition(count($statusEvents) >= 1, "Initial Servicerequeststatusevent was recorded (count: " . count($statusEvents) . ")");

echo "\n4. Testing Talent Collaboration Message Stream...\n";
// Client posts a message
$_POST = [
    'request_id' => $reqId,
    'message' => 'Please find attached the API schema specifications. Let us know if you need clarification on endpoints.',
    'visibility' => 'PUBLIC_CLIENT',
];
$msgRes = $reqCtrl->postMessageAction();
assertCondition(
    is_array($msgRes) && ($msgRes['status'] ?? 0) === 1,
    "Client posted collaboration message to thread"
);

$messages = Requestmessage::where('servicerequest', $reqId);
assertCondition(count($messages) >= 1, "Requestmessage record exists in database");

echo "\n5. Testing Deliverable Review & Formal Sign-Off with Star Rating...\n";
// Client signs off deliverables with 5-star rating
$_POST = [
    'request_id' => $reqId,
    'satisfaction_rating' => 5,
    'closure_notes' => 'API endpoints tested against sandbox environment. High quality code and excellent communication from apprentice and associate.',
];
$signoffRes = $reqCtrl->signoffRequestAction();
assertCondition(
    is_array($signoffRes) && ($signoffRes['status'] ?? 0) === 1,
    "signoffRequestAction approved deliverables and closed ticket"
);

// Verify closure record
$closure = Servicerequestclosure::where('servicerequest', $reqId);
assertCondition(!empty($closure), "Servicerequestclosure record created in database");
$rating = !empty($closure) ? (is_object($closure[0]) ? $closure[0]->satisfaction_rating : $closure[0]['satisfaction_rating']) : 0;
assertCondition($rating == 5, "Satisfaction rating recorded correctly as 5 Stars");

// Verify status transition event to CLOSED
$closedEvents = Servicerequeststatusevent::where('servicerequest', $reqId);
$latestEvent = end($closedEvents);
$sId = is_object($latestEvent) ? $latestEvent->servicerequeststatus : $latestEvent['servicerequeststatus'];
$st = Servicerequeststatus::find($sId);
$stCode = $st ? (is_object($st) ? $st->code : $st['code']) : '';
assertCondition($stCode === 'CLOSED', "Latest status event transition is CLOSED ($stCode)");

echo "\n6. Testing Client Views Rendering Integrity...\n";
$viewFiles = [
    'nav.php' => _BASE_PATH . '/views/clients/nav.php',
    'portal.php' => _BASE_PATH . '/views/clients/portal.php',
    'requests_new.php' => _BASE_PATH . '/views/clients/requests_new.php',
    'requests_index.php' => _BASE_PATH . '/views/clients/requests_index.php',
    'request_view.php' => _BASE_PATH . '/views/clients/request_view.php',
    'plans.php' => _BASE_PATH . '/views/clients/plans.php',
    'team.php' => _BASE_PATH . '/views/clients/team.php',
];

foreach ($viewFiles as $vName => $vPath) {
    assertCondition(file_exists($vPath), "View file exists: views/clients/$vName");
    $content = file_get_contents($vPath);
    // Ensure no broken php tags
    assertCondition(strlen($content) > 100, "View file views/clients/$vName is non-empty (" . strlen($content) . " bytes)");
}

// Verify localStorage key in requests_new.php
$reqNewContent = file_get_contents($viewFiles['requests_new.php']);
assertCondition(
    strpos($reqNewContent, 'tsigiro_client_work_request') !== false,
    "views/clients/requests_new.php includes localStorage draft auto-save engine ('tsigiro_client_work_request')"
);

// Verify milestone stepper in request_view.php
$reqViewContent = file_get_contents($viewFiles['request_view.php']);
assertCondition(
    strpos($reqViewContent, 'Delivery Milestone Lifecycle') !== false,
    "views/clients/request_view.php includes 5-stage milestone stepper"
);

assertCondition(
    strpos($reqViewContent, 'Accept Deliverables &amp; Sign Off') !== false,
    "views/clients/request_view.php includes deliverable acceptance & sign-off form"
);

echo "\n========================================================\n";
echo "RESULTS: $passed PASSED, $failed FAILED\n";
echo "========================================================\n";

if ($failed > 0) {
    exit(1);
}
exit(0);
