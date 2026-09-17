<?php
/**
 * Shared page variables + module scripts for the Profilerequestaudit views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'profilerequestaudits';
$table = 'profilerequestaudit';
$page_name = 'Profilerequestaudit';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "profilerequestaudit"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/profilerequestaudit.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
