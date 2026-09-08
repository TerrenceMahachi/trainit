<?php
/**
 * Shared page variables + module scripts for the Payrollitemtype views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'payrollitemtypes';
$table = 'payrollitemtype';
$page_name = 'Payrollitemtype';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "payrollitemtype"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/payrollitemtype.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
