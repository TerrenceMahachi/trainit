<?php
/**
 * Shared page variables + module scripts for the Refereecontacttiming views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'refereecontacttimings';
$table = 'refereecontacttiming';
$page_name = 'Refereecontacttiming';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "refereecontacttiming"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/refereecontacttiming.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
