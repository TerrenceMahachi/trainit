<?php
/**
 * Shared page variables + module scripts for the Prioritylevel views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'prioritylevels';
$table = 'prioritylevel';
$page_name = 'Prioritylevel';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "prioritylevel"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/prioritylevel.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
