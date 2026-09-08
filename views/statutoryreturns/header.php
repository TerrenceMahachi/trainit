<?php
/**
 * Shared page variables + module scripts for the Statutoryreturn views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'statutoryreturns';
$table = 'statutoryreturn';
$page_name = 'Statutoryreturn';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "statutoryreturn"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/statutoryreturn.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
