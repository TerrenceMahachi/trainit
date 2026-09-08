<?php
/**
 * Shared page variables + module scripts for the Leavetype views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'leavetypes';
$table = 'leavetype';
$page_name = 'Leavetype';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "leavetype"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/leavetype.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
