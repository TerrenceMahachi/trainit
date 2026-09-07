<?php
/**
 * Shared page variables + module scripts for the Rosterassessment views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'rosterassessments';
$table = 'rosterassessment';
$page_name = 'Rosterassessment';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "rosterassessment"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/rosterassessment.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
