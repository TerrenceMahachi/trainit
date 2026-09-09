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

// 8. Staff Document Vault (Upload & Audit Verification)
$router->addRoute('POST', '/admin/staff/documents/upload', function () {
    (new StaffController())->uploadDocumentAction();
    exit;
});

$router->addRoute('POST', '/admin/staff/documents/verify', function () {
    (new StaffController())->verifyDocumentAction();
    exit;
});

// 9. Staff Leave Management (Apply & Decision)
$router->addRoute('POST', '/staff/leave/apply', function () {
    (new StaffController())->applyLeaveAction();
    exit;
});

$router->addRoute('POST', '/admin/staff/leave/decide', function () {
    (new StaffController())->decideLeaveAction();
    exit;
});

// 10. Operational Time Logging & Sign-Off
$router->addRoute('POST', '/staff/time/log', function () {
    (new StaffController())->logTimeAction();
    exit;
});

$router->addRoute('POST', '/admin/staff/time/signoff', function () {
    (new StaffController())->signoffTimeAction();
    exit;
});

// 11. Staff Self-Service Hub
$router->addRoute('GET', '/staff/portal', function () {
    (new StaffController())->selfService();
    exit;
});

// 12. Unified Approvals & Compliance Queue (Admin)
$router->addRoute('GET', '/admin/staff-approvals', function () {
    (new StaffController())->approvalsQueue('documents');
    exit;
});
$router->addRoute('GET', '/admin/staff-approvals/documents', function () {
    (new StaffController())->approvalsQueue('documents');
    exit;
});
$router->addRoute('GET', '/admin/staff-approvals/expiring', function () {
    (new StaffController())->approvalsQueue('expiring');
    exit;
});
$router->addRoute('GET', '/admin/staff-approvals/leave', function () {
    (new StaffController())->approvalsQueue('leave');
    exit;
});
$router->addRoute('GET', '/admin/staff-approvals/timesheets', function () {
    (new StaffController())->approvalsQueue('timesheets');
    exit;
});


