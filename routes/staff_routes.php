<?php

use App\Controllers\StaffController;
use App\Helpers\Auth;

global $router;

// 1. Staff Directory & Operations Management (Admin Only)
$router->addRoute('GET', '/admin/staff', function () {
    (new StaffController())->index();
    exit;
});

// 2. Admin Direct Staff Registration
$router->addRoute('GET', '/admin/staff/create', function () {
    (new StaffController())->create();
    exit;
});

$router->addRoute('POST', '/admin/staff/store', function () {
    (new StaffController())->store();
    exit;
});

// 3. Issue Staff Onboarding Invitation
$router->addRoute('POST', '/admin/staff/invite', function () {
    (new StaffController())->sendInvite();
    exit;
});

// 4. View Staff Dossier
$router->addRoute('GET', '/admin/staff/view/:id', function ($id) {
    (new StaffController())->view($id);
    exit;
});

// 5. Edit & Update Staff Profile
$router->addRoute('GET', '/admin/staff/edit/:id', function ($id) {
    (new StaffController())->edit($id);
    exit;
});

$router->addRoute('POST', '/admin/staff/update', function () {
    (new StaffController())->update();
    exit;
});

// 6. Export NSSA Form P4 CSV Batch
$router->addRoute('GET', '/admin/staff/export-p4', function () {
    (new StaffController())->exportP4();
    exit;
});

// 7. Public Staff Statutory Onboarding Wizard (Invitation Token Authenticated)
$router->addRoute('GET', '/staff/onboard', function () {
    (new StaffController())->showOnboarding();
    exit;
});

$router->addRoute('POST', '/staff/onboard', function () {
    (new StaffController())->handleOnboardingSubmit();
    exit;
});
