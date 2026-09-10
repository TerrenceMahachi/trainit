<?php
/**
 * Public front controller — the ONLY web-reachable PHP entry point.
 * All application code lives one level up, outside this public/ directory.
 */

// Enable CORS for API routes
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
    } else {
        // Fallback for requests without Origin header (e.g., direct API calls)
        header("Access-Control-Allow-Origin: *");
    }
    header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token, Authorization");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Credentials: true");
    
    // Respond to preflight OPTIONS requests immediately
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit;
    }
}

require __DIR__ . '/../bootstrap.php';

use App\Helpers\SiteConfig;
use App\Router;

$siteConfig = new SiteConfig(
    _BASEURL,                                          // Site URL
    assetsUrl: _ASSETSURL,                             // Assets URL
    assetsLoc: _ASSETS_PATH,                           // Assets location on disk
    navLoc: _ASSETS_PATH . '/nav/ajax-pagination.php', // Pagination include
    siteName: _SITEDISPLAYNAME,
    defaultEmail: (defined('_DEFAULT_EMAIL') ? _DEFAULT_EMAIL : 'support@tsigiro.co.zw')
);

// Shared SQLite connection for legacy helpers that use `global $conn`.
$conn = (new App\Models\Database())->getPDO();

// Ensure every visitor has a CSRF token cookie (double-submit pattern);
// browser AJAX echoes it back via the X-CSRF-Token header (see common.js).
App\Helpers\Csrf::token();

$site = $siteConfig->siteUrl;
$sitename = $siteConfig->siteName;

// Always log errors; only display them in development.
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', _BASE_PATH . '/storage/php_errors.log'); // outside web reach (storage/ is blocked)
if (_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
}

$router = new Router();
foreach (glob(_ROUTES_PATH . '/*.php') as $routeFile) {
    include $routeFile;
}

try {
    $router->matchRoute();
} catch (Exception $e) {
    error_log('Routing error: ' . $e->getMessage());
    http_response_code(404);
    $data = [
        'status' => 'failed',
        'response_code' => '404',
        'message' => 'Page not found',
        'title' => 'Not Found',
    ];
    echo view('errors.404', compact('data'));
}
