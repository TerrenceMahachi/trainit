<?php
/**
 * Shared page variables + module scripts for the Assignmentstatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'assignmentstatuss';
$table = 'assignmentstatus';
$page_name = 'Assignmentstatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "assignmentstatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/assignmentstatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
