<?php
/**
 * Shared page variables + module scripts for the Professionalbody views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'professionalbodys';
$table = 'professionalbody';
$page_name = 'Professionalbody';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "professionalbody"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/professionalbody.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
