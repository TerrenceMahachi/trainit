<?php
/**
 * Shared page variables + module scripts for the Employmentstatus views.
 * Included by index/create/edit/view via `include __DIR__ . '/header.php'`.
 */
$page = 'employmentstatuss';
$table = 'employmentstatus';
$page_name = 'Employmentstatus';
global $siteConfig;

\App\Helpers\ViewAccess::allow([]);

if (isset($data['record'])) {
    $item = $data['record'];
}
?>
<script> var table = "employmentstatus"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/employmentstatus.js?v=<?php echo _ASSET_VERSION; ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?v=<?php echo _ASSET_VERSION; ?>"></script>
