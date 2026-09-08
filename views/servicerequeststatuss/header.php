<?php
/**
 * Shared page variables + module scripts for the Servicerequeststatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'servicerequeststatuss';
$table = 'servicerequeststatus';
$page_name = 'Servicerequeststatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "servicerequeststatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/servicerequeststatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
