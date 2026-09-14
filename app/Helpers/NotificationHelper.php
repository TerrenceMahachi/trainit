<?php

namespace App\Helpers;

use App\Models\Database;
use App\Models\User;
use App\Models\UserNotification;
use PDO;

class NotificationHelper
{
    /**
     * Dispatch an in-app notification to a specific user.
     */
    public static function notify(
        int $userId,
        string $title,
        string $message,
        ?string $link = null,
        string $type = 'info',
        string $icon = 'fa-bell'
    ): ?UserNotification {
        if ($userId <= 0) {
            return null;
        }

        try {
            $n = new UserNotification();
            $n->user = $userId;
            $n->title = trim($title);
            $n->message = trim($message);
            $n->link = $link ? trim($link) : null;
            $n->type = $type;
            $n->icon = $icon;
            $n->is_read = 0;
            $n->reg_by = Auth::id() ?? 1;
            $n->status = 1;
            $n->save();

            return $n;
        } catch (\Throwable $e) {
            error_log("Failed to create notification: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Dispatch notification to all active system administrators (Role 1).
     */
    public static function notifyAdmins(
        string $title,
        string $message,
        ?string $link = null,
        string $type = 'info',
        string $icon = 'fa-shield-alt'
    ): int {
        return self::notifyRole(1, $title, $message, $link, $type, $icon);
    }

    /**
     * Dispatch notification to all active users with a specified role.
     */
    public static function notifyRole(
        int $roleId,
        string $title,
        string $message,
        ?string $link = null,
        string $type = 'info',
        string $icon = 'fa-bell'
    ): int {
        try {
            $users = User::where('role', $roleId);
            $count = 0;
            foreach ($users as $u) {
                if ((int)$u->status === 1) {
                    self::notify((int)$u->iD, $title, $message, $link, $type, $icon);
                    $count++;
                }
            }
            return $count;
        } catch (\Throwable $e) {
            error_log("Failed to notify role {$roleId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get unread notification count for a user.
     */
    public static function getUnreadCount(int $userId): int
    {
        if ($userId <= 0) {
            return 0;
        }
        $pdo = Database::sharedPdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_notification WHERE user = ? AND (is_read = 0 OR is_read IS NULL) AND status = 1");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get recent notifications for a user.
     */
    public static function getRecent(int $userId, int $limit = 5): array
    {
        if ($userId <= 0) {
            return [];
        }
        $limit = max(1, min(50, $limit));
        return UserNotification::findByQuery(
            "SELECT * FROM user_notification WHERE user = ? AND status = 1 ORDER BY iD DESC LIMIT {$limit}",
            [$userId]
        );
    }

    /**
     * Mark a specific notification as read.
     */
    public static function markAsRead(int $notificationId, int $userId): bool
    {
        $pdo = Database::sharedPdo();
        $stmt = $pdo->prepare("UPDATE user_notification SET is_read = 1, read_at = CURRENT_TIMESTAMP WHERE iD = ? AND user = ?");
        return $stmt->execute([$notificationId, $userId]);
    }

    /**
     * Mark all unread notifications as read for a user.
     */
    public static function markAllAsRead(int $userId): bool
    {
        $pdo = Database::sharedPdo();
        $stmt = $pdo->prepare("UPDATE user_notification SET is_read = 1, read_at = CURRENT_TIMESTAMP WHERE user = ? AND (is_read = 0 OR is_read IS NULL)");
        return $stmt->execute([$userId]);
    }
}
