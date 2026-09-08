<?php
/**
 * Shared page variables + module scripts for the Servicerequestattachment views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'servicerequestattachments';
$table = 'servicerequestattachment';
$page_name = 'Servicerequestattachment';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "servicerequestattachment"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/servicerequestattachment.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
