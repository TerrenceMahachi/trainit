<?php
/**
 * Shared page variables + module scripts for the Worklocationpreference views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'worklocationpreferences';
$table = 'worklocationpreference';
$page_name = 'Worklocationpreference';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "worklocationpreference"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/worklocationpreference.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
