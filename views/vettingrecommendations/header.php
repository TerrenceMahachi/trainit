<?php
/**
 * Shared page variables + module scripts for the Vettingrecommendation views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'vettingrecommendations';
$table = 'vettingrecommendation';
$page_name = 'Vettingrecommendation';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "vettingrecommendation"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/vettingrecommendation.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
