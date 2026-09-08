<?php
/**
 * Shared page variables + module scripts for the Payrollperiodapproval views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'payrollperiodapprovals';
$table = 'payrollperiodapproval';
$page_name = 'Payrollperiodapproval';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "payrollperiodapproval"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/payrollperiodapproval.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
