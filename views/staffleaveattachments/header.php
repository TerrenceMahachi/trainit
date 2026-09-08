<?php
/**
 * Shared page variables + module scripts for the Staffleaveattachment views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'staffleaveattachments';
$table = 'staffleaveattachment';
$page_name = 'Staffleaveattachment';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "staffleaveattachment"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/staffleaveattachment.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
