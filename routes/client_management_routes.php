<?php
use App\Controllers\ClientController;
use App\Controllers\ServiceCatalogueController;

global $router;

// 1. Client Organizations Directory (Admin)
$router->addRoute('GET', '/admin/clients', function () {
    (new ClientController())->index();
    exit;
});

// 2. Client 360° Profile & Retainer Plans (Admin)
$router->addRoute('GET', '/admin/clients/view/:id', function ($id) {
    (new ClientController())->view($id);
    exit;
});

// 3. Register Client Organization
$router->addRoute('POST', '/admin/clients/create', function () {
    (new ClientController())->createAction();
    exit;
});

// 4. Subscribe Retainer Plan
$router->addRoute('POST', '/admin/clients/assign-plan', function () {
    (new ClientController())->assignPlanAction();
    exit;
});

// 5. Terminate Retainer Plan (Zero-null event)
$router->addRoute('POST', '/admin/clients/terminate-plan', function () {
    (new ClientController())->terminatePlanAction();
    exit;
});

// 6. Client Self-Service Portal
$router->addRoute('GET', '/client/portal', function () {
    (new ClientController())->portal();
    exit;
});

// 7. Service Catalogue & SLA Policies Framework
$router->addRoute('GET', '/admin/service-catalogue', function () {
    (new ServiceCatalogueController())->index();
    exit;
});
