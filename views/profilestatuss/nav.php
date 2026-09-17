<?php
/** Record tabs: All / Details / Edit. Expects $page, $table, $page_name, $item. */
$currentUrl = $_SERVER['REQUEST_URI'];
?>
<ul class="nav nav-tabs my-4">
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $siteConfig->siteUrl; ?>/<?= $page ?>">
            <i class="fa fa-arrow-left me-2"></i>All <?= $page_name ?>s</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= (strpos($currentUrl, '/view-') !== false) ? 'active' : ''; ?>"
            href="<?php echo $siteConfig->siteUrl; ?>/view-<?= $table ?>/<?= $item->iD; ?>"><?= $page_name ?> Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= (strpos($currentUrl, '/edit-') !== false) ? 'active' : ''; ?>"
            href="<?php echo $siteConfig->siteUrl; ?>/edit-<?= $table ?>/<?= $item->iD; ?>">Edit <?= $page_name ?></a>
    </li>
</ul>
