<?php

namespace App;

use Closure;
use Exception;
class Router
{
    protected $routes = []; // stores routes
    protected $basePath = "/" . _SITENAME;

    public function addRoute(string $method, string $url, Closure $target)
    {
        $this->routes[strtoupper($method)][$url] = $target; // ensure method is uppercase
    }
   
    public function sessionAuthMiddleware($token)
    {
        global $siteConfig;
        
        $sql = "SELECT * FROM usersession WHERE token = ? LIMIT 1";
        $records = \App\Models\UserSession::findByQuery($sql, [$token]);

        if (count($records) > 0) {
            $session = $records[0];
            $now = new \DateTime();
            $end = new \DateTime($session->end);

            if ($end > $now) {
                $data['user'] = (new \App\Controllers\AccountController())->getUser($session->user); 
                $data['user']->extendSession();

            }else{
                echo json_encode(['status' => '2', 'message' => 'Error: Session Expired']);
                exit;
            }
        }else{
            echo json_encode(['status' => '2', 'message' => 'Error: Session Not Found']);
            exit;
        }


        // $currentUrl = $siteConfig->siteUrl . str_replace($this->basePath, "", $_SERVER['REQUEST_URI']) ?: '/' ;
        // error_log("Current URL: " . $currentUrl);

        //   setcookie('last_page', $currentUrl, time() + (86400 * 30), "/"); // 30 days expiration

       
    }
    public function _postAuthMiddleware()
    {
        global $siteConfig;
        // $currentUrl = $siteConfig->siteUrl . str_replace($this->basePath, "", $_SERVER['REQUEST_URI']) ?: '/' ;
        // error_log("Current URL: " . $currentUrl);

        //   setcookie('last_page', $currentUrl, time() + (86400 * 30), "/"); // 30 days expiration

        $sql = "SELECT * FROM usersession WHERE token = ? LIMIT 1";
        $records = \App\Models\UserSession::findByQuery($sql, [$_POST['session_token']]);

        if (count($records) > 0) {
            $session = $records[0];
            $now = new \DateTime();
            $end = new \DateTime($session->end);

            if ($end > $now) {
                $user = (new \App\Controllers\AccountController())->getUser($session->user); 
                $user->extendSession();
                return $user;

            }else{
                echo json_encode(['status' => '2', 'message' => 'Error: Session Expired']);
                exit;
            }
        }else{
            echo json_encode(['status' => '2', 'message' => 'Error: Session Not Found']);
            exit;
        }


    }
    public function postAuthMiddleware($allowPaused = false)
    {
        global $siteConfig;

        // Verify the signed auth cookie, not merely the presence of "user".
        if (!\App\Helpers\Auth::check()) {
            echo json_encode(['status' => '2', 'message' => 'Error: Session Expired']);
            exit;
        }

        // Soft-lock: the session is either already paused, or idle past the
        // timeout (a server-side backstop for when the client idle timer never
        // fired). Tell the browser to visit the resume challenge instead of
        // running this write. The resume POST itself passes $allowPaused = true.
        if (!$allowPaused) {
            $userId = (int) \App\Helpers\Auth::id();
            $paused = \App\Helpers\PasswordResume::isPaused($userId);
            if (!$paused && \App\Helpers\Auth::isIdle()) {
                \App\Helpers\PasswordResume::markPaused($userId);
                $paused = true;
            }
            if ($paused) {
                echo json_encode([
                    'status'   => '2',
                    'locked'   => true,
                    'resume'   => $siteConfig->siteUrl . '/resume',
                    'redirect' => $siteConfig->siteUrl . '/resume',
                    'message'  => 'Session paused after inactivity. Please resume to continue.',
                ]);
                exit;
            }
        }
        \App\Helpers\Auth::touch();

        // Browser AJAX must echo back the csrf_token cookie (double-submit).
        // Token-based API routes use sessionAuthMiddleware and skip this.
        if (!\App\Helpers\Csrf::check()) {
            echo json_encode(['status' => '2', 'message' => 'Error: Invalid or missing CSRF token. Please refresh the page and try again.']);
            exit;
        }
    }

