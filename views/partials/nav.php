<?php
/**
 * Main navigation bar. Rendered from config/nav.php — do not hardcode menu
 * items here. Expects $siteConfig and (optionally) $data['user'] in scope.
 */
global $siteConfig;
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';

$navUser = \App\Helpers\Auth::user() ?? ($data['user'] ?? null);
if (!$navUser && !empty($_COOKIE['user'])) {
    $navUser = (new \App\Controllers\AccountController())->getUser($_COOKIE['user']);
}
$userRoleTags = ['guest'];
if ($navUser) {
    $roleId = (int) $navUser->role;
    $userRoleTags = ['authenticated'];
    
    if ($roleId === 1) {
        $userRoleTags[] = 'admin';
        $userRoleTags[] = 'staff';
    } elseif ($roleId === 8) {
        $userRoleTags[] = 'vetting';
        $userRoleTags[] = 'staff';
    } elseif ($roleId === 6) {
        $userRoleTags[] = 'manager';
        $userRoleTags[] = 'staff';
    } elseif ($roleId === 7) {
        $userRoleTags[] = 'finance';
        $userRoleTags[] = 'staff';
    } elseif ($roleId === 3) {
        $userRoleTags[] = 'client';
    } else {
        // Roles 2, 4, 5 (General User, Associate, Apprentice)
        $userRoleTags[] = 'user';
        $userRoleTags[] = 'candidate';
    }
    $userRoleTags[] = 'role_' . $roleId;
    $userRoleTags[] = (string) $roleId;
}

$navItems = require _BASE_PATH . '/config/nav.php';

/** Should the current user see this item? */
$navVisible = function ($item) use ($userRoleTags) {
    if (empty($item['roles'])) {
        return true;
    }
    $allowed = (array) $item['roles'];
    return !empty(array_intersect($userRoleTags, $allowed));
};
/** Is this item the active page? */
$navActive = function ($item) use ($currentUrl) {
    if (isset($item['url']) && (strpos($item['url'], '://') !== false || strpos($item['url'], '//') === 0)) {
        return '';
    }
    $needle = $item['match'] ?? ($item['url'] ?? null);
    return $needle && strpos($currentUrl, $needle) !== false ? 'active' : '';
};

/** Format navigation URL (supports absolute external URLs like the main website) */
$formatUrl = function ($url) use ($siteConfig) {
    if (!$url || $url === '#') {
        return '#';
    }
    if (strpos($url, '://') !== false || strpos($url, '//') === 0) {
        return $url;
    }
    return $siteConfig->siteUrl . $url;
};

