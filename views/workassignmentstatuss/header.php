<?php
/**
 * Shared page variables + module scripts for the Workassignmentstatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'workassignmentstatuss';
$table = 'workassignmentstatus';
$page_name = 'Workassignmentstatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "workassignmentstatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/workassignmentstatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
