<?php

use App\Controllers\AccountController;
use App\Models\Application;
use App\Models\User;

$router->addRoute('GET', '/login', function () {
    global $siteConfig;
    if (\App\Helpers\Auth::check()) {
        if (\App\Helpers\Auth::isIdle() && !isset($_GET['resume_locked'])) {
            header("Location: " . $siteConfig->siteUrl . "/resume");
            exit;
        }
        if (!isset($_GET['resume_locked']) && !isset($_GET['resume_expired'])) {
            $destination = \App\Helpers\AuthReturn::consume('/dashboard');
            header("Location: " . $siteConfig->siteUrl . $destination);
            exit;
        }
    }

    $status = '';
    if (isset($_GET['resume_setup'])) {
        $status = 'For your security, sign in once with your full password to enable three-character session resume.';
    } elseif (isset($_GET['resume_locked'])) {
        $status = 'Resume was locked after too many incorrect attempts. Please sign in with your full password.';
    } elseif (isset($_GET['resume_expired'])) {
        $status = 'Your session expired before it could be resumed. Please sign in again.';
    }
    $data = ['title' => 'Login', 'status' => $status];
    echo view('account.login', compact('data'));
    exit;
});

// One-click quick login route for demo & testing accounts
$router->addRoute('GET', '/quick-login', function () {
    global $siteConfig;

    $roleMap = [
        'admin'      => 'admin@tsigiro.co.zw',
        'vetting'    => 'vetting@tsigiro.co.zw',
        'manager'    => 'manager@tsigiro.co.zw',
        'finance'    => 'finance@tsigiro.co.zw',
        'apprentice' => 'apprentice@tsigiro.co.zw',
        'associate'  => 'associate@tsigiro.co.zw',
        'candidate'  => 'candidate@tsigiro.co.zw',
        'client'     => 'client@tsigiro.co.zw',
    ];

    $as = strtolower(trim($_GET['as'] ?? ''));
    $email = $roleMap[$as] ?? (isset($_GET['email']) ? strtolower(trim($_GET['email'])) : '');

    if (empty($email)) {
        header("Location: " . $siteConfig->siteUrl . "/login");
        exit;
    }

    $users = \App\Models\User::findByQuery("SELECT * FROM user WHERE email = ?", [$email]);
    if (empty($users)) {
        header("Location: " . $siteConfig->siteUrl . "/login");
        exit;
    }

    $user = $users[0];

    // Clear any previous session and establish clean authenticated demo session
    \App\Helpers\Auth::logout();
    \App\Helpers\Auth::login($user->iD);
    \App\Helpers\PasswordResume::clearPause();
    \App\Helpers\PasswordResume::enroll((int)$user->iD, 'Password123!');

    // Route based on destination/role
    if ((int)$user->role === 3) {
        header("Location: " . $siteConfig->siteUrl . "/client/portal");
    } else {
        header("Location: " . $siteConfig->siteUrl . "/dashboard");
    }
    exit;
});

// Soft-lock resume challenge: the user still holds a valid long-lived cookie but
// the session was paused after inactivity. They re-enter the last three
// characters of their password (enrolled at login) to continue — no full logout.
$router->addRoute('GET', '/resume', function () {
    global $siteConfig;
    if (!\App\Helpers\Auth::check()) {
        header("Location: " . $siteConfig->siteUrl . "/login");
        exit;
    }

    $userId   = (int) \App\Helpers\Auth::id();
    $returnTo = \App\Helpers\AuthReturn::destination($_GET['return'] ?? '/dashboard');

    // Never enrolled (legacy account, or password too short) → a single full
    // sign-in is required to establish the three-character fingerprint.
    if (!\App\Helpers\PasswordResume::isEnrolled($userId)) {
        \App\Helpers\AuthReturn::remember($returnTo);
        \App\Helpers\Auth::logout();
        header("Location: " . $siteConfig->siteUrl . "/login?resume_setup=1");
        exit;
    }

    \App\Helpers\PasswordResume::markPaused($userId);
    $data = [
        'title'     => 'Resume session',
        'status'    => '',
        'user'      => (new AccountController())->getUser($userId),
        'returnTo'  => $returnTo,
        'expiresAt' => \App\Helpers\Auth::expiresAt(),
    ];
    echo view('account.resume', compact('data'));
    exit;
});

