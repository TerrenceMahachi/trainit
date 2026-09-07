@extends('layouts.account')

<div class="az-signin-wrapper">

    <div class="az-card-signin">

        <div class="az-signin-header text-center">
            <a href="<?php echo $siteConfig->siteUrl; ?>/home">
                <img class="account-logo mx-auto mb-4"
                    src="<?php echo $siteConfig->assetsUrl; ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                    alt="<?= htmlspecialchars(_SITE) ?>" />
            </a>
            <h2 class="h4 mb-4">Welcome back</h2>

            <form id="_form" class="text-start">
                <div class="input-group mb-3 rounded-3 border p-1 px-3">
                    <input id="email" type="text" name="email" class="form-control bg-transparent border-0"
                        placeholder="Email:" aria-label="Username" aria-describedby="basic-addon1">
                    <span class="input-group-text border-0 bg-transparent" id="basic-addon1"><i
                            class="fa fa-envelope"></i></span>

                </div>
                <div class="input-group border rounded-3 p-1 mt-2 px-3">
                    <input id="login_password" name="password" type="password"
                        class="form-control bg-transparent border-0" placeholder="Password:" aria-label="Password"
                        aria-describedby="basic-addon2">
                    <span class="input-group-text border-0 bg-transparent" id="basic-addon2"><i
                            class="fa fa-eye show-p" data-target="login_password"></i></span>
                </div>

                <p id='msg' class="my-2 text-danger"><?= $data['status']; ?></p>

                <a type="button" onclick="login()" id="submit_btn" class="btn btn-lg button1 rounded-pill w-100 mx-auto">Sign
                    In</a> <br>
                <p class="mb-1 text-center mt-3">
                    <a class="d-block my-2 auth-link" href="<?php echo $siteConfig->siteUrl; ?>/reset">I forgot my
                        password</a>
                </p>
                <p class="mb-1 text-center">
                    <a class="d-block my-2 auth-link" href="<?php echo $siteConfig->siteUrl; ?>/register">I don't
                        have an account</a>
                </p>

            </form>
        </div><!-- az-signin-header -->

    </div><!-- az-card-signin -->
</div><!-- az-signin-wrapper -->

<script src="<?php echo $siteConfig->siteUrl; ?>/assets/scripts/login.js?v=<?php echo _ASSET_VERSION; ?>"></script>
