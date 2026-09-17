<?php

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\NotificationHelper;
use App\Models\UserNotification;

global $router, $siteConfig;

// Full Notification Center Page
$router->addRoute('GET', '/notifications', function () use ($router, $siteConfig) {
    if (!Auth::check()) {
        header("Location: " . $siteConfig->siteUrl . "/login?return=/notifications");
        exit;
    }

    $currentUser = Auth::user();
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = 20;
    $offset = ($page - 1) * $limit;
    $filter = $_GET['filter'] ?? 'all';

    $where = "user = ? AND status = 1";
    $params = [$currentUser->iD];

    if ($filter === 'unread') {
        $where .= " AND (is_read = 0 OR is_read IS NULL)";
    }

    $notifications = UserNotification::findByQuery(
        "SELECT * FROM user_notification WHERE {$where} ORDER BY iD DESC LIMIT {$limit} OFFSET {$offset}",
        $params
    );

    $unreadCount = NotificationHelper::getUnreadCount((int)$currentUser->iD);

    $data = [
        'title' => 'Notification Center',
        'user' => $currentUser,
        'notifications' => $notifications,
        'unreadCount' => $unreadCount,
        'currentFilter' => $filter,
        'page' => $page
    ];

    echo view('notifications.index', compact('data'));
    exit;
});

// JSON API: Get unread count + recent notifications for nav bell dropdown
$router->addRoute('GET', '/api/notifications/recent', function () {
    header('Content-Type: application/json');

    if (!Auth::check()) {
        echo json_encode(['status' => 'unauthenticated', 'unread_count' => 0, 'notifications' => []]);
        exit;
    }

    $userId = (int)Auth::id();
    $unreadCount = NotificationHelper::getUnreadCount($userId);
    $recent = NotificationHelper::getRecent($userId, 7);

    $formatted = array_map(function ($n) {
        $timeAgo = 'Just now';
        if (!empty($n->reg_date)) {
            $ts = strtotime($n->reg_date);
            $diff = time() - $ts;
            if ($diff < 60) {
                $timeAgo = 'Just now';
            } elseif ($diff < 3600) {
                $timeAgo = floor($diff / 60) . 'm ago';
            } elseif ($diff < 86400) {
                $timeAgo = floor($diff / 3600) . 'h ago';
            } else {
                $timeAgo = floor($diff / 86400) . 'd ago';
            }
        }

        return [
            'id' => (int)$n->iD,
            'title' => htmlspecialchars($n->title),
            'message' => htmlspecialchars($n->message),
            'link' => $n->link,
            'type' => $n->type,
            'icon' => $n->icon ?: 'fa-bell',
            'is_read' => (bool)$n->is_read,
            'time_ago' => $timeAgo
        ];
    }, $recent);

    echo json_encode([
        'status' => 'success',
        'unread_count' => $unreadCount,
        'notifications' => $formatted
    ]);
    exit;
});

// JSON API: Mark single or all as read
$router->addRoute('POST', '/api/notifications/mark-read', function () {
    header('Content-Type: application/json');

    if (!Auth::check()) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    $userId = (int)Auth::id();
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $all = !empty($_POST['all']);

    if ($all) {
        NotificationHelper::markAllAsRead($userId);
        echo json_encode(['status' => 'success', 'message' => 'All notifications marked as read', 'unread_count' => 0]);
        exit;
    }

    if ($id > 0) {
        NotificationHelper::markAsRead($id, $userId);
        $newUnread = NotificationHelper::getUnreadCount($userId);
        echo json_encode(['status' => 'success', 'message' => 'Notification marked as read', 'unread_count' => $newUnread]);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    exit;
});
