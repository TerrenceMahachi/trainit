<?php
/**
 * Shared page variables + module scripts for the Requestmessageattachment views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'requestmessageattachments';
$table = 'requestmessageattachment';
$page_name = 'Requestmessageattachment';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "requestmessageattachment"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/requestmessageattachment.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
