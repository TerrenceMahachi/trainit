<?php
/**
 * Shared page variables + module scripts for the Documentvalidity views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'documentvaliditys';
$table = 'documentvalidity';
$page_name = 'Documentvalidity';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "documentvalidity"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/documentvalidity.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
