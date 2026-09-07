<?php
/**
 * Shared page variables + module scripts for the Rosterapplication views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'rosterapplications';
$table = 'rosterapplication';
$page_name = 'Rosterapplication';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "rosterapplication"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/rosterapplication.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
