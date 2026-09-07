<?php
// Proactive idle warning + auto-pause. Only rendered for a live session; the
// countdown mirrors the server's _IDLE_TIMEOUT and, on reaching zero, sends the
// browser to /resume (which marks the session paused for the challenge).
global $siteConfig;
if (\App\Helpers\Auth::check()):
?>
<aside
    id="sessionTimer"
    class="session-timer"
    data-idle-seconds="<?= (int) _IDLE_TIMEOUT ?>"
    data-storage-key="<?= _SITENAME ?>_last_activity"
    aria-live="polite"
    title="You will be asked to resume the session after a few minutes of inactivity."
>
    <i class="fa fa-hourglass-half" aria-hidden="true"></i>
    <span class="session-timer-copy">
        <small id="sessionLifetime">Still there?</small>
        <strong>Session pauses in <span id="sessionIdleCountdown">--:--</span></strong>
    </span>
</aside>
<script src="<?= $siteConfig->assetsUrl ?>/scripts/session.js?v=<?= _ASSET_VERSION ?>" defer></script>
<?php endif; ?>
