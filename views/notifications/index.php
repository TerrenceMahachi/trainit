@extends('layouts.main')

<?php
global $siteConfig;
$notifications = $data['notifications'] ?? [];
$unreadCount   = $data['unreadCount'] ?? 0;
$currentFilter = $data['currentFilter'] ?? 'all';
$user          = $data['user'] ?? null;
?>

<main class="portal-dashboard">
    <!-- Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="portal-kicker text-warning mb-1" style="font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-bell me-1"></i> User Communications &amp; Signals
                </p>
                <h1 class="h2 fw-bold text-white mb-2">Notification Center</h1>
                <p class="mb-0 text-white-50" style="max-width: 650px;">
                    Stay updated on application shortlisting, client service requests, team approvals, and compliance reminders.
                </p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-light rounded-pill px-3" onclick="markAllNotificationsRead()">
                    <i class="fa fa-check-double me-1"></i> Mark All as Read
                </button>
                <a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="btn btn-warning text-dark fw-bold rounded-pill px-3">
                    <i class="fa fa-gauge me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </section>

    <!-- Body -->
    <section class="py-4">
        <div class="container" style="max-width: 900px;">

            <!-- Filter Tabs & Stats -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="btn-group rounded-pill p-1 bg-light">
                        <a href="<?= $siteConfig->siteUrl; ?>/notifications?filter=all" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'all' ? 'btn-dark' : 'text-muted'; ?>">
                            All Notifications
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/notifications?filter=unread" 
                           class="btn btn-sm rounded-pill px-3 fw-semibold <?= $currentFilter === 'unread' ? 'btn-dark' : 'text-muted'; ?>">
                            Unread
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge bg-danger ms-1"><?= $unreadCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    <span class="text-muted small">
                        Showing <?= count($notifications); ?> notification(s)
                    </span>
                </div>
            </div>

            <!-- Notifications List -->
            <?php if (empty($notifications)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px;">
                        <i class="fa fa-bell-slash fa-2x text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">No Notifications</h5>
                    <p class="text-muted mb-0 small">
                        <?= $currentFilter === 'unread' ? 'You have no unread notifications right now.' : 'You have no notifications in your history yet.'; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="list-group shadow-sm rounded-4 border-0 overflow-hidden mb-4">
                    <?php foreach ($notifications as $n): 
                        $isUnread = empty($n->is_read);
                        $bgClass = $isUnread ? 'bg-light bg-opacity-75' : 'bg-white';
                        $iconColor = match($n->type) {
                            'success' => 'text-success bg-success bg-opacity-10',
                            'warning' => 'text-warning bg-warning bg-opacity-10',
                            'danger', 'alert' => 'text-danger bg-danger bg-opacity-10',
                            default => 'text-primary bg-primary bg-opacity-10',
                        };
                    ?>
                        <div class="list-group-item p-3 border-start-0 border-end-0 <?= $bgClass; ?> d-flex justify-content-between align-items-start gap-3 position-relative"
                             id="notification-item-<?= $n->iD; ?>">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 <?= $iconColor; ?>" style="width: 42px; height: 42px;">
                                    <i class="fa <?= htmlspecialchars($n->icon ?: 'fa-bell'); ?>"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="fw-bold mb-0 text-dark <?= $isUnread ? '' : 'text-opacity-75'; ?>">
                                            <?= htmlspecialchars($n->title); ?>
                                        </h6>
                                        <?php if ($isUnread): ?>
                                            <span class="badge bg-primary" style="font-size: 0.65rem;">NEW</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-muted small mb-2" style="line-height: 1.5;">
                                        <?= htmlspecialchars($n->message); ?>
                                    </p>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="text-muted" style="font-size: 0.75rem;">
                                            <i class="fa fa-clock me-1"></i><?= date('M j, Y \a\t g:i A', strtotime($n->reg_date)); ?>
                                        </span>
                                        <?php if (!empty($n->link)): ?>
                                            <a href="<?= htmlspecialchars($n->link); ?>" class="small fw-semibold text-primary text-decoration-none" onclick="markSingleNotificationRead(<?= $n->iD; ?>)">
                                                Open Details <i class="fa fa-arrow-right ms-1" style="font-size: 0.75rem;"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <?php if ($isUnread): ?>
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill" 
                                            title="Mark as read"
                                            onclick="markSingleNotificationRead(<?= $n->iD; ?>)">
                                        <i class="fa fa-check"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </section>
</main>

<script>
function markSingleNotificationRead(id) {
    const fd = new FormData();
    fd.append('id', id);
    fetch('<?= $siteConfig->siteUrl; ?>/api/notifications/mark-read', {
        method: 'POST',
        body: fd
    }).then(r => r.json()).then(res => {
        if (res.status === 'success') {
            const el = document.getElementById('notification-item-' + id);
            if (el) {
                el.classList.remove('bg-light', 'bg-opacity-75');
                el.classList.add('bg-white');
                const badge = el.querySelector('.badge.bg-primary');
                if (badge) badge.remove();
            }
            if (typeof updateNavNotificationBadge === 'function') {
                updateNavNotificationBadge(res.unread_count);
            }
        }
    });
}

function markAllNotificationsRead() {
    const fd = new FormData();
    fd.append('all', 1);
    fetch('<?= $siteConfig->siteUrl; ?>/api/notifications/mark-read', {
        method: 'POST',
        body: fd
    }).then(r => r.json()).then(res => {
        if (res.status === 'success') {
            window.location.reload();
        }
    });
}
</script>
