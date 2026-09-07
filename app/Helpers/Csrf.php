<?php

namespace App\Helpers;

/**
 * CSRF protection using the double-submit cookie pattern.
 *
 * A random token is set in a `csrf_token` cookie (readable by JS on purpose —
 * that is how double-submit works). Browser JS sends it back on every AJAX
 * POST as the `X-CSRF-Token` header (wired globally in assets/scripts/common.js);
 * plain HTML forms include it via Csrf::field(). The server accepts a
 * state-changing request only when the submitted value matches the cookie.
 *
 * A cross-site attacker can make the browser SEND our cookie but cannot READ
 * it, so they cannot supply a matching header/field.
 *
 * Note: the token-based API routes (sessionAuthMiddleware) don't need this —
 * they authenticate via a bearer-style session token, not cookies.
 */
class Csrf
{
    /** Return the current token, creating the cookie if needed. */
    public static function token(): string
    {
        if (!empty($_COOKIE['csrf_token']) && preg_match('/^[a-f0-9]{32,64}$/', $_COOKIE['csrf_token'])) {
            return $_COOKIE['csrf_token'];
        }

        $token = bin2hex(random_bytes(16));
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie('csrf_token', $token, [
            'expires'  => 0,          // session cookie
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => false,      // JS must read it — that's the pattern
            'samesite' => 'Lax',
        ]);
        $_COOKIE['csrf_token'] = $token;
        return $token;
    }

    /** Hidden input for plain HTML forms. */
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    /** True when the request carries a token matching the cookie. */
    public static function check(): bool
    {
        $cookie = $_COOKIE['csrf_token'] ?? '';
        if ($cookie === '') {
            return false;
        }
        $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
        return $sent !== '' && hash_equals($cookie, $sent);
    }
}
