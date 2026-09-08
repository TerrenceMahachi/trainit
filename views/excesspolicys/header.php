<?php
/**
 * Shared page variables + module scripts for the Excesspolicy views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'excesspolicys';
$table = 'excesspolicy';
$page_name = 'Excesspolicy';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "excesspolicy"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/excesspolicy.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
