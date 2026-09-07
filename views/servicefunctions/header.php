<?php
/**
 * Shared page variables + module scripts for the Servicefunction views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'servicefunctions';
$table = 'servicefunction';
$page_name = 'Servicefunction';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "servicefunction"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/servicefunction.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
