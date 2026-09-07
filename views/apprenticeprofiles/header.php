<?php
/**
 * Shared page variables + module scripts for the Apprenticeprofile views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'apprenticeprofiles';
$table = 'apprenticeprofile';
$page_name = 'Apprenticeprofile';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "apprenticeprofile"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/apprenticeprofile.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
