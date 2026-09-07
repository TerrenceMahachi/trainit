<?php

namespace App\Helpers;

use App\Models\Database;

/** Password-suffix enrollment and rate-limited resume verification. */
class PasswordResume
{
    private const PAUSE_COOKIE = 'session_paused';
    private const MAX_ATTEMPTS = 5;
    private const LOCK_MINUTES = 15;
    private static bool $schemaReady = false;

    public static function ensureSchema(): void
    {
        if (self::$schemaReady) { return; }

        $pdo = Database::sharedPdo();
        $columns = $pdo->query('PRAGMA table_info(user_login)')->fetchAll();
        $names = array_column($columns, 'name');
        $additions = [
            'resume_tail_hash' => 'TEXT',
            'resume_failed' => 'INTEGER NOT NULL DEFAULT 0',
            'resume_locked_until' => 'DATETIME',
        ];
        foreach ($additions as $name => $definition) {
            if (!in_array($name, $names, true)) {
                $pdo->exec("ALTER TABLE user_login ADD COLUMN {$name} {$definition}");
            }
        }
        self::$schemaReady = true;
    }

    /** Enroll or replace the suffix fingerprint after a full password check. */
    public static function enroll(int $userId, string $password): void
    {
        self::ensureSchema();
        $tail = self::lastThree($password);
        if (self::length($tail) !== 3) { return; }

        Database::sharedPdo()->prepare(
            'UPDATE user_login SET resume_tail_hash = ?, resume_failed = 0, resume_locked_until = NULL WHERE user = ?'
        )->execute([self::fingerprint($userId, $tail), $userId]);
    }

    public static function isEnrolled(int $userId): bool
    {
        self::ensureSchema();
        $stmt = Database::sharedPdo()->prepare('SELECT resume_tail_hash FROM user_login WHERE user = ? LIMIT 1');
        $stmt->execute([$userId]);
        return (string) ($stmt->fetchColumn() ?: '') !== '';
    }

    /** @return array{ok:bool, code:string, message:string} */
    public static function verify(int $userId, string $supplied): array
    {
        self::ensureSchema();
        $pdo = Database::sharedPdo();
        $stmt = $pdo->prepare(
            'SELECT iD, resume_tail_hash, resume_failed, resume_locked_until FROM user_login WHERE user = ? LIMIT 1'
        );
        $stmt->execute([$userId]);
        $login = $stmt->fetch();

        if (!$login || empty($login['resume_tail_hash'])) {
            return ['ok' => false, 'code' => 'setup_required', 'message' => 'A full sign-in is required once to enable secure resume.'];
        }
        if (!empty($login['resume_locked_until']) && strtotime($login['resume_locked_until']) > time()) {
            return ['ok' => false, 'code' => 'locked', 'message' => 'Too many incorrect attempts. Please sign in with your full password.'];
        }
        if (self::length($supplied) !== 3) {
            return ['ok' => false, 'code' => 'invalid', 'message' => 'Enter exactly the last three characters of your password.'];
        }

        if (hash_equals($login['resume_tail_hash'], self::fingerprint($userId, $supplied))) {
            $pdo->prepare('UPDATE user_login SET resume_failed = 0, resume_locked_until = NULL WHERE iD = ?')
                ->execute([(int) $login['iD']]);
            return ['ok' => true, 'code' => 'ok', 'message' => 'Session resumed.'];
        }

        $failed = (int) $login['resume_failed'] + 1;
        $lockedUntil = $failed >= self::MAX_ATTEMPTS
            ? date('Y-m-d H:i:s', time() + self::LOCK_MINUTES * 60)
            : null;
        $pdo->prepare('UPDATE user_login SET resume_failed = ?, resume_locked_until = ? WHERE iD = ?')
            ->execute([$failed, $lockedUntil, (int) $login['iD']]);

        if ($lockedUntil !== null) {
            return ['ok' => false, 'code' => 'locked', 'message' => 'Too many incorrect attempts. Please sign in with your full password.'];
        }
        $remaining = self::MAX_ATTEMPTS - $failed;
        return [
            'ok' => false,
            'code' => 'invalid',
            'message' => 'Those characters do not match. ' . $remaining . ' attempt' . ($remaining === 1 ? '' : 's') . ' remaining.',
        ];
    }

    /** Mark this authenticated browser as paused until the challenge passes. */
    public static function markPaused(int $userId): void
    {
        $value = $userId . '.' . hash_hmac('sha256', 'paused|' . $userId, _APP_SECRET);
        self::setPauseCookie($value, time() + _AUTH_TTL);
        $_COOKIE[self::PAUSE_COOKIE] = $value;
    }

    public static function isPaused(int $userId): bool
    {
        $value = $_COOKIE[self::PAUSE_COOKIE] ?? '';
        if (!str_contains($value, '.')) { return false; }
        [$cookieUser, $signature] = explode('.', $value, 2);
        if (!ctype_digit($cookieUser) || (int) $cookieUser !== $userId) { return false; }
        $expected = hash_hmac('sha256', 'paused|' . $userId, _APP_SECRET);
        return hash_equals($expected, $signature);
    }

    public static function clearPause(): void
    {
        self::setPauseCookie('', time() - 3600);
        unset($_COOKIE[self::PAUSE_COOKIE]);
    }

    private static function fingerprint(int $userId, string $tail): string
    {
        return hash_hmac('sha256', 'resume|' . $userId . '|' . $tail, _APP_SECRET);
    }

    private static function lastThree(string $password): string
    {
        return function_exists('mb_substr') ? mb_substr($password, -3) : substr($password, -3);
    }

    private static function length(string $value): int
    {
        return function_exists('mb_strlen') ? mb_strlen($value) : strlen($value);
    }

    private static function setPauseCookie(string $value, int $expires): void
    {
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie(self::PAUSE_COOKIE, $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
