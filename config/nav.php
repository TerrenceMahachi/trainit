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
    ['label' => 'Home',      'url' => '/home',      'roles' => ['guest', 'user', 'admin']],
    ['label' => 'About',     'url' => '/about',     'roles' => ['guest', 'user', 'admin']],
    ['label' => 'Services',  'url' => '/services',  'roles' => ['guest', 'user', 'admin']],
    ['label' => 'Opportunities', 'url' => '/opportunities', 'roles' => ['guest', 'user', 'admin']],
    ['label' => 'Cloud',     'url' => '/cloud',     'roles' => ['guest', 'user', 'admin']],
    ['label' => 'Contact',   'url' => '/contact',   'roles' => ['guest', 'user', 'admin']],
    ['label' => 'Dashboard', 'url' => '/dashboard', 'roles' => ['user', 'admin']],
    ['label' => 'Staff Directory', 'url' => '/admin/staff', 'roles' => ['admin']],

    [
        'label' => 'Admin',
        'roles' => ['admin'],
        'match' => '/admin',
        'children' => [
            ['label' => 'Staff Directory (NSSA P4)', 'url' => '/admin/staff'],
            ['label' => 'Register Staff Member', 'url' => '/admin/staff/create'],
            ['label' => 'Talent Vetting Pipeline', 'url' => '/admin/roster'],
            ['label' => 'User Accounts', 'url' => '/users'],
            ['label' => 'Applicationtracks', 'url' => '/applicationtracks'],
            ['label' => 'Applicationstatuss', 'url' => '/applicationstatuss'],
            ['label' => 'Servicefunctions', 'url' => '/servicefunctions'],
            ['label' => 'Proficiencylevels', 'url' => '/proficiencylevels'],
            ['label' => 'Skillitems', 'url' => '/skillitems'],
            ['label' => 'Zimprovinces', 'url' => '/zimprovinces'],
            ['label' => 'Workrightstatuss', 'url' => '/workrightstatuss'],
            ['label' => 'Apprenticestatuss', 'url' => '/apprenticestatuss'],
            ['label' => 'Employmentstatuss', 'url' => '/employmentstatuss'],
            ['label' => 'Qualificationtypes', 'url' => '/qualificationtypes'],
            ['label' => 'Qualificationstatuss', 'url' => '/qualificationstatuss'],
            ['label' => 'Professionalbodys', 'url' => '/professionalbodys'],
            ['label' => 'Sectortypes', 'url' => '/sectortypes'],
            ['label' => 'Engagementbasiss', 'url' => '/engagementbasiss'],
            ['label' => 'Engagementmodels', 'url' => '/engagementmodels'],
            ['label' => 'Worklocationpreferences', 'url' => '/worklocationpreferences'],
            ['label' => 'Invoiceentitytypes', 'url' => '/invoiceentitytypes'],
            ['label' => 'Refereecontacttimings', 'url' => '/refereecontacttimings'],
            ['label' => 'Refereeverificationstatuss', 'url' => '/refereeverificationstatuss'],
            ['label' => 'Vettingrecommendations', 'url' => '/vettingrecommendations'],
            ['label' => 'Rosterapplications', 'url' => '/rosterapplications'],
            ['label' => 'Apprenticeprofiles', 'url' => '/apprenticeprofiles'],
            ['label' => 'Associateprofiles', 'url' => '/associateprofiles'],
            ['label' => 'Rosterskills', 'url' => '/rosterskills'],
            ['label' => 'Rosterqualifications', 'url' => '/rosterqualifications'],
            ['label' => 'Rosterworkhistorys', 'url' => '/rosterworkhistorys'],
            ['label' => 'Rosterreferees', 'url' => '/rosterreferees'],
            ['label' => 'Rosterjudgementresponses', 'url' => '/rosterjudgementresponses'],
            ['label' => 'Rosterassessments', 'url' => '/rosterassessments'],
            ['label' => 'Rosteronboardings', 'url' => '/rosteronboardings'],
            ['label' => 'Documenttypes', 'url' => '/documenttypes'],
            ['label' => 'Verificationstatuss', 'url' => '/verificationstatuss'],
            ['label' => 'Leavetypes', 'url' => '/leavetypes'],
            ['label' => 'Leavestatuss', 'url' => '/leavestatuss'],
            ['label' => 'Activitycategorys', 'url' => '/activitycategorys'],
            ['label' => 'Departments', 'url' => '/departments'],
            ['label' => 'Staffdocuments', 'url' => '/staffdocuments'],
            ['label' => 'Staffleaves', 'url' => '/staffleaves'],
            ['label' => 'Stafftimeentrys', 'url' => '/stafftimeentrys'],
            ['label' => 'Documentverifications', 'url' => '/documentverifications'],
            ['label' => 'Documentvaliditys', 'url' => '/documentvaliditys'],
            ['label' => 'Staffleaveapprovals', 'url' => '/staffleaveapprovals'],
            ['label' => 'Staffleaveattachments', 'url' => '/staffleaveattachments'],
            ['label' => 'Stafftimeapprovals', 'url' => '/stafftimeapprovals'],
            ['label' => 'Staffdepartmentassignments', 'url' => '/staffdepartmentassignments'],
            // BOILERPLATE_ADMIN_NAV_ITEMS
        ],
    ],
];
