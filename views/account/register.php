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
                <h1 class="auth-heading">Create your account</h1>
                <p class="auth-subheading">Join the Tsigiro Portal workspace</p>
            </div>

            <!-- Registration Form -->
            <form id="_form" class="text-start" onsubmit="event.preventDefault(); submit();">
                
                <!-- Full Name -->
                <div class="auth-field">
                    <label class="auth-label" for="name">Full Name</label>
                    <div class="auth-input-container">
                        <input id="name" type="text" name="name" class="form-control"
                            placeholder="First & Last Name" aria-label="Full name" autocomplete="name" required>
                        <span class="auth-icon-btn"><i class="fa fa-user"></i></span>
                    </div>
                </div>

                <!-- Email Address -->
                <div class="auth-field">
                    <label class="auth-label" for="email">Email Address</label>
                    <div class="auth-input-container">
                        <input id="email" type="email" name="email" class="form-control"
                            placeholder="name@company.co.zw" aria-label="Email address" autocomplete="email" required>
                        <span class="auth-icon-btn"><i class="fa fa-envelope"></i></span>
                    </div>
                </div>

                <!-- Password -->
                <div class="auth-field">
                    <label class="auth-label" for="login_password">Password</label>
                    <div class="auth-input-container">
                        <input id="login_password" name="password" type="password"
                            class="form-control" placeholder="Minimum 6 characters" aria-label="Password"
                            autocomplete="new-password" minlength="6" required>
                        <span class="auth-icon-btn show-p" data-target="login_password" title="Toggle password visibility">
                            <i class="fa fa-eye"></i>
                        </span>
                    </div>
                </div>

                <!-- Security Verification Challenge -->
                <div class="auth-field">
                    <label class="auth-label" for="txt_not_robot_answer">Security Verification</label>
                    <div class="auth-input-container">
                        <span class="badge bg-light text-dark border px-2 py-1 me-2 fw-semibold" id="not_robot_question" style="font-size: 0.85rem; letter-spacing: 0.05em;">
                            <?= htmlspecialchars($data['captcha_question'] ?? '') ?> =
                        </span>
                        <input id="txt_not_robot_answer" type="number" name="txt_not_robot_answer"
                            class="form-control" placeholder="Answer" required style="max-width: 120px;">
                        <span class="auth-icon-btn ms-auto text-muted" title="Human verification">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                    </div>
                </div>

                <!-- Status Message -->
                <div id="msg" class="my-2"></div>

                <!-- Submit Button -->
                <button type="button" id="submit_btn" onclick="submit()" class="btn-auth-submit">
                    <i class="fa fa-user-plus me-1" style="font-size: 0.85rem;"></i>
                    <span>Create Account</span>
                </button>

                <!-- Divider -->
                <div class="auth-divider">
                    <div class="auth-divider-line"></div>
                    <div class="auth-divider-text">Already have an account?</div>
                    <div class="auth-divider-line"></div>
                </div>

                <!-- Secondary Action: Sign In -->
                <a href="<?= $siteConfig->siteUrl ?>/login" class="btn-auth-register">
                    <i class="fa fa-sign-in-alt text-primary"></i>
                    <span>Sign In to Existing Account</span>
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

<script src="<?= $siteConfig->siteUrl ?>/assets/scripts/register.js?v=<?= _ASSET_VERSION ?>"></script>