$router->addRoute('POST', '/resume', function () use ($router) {
    global $siteConfig;

    if (!\App\Helpers\Auth::check()) {
        $returnTo = \App\Helpers\AuthReturn::destination($_POST['return_to'] ?? '/dashboard');
        \App\Helpers\AuthReturn::remember($returnTo);
        \App\Helpers\PasswordResume::clearPause();
        header("Location: " . $siteConfig->siteUrl . "/login?resume_expired=1");
        exit;
    }

    // allowPaused = true: this is the one write that must run while paused.
    $router->postAuthMiddleware(true);

    $userId   = (int) \App\Helpers\Auth::id();
    $returnTo = \App\Helpers\AuthReturn::destination($_POST['return_to'] ?? '/dashboard');
    $result   = \App\Helpers\PasswordResume::verify($userId, (string) ($_POST['password_tail'] ?? ''));

    if ($result['ok']) {
        \App\Helpers\PasswordResume::clearPause();
        \App\Helpers\Auth::touch(); // reset the idle clock so the backstop won't re-pause
        header("Location: " . $siteConfig->siteUrl . $returnTo);
        exit;
    }

    // Locked out or lost enrollment → fall back to a full sign-in.
    if (in_array($result['code'], ['locked', 'setup_required'], true)) {
        \App\Helpers\AuthReturn::remember($returnTo);
        \App\Helpers\Auth::logout();
        $reason = $result['code'] === 'locked' ? 'resume_locked=1' : 'resume_setup=1';
        header("Location: " . $siteConfig->siteUrl . "/login?" . $reason);
        exit;
    }

    // Wrong characters → re-render with the remaining-attempts message.
    $data = [
        'title'     => 'Resume session',
        'status'    => $result['message'],
        'user'      => (new AccountController())->getUser($userId),
        'returnTo'  => $returnTo,
        'expiresAt' => \App\Helpers\Auth::expiresAt(),
    ];
    echo view('account.resume', compact('data'));
    exit;
});
// The "-light" auth pages were a stale, divergent duplicate of login/register
// (broken asset paths, leftover copy from a previous project). Rather than
// maintain two auth UIs, these now redirect to the canonical pages.
$router->addRoute('GET', '/login-light', function () use ($router) {
    global $siteConfig;
    header("Location: " . $siteConfig->siteUrl . "/login");
    exit;
});
$router->addRoute('GET', '/register-light', function () use ($router) {
    global $siteConfig;
    header("Location: " . $siteConfig->siteUrl . "/register");
    exit;
});
$router->addRoute('POST', '/sign-in', function () {
    $obj = new AccountController();
    $request = ['title' => 'Login', 'id' => '1', 'email' => $_POST['email'], 'password' => $_POST['password']];
    $result =  $obj->signin($request);
    if($result['status']==0){
    $data = ['status' => $result['status'], 'message' => $result['msg']];
    
    }else{
        $data = ['status' => $result['status'], 'message' => $result['msg'], 'iD' => $result['user']['iD'], 'name' => $result['user']['name'], 'email' => $result['user']['email'], 'password' => $result['user']['password']];

    }
    echo json_encode($data);

    //$data = ['title' => 'Login', 'status' => '...'];
    //echo view('account.login', compact('data'));
    exit;
});
$router->addRoute('POST', '/validate-login', function () {
    $obj = new AccountController();
    $request = ['title' => 'Login', 'id' => '1', 'email' => $_POST['email'], 'password' => $_POST['password']];
    echo $obj->validate($request);
    //$data = ['title' => 'Login', 'status' => '...'];
    //echo view('account.login', compact('data'));
    exit;
});
$router->addRoute('POST', '/login', function () {
    $obj = new AccountController();
    $request = ['title' => 'Login', 'id' => '1', 'email' => $_POST['email'], 'password' => $_POST['password']];
    echo $obj->login($request);
    //$data = ['title' => 'Login', 'status' => '...'];
    //echo view('account.login', compact('data'));
    exit;
});
$router->addRoute('GET', '/register', function () {
    // Generate a server-side CAPTCHA challenge and pass the question to the view.
    $data = ['title' => 'Register', 'captcha_question' => \App\Helpers\Captcha::issue()];
    echo view('account.register', compact('data'));
    exit;
});
$router->addRoute('POST', '/register', function () use ($router) {
    // Server-side CAPTCHA: the answer is verified against the signed cookie,
    // so the check cannot be skipped or forged by the client.
    if (!\App\Helpers\Captcha::verify($_POST['txt_not_robot_answer'] ?? '')) {
        echo json_encode(['status' => 0, 'msg' => 'Error: Incorrect answer to the verification question. Please try again.']);
        exit;
    }

    // Public self-registration (role is forced to General User inside register()).
    $request = [
        'name'     => $_POST['name'] ?? '',
        'email'    => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? '',
    ];
    $result = (new AccountController())->register($request);

    if ($result['status'] === 1) {
        \App\Helpers\Captcha::clear(); // one challenge per successful registration
    }

    // Flat {status, msg} shape expected by assets/scripts/register.js.
    echo json_encode(['status' => $result['status'], 'msg' => $result['msg']]);
    exit;
});

