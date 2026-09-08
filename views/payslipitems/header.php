<?php
/**
 * Shared page variables + module scripts for the Payslipitem views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'payslipitems';
$table = 'payslipitem';
$page_name = 'Payslipitem';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "payslipitem"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/payslipitem.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
