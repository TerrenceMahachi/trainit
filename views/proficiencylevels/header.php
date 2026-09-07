<?php
/**
 * Shared page variables + module scripts for the Proficiencylevel views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'proficiencylevels';
$table = 'proficiencylevel';
$page_name = 'Proficiencylevel';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "proficiencylevel"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/proficiencylevel.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
