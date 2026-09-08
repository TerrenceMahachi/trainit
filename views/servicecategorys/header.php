<?php
/**
 * Shared page variables + module scripts for the Servicecategory views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'servicecategorys';
$table = 'servicecategory';
$page_name = 'Servicecategory';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "servicecategory"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/servicecategory.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
