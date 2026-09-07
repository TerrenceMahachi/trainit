<?php
/**
 * Shared page variables + module scripts for the Rosteronboarding views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'rosteronboardings';
$table = 'rosteronboarding';
$page_name = 'Rosteronboarding';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "rosteronboarding"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/rosteronboarding.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
