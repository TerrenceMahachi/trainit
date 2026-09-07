<!DOCTYPE html>
<html lang="en">

<head>
    <?php global $siteConfig; include _VIEWS_PATH . '/partials/head.php'; ?>
    <meta name="author" content="aytronics@gmail.com">
</head>

<body>

    <div class="content">
        @yield('content')
    </div>

    <div id="loadingOverlay" class="d-none">
        <div class="overlay-content">
            <span class="spinner-border text-light" role="status"></span>
            <span class="text-light">Processing...</span>
        </div>
    </div>

    <div id="toast-container"></div>

</body>

</html>
