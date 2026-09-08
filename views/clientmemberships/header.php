<?php
/**
 * Shared page variables + module scripts for the Clientmembership views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'clientmemberships';
$table = 'clientmembership';
$page_name = 'Clientmembership';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "clientmembership"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/clientmembership.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
