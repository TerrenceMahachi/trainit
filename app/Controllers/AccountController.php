<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\Login;
use App\Models\LoginStatus;
use App\Models\Application;
use App\Helpers\Auth;
use App\Helpers\Mailer;
use Exception;

use function App\Helpers\sendmail;

class AccountController
{
    /* =====================================================================
     * Shared internals (extracted from the previously duplicated bodies of
     * login / signin / apisignin / validate / create / insert / register).
     * ===================================================================== */

    /**
     * Verify an email + password pair against the user / user_login tables.
     *
     * On success the login counters are updated. This method NEVER sets a
     * cookie — the caller decides what to do with the outcome.
     *
     * @return array{error:bool, message:string, user:?User}
     */
    private function authenticate($email, $password): array
    {
        if ($email === "" || $email === null) {
            return ['error' => true, 'message' => 'Error: Email cannot be blank', 'user' => null];
        }

        $users = User::findByQuery("SELECT * FROM user WHERE email=?", [$email]);
        if (count($users) === 0) {
            return ['error' => true, 'message' => 'Email not found', 'user' => null];
        }

        $user = $users[0];
        $logins = $user->login();
        if (count($logins) === 0) {
            // User row exists but has no credentials (no user_login record).
            return ['error' => true, 'message' => 'Error: This account has no password set. Please reset your password or register.', 'user' => null];
        }
        $user_login = $logins[0];

        // Wrong password: count the failure and possibly block the account.
        if (!password_verify($password, $user_login->password)) {
            $user_login->failed_login += 1;
            if ($user_login->failed_login > 4) {
                $user_login->status = 5;
                $message = 'Error: Invalid password, too many failed login attempts.  Account blocked, to regain access reset your account with the forgot password option';
            } else {
                $message = 'Error: Invalid password ';
            }
            $user_login->update();
            return ['error' => true, 'message' => $message, 'user' => null];
        }

        // Correct password: enforce account status.
        switch ((int) $user_login->status) {
            case 1:
            case 2:
                $user_login->login_count = $user_login->login_count + 1;
                $user_login->last_login = date("Y-m-d H:i:s.u");
                $user_login->failed_login = 0;
                $user_login->update();
                // Re-fingerprint the last three characters so the idle-resume
                // challenge stays in sync with the current password.
                \App\Helpers\PasswordResume::enroll((int) $user->iD, $password);
                return ['error' => false, 'message' => '', 'user' => $user];
            case 3:
                return ['error' => true, 'message' => 'Error: Account suspended, please contact your administrator ', 'user' => null];
            case 4:
                return ['error' => true, 'message' => 'Error: Account de-activated by owner. If you wish to re-activate this account please use the account recovery feature to re-activate it  ', 'user' => null];
            case 6:
                return ['error' => true, 'message' => '{"status": "failed", "error_code": "004", "error_msg": "Error: Account de-activated. please contact your administrator"} ', 'user' => null];
            default:
                return ['error' => true, 'message' => 'Account inactive', 'user' => null];
        }
    }

    /**
     * Validate new-account fields. Returns an error string ('' when valid).
     */
    private function validateRegistration($name, $email, $password, $minPasswordLength = 6): string
    {
        if ($name === "" || $name === null) {
            return "<br>Error: Name cannot be blank";
        }
        if (strlen((string) $password) < $minPasswordLength) {
            return "<br>Error: Password cannot be less than {$minPasswordLength} characters";
        }
        if ($email === "" || $email === null) {
            return "Error: Email cannot be blank";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "<br>Error: Invalid Email";
        }
        if (count(User::findByQuery("SELECT * FROM user WHERE email=?", [$email])) > 0) {
            return "Error: Email already registered on this system ";
        }
        return "";
    }

    /**
     * Create a user + login row and return the new User.
     */
    private function createUser($name, $email, $password, $role, $regBy): User
    {
        new LoginStatus();

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->role = $role;
        $user->reg_by = $regBy;
        $user->save();

        $login = new Login();
        $login->user = $user->iD;
        $login->reg_by = $regBy;
        $login->password = password_hash($password, PASSWORD_BCRYPT);
        $login->save();

        // Fingerprint the password tail so idle-resume works from first login.
        \App\Helpers\PasswordResume::enroll((int) $user->iD, $password);

        return $user;
    }

    /* =====================================================================
     * Public actions
     * ===================================================================== */

    public function login($request)
    {
        $result = $this->authenticate($request["email"], $request["password"]);

        if (!$result['error']) {
            Auth::login($result['user']->iD);
            // Same-site paths only — the old "redirect" cookie was client
            // writable and could send the user to another host after login.
            global $siteConfig;
            header("Location: " . $siteConfig->siteUrl . \App\Helpers\AuthReturn::consume('/dashboard'));
            exit;
        }

        $data = [
            "status" => $result['message'],
            "response_code" => "003",
            "title" => "Home",
        ];
        return view('account.login', compact('data'));
    }

