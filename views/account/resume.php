@extends('layouts.account')

<div class="az-signin-wrapper">

    <div class="az-card-signin">

        <div class="az-signin-header text-center">
            <a href="<?php echo $siteConfig->siteUrl; ?>/home"><img class="img account-logo mx-auto mb-4"
                    src="<?php echo $siteConfig->assetsUrl; ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>"
                    alt="<?= htmlspecialchars(_SITE) ?>" /></a>

            <?php $firstName = isset($data['user']->name) ? trim(explode(' ', $data['user']->name)[0]) : ''; ?>
            <h2 class="h4 mb-1">Welcome back<?= $firstName !== '' ? ', ' . htmlspecialchars($firstName) : '' ?></h2>
            <h4 class="mb-4">Your session was paused after a few minutes of inactivity. Enter the last three characters of your password to resume.</h4>

            <?php if (!empty($data['expiresAt'])): ?>
                <p class="text-muted small mb-3" id="resume_expiry" data-expires-at="<?= (int) $data['expiresAt'] ?>">You stay signed in for up to 30 days.</p>
            <?php endif; ?>

            <form id="_form" class="text-start" action="<?php echo $siteConfig->siteUrl; ?>/resume" method="POST">
                <?= \App\Helpers\Csrf::field() ?>
                <input type="hidden" name="return_to" value="<?= htmlspecialchars($data['returnTo'] ?? '/dashboard', ENT_QUOTES) ?>">

                <div class="input-group border rounded-3 p-1 mt-2 px-3">
                    <input id="resume_tail" name="password_tail" type="password" inputmode="text"
                        minlength="3" maxlength="3" autocomplete="off" required autofocus
                        class="form-control bg-transparent border-0" placeholder="Last 3 characters" aria-label="Last three password characters">
                    <span class="input-group-text border-0 bg-transparent"><i class="fa fa-eye" id="resume_eye"></i></span>
                </div>

                <p id="msg" class="my-2 text-danger"><?= htmlspecialchars($data['status'] ?? '') ?></p>

                <button type="submit" id="submit_btn"
                    class="btn btn-lg button1 rounded-pill w-100 mx-auto">Resume session</button><br>

                <p class="mb-1 text-center mt-3">
                    <a class="d-block my-2 auth-link" href="<?php echo $siteConfig->siteUrl; ?>/logout">Not you? Log out</a>
                </p>
            </form>
        </div><!-- az-signin-header -->

    </div><!-- az-card-signin -->
</div><!-- az-signin-wrapper -->

<script>
    document.getElementById('resume_eye').addEventListener('click', function () {
        var f = document.getElementById('resume_tail');
        f.type = f.type === 'password' ? 'text' : 'password';
    });

    // Count this challenge as activity so other tabs don't also bounce to /resume.
    document.getElementById('_form').addEventListener('submit', function () {
        try { localStorage.setItem('<?= _SITENAME ?>_last_activity', String(Date.now())); } catch (e) { /* storage disabled */ }
    });

    var expiry = document.getElementById('resume_expiry');
    if (expiry) {
        expiry.textContent = 'Your sign-in stays valid until ' +
            new Date(Number(expiry.dataset.expiresAt) * 1000).toLocaleString() + '.';
    }
</script>
