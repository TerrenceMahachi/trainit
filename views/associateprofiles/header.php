<?php
/**
 * Shared page variables + module scripts for the Associateprofile views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'associateprofiles';
$table = 'associateprofile';
$page_name = 'Associateprofile';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "associateprofile"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/associateprofile.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
