<?php
/**
 * Shared page variables + module scripts for the Zimprovince views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'zimprovinces';
$table = 'zimprovince';
$page_name = 'Zimprovince';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "zimprovince"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/zimprovince.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
