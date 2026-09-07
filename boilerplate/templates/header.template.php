<?php
/**
 * Shared page variables + module scripts for the {{MODEL_NAME}} views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = '{{MODEL_NAME_LOWERCASE}}s';
$table = '{{MODEL_NAME_LOWERCASE}}';
$page_name = '{{MODEL_NAME}}';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "{{MODEL_NAME_LOWERCASE}}"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/{{MODEL_NAME_LOWERCASE}}.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
