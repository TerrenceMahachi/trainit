<?php
/**
 * Shared page variables + module scripts for the Rosterstatusevent views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'rosterstatusevents';
$table = 'rosterstatusevent';
$page_name = 'Rosterstatusevent';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "rosterstatusevent"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/rosterstatusevent.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
