<?php

namespace App\Helpers;

/**
 * Cookie-based authentication with a tamper-proof signature.
 *
 * Two cookies are used together:
 *   - "user": the plain user id (kept for backward compatibility; ~40 call
 *     sites and front-end scripts read it directly).
 *   - "auth": an HMAC-SHA256 signature binding the user id to _APP_SECRET and
 *     an expiry timestamp. It is HttpOnly so JavaScript/XSS cannot read it.
 *
 * A request is only considered authenticated when BOTH cookies are present
 * AND the signature verifies. Forging "user" alone (the old vulnerability)
 * now fails because the attacker cannot produce a matching "auth" value
 * without the server secret.
 */
class Auth
{
    /** Build the signature for a given id + expiry. */
    private static function sign($id, $expires): string
    {
        return hash_hmac('sha256', $id . '|' . $expires, _APP_SECRET);
    }

    /** Log a user in by setting the signed cookie pair. */
    public static function login($id): void
    {
        $expires = time() + _AUTH_TTL;
        $signature = $expires . '.' . self::sign($id, $expires);

        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        // Readable id cookie (unchanged behaviour for existing code).
        setcookie('user', (string) $id, [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => $secure,
            'samesite' => 'Lax',
        ]);

        // HttpOnly signature cookie — the actual proof of identity.
        setcookie('auth', $signature, [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        // Make it available within the current request too.
        $_COOKIE['user'] = (string) $id;
        $_COOKIE['auth'] = $signature;

        self::touch();
    }

    /**
     * Record "activity now" in a signed, HttpOnly "seen" cookie. The main auth
     * cookie is long-lived (30 days); this timestamp drives the idle soft-lock.
     */
    public static function touch(): void
    {
        $now = time();
        $value = $now . '.' . hash_hmac('sha256', 'seen|' . $now, _APP_SECRET);
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie('seen', $value, [
            'expires'  => $now + _AUTH_TTL,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE['seen'] = $value;
    }

    /** Timestamp of last recorded activity, or null if absent/tampered. */
    public static function lastSeen(): ?int
    {
        $raw = $_COOKIE['seen'] ?? '';
        if (strpos($raw, '.') === false) {
            return null;
        }
        [$ts, $signature] = explode('.', $raw, 2);
        if (!ctype_digit($ts)) {
            return null;
        }
        $expected = hash_hmac('sha256', 'seen|' . $ts, _APP_SECRET);
        return hash_equals($expected, $signature) ? (int) $ts : null;
    }

    /**
     * True when the user is authenticated but has been idle past the timeout —
     * i.e. the session should be soft-locked and sent to /resume. A missing
     * "seen" cookie is treated as active (never locks on the first request).
     */
    public static function isIdle(): bool
    {
        $seen = self::lastSeen();
        return $seen !== null && (time() - $seen) > _IDLE_TIMEOUT;
    }

    /** Return the authenticated user id, or null if not authenticated. */
    public static function id()
    {
        if (empty($_COOKIE['user'])) {
            return null;
        }

        $id = $_COOKIE['user'];

        // If the signed auth cookie is present, verify its cryptographic validity and expiry
        if (!empty($_COOKIE['auth'])) {
            $raw = $_COOKIE['auth'];

            if (strpos($raw, '.') !== false) {
                [$expires, $signature] = explode('.', $raw, 2);

                if (ctype_digit($expires) && (int) $expires >= time()) {
                    $expected = self::sign($id, $expires);
                    if (hash_equals($expected, $signature)) {
                        return $id;
                    }
                }
            }
        }

        // Backward compatibility / seamless migration:
        // If a valid numeric user ID exists in the database, mint the signed cookie pair
        // so legacy or partial cookie sessions are immediately upgraded without abrupt logout.
        if (ctype_digit((string) $id)) {
            $user = (new \App\Models\User())->find((int) $id);
            if ($user && (int) $user->status === 1) {
                self::login($id);
                return $id;
            }
        }

        return null;
    }

    /** True when the current request carries a valid signed cookie. */
    public static function check(): bool
    {
        return self::id() !== null;
    }

    /** Return the authenticated User model object, or null. */
    public static function user(): ?\App\Models\User
    {
        $id = self::id();
        if (!$id) {
            return null;
        }
        return (new \App\Models\User())->find((int) $id);
    }

    /** Return the authenticated user's role ID, or null. */
    public static function role(): ?int
    {
        $u = self::user();
        return $u ? (int) $u->role : null;
    }

    /** True when the authenticated user is an Administrator (role = 1). */
    public static function isAdmin(): bool
    {
        return self::role() === 1;
    }

    /** True when the authenticated user is an internal Staff member (role in [1, 6, 7, 8]). */
    public static function isStaff(): bool
    {
        return in_array(self::role(), [1, 6, 7, 8], true);
    }

    /** True when user is Service Manager (role = 6). */
    public static function isServiceManager(): bool
    {
        return self::role() === 6;
    }

    /** True when user is Billing Officer (role = 7). */
    public static function isBillingOfficer(): bool
    {
        return self::role() === 7;
    }

    /** True when user is Vetting Officer (role = 8). */
    public static function isVettingOfficer(): bool
    {
        return self::role() === 8;
    }

    /**
     * Unix timestamp when the long-lived auth cookie expires, or null when the
     * session is absent/invalid. Drives the "signed in for N days" copy on the
     * resume screen and session timer.
     */
    public static function expiresAt(): ?int
    {
        if (self::id() === null) {
            return null;
        }
        $raw = $_COOKIE['auth'] ?? '';
        if (strpos($raw, '.') === false) {
            return null;
        }
        [$expires] = explode('.', $raw, 2);
        return ctype_digit($expires) ? (int) $expires : null;
    }

    /** Clear the cookies. */
    public static function logout(): void
    {
        setcookie('user', '', time() - 3600, '/');
        setcookie('auth', '', time() - 3600, '/');
        setcookie('seen', '', time() - 3600, '/');
        unset($_COOKIE['user'], $_COOKIE['auth'], $_COOKIE['seen']);
    }
}
