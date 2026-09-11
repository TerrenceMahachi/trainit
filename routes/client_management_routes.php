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

// 6. Client Self-Service Portal Overview
$router->addRoute('GET', '/client/portal', function () {
    (new ClientController())->portal();
    exit;
});

// 7. Client Dedicated Work Request Brief Builder
$router->addRoute('GET', '/client/requests/new', function () {
    (new ClientController())->newRequest();
    exit;
});

// 8. Client Work Requests Ledger & Status Filters
$router->addRoute('GET', '/client/requests', function () {
    (new ClientController())->requests();
    exit;
});

// 9. Client Request Delivery Workspace & Collaboration
$router->addRoute('GET', '/client/requests/view/:id', function ($id) {
    (new ClientController())->viewRequest((int)$id);
    exit;
});

// 10. Client Retainer Subscriptions & Hours Capacity
$router->addRoute('GET', '/client/plans', function () {
    (new ClientController())->plans();
    exit;
});

// 11. Client Organization Profile & Authorized Team Roster
$router->addRoute('GET', '/client/team', function () {
    (new ClientController())->team();
    exit;
});

// 12. Service Catalogue & SLA Policies Framework
$router->addRoute('GET', '/admin/service-catalogue', function () {
    (new ServiceCatalogueController())->index();
    exit;
});

// 13. Client Monthly Invoices & Billing Statements
$router->addRoute('GET', '/client/invoices', function () {
    (new ClientController())->invoices();
    exit;
});

// 14. Client View Individual Detailed Invoice Statement
$router->addRoute('GET', '/client/invoices/view/:id', function ($id) {
    (new ClientController())->viewInvoice((int)$id);
    exit;
});

// 15. Admin / Billing Desk All Invoices Overview
$router->addRoute('GET', '/admin/invoices', function () {
    (new ClientController())->invoices();
    exit;
});

// 16. Dynamic Invoice PDF Generation & Streaming
$router->addRoute('GET', '/client/invoices/pdf/:id', function ($id) {
    (new ClientController())->downloadInvoicePdf((int)$id);
    exit;
});

$router->addRoute('GET', '/admin/invoices/pdf/:id', function ($id) {
    (new ClientController())->downloadInvoicePdf((int)$id);
    exit;
});

// 17. Client Submit Proof of Payment (POP)
$router->addRoute('POST', '/client/invoices/submit-payment', function () {
    (new ClientController())->submitPaymentAction();
    exit;
});

// 18. Billing Desk Reconcile / Approve Payment
$router->addRoute('POST', '/admin/invoices/reconcile-payment', function () {
    (new ClientController())->reconcilePaymentAction();
    exit;
});


