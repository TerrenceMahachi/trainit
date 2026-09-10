<?php
/**
 * Central project configuration.
 *
 * This is the ONE file you edit when spinning up a new project from this
 * boilerplate. Everything site-specific lives here instead of being scattered
 * across index.php and the controllers.
 */

// --- Identity -------------------------------------------------------------
define('_SITENAME', 'tsigiro-portal');
define('_SITE', 'Tsigiro Portal');
define('_SITEDESCRIPTION', 'The secure shared portal for Tsigiro clients, staff and approved professionals.');
define('_SITEDISPLAYNAME', 'Tsigiro Portal');

// --- URLs / paths ---------------------------------------------------------
// The app is served from the project root (public/ is the web root, mapped in
// via the root .htaccess), so URLs do NOT contain "/public".
$requestHost = strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? ''));
$isProduction = in_array($requestHost, ['portal.tsigiro.co.zw', 'www.portal.tsigiro.co.zw'], true);
define('_BASEURL', $isProduction ? 'https://portal.tsigiro.co.zw' : 'http://localhost/tsigiro/portal');
define('_ASSETSURL', _BASEURL . '/assets');
define('_WEBSITE_URL', $isProduction ? 'https://tsigiro.co.zw' : 'http://localhost/tsigiro');

// --- Environment ----------------------------------------------------------
// 'development' shows errors on-screen; 'production' hides them and logs only.
define('_ENV', $isProduction ? 'production' : 'development');

// Bump this when you change CSS/JS so browsers fetch the new version
// (used as ?v= on asset URLs instead of a random value that defeats caching).
define('_ASSET_VERSION', '20260909.2');

// Accounts are issued after Tsigiro approves a recruitment or client
// onboarding submission. The portal itself is not a public registration form.
define('_ALLOW_PUBLIC_REGISTRATION', false);
define('_DEFAULT_EMAIL', 'support@tsigiro.co.zw');
define('_ENABLE_DEMO_MODULES', false);
define('_ENABLE_MOBILE_DOWNLOADS', false);

// --- Developer tools ------------------------------------------------------
define('_DEV_TOOLS', false);

// --- Security -------------------------------------------------------------
// Secret used to sign the authentication cookie so it cannot be forged.
$secretPath = __DIR__ . '/storage/app_secret.key';
$appSecret = is_readable($secretPath) ? trim((string) file_get_contents($secretPath)) : '';
if (!preg_match('/^[a-f0-9]{64}$/', $appSecret)) {
    if (!$isProduction) {
        $appSecret = '0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef';
    } else {
        throw new RuntimeException('The portal signing secret is missing or invalid.');
    }
}
define('_APP_SECRET', $appSecret);

// How long an authenticated session cookie stays valid (seconds).
define('_AUTH_TTL', 60 * 60 * 24 * 30); // 30 days
// Idle window before the session soft-locks to the /resume challenge.
define('_IDLE_TIMEOUT', 60 * 30);       // 30 minutes of inactivity → /resume
