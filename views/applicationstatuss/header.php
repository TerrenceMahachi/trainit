<?php
/**
 * Shared page variables + module scripts for the Applicationstatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'applicationstatuss';
$table = 'applicationstatus';
$page_name = 'Applicationstatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "applicationstatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/applicationstatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
