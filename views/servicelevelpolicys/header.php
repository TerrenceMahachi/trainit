<?php
/**
 * Shared page variables + module scripts for the Servicelevelpolicy views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'servicelevelpolicys';
$table = 'servicelevelpolicy';
$page_name = 'Servicelevelpolicy';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "servicelevelpolicy"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/servicelevelpolicy.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
