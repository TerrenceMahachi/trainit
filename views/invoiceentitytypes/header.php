<?php
/**
 * Shared page variables + module scripts for the Invoiceentitytype views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'invoiceentitytypes';
$table = 'invoiceentitytype';
$page_name = 'Invoiceentitytype';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "invoiceentitytype"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/invoiceentitytype.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
