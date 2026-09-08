<?php
/**
 * Shared page variables + module scripts for the Servicerequesttriage views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'servicerequesttriages';
$table = 'servicerequesttriage';
$page_name = 'Servicerequesttriage';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "servicerequesttriage"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/servicerequesttriage.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
