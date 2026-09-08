<?php
use App\Controllers\PayrollController;

global $router;

// 1. Payroll Cycles & Summary (Admin)
$router->addRoute('GET', '/admin/payroll', function () {
    (new PayrollController())->index();
    exit;
});

// 2. View Specific Payroll Period Ledger
$router->addRoute('GET', '/admin/payroll/view/:id', function ($id) {
    (new PayrollController())->viewPeriod($id);
    exit;
});

// 3. Initialize New Payroll Period
$router->addRoute('POST', '/admin/payroll/create', function () {
    (new PayrollController())->createPeriodAction();
    exit;
});

// 4. Compute Payroll Calculations & Line Items
$router->addRoute('POST', '/admin/payroll/calculate', function () {
    (new PayrollController())->calculateAction();
    exit;
});

// 5. Executive Approve Payroll Run
$router->addRoute('POST', '/admin/payroll/approve', function () {
    (new PayrollController())->approveAction();
    exit;
});

// 6. Disburse Salaries via Bank Transfer
$router->addRoute('POST', '/admin/payroll/disburse', function () {
    (new PayrollController())->disburseAction();
    exit;
});

// 7. View Printable Staff Payslip Document
$router->addRoute('GET', '/admin/payroll/payslip/:id', function ($id) {
    (new PayrollController())->viewPayslip($id);
    exit;
});

// 8. Record Statutory Return Filing
$router->addRoute('POST', '/admin/payroll/statutory-return', function () {
    (new PayrollController())->statutoryReturnAction();
    exit;
});
