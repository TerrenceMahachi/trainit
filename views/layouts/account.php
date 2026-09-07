<!DOCTYPE html>
<html lang="en">

<head>
    <?php global $siteConfig; include _VIEWS_PATH . '/partials/head.php'; ?>
    <meta name="author" content="aytronics@gmail.com">
</head>

<body class="content account-body">

    <div class="content">
        @yield('content')
    </div>

    <div class="modal bg-lime" id="confirmModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Action</h5>
                </div>
                <div class="modal-body">
                    <p id="confirm-message"></p>
                </div>
                <div class="modal-footer">
                    <button id="confirm-action-btn-yes" type="button" class="btn btn-success">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal bg-lime" id="messageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="message-text"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div id="toast-container"></div>

</body>

</html>
