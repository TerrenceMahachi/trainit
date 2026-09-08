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
$navRole = 'guest';
if ($navUser) {
    $roleId = (int) $navUser->role;
    $navRole = ($roleId === 1 || in_array($roleId, [1, 6, 7, 8], true)) ? 'admin' : 'user';
}

$navItems = require _BASE_PATH . '/config/nav.php';

/** Should the current role see this item? */
$navVisible = function ($item) use ($navRole) {
    if (empty($item['roles'])) {
        return true;
    }
    return in_array($navRole, $item['roles'], true);
};
/** Is this item the active page? */
$navActive = function ($item) use ($currentUrl) {
    $needle = $item['match'] ?? ($item['url'] ?? null);
    return $needle && strpos($currentUrl, $needle) !== false ? 'active' : '';
};
?>
<nav class="navbar navbar-expand-lg trainit-navbar">
    <div class="container-xl">
        <a class="navbar-brand trainit-brand" href="<?= $siteConfig->siteUrl ?>/home">
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
                                    <li><a class="dropdown-item" href="<?= $siteConfig->siteUrl . ($child['url'] ?? '#') ?>"><?= htmlspecialchars($child['label']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $navActive($item) ?>" href="<?= $siteConfig->siteUrl . ($item['url'] ?? '#') ?>"><?= htmlspecialchars($item['label']) ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <div class="navbar-nav" id="user_logged_in">
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
                                <?php if (in_array((int)$navUser->role, [1, 6, 7, 8], true)): ?>
                                    <li class="list-group-item border-0"><a class="text-primary fw-bold" href="<?= $siteConfig->siteUrl ?>/staff/portal"><i class="fa fa-user-circle me-1"></i> Staff Self-Service</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark fw-bold" href="<?= $siteConfig->siteUrl ?>/admin/staff"><i class="fa fa-id-badge me-1"></i> Staff Directory</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/clients"><i class="fa fa-building me-1"></i> Client Accounts</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/requests"><i class="fa fa-ticket me-1"></i> Delivery Desk</a></li>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/admin/payroll"><i class="fa fa-money-check-dollar me-1"></i> Staff Payroll</a></li>
                                    <?php if ((int)$navUser->role === 1): ?>
                                        <li class="list-group-item border-0"><a class="text-danger fw-bold" href="<?= $siteConfig->siteUrl ?>/admin/staff-approvals"><i class="fa fa-shield-alt me-1"></i> Approvals Queue</a></li>
                                    <?php endif; ?>
                                    <li class="list-group-item border-0"><a class="text-dark" href="<?= $siteConfig->siteUrl ?>/dashboard"><i class="fa fa-tachometer-alt me-1"></i> Admin Command Center</a></li>
                                <?php elseif ((int)$navUser->role === 3): ?>
                                    <li class="list-group-item border-0"><a class="text-primary fw-bold" href="<?= $siteConfig->siteUrl ?>/client/portal"><i class="fa fa-building me-1"></i> Client Portal</a></li>
                                <?php endif; ?>
                                <li class="list-group-item border-0"><a class="text-success" href="<?= $siteConfig->siteUrl ?>/edit-profile">Edit profile</a></li>
                                <li class="list-group-item border-0"><a class="text-warning" href="<?= $siteConfig->siteUrl ?>/logout">Logout</a></li>
                            <?php else: ?>
                                <li class="list-group-item border-0"><a class="btn w-100 rounded-pill button1" href="<?= $siteConfig->siteUrl ?>/login">Login</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
