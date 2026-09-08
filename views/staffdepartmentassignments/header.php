<?php
/**
 * Shared page variables + module scripts for the Staffdepartmentassignment views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'staffdepartmentassignments';
$table = 'staffdepartmentassignment';
$page_name = 'Staffdepartmentassignment';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "staffdepartmentassignment"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/staffdepartmentassignment.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