    public function signin($request)
    {
        $result = $this->authenticate($request["email"], $request["password"]);
        $user = [];
        if (!$result['error']) {
            $u = $result['user'];
            $user = ['name' => $u->name, 'email' => $u->email, 'password' => $request["password"], 'iD' => $u->iD];
        }
        return [
            'status' => $result['error'] ? 0 : 1,
            'msg'    => $this->usernameWording($result['message']),
            'user'   => $user,
        ];
    }

    /** Preserve the original "Username" wording used by the app/mobile clients. */
    private function usernameWording($message): string
    {
        return str_replace(
            ['Email not found', 'Email cannot be blank'],
            ['Username not found', 'Username cannot be blank'],
            $message
        );
    }

    public function apisignin($request)
    {
        $result = $this->authenticate($request["email"], $request["password"]);
        $user = [];
        if (!$result['error']) {
            $u = $result['user'];
            $user = ['name' => $u->name, 'email' => $u->email, 'password' => $request["password"], 'iD' => $u->iD, 'role' => $u->role];
        }
        return [
            'status' => $result['error'] ? 0 : 1,
            'msg'    => $this->usernameWording($result['message']),
            'user'   => $user,
        ];
    }

    public function validate($request)
    {
        $result = $this->authenticate($request["email"], $request["password"]);
        if (!$result['error']) {
            Auth::login($result['user']->iD);
        }
        return json_encode([
            'status' => $result['error'] ? 0 : 1,
            'msg'    => $result['message'],
        ]);
    }

