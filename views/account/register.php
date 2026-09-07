@extends('layouts.account')

<div class="az-signin-wrapper">

    <div class="az-card-signin">

        <div class="az-signin-header text-center">
            <a href="<?php echo $siteConfig->siteUrl; ?>/home">
                <img class="account-logo mx-auto mb-4"
                    src="<?php echo $siteConfig->assetsUrl; ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                    alt="<?= htmlspecialchars(_SITE) ?>" />
            </a>
            <h2 class="h4 mb-4">Create your account</h2>

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
            </form>

            <p class="mb-1 text-center mt-3">
                <a class="d-block my-2 auth-link" href="<?php echo $siteConfig->siteUrl; ?>/login">I already have an
                    account</a>
            </p>
        </div><!-- az-signin-header -->

    </div><!-- az-card-signin -->
</div><!-- az-signin-wrapper -->

<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/register.js?v=<?php echo _ASSET_VERSION; ?>"></script>
