<?php
/**
 * Shared page variables + module scripts for the Payslipdisbursement views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'payslipdisbursements';
$table = 'payslipdisbursement';
$page_name = 'Payslipdisbursement';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "payslipdisbursement"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/payslipdisbursement.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
