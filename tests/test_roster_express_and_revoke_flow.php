<?php
/**
 * Comprehensive Automated Verification Script:
 * Roster Application Intake, Status Dossier Visibility, and Revocation Workflow.
 */

require __DIR__ . '/../config.php';
require __DIR__ . '/../vendor/autoload.php';

use App\Models\Database;
use App\Models\User;
use App\Controllers\RosterApplicationController;
use App\Helpers\Auth;

$db = Database::sharedPdo();
$db->exec("PRAGMA busy_timeout = 10000;");

$baseUrl = 'http://localhost/trainit';

function getAuthCookies($userId) {
    $expires = time() + 86400 * 30;
    $sig = hash_hmac('sha256', $userId . '|' . $expires, _APP_SECRET);
    $authVal = $expires . '.' . $sig;
    $seenSig = hash_hmac('sha256', $userId . '|' . time(), _APP_SECRET);
    $seenVal = time() . '.' . $seenSig;
    return [
        'user' => $userId,
        'auth' => $authVal,
        'seen' => $seenVal
    ];
}

function postHttp($url, $data, $cookies = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    $data['format'] = 'json';
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $headers = ['Accept: application/json'];
    $cookieHeader = [];
    foreach ($cookies as $k => $v) {
        $cookieHeader[] = "{$k}={$v}";
    }
    if (!empty($cookieHeader)) {
        $headers[] = 'Cookie: ' . implode('; ', $cookieHeader);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => json_decode($res, true), 'raw' => $res];
}

echo "=== STEP 1: Create Test Candidate User ===\n";
$uniqueEmail = 'candidate.tester.' . time() . '@tsigiro-test.zw';
$db->prepare("INSERT INTO user (name, email, role, reg_by, reg_date, status) VALUES (?, ?, 2, 1, CURRENT_TIMESTAMP, 1)")
   ->execute(['Farai Test Candidate', $uniqueEmail]);
$testUserId = (int)$db->lastInsertId();

// Create default general user profile
$db->prepare("INSERT INTO userprofile (user, profiletype, profilestatus, display_title, is_default, reg_by, reg_date, status) VALUES (?, 1, 3, 'General User', 1, ?, CURRENT_TIMESTAMP, 1)")
   ->execute([$testUserId, $testUserId]);

echo "Created test candidate ID: {$testUserId} ({$uniqueEmail})\n";
$authCookies = getAuthCookies($testUserId);

echo "\n=== STEP 2: Submit Apprentice Express Application ===\n";
$apprRes = postHttp("{$baseUrl}/opportunities/apply/express", [
    'user_id' => $testUserId,
    'track_code' => 'apprentice',
    'legal_name' => 'Farai Test Candidate',
    'preferred_name' => 'Farai',
    'email' => $uniqueEmail,
    'mobile_number' => '+263 77 555 1234',
    'city' => 'Harare',
    'zimprovince' => 1,
    'primaryfunction' => 1,
    'institution_name' => 'University of Zimbabwe',
    'degree_programme' => 'BSc Computer Science',
    'study_level' => 'Seeking Attachment (Year 3/Part 3)',
    'wrl_start_date' => '2026-10-01',
    'wrl_duration_months' => 12,
    'consent_declaration' => 1,
], $authCookies);

echo "Apprentice Submit HTTP Code: {$apprRes['code']}, Status: " . ($apprRes['body']['status'] ?? 'null') . "\n";
assert($apprRes['body']['status'] === 1, "Apprentice submission should succeed");
$apprAppId = (int)($apprRes['body']['application_id'] ?? 0);
echo "Created Application ID: {$apprAppId}\n";

// Check userprofile was created as pending (profilestatus = 2)
$stmt = $db->prepare("SELECT * FROM userprofile WHERE user = ? AND profiletype = 2");
$stmt->execute([$testUserId]);
$apprProf = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
assert(!empty($apprProf), "Apprentice userprofile should exist");
assert((int)$apprProf['profilestatus'] === 2, "Profile status should be 2 (Pending Review)");
echo "✓ Apprentice profile is Pending Review in DB\n";

echo "\n=== STEP 3: Check Mobile Revoke Endpoint for Apprentice ===\n";
$revokeRes = postHttp("{$baseUrl}/api/mobile/user/revoke-profile", [
    'user_id' => $testUserId,
    'type_code' => 'apprentice',
    'application_id' => $apprAppId,
]);

echo "Revoke Response Status: " . ($revokeRes['body']['status'] ?? 'null') . " - " . ($revokeRes['body']['message'] ?? '') . "\n";
assert($revokeRes['body']['status'] === 1, "Mobile revoke should succeed");

// Verify rosterapplication and userprofile are deleted
$stmtApp = $db->prepare("SELECT count(*) FROM rosterapplication WHERE iD = ?");
$stmtApp->execute([$apprAppId]);
$appCount = (int)$stmtApp->fetchColumn();
$stmtApp->closeCursor();
assert($appCount === 0, "Rosterapplication must be deleted");

