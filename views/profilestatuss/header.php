<?php
/**
 * Shared page variables + module scripts for the Profilestatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'profilestatuss';
$table = 'profilestatus';
$page_name = 'Profilestatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "profilestatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/profilestatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
