<?php
/**
 * Shared page variables + module scripts for the Staffleave views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'staffleaves';
$table = 'staffleave';
$page_name = 'Staffleave';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "staffleave"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/staffleave.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
