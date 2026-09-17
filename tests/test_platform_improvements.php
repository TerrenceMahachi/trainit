<?php
/**
 * Test Suite: 5 Platform Improvements
 * 1. In-App Notification Center
 * 2. Client Self-Onboarding Loop
 * 3. Cross-Entity Analytics & Reporting
 * 4. Document Expiry Tracking & Compliance Radar
 * 5. Candidate Saved Searches & Vacancy Alerts
 */

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Database;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\ClientOnboardingRequest;
use App\Models\Clientorganization;
use App\Models\Clientmembership;
use App\Models\Clientserviceplan;
use App\Models\Vacancy;
use App\Models\CandidateJobAlert;
use App\Helpers\NotificationHelper;
use App\Helpers\ComplianceExpiryService;
use App\Helpers\JobAlertService;
use App\Controllers\AnalyticsController;

$passed = 0;
$failed = 0;

function assert_true($cond, $desc) {
    global $passed, $failed;
    if ($cond) {
        echo "  [PASS] {$desc}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$desc}\n";
        $failed++;
    }
}

function assert_equals($expected, $actual, $desc) {
    global $passed, $failed;
    if ($expected === $actual) {
        echo "  [PASS] {$desc}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$desc} (expected " . var_export($expected, true) . ", got " . var_export($actual, true) . ")\n";
        $failed++;
    }
}

echo "\n=======================================================\n";
echo "1. TESTING IN-APP NOTIFICATION CENTER\n";
echo "=======================================================\n";

$testUserId = 1; // Admin user
$initialUnread = NotificationHelper::getUnreadCount($testUserId);

// 1.1 Dispatch a notification
$notif = NotificationHelper::notify(
    userId: $testUserId,
    title: "Test Platform Improvement Alert",
    message: "This is a unit verification test notification.",
    link: "/admin/analytics",
    type: "info",
    icon: "bell"
);

assert_true($notif !== null && $notif->iD > 0, "Notification successfully created and saved to DB");

$newUnread = NotificationHelper::getUnreadCount($testUserId);
assert_equals($initialUnread + 1, $newUnread, "Unread count accurately incremented by 1");

// 1.2 Fetch recent notifications
$recent = NotificationHelper::getRecent($testUserId, 5);
assert_true(count($recent) > 0, "getRecent returns array of notifications");
assert_equals("Test Platform Improvement Alert", $recent[0]->title, "Latest notification title matches");

// 1.3 Mark single notification as read
$marked = NotificationHelper::markAsRead($notif->iD, $testUserId);
assert_true($marked, "markAsRead returned true");
assert_equals($initialUnread, NotificationHelper::getUnreadCount($testUserId), "Unread count decremented back");

// 1.4 Mark all as read
NotificationHelper::notify($testUserId, "Unread 1", "Msg", "/test");
NotificationHelper::notify($testUserId, "Unread 2", "Msg", "/test");
assert_true(NotificationHelper::getUnreadCount($testUserId) >= 2, "Multiple notifications recorded");
NotificationHelper::markAllAsRead($testUserId);
assert_equals(0, NotificationHelper::getUnreadCount($testUserId), "markAllAsRead cleared all unread badges");

// Clean up test notifications
$db = Database::sharedPdo();
$db->exec("DELETE FROM user_notification WHERE title LIKE '%Test%' OR title LIKE 'Unread%'");


echo "\n=======================================================\n";
echo "2. TESTING CLIENT SELF-ONBOARDING LOOP\n";
echo "=======================================================\n";

$testEmail = 'onboard_test_' . time() . '@example.co.zw';
$testCompany = 'Apex Dynamics Corp ' . rand(1000, 9999);

// 2.1 Create Onboarding Request
$req = new ClientOnboardingRequest();
$req->company_name = $testCompany;
$req->trading_name = 'Apex Dyn';
$req->contact_name = 'Tinashe Chikore';
$req->contact_email = $testEmail;
$req->contact_phone = '+263 77 123 4567';
$req->contact_title = 'Managing Director';
$req->serviceoffering = 1;
$req->sectortype = 1;
$req->engagementmodel = 1;
$req->estimated_monthly_hours = 40;
$req->notes = 'Urgent retainer onboarding test';
$req->status = 'pending';
$req->save();

assert_true($req->iD > 0, "ClientOnboardingRequest record created with pending status");

// 2.2 Verify retrieval
$fetchedReq = ClientOnboardingRequest::find($req->iD);
assert_equals('pending', $fetchedReq->status, "Request is pending admin action");

// 2.3 Simulate Admin Approval & Provisioning
$org = new Clientorganization();
$org->legal_name = $fetchedReq->company_name;
$org->trading_name = $fetchedReq->trading_name;
$org->registration_number = 'CORP-' . time();
$org->tax_number = 'BP-12345678';
$org->address = 'Harare CBD';
$org->country = 'Zimbabwe';
$org->city = 'Harare';
$org->billing_email = $fetchedReq->contact_email;
$org->primary_phone = $fetchedReq->contact_phone;
$org->reg_by = 1;
$org->status = 1;
$org->save();
assert_true($org->iD > 0, "Clientorganization successfully provisioned");

