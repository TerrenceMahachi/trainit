<?php
/**
 * Shared page variables + module scripts for the Documentverification views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'documentverifications';
$table = 'documentverification';
$page_name = 'Documentverification';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "documentverification"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/documentverification.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
