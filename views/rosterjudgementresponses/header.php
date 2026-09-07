<?php
/**
 * Shared page variables + module scripts for the Rosterjudgementresponse views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'rosterjudgementresponses';
$table = 'rosterjudgementresponse';
$page_name = 'Rosterjudgementresponse';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "rosterjudgementresponse"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/rosterjudgementresponse.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
