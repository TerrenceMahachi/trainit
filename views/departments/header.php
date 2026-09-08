<?php
/**
 * Shared page variables + module scripts for the Department views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'departments';
$table = 'department';
$page_name = 'Department';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "department"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/department.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
