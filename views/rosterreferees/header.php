<?php
/**
 * Shared page variables + module scripts for the Rosterreferee views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'rosterreferees';
$table = 'rosterreferee';
$page_name = 'Rosterreferee';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "rosterreferee"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/rosterreferee.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
