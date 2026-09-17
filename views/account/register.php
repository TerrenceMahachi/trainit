@extends('layouts.account')

<div class="az-signin-wrapper">

    <div class="az-card-signin">

        <div class="az-signin-header text-center">
            <a href="<?php echo $siteConfig->siteUrl; ?>/home">
                <img class="account-logo mx-auto mb-4"
                    src="<?php echo $siteConfig->assetsUrl; ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                    alt="<?= htmlspecialchars(_SITE) ?>" />
            </a>
            <h2 class="h4 mb-2 fw-bold text-dark">Create your account</h2>
            <p class="text-muted small mb-3">Join the Tsigiro Portal workspace</p>

            <!-- Segmented Auth Switcher -->
            <div class="d-flex justify-content-center mb-4">
                <div class="btn-group p-1 bg-light rounded-pill border" role="group" style="max-width: 290px; width: 100%;">
                    <a href="<?php echo $siteConfig->siteUrl; ?>/login" class="btn btn-sm rounded-pill btn-light text-muted fw-semibold px-3">
                        <i class="fa fa-sign-in-alt me-1"></i> Sign In
                    </a>
                    <a href="<?php echo $siteConfig->siteUrl; ?>/register" class="btn btn-sm rounded-pill btn-primary fw-bold shadow-sm px-3">
                        <i class="fa fa-user-plus me-1"></i> Create Account
                    </a>
                </div>
            </div>

            <form id="_form" class="text-start mt-0">
                <div class="input-group mb-3 rounded-3 border p-1 px-3">
                    <input id="name" type="text" name="name" class="form-control bg-transparent border-0"
                        placeholder="Full name:" aria-label="Full name" aria-describedby="basic-addon1">
                    <span class="input-group-text border-0 bg-transparent" id="basic-addon1"><i
                            class="fa fa-user"></i></span>

                </div>
                <div class="input-group mb-3 rounded-3 border p-1 px-3">
                    <input id="email" type="email" name="email" class="form-control bg-transparent border-0"
                        placeholder="Email:" aria-label="Username" aria-describedby="basic-addon2">
                    <span class="input-group-text border-0 bg-transparent" id="basic-addon2"><i
                            class="fa fa-envelope"></i></span>

                </div>

                <div class="input-group border rounded-3 p-1 mt-2 px-3">
                    <input id="login_password" name="password" type="password"
                        class="form-control bg-transparent border-0" placeholder="Password:" aria-label="Password"
                        aria-describedby="basic-addon3">
                    <span class="input-group-text border-0 bg-transparent" id="basic-addon3"><i
                            class="fa fa-eye show-p" data-target="login_password"></i></span>
                </div>

                <div class="input-group border rounded-3 p-1 mt-2 px-3">
                    <div class="input-group-prepend rounded-3 border-0 bg-transparent">
                        <div class="input-group-text border-0 bg-transparent" id="not_robot_question">
                            <?= htmlspecialchars($data['captcha_question'] ?? '') ?> =
                        </div>
                    </div>
                    <input id="txt_not_robot_answer" type="number" name="txt_not_robot_answer"
                        class="form-control bg-transparent border-0" placeholder="Answer:">
                    <div class="input-group-append rounded-3 border-0 bg-transparent">
                        <div class="input-group-text border-0 bg-transparent"> <span class="fas fa-robot"></span>
                        </div>
                    </div>
                </div>
                <br>

                <a id="submit_btn" onclick="submit()" class="btn button1 btn-lg rounded-pill w-100 mx-auto">Create
                    Account</a>
                <p id='msg' class="my-2 text-danger"></p>

                <div class="d-flex align-items-center my-3 text-muted">
                    <hr class="flex-grow-1 my-0 border-secondary-subtle">
                    <span class="px-2 small fw-bold text-uppercase text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">Already have an account?</span>
                    <hr class="flex-grow-1 my-0 border-secondary-subtle">
                </div>

                <a href="<?php echo $siteConfig->siteUrl; ?>/login" class="btn btn-lg btn-outline-secondary rounded-pill w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mb-2">
                    <i class="fa fa-sign-in-alt"></i> Sign In to Existing Account
                </a>
            </form>
        </div><!-- az-signin-header -->

    </div><!-- az-card-signin -->
</div><!-- az-signin-wrapper -->

<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/register.js?v=<?php echo _ASSET_VERSION; ?>"></script>
