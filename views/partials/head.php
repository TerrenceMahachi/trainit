<?php
/**
 * Shared document <head>. Included by every layout so CSS/JS wiring lives in
 * ONE place. Expects $siteConfig and (optionally) $data in scope.
 */
global $siteConfig;
$pageTitle = isset($data['title']) ? $data['title'] : _SITE;
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="<?= htmlspecialchars(_SITEDESCRIPTION) ?>">
<title><?= htmlspecialchars($pageTitle) ?> &middot; <?= htmlspecialchars(_SITE) ?></title>

<script>
    const site = '<?= $siteConfig->siteUrl ?>';
</script>

<link rel="stylesheet" href="<?= $siteConfig->siteUrl ?>/vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="<?= $siteConfig->siteUrl ?>/vendor/fortawesome/font-awesome/css/all.min.css">
<link rel="stylesheet" href="<?= $siteConfig->assetsUrl ?>/css/style.css?v=<?= _ASSET_VERSION ?>">
<link rel="stylesheet" href="<?= $siteConfig->assetsUrl ?>/css/trainit.css?v=<?= _ASSET_VERSION ?>">

<link rel="icon" href="<?= $siteConfig->assetsUrl ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>" type="image/svg+xml">
<link rel="alternate icon" href="<?= $siteConfig->siteUrl ?>/favicon.ico" type="image/x-icon">
<link rel="apple-touch-icon" href="<?= $siteConfig->assetsUrl ?>/img/apple-touch-icon.png?v=<?= _ASSET_VERSION ?>">

<script src="<?= $siteConfig->assetsUrl ?>/js/jquery.min.js"></script>
<script src="<?= $siteConfig->assetsUrl ?>/scripts/common.js"></script>
<script src="<?= $siteConfig->assetsUrl ?>/js/bootstrap.bundle.min.js"></script>
