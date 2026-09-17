<?php
/**
 * Shared page variables + module scripts for the Profiletype views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'profiletypes';
$table = 'profiletype';
$page_name = 'Profiletype';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "profiletype"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/profiletype.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
