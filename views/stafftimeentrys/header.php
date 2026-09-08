<?php
/**
 * Shared page variables + module scripts for the Stafftimeentry views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'stafftimeentrys';
$table = 'stafftimeentry';
$page_name = 'Stafftimeentry';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "stafftimeentry"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/stafftimeentry.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
