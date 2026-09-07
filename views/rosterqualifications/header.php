<?php
/**
 * Shared page variables + module scripts for the Rosterqualification views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'rosterqualifications';
$table = 'rosterqualification';
$page_name = 'Rosterqualification';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "rosterqualification"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/rosterqualification.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
