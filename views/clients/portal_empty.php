<?php
$title = "Client Portal — " . _SITE;
$navUser = \App\Helpers\Auth::user();
?>
<?php include _BASE_PATH . '/views/partials/header.php'; ?>
<?php include _BASE_PATH . '/views/partials/nav.php'; ?>

<main class="py-5" style="background-color: #fcfbfe; min-height: 85vh;">
    <div class="container text-center py-5">
        <div class="card border-0 shadow-sm rounded-4 p-5 mx-auto" style="max-width: 600px;">
            <div class="rounded-circle p-4 mx-auto mb-3" style="width: 80px; height: 80px; background-color: #F8F5FC;">
                <i class="fa fa-building-user fa-2x text-primary"></i>
            </div>
            <h3 class="fw-bold mb-2" style="color: #2A114B;">Client Workspace Provisioning</h3>
            <p class="text-muted mb-4">Your account is not currently linked to an active client organization profile. If your organization has recently onboarded with Tsigiro, please contact your account manager or our operations team to activate your membership.</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="<?= $siteConfig->siteUrl ?>/dashboard" class="btn btn-outline-secondary rounded-pill px-4">
                    Back to Dashboard
                </a>
                <a href="<?= $siteConfig->siteUrl ?>/contact" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">
                    Contact Support
                </a>
            </div>
        </div>
    </div>
</main>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
