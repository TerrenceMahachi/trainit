<?php
/**
 * Application bootstrap.
 *
 * This file lives at the PROJECT ROOT (one level above the public web root).
 * It defines absolute path constants so the application never depends on the
 * current working directory, registers autoloading, and provides the view
 * engine. The public front controller (public/index.php) is the only
 * web-reachable entry point and simply requires this file.
 */

// --- Absolute paths (never rely on CWD) -----------------------------------
define('_BASE_PATH', __DIR__);                       // project root
define('_APP_PATH', _BASE_PATH . '/app');            // application classes
define('_VIEWS_PATH', _BASE_PATH . '/views');        // server-side templates
define('_ROUTES_PATH', _BASE_PATH . '/routes');      // route definitions
define('_ASSETS_PATH', _BASE_PATH . '/assets');      // asset files on disk
define('_DB_PATH', _BASE_PATH . '/database/app.db'); // SQLite database file

// --- Site configuration (identity, URLs, secret) --------------------------
require _BASE_PATH . '/config.php';

// --- Autoloading -----------------------------------------------------------
// PSR-4: the "App\" namespace maps to the app/ directory.
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = _APP_PATH . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// Load all procedural helper files.
foreach (glob(_APP_PATH . '/Helpers/*.php') as $file) {
    require_once $file;
}

// Composer dependencies, if installed.
if (is_file(_BASE_PATH . '/vendor/autoload.php')) {
    require_once _BASE_PATH . '/vendor/autoload.php';
}

/**
 * Render a view, resolving @extends / @include directives.
 */
function view($view, $data = [])
{
    $view = str_replace('.', '/', $view);
    global $siteConfig;

    $viewFile = _VIEWS_PATH . "/{$view}.php";

    if (!file_exists($viewFile)) {
        error_log("View file not found: $viewFile");
        return "View file not found: $viewFile";
    }

    extract($data);

    ob_start();
    include $viewFile;
    $content = ob_get_clean();

    $content = processIncludes($content, $data);

    if (preg_match('/@extends\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
        $layoutView = str_replace('.', '/', $matches[1]);
        $layoutFile = _VIEWS_PATH . "/{$layoutView}.php";

        if (!file_exists($layoutFile)) {
            error_log("Layout file not found: $layoutFile");
            return "Layout file not found: $layoutFile";
        }

        $content = preg_replace('/@extends\([\'"][^\'"]+[\'"]\)/', '', $content);

        ob_start();
        include $layoutFile;
        $layoutContent = ob_get_clean();

        $layoutContent = processIncludes($layoutContent, $data);
        $layoutContent = str_replace('@yield(\'content\')', $content, $layoutContent);

        return $layoutContent;
    }

    return $content;
}

/**
 * Safely parse a simple array literal of the form
 *   ['key' => 'value', 'other' => "text"]
 * from an @include(...) directive, without using eval().
 */
function parseArrayLiteral($literal)
{
    $result = [];
    $inner = trim($literal);
    $inner = preg_replace('/^\[|\]$/', '', $inner);
    if (trim($inner) === '') {
        return $result;
    }

    foreach (explode(',', $inner) as $pair) {
        if (strpos($pair, '=>') === false) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=>', $pair, 2));
        $key   = trim($key, "'\"");
        $value = trim($value, "'\"");
        $result[$key] = $value;
    }

    return $result;
}

function processIncludes($content, $data = [])
{
    while (preg_match('/@include\([\'"]([^\'"]+)[\'"](?:,\s*(\[[^\]]*\]))?\)/', $content, $matches)) {
        $includeView = str_replace('.', '/', $matches[1]);
        $includeFile = _VIEWS_PATH . "/{$includeView}.php";

        // No eval(): only a simple ['key' => 'value'] literal is understood.
        $includeData = isset($matches[2]) ? parseArrayLiteral($matches[2]) : [];

        $mergedData = array_merge($data, $includeData);

        if (file_exists($includeFile)) {
            extract($mergedData);

            ob_start();
            include $includeFile;
            $includeContent = ob_get_clean();

            $content = str_replace($matches[0], $includeContent, $content);
        } else {
            error_log("Include file not found: $includeFile");
            $content = str_replace($matches[0], "Include file not found: $includeFile", $content);
        }
    }

    return $content;
}
