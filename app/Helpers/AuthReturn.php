<?php

namespace App\Helpers;

/**
 * Preserves the internal page a guest originally requested across login or
 * registration. Only same-site paths are accepted so this cannot become an
 * open redirect to another website.
 */
class AuthReturn
{
    private const COOKIE = 'auth_return_to';
    private const TTL = 1800;

    /** Store the current GET request as the post-auth destination. */
    public static function captureCurrentRequest(): void
    {
        if (strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
            return;
        }

        $target = self::normalize($_SERVER['REQUEST_URI'] ?? '/');
        if ($target === null || self::isAuthPath($target)) {
            return;
        }

        self::setCookie($target, time() + self::TTL);
        $_COOKIE[self::COOKIE] = $target;

        // Remove the old absolute redirect cookie, which was long-lived and
        // could redirect to an external host if modified by the browser.
        setcookie('redirect', '', time() - 3600, '/');
        unset($_COOKIE['redirect']);
    }

    /** Return and clear the stored destination, or the supplied safe default. */
    public static function consume(string $default = '/dashboard'): string
    {
        $target = self::normalize($_COOKIE[self::COOKIE] ?? '');
        self::clear();

        return ($target !== null && !self::isAuthPath($target))
            ? $target
            : $default;
    }

    /** Validate a supplied destination without reading or changing cookies. */
    public static function destination(string $value, string $default = '/dashboard'): string
    {
        $target = self::normalize($value);
        return ($target !== null && !self::isAuthPath($target)) ? $target : $default;
    }

    /** Store an already validated explicit destination. */
    public static function remember(string $value): void
    {
        $target = self::destination($value, '/dashboard');
        self::setCookie($target, time() + self::TTL);
        $_COOKIE[self::COOKIE] = $target;
    }

    public static function clear(): void
    {
        self::setCookie('', time() - 3600);
        setcookie('redirect', '', time() - 3600, '/');
        unset($_COOKIE[self::COOKIE], $_COOKIE['redirect']);
    }

    /**
     * Convert an app request URI into a safe path relative to siteUrl.
     * Public so the security-sensitive normalization can be unit tested.
     */
    public static function normalize(string $value): ?string
    {
        $value = trim($value);
        if ($value === '' || preg_match('/[\x00-\x1F\x7F]/', $value)) {
            return null;
        }

        $parts = parse_url($value);
        if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || isset($parts['user'])) {
            return null;
        }

        $path = $parts['path'] ?? '';
        if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//') || str_contains($path, '\\')) {
            return null;
        }

        // Local installs are served under /<sitename>; production is served
        // at the domain root. Store one consistent site-relative path.
        $basePath = '/' . trim((string) _SITENAME, '/');
        if ($path === $basePath) {
            $path = '/';
        } elseif ($basePath !== '/' && str_starts_with($path, $basePath . '/')) {
            $path = substr($path, strlen($basePath));
        }

        $query = isset($parts['query']) && $parts['query'] !== '' ? '?' . $parts['query'] : '';
        $target = $path . $query;

        return strlen($target) <= 2048 ? $target : null;
    }

    private static function isAuthPath(string $target): bool
    {
        $path = parse_url($target, PHP_URL_PATH) ?: '';
        return in_array($path, [
            '/login',
            '/login-light',
            '/register',
            '/register-light',
            '/resume',
            '/logout',
            '/validate-login',
            '/sign-in',
        ], true);
    }

    private static function setCookie(string $value, int $expires): void
    {
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie(self::COOKIE, $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
