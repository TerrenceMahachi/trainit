<?php
/**
 * Shared page variables + module scripts for the Refereeverificationstatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'refereeverificationstatuss';
$table = 'refereeverificationstatus';
$page_name = 'Refereeverificationstatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "refereeverificationstatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/refereeverificationstatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
