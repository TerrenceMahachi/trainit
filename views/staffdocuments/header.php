<?php
/**
 * Shared page variables + module scripts for the Staffdocument views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'staffdocuments';
$table = 'staffdocument';
$page_name = 'Staffdocument';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "staffdocument"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/staffdocument.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
