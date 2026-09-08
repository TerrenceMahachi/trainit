<?php
/**
 * Shared page variables + module scripts for the Servicerequeststatusevent views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'servicerequeststatusevents';
$table = 'servicerequeststatusevent';
$page_name = 'Servicerequeststatusevent';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "servicerequeststatusevent"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/servicerequeststatusevent.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
