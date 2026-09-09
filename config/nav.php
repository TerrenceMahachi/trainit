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
    ['label' => 'Opportunities', 'url' => '/opportunities', 'roles' => ['guest', 'user', 'admin']],
    ['label' => 'Cloud',         'url' => '/cloud',         'roles' => ['guest']],
    ['label' => 'Contact',       'url' => '/contact',       'roles' => ['guest']],
    ['label' => 'Dashboard',     'url' => '/dashboard',     'roles' => ['user', 'admin']],
    ['label' => 'Staff Portal',  'url' => '/staff/portal',  'roles' => ['admin', 'user']],
    ['label' => 'Staff Directory', 'url' => '/admin/staff', 'roles' => ['admin']],

    [
        'label' => 'Admin',
        'roles' => ['admin'],
        'match' => '/admin',
        'children' => [
            ['label' => 'Approvals & Compliance Queue', 'url' => '/admin/staff-approvals'],
            ['label' => 'Staff Directory (NSSA P4)', 'url' => '/admin/staff'],
            ['label' => 'Staff Payroll & Payslips', 'url' => '/admin/payroll'],
            ['label' => 'Register Staff Member', 'url' => '/admin/staff/create'],
            ['label' => 'Client Organizations & Retainers', 'url' => '/admin/clients'],
            ['label' => 'Service Requests & Dispatch', 'url' => '/admin/requests'],
            ['label' => 'Service Catalogue & SLA', 'url' => '/admin/service-catalogue'],
            ['label' => 'Talent Vetting Pipeline', 'url' => '/admin/roster'],
            ['label' => 'User Accounts', 'url' => '/users'],
            ['label' => 'Rosterdocuments', 'url' => '/rosterdocuments'],
            ['label' => 'Rosterstatusevents', 'url' => '/rosterstatusevents'],
            // BOILERPLATE_ADMIN_NAV_ITEMS
        ],
    ],
];