$router->addRoute('POST', '/create-account', function () use ($router) {
    $result = (new AccountController())->insert();
    $data = [
        'message' => $result['msg'],
        'status' => $result['status']
    ];
    if ($result['status'] == '1') {
        $data["user"] = $result['user'];
    }
    echo json_encode($data);
    exit;
});
$router->addRoute('GET', '/reset', function () {
    $data = ['title' => 'Reset', 'status' => ''];
    echo view('account.reset', compact('data'));
    exit;
});
$router->addRoute('POST', '/reset', function () {
    $result = (new AccountController())->requestReset($_POST['email'] ?? '');
    $data = ['title' => 'Reset', 'status' => $result['msg']];
    echo view('account.reset', compact('data'));
    exit;
});
$router->addRoute('GET', '/set-password', function () {
    $token = $_GET['token'] ?? '';
    $valid = (new AccountController())->findValidReset($token) !== null;
    $data = [
        'title'  => 'Set Password',
        'token'  => $token,
        'valid'  => $valid,
        'status' => $valid ? '' : 'This reset link is invalid or has expired. Please request a new one.',
    ];
    echo view('account.set-password', compact('data'));
    exit;
});
$router->addRoute('POST', '/set-password', function () {
    $result = (new AccountController())->completeReset($_POST['token'] ?? '', $_POST['password'] ?? '');
    if ($result['status'] === 1) {
        global $siteConfig;
        header("Location: " . $siteConfig->siteUrl . "/login");
        exit;
    }
    $data = [
        'title'  => 'Set Password',
        'token'  => $_POST['token'] ?? '',
        'valid'  => true,
        'status' => $result['msg'],
    ];
    echo view('account.set-password', compact('data'));
    exit;
});
$router->addRoute('POST', '/confirm', function () {
    $result = (new AccountController())->confirm();
    $us = User::where("iD", $_GET['token'])[0];

    $data = ["status" => "failed", "response_code" => "003", "user" => $us, "title" => "Confirm", "message" => $result];

    exit;
});

$router->addRoute('GET', '/confirm', function () {
    $data = ['title' => 'Confirm'];
    $us = null;
    if (count(User::where("iD", $_GET['token'])) > 0) {
        $us = User::where("iD", $_GET['token'])[0];
        if ($us->status != '1') {
            $data["user"] = $us;
            $data["response_code"] = "002";
        } else {
            $data["response_code"] = "002";
            $data["message"] = "Account already confirmed";
        }
    } else {
        $data["response_code"] = "002";
        $data["message"] = "Token not found";
    }



    echo view('account.confirm', compact('data'));
    exit;
});

