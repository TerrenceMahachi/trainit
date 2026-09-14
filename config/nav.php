<?php
/**
 * Main navigation, as data.
 *
 * Edit THIS file to change the menu for a new project — the layout renders it
 * generically (see views/partials/nav.php), so you never touch nav markup.
 *
 * Each item:
 *   label    - text shown
 *   url      - path appended to the site URL (omit for a pure dropdown parent)
 *   roles    - who sees it: any of 'guest' (logged out), 'user' (role 2),
 *              'admin' (role 1). Empty/omitted = everyone.
 *   match    - substring of the current URL that marks the item "active"
 *              (defaults to url)
 *   children - array of sub-items (renders as a dropdown)
 */

return [
    ['label' => 'Home',          'url' => defined('_WEBSITE_URL') ? _WEBSITE_URL : '/home', 'roles' => ['guest']],
    ['label' => 'About',         'url' => '/about',         'roles' => ['guest']],
    ['label' => 'Services',      'url' => '/services',      'roles' => ['guest']],
    ['label' => 'Opportunities', 'url' => '/opportunities', 'roles' => ['guest', 'user', 'staff', 'admin', 'vetting', 'manager', 'finance', 'client']],
    ['label' => 'Mobile App',    'url' => '/mobile',        'roles' => ['guest', 'user', 'staff', 'admin', 'vetting', 'manager', 'finance', 'client']],
    ['label' => 'Register',      'url' => '/register',      'roles' => ['guest']],
    ['label' => 'Cloud',         'url' => '/cloud',         'roles' => ['guest']],
    ['label' => 'Contact',       'url' => '/contact',       'roles' => ['guest']],
    
    // Universal Dashboard for logged-in users (routes to role-specific view)
    ['label' => 'Dashboard',     'url' => '/dashboard',     'roles' => ['user', 'staff', 'admin', 'vetting', 'manager', 'finance']],

    // Client Enterprise Portal (Role 3)
    [
        'label' => 'Client Desk',
        'roles' => ['client'],
        'match' => '/client',
        'children' => [
            ['label' => 'Overview Workspace', 'url' => '/client/portal'],
            ['label' => 'Work Requests', 'url' => '/client/requests'],
            ['label' => 'Submit New Request', 'url' => '/client/requests/new'],
            ['label' => 'Retainer Service Plans', 'url' => '/client/plans'],
            ['label' => 'Team Roster', 'url' => '/client/team'],
            ['label' => 'Tax Invoices & Billing', 'url' => '/client/invoices'],
        ],
    ],

    // Staff Portal for internal staff employees (Overview, Payslips, Leave, Documents, Timesheets)
    [
        'label' => 'Staff Portal',
        'roles' => ['staff'],
        'match' => '/staff',
        'children' => [
            ['label' => 'Staff Hub Overview',    'url' => '/staff/portal',            'icon' => 'fa fa-user-circle'],
            ['label' => 'My Payslips',          'url' => '/staff/portal/payslips',   'icon' => 'fa fa-money-check-dollar'],
            ['label' => 'Apply for Leave',       'url' => '/staff/portal/leave',      'icon' => 'fa fa-calendar-check'],
            ['label' => 'Compliance Documents',  'url' => '/staff/portal/documents',  'icon' => 'fa fa-folder-open'],
            ['label' => 'Log Timesheets',        'url' => '/staff/portal/timesheets', 'icon' => 'fa fa-clock'],
        ],
    ],

    // Vetting Officer Dedicated Menu (Role 8)
    [
        'label' => 'Vetting Desk',
        'roles' => ['vetting'],
        'match' => '/admin',
        'children' => [
            ['label' => 'Talent Vetting Pipeline', 'url' => '/admin/roster'],
            ['label' => 'Compliance Radar & Expiry Tracker', 'url' => '/admin/compliance'],
            ['label' => 'Recruitment & Vacancies', 'url' => '/admin/vacancies'],
            ['label' => 'Candidate Vacancy Alerts', 'url' => '/admin/candidate-alerts'],
        ],
    ],

    // Service Delivery Manager Dedicated Menu (Role 6)
    [
        'label' => 'Service Desk',
        'roles' => ['manager'],
        'match' => '/admin',
        'children' => [
            ['label' => 'Service Requests & Dispatch', 'url' => '/admin/requests'],
            ['label' => 'Client Organizations & Retainers', 'url' => '/admin/clients'],
            ['label' => 'Client Onboarding Queue', 'url' => '/admin/clients/onboarding'],
            ['label' => 'Service Catalogue & SLA', 'url' => '/admin/service-catalogue'],
            ['label' => 'Talent Roster Review', 'url' => '/admin/roster'],
        ],
    ],

    // Billing & Finance Officer Dedicated Menu (Role 7)
    [
        'label' => 'Billing Desk',
        'roles' => ['finance'],
        'match' => '/admin',
        'children' => [
            ['label' => 'Client Invoices & Settlements', 'url' => '/client/invoices'],
            ['label' => 'Staff Payroll & Payslips', 'url' => '/admin/payroll'],
            ['label' => 'NSSA Form P4 Export', 'url' => '/admin/staff/export-p4'],
            ['label' => 'Client Organizations & Retainers', 'url' => '/admin/clients'],
        ],
    ],

    // Administrator Only Links (Role 1)
    ['label' => 'Staff Directory', 'url' => '/admin/staff', 'roles' => ['admin']],
    [
        'label' => 'Admin',
        'roles' => ['admin'],
        'match' => '/admin',
        'children' => [
            ['label' => 'Executive Analytics & Reports', 'url' => '/admin/analytics'],
            ['label' => 'Talent Vetting Pipeline', 'url' => '/admin/roster'],
            ['label' => 'Compliance Radar & Expiry Tracker', 'url' => '/admin/compliance'],
            ['label' => 'Recruitment & Vacancies', 'url' => '/admin/vacancies'],
            ['label' => 'Candidate Vacancy Alerts', 'url' => '/admin/candidate-alerts'],
            ['label' => 'Client Organizations & Retainers', 'url' => '/admin/clients'],
            ['label' => 'Client Invoices & Tax Billing', 'url' => '/client/invoices'],
            ['label' => 'Service Requests & Dispatch', 'url' => '/admin/requests'],
            ['label' => 'Service Catalogue & SLA', 'url' => '/admin/service-catalogue'],
            ['label' => 'Client Onboarding Queue', 'url' => '/admin/clients/onboarding'],
            ['label' => 'Approvals & Compliance Queue', 'url' => '/admin/staff-approvals'],
            ['label' => 'Staff Directory (NSSA P4)', 'url' => '/admin/staff'],
            ['label' => 'Staff Payroll & Payslips', 'url' => '/admin/payroll'],
            ['label' => 'Register Staff Member', 'url' => '/admin/staff/create'],
            ['label' => 'User Accounts', 'url' => '/users'],
            ['label' => 'System Dictionaries', 'url' => '/userprofiles'],
        ],
    ],
];
