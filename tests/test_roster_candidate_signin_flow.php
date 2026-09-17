<?php
/**
 * Test Suite: Roster Candidate Sign-In & Password Intake Flow
 * Verifies that guest candidates specify their login credentials during application,
 * can log into the portal anytime using their chosen password, and that forms and validation
 * work correctly.
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\RosterApplicationController;
use App\Controllers\AccountController;
use App\Helpers\Auth;
use App\Models\User;
use App\Models\Login;
use App\Models\Rosterapplication;

echo "=== STARTING CANDIDATE SIGN-IN INTAKE TEST SUITE ===\n\n";

$controller = new RosterApplicationController();
$accountController = new AccountController();
$pdo = \App\Models\Database::sharedPdo();

// Clean any active session
Auth::logout();
$_SESSION = [];

// Create a mock CV file for upload
$tmpCv = tempnam(sys_get_temp_dir(), 'test_cv_');
file_put_contents($tmpCv, "%PDF-1.4 Mock CV Content for Sign-in Test");

function makeCvFileArray($path) {
    return [
        'name' => 'test_cv.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $path,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($path),
    ];
}

// -----------------------------------------------------------------------------
// [Test 1] Apprentice Application with Sign-In Password
// -----------------------------------------------------------------------------
echo "[Test 1] Testing Apprentice Guest Application with Custom Password...\n";
$uniq1 = 'appr_' . bin2hex(random_bytes(4));
$email1 = "{$uniq1}@example.com";
$pass1 = "SecretP@ss123";

$_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';
$_POST = [
    'track_code' => 'apprentice',
    'legal_name' => 'Kudzi Mandaza',
    'preferred_name' => 'Kudzi',
    'email' => $email1,
    'password' => $pass1,
    'password_confirmation' => $pass1,
    'mobile_number' => '+263 77 111 2233',
    'city' => 'Harare',
    'zimprovince' => 1,
    'primaryfunction' => 1,
    'institution_name' => 'University of Zimbabwe',
    'degree_programme' => 'BSc Information Technology',
    'study_level' => 'Seeking Attachment (Year 3/Part 3)',
    'wrl_start_date' => date('Y-m-d'),
    'wrl_duration_months' => 12,
    'consent_declaration' => 1,
];
$_FILES['cv_doc'] = makeCvFileArray($tmpCv);

$res1 = $controller->handleExpressSubmit();
assert(is_array($res1) && isset($res1['status']) && $res1['status'] === 1, "Apprentice submission should succeed: " . json_encode($res1));
echo " PASSED: Apprentice application submitted. App ID: {$res1['application_id']}\n";

// Verify user exists and can authenticate via AccountController with the chosen password
$users1 = User::findByQuery("SELECT * FROM user WHERE email = ?", [$email1]);
assert(!empty($users1), "User record must exist");
$user1 = $users1[0];

// Log out to verify fresh authentication with provided password
Auth::logout();
$_SESSION = [];

$signinRes = $accountController->signin([
    'email' => $email1,
    'password' => $pass1
]);
assert($signinRes['status'] === 1, "Candidate must be able to sign in with password chosen during application! Error: " . ($signinRes['msg'] ?? ''));
assert($signinRes['user']['email'] === $email1, "Signed in user email matches");
echo " PASSED: Candidate successfully authenticated with application password '{$pass1}'\n";


// -----------------------------------------------------------------------------
// [Test 2] Associate Application with Sign-In Password
// -----------------------------------------------------------------------------
echo "\n[Test 2] Testing Associate Guest Application with Custom Password...\n";
$uniq2 = 'assoc_' . bin2hex(random_bytes(4));
$email2 = "{$uniq2}@example.com";
$pass2 = "SeniorExpert#456";

Auth::logout();
$_SESSION = [];

$_POST = [
    'track_code' => 'associate',
    'legal_name' => 'Dr. Chengetai Sibanda',
    'preferred_name' => 'Chengetai',
    'email' => $email2,
    'password' => $pass2,
    'password_confirmation' => $pass2,
    'mobile_number' => '+263 71 888 9999',
    'city' => 'Bulawayo',
    'zimprovince' => 2,
    'primaryfunction' => 2,
    'years_experience' => '10+ years',
    'highest_qualification' => 'PhD / Master of Science',
    'day_rate_usd' => 450,
    'availability_days_month' => 15,
    'consent_declaration' => 1,
];
$_FILES['cv_doc'] = makeCvFileArray($tmpCv);

$res2 = $controller->handleExpressSubmit();
assert(is_array($res2) && isset($res2['status']) && $res2['status'] === 1, "Associate submission should succeed: " . json_encode($res2));
echo " PASSED: Associate application submitted. App ID: {$res2['application_id']}\n";

// Log out and verify sign in with the chosen password
Auth::logout();
$_SESSION = [];

$signinRes2 = $accountController->signin([
    'email' => $email2,
    'password' => $pass2
]);
assert($signinRes2['status'] === 1, "Associate must be able to sign in with application password! Error: " . ($signinRes2['msg'] ?? ''));
echo " PASSED: Associate consultant successfully authenticated with password '{$pass2}'\n";


// -----------------------------------------------------------------------------
// [Test 3] Password Validation Tests (Mismatch & Short Password)
// -----------------------------------------------------------------------------
echo "\n[Test 3] Testing Password Validation (Mismatch & Minimum Length)...\n";

Auth::logout();
$_SESSION = [];

// Test A: Mismatched passwords
$_POST['email'] = 'test_mismatch_' . bin2hex(random_bytes(3)) . '@example.com';
$_POST['password'] = 'ValidPass123';
$_POST['password_confirmation'] = 'DifferentPass456';
$mismatchRes = $controller->handleExpressSubmit();
assert(is_array($mismatchRes) && $mismatchRes['status'] === 0, "Should return status 0 on mismatch");
assert(strpos($mismatchRes['message'], 'Passwords do not match') !== false, "Message should mention mismatch: " . $mismatchRes['message']);
echo " PASSED: Mismatched passwords rejected with clear error: '{$mismatchRes['message']}'.\n";

// Test B: Password too short (< 6 characters)
$_POST['email'] = 'test_short_' . bin2hex(random_bytes(3)) . '@example.com';
$_POST['password'] = '12345';
$_POST['password_confirmation'] = '12345';
$shortRes = $controller->handleExpressSubmit();
assert(is_array($shortRes) && $shortRes['status'] === 0, "Should return status 0 on short password");
assert(strpos($shortRes['message'], 'at least 6 characters') !== false, "Message should mention length: " . $shortRes['message']);
echo " PASSED: Short passwords (< 6 chars) rejected with clear error: '{$shortRes['message']}'.\n";


// -----------------------------------------------------------------------------
// [Test 4] Existing User Applying with Existing Account Password
// -----------------------------------------------------------------------------
echo "\n[Test 4] Testing Existing User Verification during Application...\n";

// A: Wrong password for existing user
Auth::logout();
$_SESSION = [];
$_POST['email'] = $email1; // User from Test 1
$_POST['password'] = 'WrongPassword999';
$_POST['password_confirmation'] = 'WrongPassword999';
$wrongPassRes = $controller->handleExpressSubmit();
assert(is_array($wrongPassRes) && $wrongPassRes['status'] === 0, "Should return status 0 for wrong existing password");
assert(strpos($wrongPassRes['message'], 'already exists') !== false, "Message should mention account already exists");
echo " PASSED: Existing user with incorrect password blocked from hijacking account.\n";

// B: Correct password for existing user allows application and signs in
$_POST['password'] = $pass1;
$_POST['password_confirmation'] = $pass1;
$resExisting = $controller->handleExpressSubmit();
assert(is_array($resExisting) && $resExisting['status'] === 1, "Existing user with correct password should succeed: " . json_encode($resExisting));
assert((int)Auth::id() === (int)$user1->iD, "Authenticated user ID matches existing user");
echo " PASSED: Existing user authenticated and application processed.\n";


// -----------------------------------------------------------------------------
// [Test 5] Verify Views Render Sign-In Credentials Fields for Guests
// -----------------------------------------------------------------------------
echo "\n[Test 5] Verifying Views Render Sign-In Credential Inputs for Guests...\n";

Auth::logout();
$_SESSION = [];

$apprenticeHtml = (string) $controller->showExpressForm('apprentice');
assert(strpos($apprenticeHtml, 'name="password"') !== false, "Apprentice form must contain password input for guests");
assert(strpos($apprenticeHtml, 'name="password_confirmation"') !== false, "Apprentice form must contain password_confirmation input");
assert(strpos($apprenticeHtml, 'Create Portal Account Sign-In Details') !== false, "Apprentice form must contain credentials section title");
assert(strpos($apprenticeHtml, 'toggle-password-btn') !== false, "Apprentice form must contain password visibility toggle");
echo " PASSED: Apprentice view renders sign-in credential inputs, toggles, and help text.\n";

$associateHtml = (string) $controller->showExpressForm('associate');
assert(strpos($associateHtml, 'name="password"') !== false, "Associate form must contain password input for guests");
assert(strpos($associateHtml, 'name="password_confirmation"') !== false, "Associate form must contain password_confirmation input");
assert(strpos($associateHtml, 'Create Portal Account Sign-In Details') !== false, "Associate form must contain credentials section title");
echo " PASSED: Associate view renders sign-in credential inputs, toggles, and help text.\n";

// -----------------------------------------------------------------------------
// [Test 6] Verify Views Omit Password Fields for Already Authenticated Users
// -----------------------------------------------------------------------------
echo "\n[Test 6] Verifying Views Omit Password Fields for Already Authenticated Users...\n";

// Create a new user who has not submitted an Apprentice application yet
$newCandidate = new User();
$newCandidate->name = 'Rufaro Gumbo';
$newCandidate->email = 'rufaro_' . bin2hex(random_bytes(3)) . '@example.com';
$newCandidate->role = 2;
$newCandidate->status = 1;
$newCandidate->save();

Auth::login($newCandidate->iD);

$apprAuthHtml = (string) $controller->showExpressForm('apprentice');
assert(strpos($apprAuthHtml, 'Signed in as') !== false, "Authenticated apprentice view should show signed in banner");
assert(strpos($apprAuthHtml, 'Create Portal Account Sign-In Details') === false, "Authenticated apprentice view should omit password section");
assert(strpos($apprAuthHtml, 'name="password"') === false, "Authenticated apprentice view should not contain password input");
echo " PASSED: Authenticated candidate sees 'Signed in as' alert and no redundant password inputs.\n";

// Clean up
@unlink($tmpCv);

echo "\n=== ALL CANDIDATE SIGN-IN TESTS PASSED 100%! ===\n";
