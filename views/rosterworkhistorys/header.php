<?php
/**
 * Shared page variables + module scripts for the Rosterworkhistory views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'rosterworkhistorys';
$table = 'rosterworkhistory';
$page_name = 'Rosterworkhistory';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "rosterworkhistory"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/rosterworkhistory.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
