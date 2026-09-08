<?php
/**
 * Shared page variables + module scripts for the Documenttype views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'documenttypes';
$table = 'documenttype';
$page_name = 'Documenttype';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "documenttype"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/documenttype.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
