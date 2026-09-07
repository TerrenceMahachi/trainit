<?php
/**
 * Shared page variables + module scripts for the Qualificationtype views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'qualificationtypes';
$table = 'qualificationtype';
$page_name = 'Qualificationtype';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "qualificationtype"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/qualificationtype.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