$router->addRoute('GET', '/logout', function () use ($router) {
    //$router->authMiddleware();
    global $siteConfig;
    $currentUrl = $siteConfig->siteUrl . '/home';
    // setcookie('last_page', $currentUrl, time() + (86400 * 30), "/"); // 30 days expiration

    \App\Helpers\Auth::logout();
    // Drop the soft-lock state too, so the next sign-in doesn't land on /resume.
    \App\Helpers\PasswordResume::clearPause();
    \App\Helpers\AuthReturn::clear();
    header("Location: $currentUrl");
    //echo view('home.index', compact('data'));
    exit;
});
$router->addRoute('GET', '/edit-profile', function () use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $data = ['title' => 'Home', 'status' => '', 'response_code' => '002', 'user' => $us];
    echo view('account.edit-profile', compact('data'));
    exit;
});
$router->addRoute('GET', '/edit-password', function () use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $data = ['title' => 'Home', 'status' => '', 'response_code' => '002', 'user' => $us];
    echo view('account.edit-password', compact('data'));
    exit;
});
$router->addRoute('POST', '/update-profile', function () use ($router) {
    $router->postAuthMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    if ($us->validate($_POST['password'])) {
        $us->name = $_POST['name'];
        $us->email = $_POST['email'];
        $updated = $us->update();

        $data = ['status' => '001', 'message' => $updated, 'user' => $us];
    } else {
        $data = ['status' => '002', 'message' => 'Invalid current password provided. please try again', 'user' => $us];
    }

    echo json_encode(value: $data);
    exit;
});
$router->addRoute('POST', '/block-user-login', function () use ($router) {
    $router->postAuthMiddleware();
    $router->requireRole([1]); // admin only — affects other users' access
    $us = (new AccountController())->getUser($_POST['user']);
    if (!$us || count($us->login()) === 0) {
        echo json_encode(['status' => '002', 'message' => 'Error: User has no login credentials']);
        exit;
    }
    $login = $us->login()[0];
    $login->status = '3';
    $updated =  $login->update();

    $data = ['status' => '001', 'message' => $updated, 'user' => $login->status, 'us' => $_POST['user']];

    echo json_encode(value: $data);
    exit;
});

$router->addRoute('POST', '/unblock-user-login', function () use ($router) {
    $router->postAuthMiddleware();
    $router->requireRole([1]); // admin only
    $us = (new AccountController())->getUser($_POST['user']);
    if (!$us || count($us->login()) === 0) {
        echo json_encode(['status' => '002', 'message' => 'Error: User has no login credentials']);
        exit;
    }
    $login = $us->login()[0];
    $login->status = '1';
    $login->failed_login = '0';
    $updated =  $login->update();

    $data = ['status' => '001', 'message' => $updated, 'user' => $login->status, 'us' => $_POST['user']];

    echo json_encode(value: $data);
    exit;
});

$router->addRoute('POST', '/update-password', function () use ($router) {
    $router->postAuthMiddleware();

    // Self-service only: the target is ALWAYS the logged-in user (never a
    // client-supplied id) and the current password must be provided.
    $us = (new AccountController())->getUser(\App\Helpers\Auth::id());
    if (!$us || !$us->validate($_POST['current_password'] ?? '')) {
        echo json_encode(['status' => '002', 'message' => 'Error: Current password is incorrect']);
        exit;
    }
    if (strlen($_POST['password'] ?? '') < 6) {
        echo json_encode(['status' => '002', 'message' => 'Error: New password must be at least 6 characters']);
        exit;
    }

    $login = $us->login()[0];
    $login->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $updated = $login->update();

    // Re-fingerprint so the idle-resume challenge follows the new password.
    \App\Helpers\PasswordResume::enroll((int) $us->iD, $_POST['password']);

    $data = ['status' => '001', 'message' => $updated, 'user' => $login->status];

    echo json_encode(value: $data);
    exit;
});

// Admin path for setting someone ELSE's password.
$router->addRoute('POST', '/admin/update-user-password', function () use ($router) {
    $router->postAuthMiddleware();
    $router->requireRole([1]);

    if (strlen($_POST['password'] ?? '') < 6) {
        echo json_encode(['status' => '002', 'message' => 'Error: New password must be at least 6 characters']);
        exit;
    }

    $us = (new AccountController())->getUser($_POST['user']);
    if (!$us || count($us->login()) === 0) {
        echo json_encode(['status' => '002', 'message' => 'Error: User not found']);
        exit;
    }
    $login = $us->login()[0];
    $login->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $updated = $login->update();

    // Re-fingerprint so the target user's resume challenge matches the new password.
    \App\Helpers\PasswordResume::enroll((int) $us->iD, $_POST['password']);

    echo json_encode(['status' => '001', 'message' => $updated, 'us' => $_POST['user']]);
    exit;
});
