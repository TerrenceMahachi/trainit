<?php
use App\Controllers\RequestController;

global $router;

// 1. Service Delivery & Dispatch Desk (Admin)
$router->addRoute('GET', '/admin/requests', function () {
    (new RequestController())->index();
    exit;
});

// 2. Request Workspace & Threaded Stream
$router->addRoute('GET', '/admin/requests/view/:id', function ($id) {
    (new RequestController())->view($id);
    exit;
});

// 3. Submit New Service Request
$router->addRoute('POST', '/client/requests/submit', function () {
    (new RequestController())->submitRequestAction();
    exit;
});

// 4. Triage Ticket & Commit SLA
$router->addRoute('POST', '/admin/requests/triage', function () {
    (new RequestController())->triageAction();
    exit;
});

// 5. Dispatch Talent & Supervisor Mentor
$router->addRoute('POST', '/admin/requests/assign', function () {
    (new RequestController())->assignTalentAction();
    exit;
});

// 6. Update Request Status
$router->addRoute('POST', '/admin/requests/status', function () {
    (new RequestController())->updateStatusAction();
    exit;
});

// 7. Post Collaboration Message (Client vs Internal)
$router->addRoute('POST', '/requests/message', function () {
    (new RequestController())->postMessageAction();
    exit;
});

// 8. Complete & Close Engagement
$router->addRoute('POST', '/admin/requests/close', function () {
    (new RequestController())->closeRequestAction();
    exit;
});
