<?php
/**
 * Shared page variables + module scripts for the Servicerequestclosure views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'servicerequestclosures';
$table = 'servicerequestclosure';
$page_name = 'Servicerequestclosure';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "servicerequestclosure"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/servicerequestclosure.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
