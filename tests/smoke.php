<?php
/**
 * Smoke test: hits the running site over HTTP and asserts the basics work.
 *
 *   php tests/smoke.php            # tests this project (slug from config.php)
 *   php tests/smoke.php myshop     # tests a sibling project by slug
 *
 * Exits 0 when everything passes, 1 otherwise.
 */

$root = dirname(__DIR__);
require $root . '/config.php';

$slug = $argv[1] ?? _SITENAME;
$base = preg_match('#^https?://#', $slug) ? rtrim($slug, '/') : "http://localhost/$slug";

$failures = 0;

function http($method, $url, $data = null, $cookies = '')
{
    $opts = [
        'http' => [
            'method' => $method,
            'ignore_errors' => true,
            'header' => "Accept: */*\r\n" . ($cookies ? "Cookie: $cookies\r\n" : ''),
            'timeout' => 10,
        ],
    ];
    if ($data !== null) {
        $body = http_build_query($data);
        $opts['http']['content'] = $body;
        $opts['http']['header'] .= "Content-Type: application/x-www-form-urlencoded\r\n";
    }
    $bodyOut = @file_get_contents($url, false, stream_context_create($opts));
    $status = 0;
    foreach ($http_response_header ?? [] as $h) {
        if (preg_match('#^HTTP/\S+\s+(\d{3})#', $h, $m)) {
            $status = (int) $m[1]; // last one wins (follows redirects)
        }
    }
    return [$status, (string) $bodyOut];
}

function check($label, $ok, $detail = '')
{
    global $failures;
    if ($ok) {
        echo "  PASS  $label\n";
    } else {
        $failures++;
        echo "  FAIL  $label" . ($detail !== '' ? "  ($detail)" : '') . "\n";
    }
}

echo "Smoke-testing $base\n\n";

// --- public pages -----------------------------------------------------------
foreach (['/', '/home', '/about', '/services', '/opportunities', '/cloud', '/contact', '/login', '/register', '/reset'] as $page) {
    [$s] = http('GET', $base . $page);
    check("GET $page -> 200", $s === 200, "got $s");
}

// --- 404 handling ------------------------------------------------------------
[$s] = http('GET', $base . '/definitely-not-a-real-route');
check("GET unknown route -> 404", $s === 404, "got $s");

// --- internals must be blocked ------------------------------------------------
foreach (['/database/app.db', '/config.php', '/app/Router.php', '/vendor/autoload.php', '/storage/php_errors.log'] as $path) {
    [$s] = http('GET', $base . $path);
    check("GET $path -> blocked", in_array($s, [403, 404], true), "got $s");
}

// --- auth: unknown login rejected with JSON ------------------------------------
[$s, $b] = http('POST', $base . '/validate-login', ['email' => 'smoke-nobody@example.com', 'password' => 'x']);
$json = json_decode($b, true);
check("POST /validate-login (bad creds) -> JSON status 0", $s === 200 && is_array($json) && ($json['status'] ?? null) == 0, "status=$s body=" . substr($b, 0, 80));

// --- auth gate: protected page redirects without a valid signed cookie ---------
foreach (["", "user=1"] as $ck) {
    [$s, $b] = http('GET', $base . '/users', null, $ck);
    // follow_location is on by default, so a redirect lands on /home (200
    // without the users table) — detect by the final URL content instead.
    $gated = strpos($b, 'id="searchForm"') === false; // users list has the search form
    check("GET /users " . ($ck ? "(forged cookie)" : "(no cookie)") . " -> gated", $gated);
}

// --- CSRF: authed-style POST without token is rejected --------------------------
[$s, $b] = http('POST', $base . '/update-profile', ['name' => 'x', 'email' => 'x@x.com', 'password' => 'x']);
$json = json_decode($b, true);
check("POST /update-profile (no auth/CSRF) -> rejected", is_array($json) && ($json['status'] ?? '') === '2', substr($b, 0, 80));

echo "\n" . ($failures === 0 ? "ALL CHECKS PASSED" : "$failures CHECK(S) FAILED") . "\n";
exit($failures === 0 ? 0 : 1);
