@extends('layouts.account')

<div class="az-signin-wrapper">
    <div class="az-card-signin">

        <div class="az-signin-header text-center">
            <a href="<?php echo $siteConfig->siteUrl; ?>/home"><img class="img account-logo mx-auto mb-4"
                    src="<?php echo $siteConfig->assetsUrl; ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                    alt="<?= htmlspecialchars(_SITE) ?>" /></a>

            <?php if (!empty($data['valid'])): ?>
                <h2 class="h4 mb-4">Choose a new password</h2>
                <form id="_form" action="<?= $siteConfig->siteUrl ?>/set-password" method="POST" class="text-start">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($data['token'] ?? '', ENT_QUOTES) ?>">

                    <div class="input-group mb-3 rounded-3 border p-1 px-3">
                        <input id="new_password" type="password" name="password"
                            class="form-control bg-transparent border-0" placeholder="New password:"
                            aria-label="New password" minlength="6" required>
                        <span class="input-group-text border-0 bg-transparent"><i class="fa fa-eye show-p"
                                data-target="new_password"></i></span>
                    </div>

                    <p id='msg' class="my-2"><?= htmlspecialchars($data['status'] ?? '') ?></p>

                    <button type="submit" id="submit_btn"
                        class="btn btn-lg button1 rounded-pill w-100 mx-auto">Set Password</button>
                </form>
            <?php else: ?>
                <h2 class="h4 mb-3">Link expired</h2>
                <p id='msg' class="my-2 text-danger"><?= htmlspecialchars($data['status'] ?? '') ?></p>
                <a class="btn btn-lg button1 rounded-pill w-100 mx-auto" href="<?= $siteConfig->siteUrl ?>/reset">Request a new link</a>
            <?php endif; ?>

            <p class="mb-1 text-center mt-3">
                <a class="d-block my-2 auth-link" href="<?php echo $siteConfig->siteUrl; ?>/login">I want to
                    login</a>
            </p>
        </div><!-- az-signin-header -->

    </div><!-- az-card-signin -->
</div><!-- az-signin-wrapper -->
