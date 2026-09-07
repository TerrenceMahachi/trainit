<!DOCTYPE html>
<html lang="en">

<head>
    <?php global $siteConfig; include _VIEWS_PATH . '/partials/head.php'; ?>
    <meta name="author" content="aytronics@gmail.com">
</head>

<body>

    <?php include _VIEWS_PATH . '/partials/nav.php'; ?>

    <div class="content">
        @yield('content')
    </div>

    <?php include _VIEWS_PATH . '/partials/session-timer.php'; ?>
    <?php include _VIEWS_PATH . '/partials/footer.php'; ?>
    <?php include _VIEWS_PATH . '/partials/modals.php'; ?>

</body>

</html>
