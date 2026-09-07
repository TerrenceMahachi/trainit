<?php
/**
 * Shared page variables + module scripts for the Rosterskill views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'rosterskills';
$table = 'rosterskill';
$page_name = 'Rosterskill';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "rosterskill"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/rosterskill.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