    /**
     * Step 1 of password reset: send a one-time link to the given email.
     * Always returns the same generic message so the endpoint cannot be used
     * to probe which emails are registered.
     */
    public function requestReset($email)
    {
        $generic = "If that email is registered, a password reset link has been sent to it.";

        if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 1, 'msg' => $generic];
        }

        $users = User::findByQuery("SELECT * FROM user WHERE email=?", [$email]);
        if (count($users) === 0 || count($users[0]->login()) === 0) {
            return ['status' => 1, 'msg' => $generic];
        }
        $user = $users[0];

        $reset = new \App\Models\PasswordReset();
        $reset->user = $user->iD;
        $reset->token = bin2hex(random_bytes(32));
        $reset->expires = (new \DateTime('+1 hour'))->format('Y-m-d H:i:s');
        $reset->used = 0;
        $reset->reg_by = $user->iD;
        $reset->save();

        global $siteConfig;
        $link = $siteConfig->siteUrl . "/set-password?token=" . $reset->token;
        Mailer::sendPasswordReset($user, $link);

        return ['status' => 1, 'msg' => $generic];
    }

    /** Look up a reset token; returns the PasswordReset row or null. */
    public function findValidReset($token)
    {
        if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }
        $rows = \App\Models\PasswordReset::findByQuery(
            "SELECT * FROM password_reset WHERE token=? AND used=0 LIMIT 1",
            [$token]
        );
        if (count($rows) === 0) {
            return null;
        }
        $reset = $rows[0];
        if (new \DateTime($reset->expires) < new \DateTime()) {
            return null;
        }
        return $reset;
    }

    /**
     * Step 2 of password reset: set the new password. Also clears the
     * failed-login counter and unblocks the account (status 5 → 1), which is
     * the promised recovery path for locked accounts.
     */
    public function completeReset($token, $password)
    {
        if (strlen((string) $password) < 6) {
            return ['status' => 0, 'msg' => "Error: Password cannot be less than 6 characters"];
        }

        $reset = $this->findValidReset($token);
        if (!$reset) {
            return ['status' => 0, 'msg' => "Error: This reset link is invalid or has expired. Please request a new one."];
        }

        $user = $this->getUser($reset->user);
        if (!$user || count($user->login()) === 0) {
            return ['status' => 0, 'msg' => "Error: Account not found."];
        }

        $login = $user->login()[0];
        $login->password = password_hash($password, PASSWORD_BCRYPT);
        $login->failed_login = 0;
        if ((int) $login->status === 5) {
            $login->status = 1; // unblock accounts locked by failed attempts
        }
        $login->update();

        // Keep the idle-resume fingerprint aligned with the new password.
        \App\Helpers\PasswordResume::enroll((int) $user->iD, $password);

        $reset->used = 1;
        $reset->update();

        // Send confirmation email
        Mailer::sendPasswordResetSuccess($user);

        return ['status' => 1, 'msg' => "Password updated successfully. You can now log in."];
    }

    /**
     * The role a new account may be created with. A client-supplied role is
     * honored ONLY when the requester is an authenticated Administrator;
     * everyone else gets the default General User role (2). This blocks the
     * old privilege-escalation hole of posting role=1 at registration.
     */
    private function requestedRole(): int
    {
        $requested = (int) ($_POST["role"] ?? 2);
        if ($requested === 2) {
            return 2;
        }
        $requesterId = Auth::id();
        if ($requesterId) {
            $requester = $this->getUser($requesterId);
            if ($requester && (int) $requester->role === 1) {
                return $requested;
            }
        }
        return 2;
    }

    function insert()
    {
        $role = $this->requestedRole();
        $error = $this->validateRegistration($_POST["name"], $_POST["email"], $_POST["password"], 4);

        if ($error !== "") {
            return ['status' => 0, 'msg' => $error];
        }

        $user = $this->createUser($_POST["name"], $_POST["email"], $_POST["password"], $role, Auth::id() ?? 1);

        // Only switch the session for self-registration. When an admin is
        // creating an account for someone else, keep the admin logged in.
        if (!Auth::check()) {
            Auth::login($user->iD);
        }

        if (!empty($user->email)) {
            Mailer::sendWelcome($user);
        }

        return ['status' => 1, 'msg' => "Account registered successfully.", 'user' => $user->name];
    }

    function create()
    {
        // Server-side CAPTCHA (see app/Helpers/Captcha.php). Replaces the old
        // check against a client-set `captcha_answer` cookie, which was forgeable.
        if (!\App\Helpers\Captcha::verify($_POST["txt_not_robot_answer"] ?? '')) {
            return ['status' => 0, 'msg' => "<br>Error: Incorrect answer to the verification question"];
        }

        $role = $this->requestedRole();
        $error = $this->validateRegistration($_POST["name"], $_POST["email"], $_POST["password"], 4);
        if ($error !== "") {
            return ['status' => 0, 'msg' => $error];
        }

        $user = $this->createUser($_POST["name"], $_POST["email"], $_POST["password"], $role, Auth::id() ?? 1);

        // Only switch the session for self-registration (see insert()).
        if (!Auth::check()) {
            Auth::login($user->iD);
        }

        if (!empty($user->email)) {
            Mailer::sendWelcome($user);
        }

        return ['status' => 1, 'msg' => "Account registered successfully."];
    }

    function register($request)
    {
        $error = $this->validateRegistration($request["name"], $request["email"], $request["password"], 6);
        if ($error !== "") {
            return ['status' => 0, 'msg' => $error];
        }

        $user = $this->createUser($request["name"], $request["email"], $request["password"], 2, 1);
        Auth::login($user->iD);

        if (!empty($user->email)) {
            Mailer::sendWelcome($user);
        }

        return ['status' => 1, 'msg' => "Account registered successfully. "];
    }

    public function activate($request)
    {
        $record = null;
        if (count(Application::where("iD", $_GET['token'])) > 0) {
            $record = Application::where("iD", $_GET['token'])[0];
            if ($record->status == 1) {
                $record->status = 2;
                $record->update();
                $msg = "Hi " . $record->creator()->name . ".<br>Your record has been activated.";
            } else {
                $msg = "This record has already been activated.";
            }
        } else {
            $msg = "Document not found.";
        }

        $data = ["status" => "failed", "response_code" => "003", "user" => $record, "title" => "Confirm", "message" => $msg];
        return view('home.activate', compact('data'));
    }

    public function confirm($request)
    {
        $_error = false;
        $_result = "";
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = $_POST["password"];

        $error = $this->validateRegistration($name, $email, $password, 6);
        if ($error !== "") {
            $_error = true;
            $_result = $error;
        }

        $us = null;
        $msg = $_result;
        if (count(User::where("iD", $_POST['token'])) > 0) {
            $user = User::where("iD", $_POST['token'])[0];
            $user->name = $name;
            $user->email = $email;
            $user->password = password_hash($password, PASSWORD_BCRYPT);
            $user->status = 2;
            $user->update();

            Auth::login($user->iD);

            if (count(Application::where("reg_by", $user->iD)) > 0) {
                $record = Application::where("reg_by", $user->iD)[0];
                $record->status = 2;
                $record->update();
            }

            $msg = "Welcome " . $_POST['name'] . "<br>Your application has been activated.";
            header("Location: dashboard");
        }

        $data = ["status" => "failed", "response_code" => "003", "user" => $us, "title" => "Confirm", "message" => $msg];
        return view('home.confirm', compact('data'));
    }

    function getUser($user)
    {
        $users = User::findByQuery("SELECT * FROM user WHERE iD=?", [$user]);
        return count($users) > 0 ? $users[0] : false;
    }
}
