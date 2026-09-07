<?php global $siteConfig; ?>
<footer class="trainit-footer">
    <div class="container-xl">
        <div class="trainit-footer-grid">
            <!-- Brand column -->
            <div class="trainit-footer-brand-col">
                <a href="<?= $siteConfig->siteUrl ?>/home" class="trainit-footer-brand-link">
                    <img src="<?= $siteConfig->assetsUrl ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                         alt="<?= htmlspecialchars(_SITE) ?>"
                         width="44" height="44"
                         style="object-fit: contain; flex-shrink: 0;">
                    <span><?= htmlspecialchars(_SITE) ?></span>
                </a>
                <p class="trainit-footer-tagline">Technology that keeps your organisation moving.</p>
                <p class="trainit-footer-email"><a href="mailto:hello@trainit.co.zw">hello@trainit.co.zw</a></p>
            </div>
            <!-- Links columns -->
            <div class="trainit-footer-links-col">
                <p class="trainit-footer-col-heading">Services</p>
                <a href="<?= $siteConfig->siteUrl ?>/services">Cloud &amp; Hosting</a>
                <a href="<?= $siteConfig->siteUrl ?>/services">Business Systems</a>
                <a href="<?= $siteConfig->siteUrl ?>/services">Web Presence</a>
                <a href="<?= $siteConfig->siteUrl ?>/cloud">Cloud Portal</a>
            </div>
            <div class="trainit-footer-links-col">
                <p class="trainit-footer-col-heading">Company</p>
                <a href="<?= $siteConfig->siteUrl ?>/about">About</a>
                <a href="<?= $siteConfig->siteUrl ?>/opportunities">Opportunities</a>
                <a href="<?= $siteConfig->siteUrl ?>/contact">Contact</a>
            </div>
            <div class="trainit-footer-links-col">
                <p class="trainit-footer-col-heading">Account</p>
                <a href="<?= $siteConfig->siteUrl ?>/login">Sign In</a>
                <a href="<?= $siteConfig->siteUrl ?>/register">Register</a>
                <a href="<?= $siteConfig->siteUrl ?>/dashboard">Dashboard</a>
            </div>
        </div>
        <div class="trainit-footer-bottom">
            <span>&copy; <?php echo date('Y'); ?> <?= htmlspecialchars(_SITE) ?>. All rights reserved.</span>
        </div>
    </div>
</footer>
