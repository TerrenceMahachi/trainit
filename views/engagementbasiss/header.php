<?php
/**
 * Shared page variables + module scripts for the Engagementbasis views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'engagementbasiss';
$table = 'engagementbasis';
$page_name = 'Engagementbasis';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "engagementbasis"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/engagementbasis.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