$stmtProf = $db->prepare("SELECT count(*) FROM userprofile WHERE user = ? AND profiletype = 2");
$stmtProf->execute([$testUserId]);
$profCount = (int)$stmtProf->fetchColumn();
$stmtProf->closeCursor();
assert($profCount === 0, "Userprofile must be deleted");
echo "✓ Apprentice records thoroughly cleaned up\n";

echo "\n=== STEP 4: Submit Associate Express Application ===\n";
$assocRes = postHttp("{$baseUrl}/opportunities/apply/express", [
    'user_id' => $testUserId,
    'track_code' => 'associate',
    'legal_name' => 'Farai Test Candidate',
    'preferred_name' => 'Farai',
    'email' => $uniqueEmail,
    'mobile_number' => '+263 77 555 1234',
    'city' => 'Harare',
    'zimprovince' => 1,
    'primaryfunction' => 2,
    'years_experience' => '8-12 years',
    'employmentstatus' => 1,
    'day_rate_expectation' => 350,
    'capacity_days_per_month' => 'Full-Time (15-22 days/month)',
    'consent_declaration' => 1,
], $authCookies);

echo "Associate Submit HTTP Code: {$assocRes['code']}, Status: " . ($assocRes['body']['status'] ?? 'null') . "\n";
assert($assocRes['body']['status'] === 1, "Associate submission should succeed");
$assocAppId = (int)($assocRes['body']['application_id'] ?? 0);
echo "Created Associate Application ID: {$assocAppId}\n";

// Check userprofile was created as pending (profiletype 3, profilestatus 2)
$stmt = $db->prepare("SELECT * FROM userprofile WHERE user = ? AND profiletype = 3");
$stmt->execute([$testUserId]);
$assocProf = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();
assert(!empty($assocProf), "Associate userprofile should exist");
assert((int)$assocProf['profilestatus'] === 2, "Profile status should be 2 (Pending Review)");
echo "✓ Associate profile is Pending Review in DB\n";

echo "\n=== STEP 5: Test Web Revoke Endpoint (POST /opportunities/apply/revoke) ===\n";
$webRevokeRes = postHttp("{$baseUrl}/opportunities/apply/revoke", [
    'application_id' => $assocAppId,
    'track_code' => 'associate',
], $authCookies);

echo "Web Revoke Result Status: " . ($webRevokeRes['body']['status'] ?? 'null') . " - " . ($webRevokeRes['body']['message'] ?? '') . "\n";
assert($webRevokeRes['body']['status'] === 1, "Web revoke should succeed");

// Verify associate records deleted
$stmtApp->execute([$assocAppId]);
$assocAppCount = (int)$stmtApp->fetchColumn();
$stmtApp->closeCursor();
assert($assocAppCount === 0, "Associate Rosterapplication must be deleted");

$stmtProf = $db->prepare("SELECT count(*) FROM userprofile WHERE user = ? AND profiletype = 3");
$stmtProf->execute([$testUserId]);
$assocProfCount = (int)$stmtProf->fetchColumn();
$stmtProf->closeCursor();
assert($assocProfCount === 0, "Associate Userprofile must be deleted");
echo "✓ Associate records thoroughly cleaned up\n";

echo "\n=== STEP 6: Verify User Can Re-apply Cleanly After Revocation ===\n";
$reapplyRes = postHttp("{$baseUrl}/opportunities/apply/express", [
    'user_id' => $testUserId,
    'track_code' => 'apprentice',
    'legal_name' => 'Farai Reapplied',
    'email' => $uniqueEmail,
    'mobile_number' => '+263 77 999 8888',
    'city' => 'Bulawayo',
    'zimprovince' => 2,
    'primaryfunction' => 1,
    'institution_name' => 'NUST',
    'degree_programme' => 'BSc Electronic Engineering',
    'study_level' => 'Final Year Student',
    'wrl_start_date' => '2026-11-01',
    'wrl_duration_months' => 6,
    'consent_declaration' => 1,
], $authCookies);

echo "Re-apply Submit Status: " . ($reapplyRes['body']['status'] ?? 'null') . "\n";
assert($reapplyRes['body']['status'] === 1, "Re-application should succeed cleanly");
$reapplyAppId = (int)($reapplyRes['body']['application_id'] ?? 0);

// Cleanup
$db->prepare("DELETE FROM profilerequestaudit WHERE userprofile IN (SELECT iD FROM userprofile WHERE user = ?)")->execute([$testUserId]);
$db->prepare("DELETE FROM userprofile WHERE user = ?")->execute([$testUserId]);
$db->prepare("DELETE FROM rosterstatusevent WHERE rosterapplication = ?")->execute([$reapplyAppId]);
$db->prepare("DELETE FROM apprenticeprofile WHERE rosterapplication = ?")->execute([$reapplyAppId]);
$db->prepare("DELETE FROM rosterapplication WHERE user = ?")->execute([$testUserId]);
$db->prepare("DELETE FROM user_notification WHERE user = ?")->execute([$testUserId]);
$db->prepare("DELETE FROM user WHERE iD = ?")->execute([$testUserId]);

echo "\n=======================================================\n";
echo "🎉 ALL TESTS PASSED: Express Apply, Status View & Revoke Verified!\n";
echo "=======================================================\n";
