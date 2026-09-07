<?php
/**
 * Shared page variables + module scripts for the Skillitem views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'skillitems';
$table = 'skillitem';
$page_name = 'Skillitem';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "skillitem"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/skillitem.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
