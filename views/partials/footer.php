<?php global $siteConfig; ?>
<footer class="trainit-footer">
    <div class="container-xl">
        <div class="trainit-footer-grid">
            <!-- Brand column -->
            <div class="trainit-footer-brand-col">
                <a href="<?= defined('_WEBSITE_URL') ? _WEBSITE_URL : 'https://tsigiro.co.zw' ?>" class="trainit-footer-brand-link">
                    <img src="<?= $siteConfig->assetsUrl ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                         alt="<?= htmlspecialchars(_SITE) ?>"
                         width="44" height="44"
                         style="object-fit: contain; flex-shrink: 0; border-radius: 8px;">
                    <span><?= htmlspecialchars(_SITE) ?></span>
                </a>
                <p class="trainit-footer-tagline">Back-office shared services for mission-driven organisations.</p>
                <p class="trainit-footer-email"><a href="mailto:hello@tsigiro.co.zw">hello@tsigiro.co.zw</a></p>
            </div>
            <!-- Links columns -->
            <div class="trainit-footer-links-col">
                <p class="trainit-footer-col-heading">Service Lines</p>
                <a href="<?= $siteConfig->siteUrl ?>/services">IT as a Service</a>
                <a href="<?= $siteConfig->siteUrl ?>/services">Finance as a Service</a>
                <a href="<?= $siteConfig->siteUrl ?>/services">HR as a Service</a>
                <a href="<?= $siteConfig->siteUrl ?>/services">Audit &amp; Compliance</a>
            </div>
            <div class="trainit-footer-links-col">
                <p class="trainit-footer-col-heading">Tsigiro</p>
                <a href="<?= $siteConfig->siteUrl ?>/about">About</a>
                <a href="<?= $siteConfig->siteUrl ?>/opportunities">Recruitment Portal</a>
                <a href="<?= $siteConfig->siteUrl ?>/contact">Contact</a>
                <a href="<?= defined('_WEBSITE_URL') ? _WEBSITE_URL : 'https://tsigiro.co.zw' ?>" target="_blank" rel="noopener">Main Website</a>
            </div>
            <div class="trainit-footer-links-col">
                <p class="trainit-footer-col-heading">Portal Access</p>
                <a href="<?= $siteConfig->siteUrl ?>/login">Sign In</a>
                <a href="<?= $siteConfig->siteUrl ?>/dashboard">Dashboard</a>
                <a href="<?= $siteConfig->siteUrl ?>/opportunities">Join Roster</a>
            </div>
        </div>
        <div class="trainit-footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> Trainit Technologies (Pvt) Ltd T/A Tsigiro. All rights reserved.</span>
        </div>
    </div>
</footer>
