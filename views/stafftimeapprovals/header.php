<?php
/**
 * Shared page variables + module scripts for the Stafftimeapproval views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'stafftimeapprovals';
$table = 'stafftimeapproval';
$page_name = 'Stafftimeapproval';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "stafftimeapproval"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/stafftimeapproval.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