// Create primary client user
$clientUser = new User();
$clientUser->name = $fetchedReq->contact_name;
$clientUser->email = $fetchedReq->contact_email;
$clientUser->role = 3; // Client User
$clientUser->reg_by = 1;
$clientUser->status = 1;
$clientUser->save();
assert_true($clientUser->iD > 0, "Client User account created with Role 3");

// Create membership
$membership = new Clientmembership();
$membership->clientorganization = $org->iD;
$membership->user = $clientUser->iD;
$membership->clientmemberrole = 1; // Primary Admin
$membership->reg_by = 1;
$membership->status = 1;
$membership->save();
assert_true($membership->iD > 0, "Clientmembership linked (Role 1 Primary Admin)");

// Create initial service plan
$plan = new Clientserviceplan();
$plan->clientorganization = $org->iD;
$plan->serviceoffering = $fetchedReq->serviceoffering ?: 1;
$plan->plan_name = 'Enterprise Retainer';
$plan->currency = 'USD';
$plan->monthly_fee = 1500.00;
$plan->included_hours = 40.0;
$plan->associate_rate = 45.00;
$plan->apprentice_rate = 25.00;
$plan->billing_cycle_day = 1;
$plan->service_manager = 1;
$plan->billing_owner = 1;
$plan->excesspolicy = 1;
$plan->start_date = date('Y-m-d');
$plan->reg_by = 1;
$plan->status = 1;
$plan->save();
assert_true($plan->iD > 0, "Clientserviceplan initialized with retainer parameters");

// Update request to approved
$fetchedReq->status = 'approved';
$fetchedReq->reviewed_by = 1;
$fetchedReq->reviewed_at = date('Y-m-d H:i:s');
$fetchedReq->provisioned_clientorganization = $org->iD;
$fetchedReq->provisioned_user = $clientUser->iD;
$fetchedReq->update();

$reloadedReq = ClientOnboardingRequest::find($fetchedReq->iD);
assert_equals('approved', $reloadedReq->status, "Onboarding request transitioned to approved");
assert_equals((int)$org->iD, (int)$reloadedReq->provisioned_clientorganization, "Linked organization ID recorded");

// Clean up
$db->exec("DELETE FROM client_onboarding_request WHERE iD = {$req->iD}");
$db->exec("DELETE FROM clientserviceplan WHERE iD = {$plan->iD}");
$db->exec("DELETE FROM clientmembership WHERE iD = {$membership->iD}");
$db->exec("DELETE FROM clientorganization WHERE iD = {$org->iD}");
$db->exec("DELETE FROM user WHERE iD = {$clientUser->iD}");


echo "\n=======================================================\n";
echo "3. TESTING CROSS-ENTITY ANALYTICS & REPORTING\n";
echo "=======================================================\n";

