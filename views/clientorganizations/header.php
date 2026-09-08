<?php
/**
 * Shared page variables + module scripts for the Clientorganization views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'clientorganizations';
$table = 'clientorganization';
$page_name = 'Clientorganization';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "clientorganization"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/clientorganization.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
