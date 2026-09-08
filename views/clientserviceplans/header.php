<?php
/**
 * Shared page variables + module scripts for the Clientserviceplan views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'clientserviceplans';
$table = 'clientserviceplan';
$page_name = 'Clientserviceplan';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "clientserviceplan"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/clientserviceplan.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
