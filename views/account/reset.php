@extends('layouts.account')

<div class="az-signin-wrapper">
    <div class="container my-auto py-4" style="max-width: 440px;">
        <div class="az-card-signin-modern shadow-lg w-100">

            <!-- Header -->
            <div class="text-center mb-3">
                <a href="<?= $siteConfig->siteUrl ?>/home" class="d-inline-block text-decoration-none">
                    <img class="account-logo-badge"
                        src="<?= $siteConfig->assetsUrl ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                        alt="<?= htmlspecialchars(_SITE) ?>" />
                </a>
                <h1 class="auth-heading">Reset your password</h1>
                <p class="auth-subheading">Enter your email and we'll send you a recovery link.</p>
            </div>

            <form id="_form" action="<?= $siteConfig->siteUrl ?>/reset" method="POST" class="text-start">
                
                <!-- Email Input -->
                <div class="auth-field">
                    <label class="auth-label" for="email">Account Email</label>
                    <div class="auth-input-container">
                        <input id="email" type="email" name="email" class="form-control"
                            placeholder="name@company.co.zw" aria-label="Email address" autocomplete="email" required>
                        <span class="auth-icon-btn"><i class="fa fa-envelope"></i></span>
                    </div>
                </div>

                <!-- Status Message -->
                <?php if (!empty($data['status'])): ?>
                    <div id="msg" class="my-3 text-primary small fw-semibold p-3 bg-light rounded-3 border text-center">
                        <i class="fa fa-info-circle me-1"></i> <?= htmlspecialchars($data['status']) ?>
                    </div>
                <?php else: ?>
                    <div id="msg"></div>
                <?php endif; ?>

                <!-- Submit Button -->
                <button type="submit" id="submit_btn" class="btn-auth-submit">
                    <span>Send Reset Link</span>
                    <i class="fa fa-paper-plane ms-1" style="font-size: 0.8rem;"></i>
                </button>

                <!-- Divider -->
                <div class="auth-divider">
                    <div class="auth-divider-line"></div>
                    <div class="auth-divider-text">Remember your password?</div>
                    <div class="auth-divider-line"></div>
                </div>

                <!-- Secondary Action: Back to Login -->
                <a href="<?= $siteConfig->siteUrl ?>/login" class="btn-auth-register">
                    <i class="fa fa-arrow-left text-primary"></i>
                    <span>Back to Sign In</span>
                </a>

                <!-- Footer Link -->
                <div class="auth-bottom-links">
                    <a href="<?= $siteConfig->siteUrl ?>/opportunities">
                        <i class="fa fa-briefcase me-1 opacity-75"></i> Explore open opportunities &rarr;
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>
