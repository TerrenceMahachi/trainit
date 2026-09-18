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

                <?php if (!empty($data['valid'])): ?>
                    <h1 class="auth-heading">Choose a new password</h1>
                    <p class="auth-subheading">Create a secure password for your account</p>
                </div>

                <form id="_form" action="<?= $siteConfig->siteUrl ?>/set-password" method="POST" class="text-start">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($data['token'] ?? '', ENT_QUOTES) ?>">

                    <div class="auth-field">
                        <label class="auth-label" for="new_password">New Password</label>
                        <div class="auth-input-container">
                            <input id="new_password" type="password" name="password"
                                class="form-control" placeholder="Minimum 6 characters"
                                aria-label="New password" minlength="6" required>
                            <span class="auth-icon-btn show-p" data-target="new_password" title="Toggle password visibility">
                                <i class="fa fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($data['status'])): ?>
                        <div id="msg" class="my-3 text-danger small fw-semibold p-3 bg-danger-subtle rounded-3 border border-danger-subtle text-center">
                            <?= htmlspecialchars($data['status']) ?>
                        </div>
                    <?php else: ?>
                        <div id="msg"></div>
                    <?php endif; ?>

                    <button type="submit" id="submit_btn" class="btn-auth-submit">
                        <span>Save New Password</span>
                        <i class="fa fa-check ms-1" style="font-size: 0.8rem;"></i>
                    </button>

                    <!-- Divider -->
                    <div class="auth-divider">
                        <div class="auth-divider-line"></div>
                        <div class="auth-divider-text">Or</div>
                        <div class="auth-divider-line"></div>
                    </div>

                    <div class="auth-bottom-links mt-0">
                        <a href="<?= $siteConfig->siteUrl ?>/login">
                            <i class="fa fa-arrow-left me-1"></i> Back to Sign In
                        </a>
                    </div>
                </form>

                <?php else: ?>
                    <h1 class="auth-heading text-danger">Link Expired</h1>
                    <p class="auth-subheading"><?= htmlspecialchars($data['status'] ?? 'This password reset link is invalid or has expired.') ?></p>
                </div>

                <a class="btn-auth-submit text-decoration-none" href="<?= $siteConfig->siteUrl ?>/reset">
                    <i class="fa fa-redo me-1"></i> Request a New Link
                </a>

                <div class="auth-bottom-links mt-4">
                    <a href="<?= $siteConfig->siteUrl ?>/login">
                        <i class="fa fa-arrow-left me-1"></i> Back to Sign In
                    </a>
                </div>
                <?php endif; ?>

        </div>
    </div>
</div>
