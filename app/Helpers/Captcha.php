<?php

namespace App\Helpers;

/**
 * Server-side "not a robot" math CAPTCHA.
 *
 * The server generates the question, renders it into the page, and stores the
 * expected answer in an HMAC-signed, HttpOnly cookie:
 *
 *     captcha = <expires>.<HMAC(answer|expires, _APP_SECRET)>
 *
 * The plaintext answer is NEVER sent to the client, and the signature cannot
 * be forged or reversed without the server secret. Verification recomputes the
 * HMAC from the SUBMITTED answer and compares — so the only way to pass is to
 * read the rendered question and submit the correct answer.
 *
 * This is a basic bot deterrent (the challenge is plain text, so a determined
 * bot can still parse it). For strong protection use a dedicated service such
 * as hCaptcha or Cloudflare Turnstile.
 */
class Captcha
{
    private const TTL = 600; // 10 minutes

    private static function sign(string $answer, string $expires): string
    {
        return hash_hmac('sha256', $answer . '|' . $expires, _APP_SECRET);
    }

    private static function setCookie(string $value, int $expires): void
    {
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie('captcha', $value, [
            'expires'  => $expires,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE['captcha'] = $value;
    }

    /**
     * Generate a new challenge, set the signed cookie, and return the question
     * string to render (e.g. "7 + 2").
     */
    public static function issue(): string
    {
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        $op = random_int(0, 1) ? '+' : '-';
        if ($op === '-' && $b > $a) {
            [$a, $b] = [$b, $a]; // keep the answer non-negative
        }
        $answer = $op === '+' ? $a + $b : $a - $b;

        $expires = time() + self::TTL;
        self::setCookie($expires . '.' . self::sign((string) $answer, (string) $expires), $expires);

        return "$a $op $b";
    }

    /** True when $submitted matches the answer bound in the signed cookie. */
    public static function verify($submitted): bool
    {
        $cookie = $_COOKIE['captcha'] ?? '';
        if ($cookie === '' || strpos($cookie, '.') === false) {
            return false;
        }
        [$expires, $sig] = explode('.', $cookie, 2);
        if (!ctype_digit($expires) || (int) $expires < time()) {
            return false;
        }

        $submitted = trim((string) $submitted);
        if ($submitted === '' || !preg_match('/^-?\d+$/', $submitted)) {
            return false;
        }

        return hash_equals(self::sign($submitted, $expires), $sig);
    }

    /** Invalidate the current challenge (call after a successful submission). */
    public static function clear(): void
    {
        setcookie('captcha', '', time() - 3600, '/');
        unset($_COOKIE['captcha']);
    }
}
