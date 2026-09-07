@extends('layouts.account')

<div class="az-signin-wrapper">
    <div class="az-card-signin">

        <div class="az-signin-header text-center">
            <a href="<?php echo $siteConfig->siteUrl; ?>/home">
                <img class="account-logo mx-auto mb-4"
                    src="<?php echo $siteConfig->assetsUrl; ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                    alt="<?= htmlspecialchars(_SITE) ?>" />
            </a>
            <h2 class="h4 mb-2">Reset your password</h2>
            <p class="text-muted mb-4">Enter your email and we'll send you a reset link.</p>

            <form id="_form" action="<?= $siteConfig->siteUrl ?>/reset" method="POST" class="text-start">
                <div class="input-group mb-3 rounded-3 border p-1 px-3">
                    <input id="email" type="email" name="email" class="form-control bg-transparent border-0"
                        placeholder="Email:" aria-label="Username" aria-describedby="basic-addon1">
                    <span class="input-group-text border-0 bg-transparent" id="basic-addon1"><i
                            class="fa fa-envelope"></i></span>

                </div>

                <p id='msg' class="my-2"><?= htmlspecialchars($data['status'] ?? '') ?></p>

                <button type="submit" id="submit_btn" class="btn btn-lg button1 rounded-pill w-100 mx-auto">Reset</button>
                <p class="mb-1 text-center mt-3"> <a class="d-block my-2 auth-link"
                        href="<?php echo $siteConfig->siteUrl; ?>/login">I want to login</a>
                </p>
            </form>
        </div><!-- az-signin-header -->

    </div><!-- az-card-signin -->
</div><!-- az-signin-wrapper -->
