<?php
/**
 * Shared page variables + module scripts for the Statutoryreturnfile views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'statutoryreturnfiles';
$table = 'statutoryreturnfile';
$page_name = 'Statutoryreturnfile';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "statutoryreturnfile"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/statutoryreturnfile.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
