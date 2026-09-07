<?php
/**
 * Shared page variables + module scripts for the Applicationtrack views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'applicationtracks';
$table = 'applicationtrack';
$page_name = 'Applicationtrack';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "applicationtrack"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/applicationtrack.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
