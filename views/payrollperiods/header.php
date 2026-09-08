<?php
/**
 * Shared page variables + module scripts for the Payrollperiod views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'payrollperiods';
$table = 'payrollperiod';
$page_name = 'Payrollperiod';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "payrollperiod"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/payrollperiod.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
