<?php
/**
 * Shared page variables + module scripts for the Verificationstatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'verificationstatuss';
$table = 'verificationstatus';
$page_name = 'Verificationstatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "verificationstatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/verificationstatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
