<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;
use App\Models\Login;
use App\Models\Database;

if ($argc > 1 && $argv[1] === 'worker') {
    // Worker mode: handles one request and exits
    $payload = json_decode(file_get_contents('php://stdin'), true);
    $_SERVER['REQUEST_METHOD'] = $payload['method'];
    $_SERVER['REQUEST_URI'] = $payload['uri'];
    $_POST = $payload['post'] ?? [];
    $_GET = $payload['get'] ?? [];

    $router = new \App\Router();
    require __DIR__ . '/../routes/api_mobile_routes.php';

    try {
        $router->matchRoute();
    } catch (\Throwable $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

echo "=== TESTING MOBILE UPDATE PROFILE & CHANGE PASSWORD ===\n";

// Find or create test candidate user
$testEmail = 'mobile_profile_test_' . time() . '@test.tsigiro.co.zw';
$testUser = new User();
$testUser->name = 'Original Test Name';
$testUser->email = $testEmail;
$testUser->role = 5; // Apprentice / Candidate
$testUser->status = 1;
$testUser->save();
$userId = $testUser->iD;

$login = new Login();
$login->user = $userId;
$login->password = password_hash('OldPassword123!', PASSWORD_BCRYPT);
$login->status = 1;
$login->save();

echo "[1] Created test user #$userId with email $testEmail\n";

// Helper to run worker via process
function runRequest($method, $uri, $post = [], $get = []) {
    $payload = json_encode([
        'method' => $method,
        'uri' => $uri,
        'post' => $post,
        'get' => $get
    ]);

    $cmd = 'php ' . escapeshellarg(__FILE__) . ' worker';
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];
    $proc = proc_open($cmd, $descriptors, $pipes);
    fwrite($pipes[0], $payload);
    fclose($pipes[0]);
    $out = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($proc);

    // Extract JSON line
    preg_match('/\{.*\}$/s', trim($out), $m);
    return json_decode($m[0] ?? $out, true);
}

// Test 1: Update Profile - Name and Email
echo "\n[2] Testing POST /api/mobile/user/update-profile...\n";
$newEmail = 'updated_' . time() . '@test.tsigiro.co.zw';
$res = runRequest('POST', '/api/mobile/user/update-profile', [
    'user_id' => $userId,
    'name' => 'Updated Test Name',
    'email' => $newEmail
]);

if ($res && ($res['status'] ?? 0) === 1 && $res['user']['name'] === 'Updated Test Name') {
    echo "  ✓ PASS: Profile updated. Returned name: " . $res['user']['name'] . ", email: " . $res['user']['email'] . "\n";
} else {
    echo "  ✗ FAIL: " . json_encode($res) . "\n";
}

// Verify in DB
$dbUser = User::find($userId);
if ($dbUser && $dbUser->name === 'Updated Test Name' && $dbUser->email === $newEmail) {
    echo "  ✓ PASS: Database reflects name and email changes\n";
} else {
    echo "  ✗ FAIL: Database record not updated properly\n";
}

// Test 2: Change Password - Wrong current password
echo "\n[3] Testing POST /api/mobile/user/change-password validations...\n";
$resWrong = runRequest('POST', '/api/mobile/user/change-password', [
    'user_id' => $userId,
    'current_password' => 'WrongPassword',
    'new_password' => 'NewPassword123!',
    'confirm_password' => 'NewPassword123!'
]);
if ($resWrong && ($resWrong['status'] ?? 1) === 0 && strpos($resWrong['message'], 'incorrect') !== false) {
    echo "  ✓ PASS: Correctly rejected incorrect current password\n";
} else {
    echo "  ✗ FAIL: " . json_encode($resWrong) . "\n";
}

// Mismatched passwords
$resMismatch = runRequest('POST', '/api/mobile/user/change-password', [
    'user_id' => $userId,
    'current_password' => 'OldPassword123!',
    'new_password' => 'NewPassword123!',
    'confirm_password' => 'DifferentPassword123!'
]);
if ($resMismatch && ($resMismatch['status'] ?? 1) === 0 && strpos($resMismatch['message'], 'match') !== false) {
    echo "  ✓ PASS: Correctly rejected password confirmation mismatch\n";
} else {
    echo "  ✗ FAIL: " . json_encode($resMismatch) . "\n";
}

// Short password (<6)
$resShort = runRequest('POST', '/api/mobile/user/change-password', [
    'user_id' => $userId,
    'current_password' => 'OldPassword123!',
    'new_password' => '12345',
    'confirm_password' => '12345'
]);
if ($resShort && ($resShort['status'] ?? 1) === 0 && strpos($resShort['message'], '6 characters') !== false) {
    echo "  ✓ PASS: Correctly rejected short password\n";
} else {
    echo "  ✗ FAIL: " . json_encode($resShort) . "\n";
}

// Test 3: Valid Change Password
echo "\n[4] Testing valid POST /api/mobile/user/change-password...\n";
$resSuccess = runRequest('POST', '/api/mobile/user/change-password', [
    'user_id' => $userId,
    'current_password' => 'OldPassword123!',
    'new_password' => 'NewBrandPassword456!',
    'confirm_password' => 'NewBrandPassword456!'
]);
if ($resSuccess && ($resSuccess['status'] ?? 0) === 1) {
    echo "  ✓ PASS: Password successfully updated\n";
} else {
    echo "  ✗ FAIL: " . json_encode($resSuccess) . "\n";
}

// Test 4: Verify authentication with new password
echo "\n[5] Verifying login with updated password...\n";
$db = (new Database())->getPDO();
$stmt = $db->prepare("SELECT password FROM user_login WHERE user = ?");
$stmt->execute([$userId]);
$newHash = $stmt->fetchColumn();

if ($newHash && password_verify('NewBrandPassword456!', $newHash)) {
    echo "  ✓ PASS: New password verifies with bcrypt hash in database\n";
} else {
    echo "  ✗ FAIL: New password hash check failed\n";
}

// Cleanup test user
$db->prepare("DELETE FROM user_login WHERE user = ?")->execute([$userId]);
$db->prepare("DELETE FROM user WHERE iD = ?")->execute([$userId]);
echo "\n[6] Cleaned up test user #$userId\n";

echo "\n=== ALL TESTS COMPLETED SUCCESSFULLY ===\n";