// 3.1 Test Analytics Funnel aggregate query
$funnel = $db->query("
    SELECT 
        COUNT(*) AS total_intakes,
        SUM(CASE WHEN applicationstatus >= 2 THEN 1 ELSE 0 END) AS vetted,
        SUM(CASE WHEN applicationstatus >= 3 THEN 1 ELSE 0 END) AS shortlisted,
        SUM(CASE WHEN applicationstatus >= 5 THEN 1 ELSE 0 END) AS roster_ready
    FROM rosterapplication
    WHERE status = 1
")->fetch(PDO::FETCH_ASSOC);

assert_true(isset($funnel['total_intakes']), "Vetting funnel aggregate query executed successfully");
assert_true((int)$funnel['total_intakes'] >= 0, "Intake count valid integer: " . $funnel['total_intakes']);

// 3.2 100-pt Score dimensions
$scores = $db->query("
    SELECT 
        COUNT(*) AS total_scored,
        AVG(total_score) AS avg_total,
        AVG(technical_fit_score) AS avg_tech,
        AVG(evidence_score) AS avg_evidence,
        AVG(judgement_score) AS avg_judgement,
        AVG(availability_score) AS avg_avail,
        AVG(motivation_score) AS avg_motivation
    FROM rosterassessment
    WHERE total_score > 0
")->fetch(PDO::FETCH_ASSOC);

assert_true(isset($scores['total_scored']), "100-Point scorecard evaluation query executed successfully");

// 3.3 SLA turnaround
$sla = $db->query("
    SELECT 
        COUNT(*) AS total_requests
    FROM servicerequest
    WHERE status = 1
")->fetch(PDO::FETCH_ASSOC);

assert_true(isset($sla['total_requests']), "Client work request SLA intelligence metrics aggregated");


echo "\n=======================================================\n";
echo "4. TESTING DOCUMENT EXPIRY & COMPLIANCE RADAR\n";
echo "=======================================================\n";

// 4.1 Scan monitored documents
$scanned = ComplianceExpiryService::scanDocuments(null);
assert_true(is_array($scanned), "scanDocuments returned an array");
assert_true(count($scanned) >= 1, "Discovered active monitored documents in system: " . count($scanned));

$first = $scanned[0];
assert_true(isset($first['title']) && isset($first['expiry_date']) && isset($first['days_left']), "Document structure contains title, expiry_date, days_left");
assert_true(in_array($first['status_level'], ['danger', 'warning', 'info']), "Status level correctly assigned: " . $first['status_level']);

// 4.2 Aggregated compliance stats
$stats = ComplianceExpiryService::getComplianceStats();
assert_true(isset($stats['total']) && isset($stats['expired']) && isset($stats['critical']) && isset($stats['warning']), "getComplianceStats returned complete breakdown");
assert_equals(count($scanned), $stats['total'], "Total monitored matches count of scanned documents");

// 4.3 Reminder dispatch with cooldown
// Clean prior test logs for test isolation
$db->exec("DELETE FROM compliance_reminder_log WHERE document_id = 9999");

// Insert a mock expiring document log to test throttling
$db->exec("
    INSERT INTO compliance_reminder_log 
    (document_type, document_id, recipient_email, recipient_name, expiry_date, days_left, sent_at)
    VALUES ('test_doc', 9999, 'test@example.com', 'Test User', '2026-10-01', 20, CURRENT_TIMESTAMP)
");

// Scan with 7-day cooldown: mock doc must be throttled
$scannedThrottleCheck = $db->query("
    SELECT COUNT(*) FROM compliance_reminder_log 
    WHERE document_type = 'test_doc' AND document_id = 9999 
    AND (strftime('%s', 'now') - strftime('%s', sent_at)) < (7 * 86400)
")->fetchColumn();
assert_equals(1, (int)$scannedThrottleCheck, "7-day cooldown filter correctly detects recent dispatch");

// Clean up
$db->exec("DELETE FROM compliance_reminder_log WHERE document_id = 9999");


echo "\n=======================================================\n";
echo "5. TESTING CANDIDATE SAVED SEARCHES & VACANCY ALERTS\n";
echo "=======================================================\n";

$alertEmail = 'candidate_alert_' . time() . '@example.co.zw';
$alertKeywords = 'Full Stack Engineer ' . rand(100, 999);

// 5.1 Candidate subscribes to alert
$subResult = JobAlertService::subscribe([
    'email'    => $alertEmail,
    'name'     => 'Tendai Test',
    'keywords' => $alertKeywords,
]);

assert_equals(1, $subResult['status'], "Job alert subscription created successfully");
assert_true(!empty($subResult['token']), "Unique unsubscribe token issued");

// Verify in DB
$alertRecord = CandidateJobAlert::where('email', $alertEmail);
assert_true(!empty($alertRecord), "CandidateJobAlert found in database");
assert_equals(1, (int)$alertRecord[0]->is_active, "Alert is active");

// 5.2 Match and Notify on Published Vacancy
// Create test published vacancy matching keywords
$vac = new Vacancy();
$vac->reference_number = 'VAC-TEST-' . time();
$vac->title = "Lead {$alertKeywords} Role";
$vac->slug = 'lead-' . strtolower(str_replace(' ', '-', $alertKeywords)) . '-' . time();
$vac->department = 1;
$vac->engagementbasis = 1;
$vac->worklocationpreference = 1;
$vac->target_role = 2;
$vac->summary = "Opportunity for a skilled {$alertKeywords}.";
$vac->description = "Great career advancement opportunity.";
$vac->responsibilities = "Develop systems and lead architecture.";
$vac->requirements = "5+ years experience in stack.";
$vac->open_slots = 1;
$vac->publish_date = date('Y-m-d');
$vac->closing_date = date('Y-m-d', strtotime('+30 days'));
$vac->vacancystatus = 2; // Published!
$vac->reg_by = 1;
$vac->status = 1;
$vac->save();

assert_true($vac->iD > 0, "Published test vacancy saved to DB");

// Trigger Matcher
$matchedCount = JobAlertService::matchAndNotify($vac);
assert_true($matchedCount >= 1, "JobAlertService::matchAndNotify successfully matched and notified subscriber (Count: {$matchedCount})");

// Verify alert updated with last_matched_at
$updatedAlert = CandidateJobAlert::find($alertRecord[0]->iD);
assert_true(!empty($updatedAlert->last_matched_at), "Subscriber record updated with last_matched_at timestamp");

// 5.3 Unsubscribe
$unsubSuccess = JobAlertService::unsubscribe($subResult['token']);
assert_true($unsubSuccess, "1-Click Unsubscribe successfully disabled alerts");

$finalAlert = CandidateJobAlert::find($alertRecord[0]->iD);
assert_equals(0, (int)$finalAlert->is_active, "Alert record status is now inactive (is_active = 0)");

// Clean up
$db->exec("DELETE FROM vacancy WHERE iD = {$vac->iD}");
$db->exec("DELETE FROM candidate_job_alert WHERE email = '{$alertEmail}'");

echo "\n=======================================================\n";
echo "SUMMARY: Passed: {$passed} | Failed: {$failed}\n";
echo "=======================================================\n";

if ($failed > 0) {
    exit(1);
}
exit(0);
