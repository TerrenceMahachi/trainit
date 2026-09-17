<?php
/**
 * Automated Verification Script (HTTP End-to-End):
 * Profile Application, Revocation / Cancellation, and Re-application Lifecycle.
 */

require __DIR__ . '/../config.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Models\Database;
use App\Models\User;

$db = Database::sharedPdo();
$db->exec("PRAGMA busy_timeout = 10000;");

$baseUrl = 'http://localhost/trainit';

echo "=== STEP 1: Create Test User with Complete Credentials ===\n";
$uniqueEmail = 'test.revoke.' . time() . '@tsigiro-test.zw';
$db->prepare("INSERT INTO user (name, email, role, reg_by, reg_date, status) VALUES (?, ?, 2, 1, CURRENT_TIMESTAMP, 1)")
   ->execute(['Tinashe Revoke Tester', $uniqueEmail]);
$testUserId = (int)$db->lastInsertId();

// Create default general user profile
$db->prepare("INSERT INTO userprofile (user, profiletype, profilestatus, display_title, is_default, reg_by, reg_date, status) VALUES (?, 1, 3, 'General User', 1, ?, CURRENT_TIMESTAMP, 1)")
   ->execute([$testUserId, $testUserId]);

// Create personal details
$db->prepare("
    INSERT INTO rosterapplication (user, applicationtrack, applicationstatus, primaryfunction, legal_name, email, mobile_number, city, country, province_name, reg_by, reg_date, status)
    VALUES (?, 1, 1, 1, 'Tinashe Tester', ?, '+263771234567', 'Harare', 'Zimbabwe', 'Harare', ?, CURRENT_TIMESTAMP, 1)
")->execute([$testUserId, $uniqueEmail, $testUserId]);
$rosterAppId = (int)$db->lastInsertId();

// Create 1 verified qualification
$db->prepare("
    INSERT INTO rosterqualification (rosterapplication, qualificationtype, title, institution_name, date_obtained, reg_by, reg_date, status)
    VALUES (?, 1, 'BSc Electrical Engineering', 'University of Zimbabwe', '2024-11-15', ?, CURRENT_TIMESTAMP, 1)
")->execute([$rosterAppId, $testUserId]);

function postHttp($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => json_decode($res, true), 'raw' => $res];
}

echo "User created (ID: {$testUserId})\n";

echo "\n=== STEP 2: Submit 1-Click Application for Apprentice ===\n";
$req = postHttp("{$baseUrl}/api/mobile/user/request-profile", [
    'user_id' => $testUserId,
    'type_code' => 'apprentice',
    'display_title' => 'Electrical & IoT Apprentice',
    'notes' => 'Final year Electrical Engineering student seeking apprenticeship.',
]);

echo "Request Profile Response Status: " . ($req['body']['status'] ?? 'null') . " - " . ($req['body']['message'] ?? '') . "\n";
assert($req['body']['status'] === 1, "Application submission should succeed");

// Verify pending profile in database
$stmt = $db->prepare("SELECT up.*, pt.code as type_code FROM userprofile up JOIN profiletype pt ON up.profiletype = pt.iD WHERE up.user = ? AND pt.code = 'apprentice'");
$stmt->execute([$testUserId]);
$appProfile = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
assert(!empty($appProfile), "Apprentice profile should exist in DB");
assert((int)$appProfile['profilestatus'] === 2, "Status should be 2 (Pending Review)");
echo "✓ Apprentice profile is Pending Review in DB (ID: {$appProfile['iD']})\n";

echo "\n=== STEP 3: Revoke Pending Application via POST /api/mobile/user/revoke-profile ===\n";
$revoke = postHttp("{$baseUrl}/api/mobile/user/revoke-profile", [
    'user_id' => $testUserId,
    'profile_id' => $appProfile['iD'],
    'type_code' => 'apprentice',
]);

echo "Revoke Response Status: " . ($revoke['body']['status'] ?? 'null') . " - " . ($revoke['body']['message'] ?? '') . "\n";
assert($revoke['body']['status'] === 1, "Revoke should succeed");

// Verify profile is removed from DB
$stmt->execute([$testUserId]);
$checkProfile = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
assert(empty($checkProfile), "Apprentice profile should be deleted from DB");
echo "✓ Apprentice profile successfully removed from database\n";

// Verify audit entries removed
$stmtAudit = $db->prepare("SELECT count(*) FROM profilerequestaudit WHERE userprofile = ?");
$stmtAudit->execute([$appProfile['iD']]);
$auditCount = (int)$stmtAudit->fetchColumn();
$stmtAudit->closeCursor();
assert($auditCount === 0, "Audit entries for deleted profile must be 0");
echo "✓ Linked audit entries cleaned up\n";

echo "\n=== STEP 4: Re-apply to Ensure No Conflict or Stale State Errors ===\n";
// Re-insert candidate base qualification for 1-click eligibility
$db->prepare("
    INSERT INTO rosterapplication (user, applicationtrack, applicationstatus, primaryfunction, legal_name, email, mobile_number, city, country, province_name, reg_by, reg_date, status)
    VALUES (?, 1, 1, 1, 'Tinashe Tester', ?, '+263771234567', 'Harare', 'Zimbabwe', 'Harare', ?, CURRENT_TIMESTAMP, 1)
")->execute([$testUserId, $uniqueEmail, $testUserId]);
$newRosterAppId = (int)$db->lastInsertId();

$db->prepare("
    INSERT INTO rosterqualification (rosterapplication, qualificationtype, title, institution_name, date_obtained, reg_by, reg_date, status)
    VALUES (?, 1, 'BSc Electrical Engineering', 'University of Zimbabwe', '2024-11-15', ?, CURRENT_TIMESTAMP, 1)
")->execute([$newRosterAppId, $testUserId]);

$reapply = postHttp("{$baseUrl}/api/mobile/user/request-profile", [
    'user_id' => $testUserId,
    'type_code' => 'apprentice',
    'display_title' => 'Mechatronics & IoT Apprentice',
    'notes' => 'Updated motivation notes for re-application.',
]);

echo "Re-apply Response Status: " . ($reapply['body']['status'] ?? 'null') . " - " . ($reapply['body']['message'] ?? '') . "\n";
assert($reapply['body']['status'] === 1, "Re-applying should succeed cleanly without conflict");

// Allow Apache connection to close DB lock
usleep(200000);

// Cleanup test user
$db->prepare("DELETE FROM profilerequestaudit WHERE userprofile IN (SELECT iD FROM userprofile WHERE user = ?)")->execute([$testUserId]);
$db->prepare("DELETE FROM userprofile WHERE user = ?")->execute([$testUserId]);
$db->prepare("DELETE FROM rosterqualification WHERE rosterapplication = ?")->execute([$newRosterAppId]);
$db->prepare("DELETE FROM rosterapplication WHERE user = ?")->execute([$testUserId]);
$db->prepare("DELETE FROM user_notification WHERE user = ?")->execute([$testUserId]);
$db->prepare("DELETE FROM user WHERE iD = ?")->execute([$testUserId]);

echo "\n=========================================\n";
echo "🎉 ALL TESTS PASSED: Application Lifecycle & Revocation Verified via HTTP!\n";
echo "=========================================\n";
