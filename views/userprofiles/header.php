<?php
/**
 * Shared page variables + module scripts for the Userprofile views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'userprofiles';
$table = 'userprofile';
$page_name = 'Userprofile';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "userprofile"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/userprofile.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
