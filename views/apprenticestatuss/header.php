<?php
/**
 * Shared page variables + module scripts for the Apprenticestatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'apprenticestatuss';
$table = 'apprenticestatus';
$page_name = 'Apprenticestatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "apprenticestatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/apprenticestatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
