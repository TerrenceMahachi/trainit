(function () {
    'use strict';

    var timer = document.getElementById('sessionTimer');
    if (!timer) { return; }

    // Base URL for the resume redirect. `site` is defined globally in head.php;
    // fall back to a relative path if it is somehow missing.
    var base = (typeof site === 'string' && site) ? site : '';

    var idleSeconds = Number(timer.dataset.idleSeconds || 180);
    var idleCountdown = document.getElementById('sessionIdleCountdown');
    var lifetime = document.getElementById('sessionLifetime');
    var storageKey = timer.dataset.storageKey || 'app_last_activity';
    var lastActivity = Date.now();
    var lastRecorded = 0;
    var pausing = false;
    var warningSeconds = 30;

    function two(value) {
        return String(value).padStart(2, '0');
    }

    function recordActivity() {
        var now = Date.now();
        lastActivity = now;
        if (now - lastRecorded > 1000) {
            lastRecorded = now;
            try { localStorage.setItem(storageKey, String(now)); } catch (e) { /* storage can be disabled */ }
        }
        timer.classList.remove('session-timer-visible', 'session-timer-warning');
    }

    // Activity is shared across tabs via localStorage so a user working in one
    // tab does not get bounced to /resume in another.
    function sharedLastActivity() {
        try {
            var stored = Number(localStorage.getItem(storageKey) || 0);
            return Math.max(lastActivity, stored);
        } catch (e) {
            return lastActivity;
        }
    }

    function pauseSession() {
        if (pausing) { return; }
        pausing = true;
        var returnTo = window.location.pathname + window.location.search + window.location.hash;
        window.location.replace(base + '/resume?return=' + encodeURIComponent(returnTo));
    }

    function update() {
        var inactiveFor = Math.floor((Date.now() - sharedLastActivity()) / 1000);
        var remaining = Math.max(0, idleSeconds - inactiveFor);
        var minutes = Math.floor(remaining / 60);
        var seconds = remaining % 60;

        if (idleCountdown) { idleCountdown.textContent = two(minutes) + ':' + two(seconds); }
        if (lifetime) { lifetime.textContent = remaining <= warningSeconds ? 'No activity detected' : 'Still there?'; }
        timer.classList.toggle('session-timer-visible', remaining <= warningSeconds);
        timer.classList.toggle('session-timer-warning', remaining <= warningSeconds);

        if (remaining <= 0) { pauseSession(); }
    }

    ['pointerdown', 'keydown', 'scroll', 'touchstart'].forEach(function (eventName) {
        window.addEventListener(eventName, recordActivity, { passive: true });
    });
    window.addEventListener('storage', function (event) {
        if (event.key === storageKey && Number(event.newValue || 0) > lastActivity) {
            lastActivity = Number(event.newValue);
        }
    });
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) { update(); }
    });

    recordActivity();
    update();
    window.setInterval(update, 1000);
}());
