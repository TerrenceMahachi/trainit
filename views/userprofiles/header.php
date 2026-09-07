<?php


$page = 'userprofiles';
$table = 'userprofile';
$page_name = 'UserProfile';
global $siteConfig;

if (isset($data['record'])) {
    $item = $data['record'];
    $_COOKIE['item'] = $data['record'];
}

?>
<script> var table = "userprofile"; </script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/userprofile.js?id=<?php echo rand(); ?>"></script>
<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/process.js?id=<?php echo rand(); ?>"></script>
