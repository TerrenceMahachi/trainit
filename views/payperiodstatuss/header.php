<?php
/**
 * Shared page variables + module scripts for the Payperiodstatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'payperiodstatuss';
$table = 'payperiodstatus';
$page_name = 'Payperiodstatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "payperiodstatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/payperiodstatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
