<?php
/**
 * Shared page variables + module scripts for the Servicerequest views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'servicerequests';
$table = 'servicerequest';
$page_name = 'Servicerequest';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "servicerequest"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/servicerequest.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
