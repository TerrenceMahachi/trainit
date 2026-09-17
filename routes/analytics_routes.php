<?php

use App\Controllers\AnalyticsController;

global $router;

$router->addRoute('GET', '/admin/analytics', function () {
    (new AnalyticsController())->index();
    exit;
});

$router->addRoute('GET', '/admin/reports', function () {
    (new AnalyticsController())->index();
    exit;
});

$router->addRoute('GET', '/admin/analytics/export', function () {
    (new AnalyticsController())->exportCsv();
    exit;
});