$brandHref = $navUser ? ($siteConfig->siteUrl . '/dashboard') : (defined('_WEBSITE_URL') ? _WEBSITE_URL : 'https://tsigiro.co.zw');
?>
<nav class="navbar navbar-expand-lg trainit-navbar">
    <div class="container-xl">
        <a class="navbar-brand trainit-brand" href="<?= $brandHref ?>">
            <img src="<?= $siteConfig->assetsUrl ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                 alt="<?= htmlspecialchars(_SITE) ?>"
                 class="trainit-brand-logo"
                 width="40" height="40"
                 style="object-fit: contain; flex-shrink: 0;">
            <span class="trainit-brand-name"><?= htmlspecialchars(_SITE) ?></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach ($navItems as $item): ?>
                    <?php if (!$navVisible($item)) continue; ?>

                    <?php if (!empty($item['children'])): ?>
                        <?php $kids = array_filter($item['children'], $navVisible); ?>
                        <?php if (!$kids) continue; ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= $navActive($item) ?>" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false"><?= htmlspecialchars($item['label']) ?></a>
                            <ul class="dropdown-menu dropdown-menu-start">
                                <?php foreach ($kids as $child): ?>
                                    <li><a class="dropdown-item" href="<?= $formatUrl($child['url'] ?? '#') ?>"><?= htmlspecialchars($child['label']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $navActive($item) ?>" href="<?= $formatUrl($item['url'] ?? '#') ?>"><?= htmlspecialchars($item['label']) ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <div class="navbar-nav d-flex flex-row align-items-center gap-2" id="user_logged_in">
                <?php if ($navUser): 
                    $navUnread = \App\Helpers\NotificationHelper::getUnreadCount((int)$navUser->iD);
                ?>
                    <!-- In-App Notification Bell -->
                    <div class="dropdown position-relative" id="nav_notification_center">
                        <button class="btn btn-light position-relative rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm"
                                type="button" id="notificationBellDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                style="width: 38px; height: 38px; background: #fff; border: 1px solid #e2e8f0;"
                                title="Notifications"
                                onclick="loadNavNotifications()">
                            <i class="fa fa-bell text-secondary"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light <?= $navUnread > 0 ? '' : 'd-none'; ?>" 
                                  id="navNotificationBadge" style="font-size: 0.65rem; padding: 0.25em 0.45em;">
                                <?= $navUnread; ?>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-0 mt-2" 
                             aria-labelledby="notificationBellDropdown" 
                             style="width: 340px; max-width: 90vw; overflow: hidden; z-index: 1060;">
                            <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0 text-dark small"><i class="fa fa-bell me-1 text-primary"></i> Notifications</h6>
                                <button class="btn btn-link btn-sm text-decoration-none text-muted p-0 small" onclick="markAllNotificationsReadNav(event)">
                                    Mark all read
                                </button>
                            </div>
                            <div class="p-0" id="navNotificationList" style="max-height: 340px; overflow-y: auto;">
                                <div class="text-center py-4 text-muted small">
                                    <i class="fa fa-spinner fa-spin me-1"></i> Loading...
                                </div>
                            </div>
                            <div class="p-2 border-top bg-light text-center">
                                <a href="<?= $siteConfig->siteUrl; ?>/notifications" class="text-decoration-none small fw-semibold text-primary">
                                    View All Notifications <i class="fa fa-arrow-right ms-1" style="font-size: 0.75rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="dropdown">
                    <button class="btn dropdown-toggle border rounded-pill py-1 my-1 pt-1 px-3 trainit-account-button"
                        type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="mr-3"><i class="fa fa-circle-user mr-2"></i>
                            <?= $navUser ? htmlspecialchars($navUser->name) : 'Client portal' ?></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end border-0 rounded-3 text-center" style="max-width:500px">
                        <ul class="list-group m-2" aria-labelledby="dropdownMenuButton1">
                            <?php if ($navUser): ?>
                                <li class="list-group-item border-0 text-black-50 fw-bold"><?= htmlspecialchars($navUser->role()->name) ?></li>
                                <li class="list-group-item border-0"><a class="text-secondary fw-semibold" href="<?= $siteConfig->siteUrl ?>/notifications"><i class="fa fa-bell me-1"></i> Notification Center</a></li>
                                <?php $rId = (int)$navUser->role; ?>
                                <?php if ($rId === 1): ?>
                                    <li class="list-group-item border-0"><a class="text-primary fw-bold" href="<?= $siteConfig->siteUrl ?>/dashboard"><i class="fa fa-tachometer-alt me-1"></i> Admin Command Center</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark fw-bold" href="<?= $siteConfig->siteUrl ?>/admin/staff"><i class="fa fa-id-badge me-1"></i> Staff Directory</a></li>
                                    <li class="list-group-item border-0"><a class="text-danger fw-bold" href="<?= $siteConfig->siteUrl ?>/admin/staff-approvals"><i class="fa fa-shield-alt me-1"></i> Approvals Queue</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/clients"><i class="fa fa-building me-1"></i> Client Accounts</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/payroll"><i class="fa fa-money-check-dollar me-1"></i> Staff Payroll</a></li>
                                    <li class="list-group-item border-0"><a class="text-secondary" href="<?= $siteConfig->siteUrl ?>/staff/portal"><i class="fa fa-user-circle me-1"></i> Staff Self-Service</a></li>
                                <?php elseif ($rId === 8): ?>
                                    <li class="list-group-item border-0"><a class="text-primary fw-bold" href="<?= $siteConfig->siteUrl ?>/dashboard"><i class="fa fa-gavel me-1"></i> Vetting Dashboard</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark fw-bold" href="<?= $siteConfig->siteUrl ?>/admin/roster"><i class="fa fa-clipboard-check me-1"></i> Talent Pipeline</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/compliance"><i class="fa fa-shield-halved me-1 text-warning"></i> Compliance Radar</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/vacancies"><i class="fa fa-briefcase me-1"></i> Vacancies Console</a></li>
                                    <li class="list-group-item border-0"><a class="text-secondary" href="<?= $siteConfig->siteUrl ?>/staff/portal"><i class="fa fa-user-circle me-1"></i> Staff Self-Service</a></li>
                                <?php elseif ($rId === 6): ?>
                                    <li class="list-group-item border-0"><a class="text-primary fw-bold" href="<?= $siteConfig->siteUrl ?>/dashboard"><i class="fa fa-handshake me-1"></i> Service Dashboard</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark fw-bold" href="<?= $siteConfig->siteUrl ?>/admin/requests"><i class="fa fa-tasks me-1"></i> Service Requests</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/clients"><i class="fa fa-building me-1"></i> Client Accounts</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/service-catalogue"><i class="fa fa-book me-1"></i> Service Catalogue</a></li>
                                    <li class="list-group-item border-0"><a class="text-secondary" href="<?= $siteConfig->siteUrl ?>/staff/portal"><i class="fa fa-user-circle me-1"></i> Staff Self-Service</a></li>
                                <?php elseif ($rId === 7): ?>
                                    <li class="list-group-item border-0"><a class="text-primary fw-bold" href="<?= $siteConfig->siteUrl ?>/dashboard"><i class="fa fa-coins me-1"></i> Finance Dashboard</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark fw-bold" href="<?= $siteConfig->siteUrl ?>/client/invoices"><i class="fa fa-file-invoice-dollar me-1"></i> Client Invoices</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/payroll"><i class="fa fa-money-check-dollar me-1"></i> Staff Payroll</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/staff/export-p4"><i class="fa fa-file-excel me-1"></i> NSSA Form P4</a></li>
                                    <li class="list-group-item border-0"><a class="text-secondary" href="<?= $siteConfig->siteUrl ?>/staff/portal"><i class="fa fa-user-circle me-1"></i> Staff Self-Service</a></li>
                                <?php elseif ($rId === 3): ?>
                                    <li class="list-group-item border-0"><a class="text-primary fw-bold" href="<?= $siteConfig->siteUrl ?>/client/portal"><i class="fa fa-building me-1"></i> Client Portal</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/client/requests"><i class="fa fa-tasks me-1"></i> Work Requests</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/client/invoices"><i class="fa fa-file-invoice me-1"></i> Tax Invoices</a></li>
                                <?php else: ?>
                                    <li class="list-group-item border-0"><a class="text-primary fw-bold" href="<?= $siteConfig->siteUrl ?>/dashboard"><i class="fa fa-tachometer-alt me-1"></i> My Dashboard</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/opportunities"><i class="fa fa-briefcase me-1"></i> Opportunities</a></li>
                                <?php endif; ?>
                                <li class="list-group-item border-0"><a class="text-success" href="<?= $siteConfig->siteUrl ?>/edit-profile">Edit profile</a></li>
                                <li class="list-group-item border-0"><a class="text-warning" href="<?= $siteConfig->siteUrl ?>/logout">Logout</a></li>
                            <?php else: ?>
                                <li class="list-group-item border-0"><a class="btn w-100 rounded-pill button1 mb-2" href="<?= $siteConfig->siteUrl ?>/login">Sign In</a></li>
                                <li class="list-group-item border-0 p-0"><a class="btn w-100 rounded-pill btn-outline-primary btn-sm" href="<?= $siteConfig->siteUrl ?>/register">Register Free</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<?php if ($navUser): ?>
<script>
function updateNavNotificationBadge(count) {
    const badge = document.getElementById('navNotificationBadge');
    if (!badge) return;
    if (count > 0) {
        badge.textContent = count;
        badge.classList.remove('d-none');
    } else {
        badge.classList.add('d-none');
    }
}

function loadNavNotifications() {
    const list = document.getElementById('navNotificationList');
    if (!list) return;
    fetch('<?= $siteConfig->siteUrl; ?>/api/notifications/recent')
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                updateNavNotificationBadge(data.unread_count);
                if (!data.notifications || data.notifications.length === 0) {
                    list.innerHTML = '<div class="text-center py-4 text-muted small"><i class="fa fa-bell-slash me-1 opacity-50"></i> No notifications yet</div>';
                    return;
                }
                let html = '<div class="list-group list-group-flush small">';
                data.notifications.forEach(n => {
                    const unreadClass = !n.is_read ? 'bg-light' : '';
                    const link = n.link || '<?= $siteConfig->siteUrl; ?>/notifications';
                    html += `
                        <a href="${link}" class="list-group-item list-group-item-action p-3 border-bottom ${unreadClass} text-start d-flex gap-2 align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 bg-light" style="width: 28px; height: 28px;">
                                <i class="fa ${n.icon} text-primary" style="font-size: 0.8rem;"></i>
                            </div>
                            <div class="flex-grow-1" style="min-width: 0;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-dark text-truncate d-block" style="max-width: 190px;">${n.title}</strong>
                                    <span class="text-muted" style="font-size: 0.7rem;">${n.time_ago}</span>
                                </div>
                                <div class="text-muted text-truncate mt-1" style="font-size: 0.78rem;">${n.message}</div>
                            </div>
                        </a>
                    `;
                });
                html += '</div>';
                list.innerHTML = html;
            }
        }).catch(err => {
            list.innerHTML = '<div class="text-center py-3 text-danger small">Failed to load</div>';
        });
}

function markAllNotificationsReadNav(e) {
    if (e) e.stopPropagation();
    const fd = new FormData();
    fd.append('all', 1);
    fetch('<?= $siteConfig->siteUrl; ?>/api/notifications/mark-read', {
        method: 'POST',
        body: fd
    }).then(r => r.json()).then(data => {
        if (data.status === 'success') {
            updateNavNotificationBadge(0);
            loadNavNotifications();
        }
    });
}
</script>
<?php endif; ?>

