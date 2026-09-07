<!DOCTYPE html>
<html>

<head>
    <title><?php echo isset($data['title']) ? $data['title'] : 'Default Title'; ?></title>
    <link rel="stylesheet" href="<?php echo $siteConfig->siteUrl; ?>/vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->

    <link rel="stylesheet" href="<?php echo $siteConfig->siteUrl; ?>/vendor/fortawesome/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $siteConfig->siteUrl; ?>/assets/css/style.css?id=<?php echo rand(); ?>">
    <script src="<?php echo $siteConfig->siteUrl; ?>/vendor/components/jquery/jquery.min.js"></script>
    <script src="<?php echo $siteConfig->siteUrl; ?>/src/scripts/common.js"></script>

      <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


 <meta name="description" content="Loia">
 <meta name="author" content="aytronics@gmail.com">
</head>

<body class="content">

    <?php // record_page_view($_SERVER['REQUEST_URI']);  ?>
    <script>var site = "<?php echo $siteConfig->siteUrl; ?>"</script>

    <div class="content">
        @yield('content')
    </div>
 
    <div id="toast-container"></div>
    <!-- jQuery -->
    <!-- Bootstrap JS -->
    <script src="<?php echo $siteConfig->siteUrl; ?>/vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>
        $('.closeModalBtn').on('click', function () {
        window.parent.postMessage('closeAllModals', '*');
        console.log('closing request:');
    })
</script>
</body>

</html>