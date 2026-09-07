<?php
/**
 * Central project configuration.
 *
 * This is the ONE file you edit when spinning up a new project from this
 * boilerplate. Everything site-specific lives here instead of being scattered
 * across index.php and the controllers.
 */

// --- Identity -------------------------------------------------------------
define('_SITENAME', 'trainit');                 // URL slug / folder name
define('_SITE', 'Trainit');                     // Short name
define('_SITEDESCRIPTION', 'Practical technology, managed services, and client support for growing organisations.');
define('_SITEDISPLAYNAME', 'Trainit');           // Human-friendly display name

// --- URLs / paths ---------------------------------------------------------
// The app is served from the project root (public/ is the web root, mapped in
// via the root .htaccess), so URLs do NOT contain "/public".
define('_PROD_HOST', 'trainit.co.zw');
$__host = $_SERVER['HTTP_HOST'] ?? (getenv('APP_HOST') ?: '');
$__host = strtolower(preg_replace('/:\d+$/', '', $__host));
$__isProd = in_array($__host, [_PROD_HOST, 'www.' . _PROD_HOST], true);

if ($__isProd) {
    define('_BASEURL', 'https://' . $__host);
    define('_ASSETSURL', 'https://' . $__host . '/assets');
} else {
    define('_BASEURL', 'http://localhost/' . _SITENAME);
    define('_ASSETSURL', 'http://localhost/' . _SITENAME . '/assets');
}

// --- Environment ----------------------------------------------------------
// 'development' shows errors on-screen; 'production' hides them and logs only.
define('_ENV', $__isProd ? 'production' : 'development');

// Bump this when you change CSS/JS so browsers fetch the new version
// (used as ?v= on asset URLs instead of a random value that defeats caching).
define('_ASSET_VERSION', '20260820.2');

// --- Security -------------------------------------------------------------
// Secret used to sign the authentication cookie so it cannot be forged.
// REGENERATE THIS FOR EVERY NEW PROJECT, e.g.:
//   php -r "echo bin2hex(random_bytes(32));"
// Keep it private; anyone who has it can mint valid login cookies.
$appSecret = getenv('TRAINIT_APP_SECRET');
if (($appSecret === false || $appSecret === '') && $__isProd) {
    $secretFile = __DIR__ . '/storage/app_secret.key';
    if (!is_file($secretFile)) {
        file_put_contents($secretFile, bin2hex(random_bytes(32)), LOCK_EX);
        @chmod($secretFile, 0600);
    }
    $appSecret = trim((string) file_get_contents($secretFile));
}
define('_APP_SECRET', $appSecret !== false && $appSecret !== ''
    ? $appSecret
    : 'local-development-only-change-before-production');

// How long an authenticated session cookie stays valid (seconds). The sign-in
// itself is long-lived, but the session goes into a soft "resume" lock after
// _IDLE_TIMEOUT of no activity: the user re-enters the last three characters of
// their password instead of signing in again from scratch.
define('_AUTH_TTL', 60 * 60 * 24 * 30); // 30 days
define('_IDLE_TIMEOUT', 180);           // 3 minutes of inactivity → /resume
