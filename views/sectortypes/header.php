<?php
/**
 * Shared page variables + module scripts for the Sectortype views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'sectortypes';
$table = 'sectortype';
$page_name = 'Sectortype';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "sectortype"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/sectortype.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
