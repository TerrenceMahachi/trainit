<?php

namespace App\Helpers;

use App\Models\User;

class ViewAccess
{
    protected static $rolesByView = [];

    public static function allow(array $roles = []): void
    {
        $view = self::callingView();
        if ($view !== null) {
            self::$rolesByView[$view] = $roles;
        }
        self::enforce($roles);
    }

    public static function enforceForView(string $view): void
    {
        self::enforce(self::rolesForView($view));
    }

    protected static function callingView(): ?string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $file = $trace[1]['file'] ?? null;
        if (!$file) {
            return null;
        }
        return basename(dirname($file));
    }

    protected static function rolesForView(string $view): array
    {
        if (array_key_exists($view, self::$rolesByView)) {
            return self::$rolesByView[$view];
        }

        $header = _VIEWS_PATH . '/' . $view . '/header.php';
        if (!is_file($header)) {
            return [];
        }

        $content = file_get_contents($header);
        if (!preg_match('/^[ \t]*\\\\?App\\\\Helpers\\\\ViewAccess::allow\(\s*\[([^\]]*)\]\s*\)/m', $content, $matches)) {
            return [];
        }

        $roles = [];
        if (preg_match_all('/[\'"]([^\'"]+)[\'"]|(\d+)/', $matches[1], $roleMatches, PREG_SET_ORDER)) {
            foreach ($roleMatches as $role) {
                $roles[] = $role[1] !== '' ? $role[1] : (int) $role[2];
            }
        }

        self::$rolesByView[$view] = $roles;
        return $roles;
    }

    protected static function enforce(array $roles): void
    {
        if (empty($roles)) {
            return;
        }

        $id = Auth::id();
        $user = $id ? User::find($id) : null;

        if (!$user || !self::matches($user, $roles)) {
            self::deny();
        }
    }

    protected static function matches(User $user, array $roles): bool
    {
        $roleId = (int) $user->role;
        $roleName = strtolower(trim((string) ($user->role()->name ?? '')));

        foreach ($roles as $role) {
            if (is_numeric($role) && $roleId === (int) $role) {
                return true;
            }
            if (!is_numeric($role) && strtolower(trim((string) $role)) === $roleName) {
                return true;
            }
        }

        return false;
    }

    protected static function deny(): void
    {
        global $siteConfig;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode(['status' => '2', 'message' => 'Error: You do not have permission to access this section']);
        } else {
            header("Location: " . $siteConfig->siteUrl . "/dashboard");
        }
        exit;
    }
}
