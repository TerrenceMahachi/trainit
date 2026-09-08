<?php
/**
 * Shared page variables + module scripts for the Clientserviceplantermination views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'clientserviceplanterminations';
$table = 'clientserviceplantermination';
$page_name = 'Clientserviceplantermination';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "clientserviceplantermination"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/clientserviceplantermination.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
