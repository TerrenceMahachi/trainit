<?php
/**
 * Shared page variables + module scripts for the Requestmessage views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'requestmessages';
$table = 'requestmessage';
$page_name = 'Requestmessage';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "requestmessage"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/requestmessage.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