    /**
     * Require the logged-in user to have one of the given roles
     * (1 = Administrator). Responds with JSON for POST, redirect otherwise.
     * Returns the User on success so callers can reuse it.
     */
    public function requireRole($roles = [1])
    {
        global $siteConfig;
        $roles = (array) $roles;

        $id = \App\Helpers\Auth::id();
        $user = $id ? \App\Models\User::find($id) : null;

        if (!$user || !in_array((int) $user->role, array_map('intval', $roles), true)) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['status' => '2', 'message' => 'Error: You do not have permission to perform this action']);
            } else {
                header("Location: " . $siteConfig->siteUrl . "/dashboard");
            }
            exit;
        }

        return $user;
    }
    public function authMiddleware()
    {
        global $siteConfig;

        // Verify the signed auth cookie, not merely the presence of "user".
        if (!\App\Helpers\Auth::check()) {

            // Preserve the requested page as a same-site-only return path.
            \App\Helpers\AuthReturn::captureCurrentRequest();

            // Absolute URL — a relative "home" would resolve against the current
            // path (e.g. /view-car/home) and redirect-loop on expiry.
            header("Location: " . $siteConfig->siteUrl . "/login");
            exit;
        }

        // Authenticated but paused (or idle past the timeout, as a server-side
        // backstop for when the client idle timer never fired) → soft-lock to
        // the resume challenge rather than a full logout; the long-lived cookie
        // is still valid. The requested page is preserved as a same-site return.
        $userId = (int) \App\Helpers\Auth::id();
        $paused = \App\Helpers\PasswordResume::isPaused($userId);
        if (!$paused && \App\Helpers\Auth::isIdle()) {
            \App\Helpers\PasswordResume::markPaused($userId);
            $paused = true;
        }
        if ($paused) {
            $returnTo = \App\Helpers\AuthReturn::destination($_SERVER['REQUEST_URI'] ?? '/dashboard');
            header("Location: " . $siteConfig->siteUrl . "/resume?return=" . rawurlencode($returnTo));
            exit;
        }

        \App\Helpers\Auth::touch();
    }

    public function matchRoute()
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $url = strtok($_SERVER['REQUEST_URI'], '?'); // remove query string
        $url = rtrim($url, '/'); // remove trailing slash

        // Strip known environment folder prefixes (e.g. /trainit.co.zw on production cPanel, /trainit on local)
        $prefixes = [
            '/' . (defined('_PROD_HOST') ? _PROD_HOST : ''),
            '/www.' . (defined('_PROD_HOST') ? _PROD_HOST : ''),
            $this->basePath,
        ];
        foreach ($prefixes as $prefix) {
            if ($prefix !== '/' && $prefix !== '' && strpos($url, $prefix) === 0) {
                $url = substr($url, strlen($prefix));
                break;
            }
        }
        $url = rtrim($url, '/');


        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routeUrl => $target) {
                // Use named subpatterns in the regular expression pattern to capture each parameter value separately
                $pattern = preg_replace('/\/:([^\/]+)/', '/(?P<$1>[^/]+)', $routeUrl);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $url, $matches)) {
                    // Pass the captured parameter values as named arguments to the target function
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY); // Only keep named subpattern matches
                    // A route matched. Any error thrown by the handler is a
                    // real failure (500) — do NOT let it fall through and be
                    // reported as a 404, which hides the true cause.
                    try {
                        call_user_func_array($target, $params);
                    } catch (\Throwable $e) {
                        error_log('Handler error for ' . $method . ' ' . $url . ': ' . $e);
                        http_response_code(500);
                        if (defined('_ENV') && _ENV === 'development') {
                            echo 'Server error: ' . $e->getMessage();
                        } else {
                            echo view('errors.500', ['data' => ['title' => 'Error']]);
                        }
                    }
                    return;
                }
            }
        }
        $data = [
            "status" => "failed",
            "response_code" => "404",
            "message" => "Category not found"
        ];
        //return view('errors.404', compact('data'));

        throw new Exception('Route not found' . $url);
    }
}
