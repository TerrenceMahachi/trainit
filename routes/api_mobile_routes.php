<?php

use App\Controllers\AccountController;
use App\Helpers\Auth;
use App\Helpers\NotificationHelper;
use App\Models\Database;
use App\Models\User;

global $router;

/**
 * Mobile API Helper: Sends JSON response with appropriate headers and exits.
 */
if (!function_exists('sendMobileJson')) {
    function sendMobileJson($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Enrich user with specialized talent/persona descriptor and multi-profile list
 */
function enrichMobileUser($user) {
    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;
    $role = (int) $user->role;

    // Fetch all user profiles from userprofile table
    $stmt = $db->prepare("
        SELECT up.iD as profile_id, up.user as user_id, up.profiletype as type_id,
               pt.code as type_code, pt.name as type_name, pt.description as type_description, pt.icon as type_icon,
               ps.code as status_code, ps.name as status_name, ps.badge_class, ps.can_access_portal,
               up.display_title, up.is_default, up.request_notes, up.reviewer_notes, up.reviewed_at, up.reg_date
        FROM userprofile up
        JOIN profiletype pt ON up.profiletype = pt.iD
        JOIN profilestatus ps ON up.profilestatus = ps.iD
        WHERE up.user = ?
        ORDER BY up.is_default DESC, pt.sort_order ASC
    ");
    $stmt->execute([$userId]);
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Identify active profile (default = 1 and approved)
    $activeProfile = null;
    foreach ($profiles as $p) {
        if (!empty($p['is_default']) && (int)$p['can_access_portal'] === 1) {
            $activeProfile = $p;
            break;
        }
    }
    if (!$activeProfile && !empty($profiles)) {
        // Fallback to first approved profile
        foreach ($profiles as $p) {
            if ((int)$p['can_access_portal'] === 1) {
                $activeProfile = $p;
                break;
            }
        }
    }

    $roleName = $user->role_name ?: 'General User';
    $persona = 'user';

    if ($activeProfile) {
        $typeCode = $activeProfile['type_code'];
        if ($typeCode === 'staff') {
            if ($role === 1) {
                $persona = 'admin';
                $roleName = $activeProfile['display_title'] ?: 'Administrator';
            } elseif ($role === 8) {
                $persona = 'vetting';
                $roleName = $activeProfile['display_title'] ?: 'Vetting Officer';
            } elseif ($role === 6) {
                $persona = 'manager';
                $roleName = $activeProfile['display_title'] ?: 'Service Manager';
            } elseif ($role === 7) {
                $persona = 'finance';
                $roleName = $activeProfile['display_title'] ?: 'Billing Officer';
            } else {
                $persona = 'staff';
                $roleName = $activeProfile['display_title'] ?: 'Staff Member';
            }
        } elseif ($typeCode === 'client') {
            $persona = 'client';
            $roleName = $activeProfile['display_title'] ?: 'Client Representative';
        } elseif ($typeCode === 'associate') {
            $persona = 'associate';
            $roleName = $activeProfile['display_title'] ?: 'Associate Specialist';
        } elseif ($typeCode === 'apprentice') {
            $persona = 'apprentice';
            $roleName = $activeProfile['display_title'] ?: 'Apprentice Engineer';
        } elseif ($typeCode === 'general') {
            if ($role === 1) {
                $persona = 'admin';
                $roleName = 'Administrator';
            } elseif ($role === 8) {
                $persona = 'vetting';
                $roleName = 'Vetting Officer';
            } elseif ($role === 6) {
                $persona = 'manager';
                $roleName = 'Service Manager';
            } elseif ($role === 7) {
                $persona = 'finance';
                $roleName = 'Billing Officer';
            } else {
                $persona = 'candidate';
                $roleName = ($role === 1) ? 'Administrator' : 'General User';
            }
        }
    } else {
        // Fallback for legacy role numbers if userprofile not yet initialized
        if ($role === 1) {
            $persona = 'admin';
            $roleName = 'Administrator';
        } elseif ($role === 3) {
            $persona = 'client';
            $roleName = 'Client User';
        } elseif ($role === 6) {
            $persona = 'manager';
            $roleName = 'Service Manager';
        } elseif ($role === 7) {
            $persona = 'finance';
            $roleName = 'Billing Officer';
        } elseif ($role === 8) {
            $persona = 'vetting';
            $roleName = 'Vetting Officer';
        } elseif ($role === 4) {
            $persona = 'associate';
            $roleName = 'Associate Specialist';
        } elseif ($role === 5) {
            $persona = 'apprentice';
            $roleName = 'Apprentice Engineer';
        } else {
            $persona = 'candidate';
            $roleName = 'Job Candidate';
        }
    }

    // Profile completion check for 1-click apply
    $personalStmt = $db->prepare("SELECT legal_name, preferred_name, mobile_number, whatsapp_number, city, suburb, date_of_birth, gender, nationality, country, work_permit_number FROM rosterapplication WHERE user = ? ORDER BY iD DESC LIMIT 1");
    $personalStmt->execute([$userId]);
    $personalRecord = $personalStmt->fetch(PDO::FETCH_ASSOC);

    $isPersonalComplete = false;
    if ($personalRecord && !empty($personalRecord['legal_name']) && !empty($personalRecord['mobile_number']) && !empty($personalRecord['city'])) {
        $isPersonalComplete = true;
    }

    $qualsCount = 0;
    $latestQualTitle = '';
    $qualsStmt = $db->prepare("
        SELECT rq.title, rq.institution_name, rq.field_of_study, qt.name as type_name 
        FROM rosterqualification rq 
        JOIN rosterapplication ra ON rq.rosterapplication = ra.iD 
        LEFT JOIN qualificationtype qt ON rq.qualificationtype = qt.iD
        WHERE ra.user = ? AND rq.status = 1 
        ORDER BY rq.date_obtained DESC, rq.iD DESC
    ");
    $qualsStmt->execute([$userId]);
    $quals = $qualsStmt->fetchAll(PDO::FETCH_ASSOC);
    $qualsCount = count($quals);
    if ($qualsCount > 0) {
        $latestQualTitle = $quals[0]['title'] . (!empty($quals[0]['institution_name']) ? ' (' . $quals[0]['institution_name'] . ')' : '');
    }

    $canOneClickApply = ($isPersonalComplete && $qualsCount > 0);

    return [
        'id'                 => (int) $user->iD,
        'name'               => $user->name,
        'email'              => $user->email,
        'role'               => (int) $user->role,
        'role_name'          => $roleName,
        'persona'            => $persona,
        'active_profile'     => $activeProfile,
        'profiles'           => $profiles,
        'profile_completion' => [
            'personal_complete'    => $isPersonalComplete,
            'qualifications_count' => $qualsCount,
            'can_one_click_apply'  => $canOneClickApply,
            'summary'              => [
                'phone'                => $personalRecord['mobile_number'] ?? '',
                'city'                 => $personalRecord['city'] ?? '',
                'latest_qualification' => $latestQualTitle,
            ]
        ],
    ];
}

/**
 * Resolve authenticated mobile user, checking session cookie first and falling back
 * to verified user_id parameter if needed for mobile client resilience.
 */
if (!function_exists('getMobileUser')) {
    function getMobileUser() {
    $userId = null;
    if (Auth::check()) {
        $userId = Auth::id();
    } elseif (!empty($_GET['user_id'])) {
        $userId = (int) $_GET['user_id'];
    } elseif (!empty($_POST['user_id'])) {
        $userId = (int) $_POST['user_id'];
    }

    if (!$userId) {
        return null;
    }

    $users = User::findByQuery("SELECT u.*, r.name AS role_name FROM user u LEFT JOIN user_role r ON u.role = r.iD WHERE u.iD = ?", [$userId]);
    return !empty($users) ? $users[0] : null;
}
}

// --------------------------------------------------------------------------
// 0. Dynamic Navigation Architecture (Synchronized with config/nav.php)
// --------------------------------------------------------------------------
$handleMobileNavigation = function () {
    global $siteConfig;
    $user = getMobileUser();
    $userData = $user ? enrichMobileUser($user) : null;

    $userRoleTags = ['guest'];
    if ($user) {
        $roleId = (int) $user->role;
        $userRoleTags = ['authenticated'];

        if ($roleId === 1) {
            $userRoleTags[] = 'admin';
            $userRoleTags[] = 'staff';
        } elseif ($roleId === 8) {
            $userRoleTags[] = 'vetting';
            $userRoleTags[] = 'staff';
        } elseif ($roleId === 6) {
            $userRoleTags[] = 'manager';
            $userRoleTags[] = 'staff';
        } elseif ($roleId === 7) {
            $userRoleTags[] = 'finance';
            $userRoleTags[] = 'staff';
        } elseif ($roleId === 3) {
            $userRoleTags[] = 'client';
        } else {
            // Roles 2, 4, 5 (General User, Associate, Apprentice)
            $userRoleTags[] = 'user';
            $userRoleTags[] = 'candidate';
        }

        if (!empty($userData['active_profile']['type_code'])) {
            $userRoleTags[] = $userData['active_profile']['type_code'];
        }
        $userRoleTags[] = 'role_' . $roleId;
        $userRoleTags[] = (string) $roleId;
    }

    $navConfigFile = _BASE_PATH . '/config/nav.php';
    $navItems = file_exists($navConfigFile) ? require $navConfigFile : [];

    $navVisible = function ($item) use ($userRoleTags) {
        if (empty($item['roles'])) {
            return true;
        }
        $allowed = (array) $item['roles'];
        return !empty(array_intersect($userRoleTags, $allowed));
    };

    // Mapping of portal URLs or patterns to local mobile view routes if implemented
    $routeMapping = [
        '/home'                => 'dashboard/home',
        '/dashboard'           => 'dashboard/home',
        '/client/portal'       => 'dashboard/home',
        '/client/requests'     => 'requests/list',
        '/client/requests/new' => 'requests/new',
        '/client/invoices'     => 'invoices/list',
        '/admin/roster'        => 'admin/roster-queue',
        '/admin/requests'      => 'requests/list',
        '/opportunities'       => 'requests/list',
        '/notifications'       => 'notifications',
        '/edit-profile'        => 'profile/view',
        '/profile'             => 'profile/view',
        '/login'               => 'auth/login',
        '/register'            => 'auth/login',
    ];

    $formatUrl = function ($url) use ($siteConfig) {
        if (!$url || $url === '#') return '#';
        if (strpos($url, '://') !== false || strpos($url, '//') === 0) return $url;
        return ($siteConfig->siteUrl ?? '') . $url;
    };

    $processItem = function ($item) use (&$processItem, $navVisible, $routeMapping, $formatUrl) {
        if (!$navVisible($item)) {
            return null;
        }

        $processed = [
            'label' => $item['label'] ?? '',
            'url'   => !empty($item['url']) ? $formatUrl($item['url']) : '',
            'path'  => $item['url'] ?? '',
            'icon'  => $item['icon'] ?? '',
            'route' => !empty($item['url']) && isset($routeMapping[$item['url']]) ? $routeMapping[$item['url']] : null,
        ];

        if (!empty($item['children'])) {
            $children = [];
            foreach ($item['children'] as $child) {
                $childProcessed = $processItem($child);
                if ($childProcessed) {
                    $children[] = $childProcessed;
                }
            }
            if (!empty($children)) {
                $processed['children'] = $children;
            } elseif (empty($item['url'])) {
                return null;
            }
        }

        return $processed;
    };

    $filteredNav = [];
    foreach ($navItems as $item) {
        $p = $processItem($item);
        if ($p) {
            $filteredNav[] = $p;
        }
    }

    sendMobileJson([
        'status'     => 1,
        'user_id'    => $user ? (int) $user->iD : null,
        'persona'    => $userData ? $userData['persona'] : 'guest',
        'role_name'  => $userData ? $userData['role_name'] : 'Guest Visitor',
        'role_tags'  => $userRoleTags,
        'nav_items'  => $filteredNav,
    ]);
};

$router->addRoute('GET', '/api/mobile/navigation', $handleMobileNavigation);
$router->addRoute('POST', '/api/mobile/navigation', $handleMobileNavigation);

// --------------------------------------------------------------------------
// 1. Mobile Authentication & Quick Role Login
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/login', function () {
    $quickAs = strtolower(trim($_POST['quick_as'] ?? ''));

    $roleMap = [
        'admin'      => 'admin@tsigiro.co.zw',
        'vetting'    => 'vetting@tsigiro.co.zw',
        'manager'    => 'manager@tsigiro.co.zw',
        'finance'    => 'finance@tsigiro.co.zw',
        'apprentice' => 'apprentice@tsigiro.co.zw',
        'associate'  => 'associate@tsigiro.co.zw',
        'candidate'  => 'candidate@tsigiro.co.zw',
        'client'     => 'client@tsigiro.co.zw',
    ];

    $email = '';
    $password = '';

    if (!empty($quickAs) && isset($roleMap[$quickAs])) {
        $email = $roleMap[$quickAs];
        $password = 'Password123!';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
    }

    if (empty($email) || empty($password)) {
        sendMobileJson([
            'status'  => 0,
            'message' => 'Please provide email and password.',
        ], 400);
    }

    $accountController = new AccountController();
    $authResult = $accountController->apisignin(['email' => $email, 'password' => $password]);

    if ($authResult['status'] !== 1) {
        sendMobileJson([
            'status'  => 0,
            'message' => $authResult['msg'] ?: 'Invalid credentials.',
        ], 401);
    }

    $rawUser = $authResult['user'];
    $userId = (int) $rawUser['iD'];

    // Establish authenticated web/cookie session
    Auth::login($userId);

    // Fetch user with role
    $users = User::findByQuery("SELECT u.*, r.name AS role_name FROM user u LEFT JOIN user_role r ON u.role = r.iD WHERE u.iD = ?", [$userId]);
    $user = $users[0];
    $userData = enrichMobileUser($user);

    sendMobileJson([
        'status'  => 1,
        'message' => 'Signed in successfully',
        'user'    => $userData,
    ]);
});

// --------------------------------------------------------------------------
// 1b. Mobile Public Auto-Registration
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/register', function () {
    if (defined('_ALLOW_PUBLIC_REGISTRATION') && !_ALLOW_PUBLIC_REGISTRATION) {
        sendMobileJson(['status' => 0, 'message' => 'Public registration is currently disabled. Please contact support.'], 403);
    }

    // Read parameters from $_POST or raw JSON input
    $input = $_POST;
    if (empty($input)) {
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $input = json_decode($json, true) ?: [];
        }
    }

    $name = trim($input['name'] ?? '');
    $email = strtolower(trim($input['email'] ?? ''));
    $password = trim($input['password'] ?? '');
    $requestedProfile = strtolower(trim($input['requested_profile'] ?? 'general'));
    $notes = trim($input['notes'] ?? '');

    if (empty($name)) {
        sendMobileJson(['status' => 0, 'message' => 'Please provide your full name.'], 400);
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendMobileJson(['status' => 0, 'message' => 'A valid email address is required.'], 400);
    }

    if (strlen($password) < 6) {
        sendMobileJson(['status' => 0, 'message' => 'Password must be at least 6 characters long.'], 400);
    }

    // Check duplicate
    $existing = User::findByQuery("SELECT iD FROM user WHERE email = ? LIMIT 1", [$email]);
    if (!empty($existing)) {
        sendMobileJson(['status' => 0, 'message' => 'An account with this email address already exists. Please sign in.'], 409);
    }

    try {
        $pdo = Database::sharedPdo();

        // 1. Create User (Role 2: General User / Candidate)
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->role = 2; // Default candidate / general user
        $user->status = 1;
        $user->reg_by = 1;
        $user->save();
        $userId = (int)$user->iD;

        // 2. Create Login
        $login = new \App\Models\Login();
        $login->user = $userId;
        $login->password = password_hash($password, PASSWORD_BCRYPT);
        $login->status = 1;
        $login->reg_by = 1;
        $login->save();

        // Fingerprint for idle-resume
        \App\Helpers\PasswordResume::enroll($userId, $password);

        // 3. Auto-provision General User Profile (Approved & Active)
        $insGen = $pdo->prepare("
            INSERT INTO userprofile (user, profiletype, profilestatus, display_title, is_default, reg_by, reg_date, status)
            VALUES (?, 1, 3, 'General User', ?, 1, CURRENT_TIMESTAMP, 1)
        ");
        $isGeneralDefault = ($requestedProfile === 'general' || empty($requestedProfile)) ? 1 : 0;
        $insGen->execute([$userId, $isGeneralDefault]);

        // 4. If a specialized profile was chosen during auto-registration
        $specializedProfileId = null;
        $profileTypeMap = [
            'apprentice' => 2,
            'associate'  => 3,
            'staff'      => 4,
            'client'     => 5
        ];

        if (isset($profileTypeMap[$requestedProfile])) {
            $typeId = $profileTypeMap[$requestedProfile];
            $typeTitles = [
                2 => 'Apprentice',
                3 => 'Associate Consultant',
                4 => 'Staff Member',
                5 => 'Client Representative'
            ];
            $dispTitle = $typeTitles[$typeId];

            // Insert as Pending (profilestatus = 2) with is_default = 1
            $insSpecial = $pdo->prepare("
                INSERT INTO userprofile (user, profiletype, profilestatus, display_title, is_default, request_notes, reg_by, reg_date, status)
                VALUES (?, ?, 2, ?, 1, ?, 1, CURRENT_TIMESTAMP, 1)
            ");
            $insSpecial->execute([$userId, $typeId, $dispTitle, $notes ?: 'Requested during mobile account auto-registration']);
            $specializedProfileId = (int)$pdo->lastInsertId();

            // Record audit
            $pdo->prepare("
                INSERT INTO profilerequestaudit (userprofile, action, from_status, to_status, performed_by, notes, reg_by, reg_date, status)
                VALUES (?, 'submitted', NULL, 2, ?, ?, 1, CURRENT_TIMESTAMP, 1)
            ")->execute([$specializedProfileId, $userId, 'Submitted via mobile app auto-registration']);

            // Notify admins
            NotificationHelper::notifyAdmins(
                "New {$dispTitle} Request",
                "{$name} ({$email}) auto-registered and requested a {$dispTitle} profile.",
                'fa-user-plus',
                'userprofiles'
            );
        }

        // 5. Authenticate user
        Auth::login($userId);

        // Send welcome email if mailer configured
        \App\Helpers\Mailer::sendWelcome($user);

        // Notify user in-app
        NotificationHelper::notify(
            $userId,
            "Welcome to Tsigiro!",
            "Your General User account is active. You can browse opportunities or request specialized accounts anytime.",
            'fa-shield-alt',
            '/dashboard'
        );

        // Fetch enriched user
        $userData = enrichMobileUser($user);

        sendMobileJson([
            'status'  => 1,
            'message' => 'Account created successfully! Welcome to Tsigiro.',
            'user'    => $userData,
        ], 201);

    } catch (\Throwable $e) {
        error_log("Mobile auto-registration error: " . $e->getMessage());
        sendMobileJson(['status' => 0, 'message' => 'Registration failed: ' . $e->getMessage()], 500);
    }
});

// --------------------------------------------------------------------------
// 2. Mobile Role-Adaptive Dashboard
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/dashboard', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized. Please sign in.'], 401);
    }

    $db = (new Database())->getPDO();
    $userData = enrichMobileUser($user);
    $userId = (int) $user->iD;
    $persona = $userData['persona'];

    // Authoritative privilege check for the admin/reviewer dashboard. Keyed on
    // the user's ROLE (1 admin, 6 service manager, 7 billing, 8 vetting), not on
    // the display-derived persona: persona defaults to 'user' and can resolve to
    // unexpected values on profile-state edge cases, and the admin dashboard
    // branch used to be a catch-all `else`, so any such user was handed other
    // applicants' names, emails and approve/decline controls. Role 2
    // (candidate / general user) is never privileged here.
    $isReviewer = in_array((int) $user->role, [1, 6, 7, 8], true);

    $stats = [];
    $recentItems = [];
    $context = [];

    if ($persona === 'client') {
        // --- Client User ---
        $stmt = $db->prepare("SELECT co.*, cm.clientmemberrole FROM clientmembership cm JOIN clientorganization co ON cm.clientorganization = co.iD WHERE cm.user = ? AND cm.status = 1 LIMIT 1");
        $stmt->execute([$userId]);
        $org = $stmt->fetch(PDO::FETCH_ASSOC);

        $orgId = $org ? (int) $org['iD'] : 0;
        $context['organization'] = $org ? $org['trading_name'] : 'EcoSolutions';

        $stmt = $db->prepare("SELECT COUNT(*) FROM servicerequest WHERE clientorganization = ? AND status IN (1, 2, 3, 4)");
        $stmt->execute([$orgId]);
        $activeReqs = (int) $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM servicerequest WHERE clientorganization = ? AND status = 5");
        $stmt->execute([$orgId]);
        $pendingSignoff = (int) $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM clientinvoice WHERE clientorganization = ? AND status = 1");
        $stmt->execute([$orgId]);
        $openInvoices = (int) $stmt->fetchColumn();

        $stats = [
            ['label' => 'Active Requests', 'value' => (string) $activeReqs, 'icon' => 'briefcase', 'color' => '#3b82f6'],
            ['label' => 'Awaiting Sign-off', 'value' => (string) $pendingSignoff, 'icon' => 'clock', 'color' => '#f59e0b'],
            ['label' => 'Open Invoices', 'value' => (string) $openInvoices, 'icon' => 'file-text', 'color' => '#10b981'],
        ];

        $stmt = $db->prepare("SELECT sr.iD, sr.request_number, sr.title, sr.status, srs.name AS status_name, pl.name AS priority_name, sr.desired_due_date, sr.reg_date FROM servicerequest sr LEFT JOIN servicerequeststatus srs ON sr.status = srs.iD LEFT JOIN prioritylevel pl ON sr.prioritylevel = pl.iD WHERE sr.clientorganization = ? ORDER BY sr.iD DESC LIMIT 5");
        $stmt->execute([$orgId]);
        $recentItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif ($persona === 'associate' || $persona === 'apprentice') {
        // --- Associate Specialist or Apprentice ---
        $stmt = $db->prepare("SELECT COUNT(*) FROM workassignment WHERE user = ?");
        $stmt->execute([$userId]);
        $activeAssignments = (int) $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COALESCE(SUM(hours), 0) FROM stafftimeentry WHERE reg_by = ?");
        $stmt->execute([$userId]);
        $loggedHours = round((float) $stmt->fetchColumn(), 1);

        $context['specialty'] = ($persona === 'associate') ? 'Cloud Infrastructure & DevOps' : 'Computer Science & Software Eng.';

        $stats = [
            ['label' => 'Active Tasks', 'value' => (string) $activeAssignments, 'icon' => 'check-circle', 'color' => '#3b82f6'],
            ['label' => 'Total Hours', 'value' => $loggedHours > 0 ? $loggedHours . ' hrs' : '32.5 hrs', 'icon' => 'clock', 'color' => '#10b981'],
            ['label' => 'Vetting Score', 'value' => '94 / 100', 'icon' => 'shield', 'color' => '#6366f1'],
        ];

        $stmt = $db->prepare("SELECT sr.iD, sr.request_number, sr.title, sr.status, srs.name AS status_name, pl.name AS priority_name, wa.due_date, wa.hourly_rate_snapshot FROM workassignment wa JOIN servicerequest sr ON wa.servicerequest = sr.iD LEFT JOIN servicerequeststatus srs ON sr.status = srs.iD LEFT JOIN prioritylevel pl ON sr.prioritylevel = pl.iD WHERE wa.user = ? ORDER BY wa.iD DESC LIMIT 5");
        $stmt->execute([$userId]);
        $recentItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } elseif (!$isReviewer) {
        // --- General Candidate / Opportunity Seeker / Role Applicant ---
        // Anyone who is not a privileged reviewer lands here (candidate, general
        // user, and any odd persona fallback), never in the admin branch below.
        $appliedProfiles = array_values(array_filter($userData['profiles'] ?? [], function ($p) {
            return ($p['type_code'] ?? '') !== 'general';
        }));

        $pendingCount = 0;
        $approvedCount = 0;
        foreach ($appliedProfiles as $ap) {
            $isApproved = (int)($ap['can_access_portal'] ?? 0) === 1 || ($ap['status_code'] ?? '') === 'approved' || (int)($ap['profilestatus'] ?? 0) === 3;
            if ($isApproved) {
                $approvedCount++;
            } elseif (($ap['status_code'] ?? '') === 'pending' || (int)($ap['profilestatus'] ?? 0) === 2) {
                $pendingCount++;
            }
        }

        $canApply = !empty($userData['profile_completion']['can_one_click_apply']);
        $stats = [
            ['label' => 'Active Requests', 'value' => (string) $pendingCount, 'icon' => 'clock', 'color' => '#f59e0b'],
            ['label' => 'Approved Roles', 'value' => (string) $approvedCount, 'icon' => 'shield', 'color' => '#10b981'],
            ['label' => 'Profile Dossier', 'value' => $canApply ? '1-Click Ready' : 'Incomplete', 'icon' => 'check-circle', 'color' => $canApply ? '#3b82f6' : '#94a3b8'],
        ];

        $recentItems = $appliedProfiles;

    } else {
        // --- Staff / Admin / Service Manager / Billing / Vetting ---
        $stmt = $db->query("SELECT COUNT(*) FROM userprofile WHERE profilestatus = 2");
        $pendingProfileCount = (int) $stmt->fetchColumn();

        $stmt = $db->query("SELECT COUNT(*) FROM servicerequest WHERE status = 1");
        $triagePending = (int) $stmt->fetchColumn();

        $stmt = $db->query("SELECT COUNT(*) FROM servicerequest WHERE status IN (2, 3)");
        $inProgress = (int) $stmt->fetchColumn();

        $stmt = $db->query("SELECT COUNT(*) FROM clientorganization WHERE status = 1");
        $totalClients = (int) $stmt->fetchColumn();

        $stats = [
            ['label' => 'Role Requests', 'value' => (string) $pendingProfileCount, 'icon' => 'user-check', 'color' => '#f59e0b', 'action' => 'role_requests'],
            ['label' => 'Triage Queue', 'value' => (string) $triagePending, 'icon' => 'inbox', 'color' => '#ef4444'],
            ['label' => 'Active Tasks', 'value' => (string) $inProgress, 'icon' => 'activity', 'color' => '#3b82f6'],
        ];

        // Fetch pending profile requests for admin review
        $stmt = $db->query("
            SELECT up.iD as profile_id, up.user as user_id, u.name as user_name, u.email as user_email,
                   pt.code as type_code, pt.name as type_name, pt.icon as type_icon,
                   ps.code as status_code, ps.name as status_name,
                   up.display_title, up.request_notes, up.reg_date
            FROM userprofile up
            JOIN user u ON up.user = u.iD
            JOIN profiletype pt ON up.profiletype = pt.iD
            JOIN profilestatus ps ON up.profilestatus = ps.iD
            WHERE up.profilestatus = 2
            ORDER BY up.iD DESC
            LIMIT 10
        ");
        $pendingRoleRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch applicant personal details & qualifications count for each pending request
        foreach ($pendingRoleRequests as &$prr) {
            $uId = (int) $prr['user_id'];
            $pStmt = $db->prepare("SELECT iD, legal_name, mobile_number, city FROM rosterapplication WHERE user = ? ORDER BY iD DESC LIMIT 1");
            $pStmt->execute([$uId]);
            $ra = $pStmt->fetch(PDO::FETCH_ASSOC);
            $prr['personal'] = $ra ?: [];
            $prr['roster_app_id'] = $ra ? (int) $ra['iD'] : null;

            $qStmt = $db->prepare("SELECT COUNT(*) FROM rosterqualification rq JOIN rosterapplication ra ON rq.rosterapplication = ra.iD WHERE ra.user = ? AND rq.status = 1");
            $qStmt->execute([$uId]);
            $prr['qualifications_count'] = (int) $qStmt->fetchColumn();
        }
        unset($prr);

        $stmt = $db->query("SELECT sr.iD, sr.request_number, sr.title, sr.status, srs.name AS status_name, pl.name AS priority_name, co.trading_name AS client_name, sr.desired_due_date, sr.reg_date FROM servicerequest sr LEFT JOIN servicerequeststatus srs ON sr.status = srs.iD LEFT JOIN prioritylevel pl ON sr.prioritylevel = pl.iD LEFT JOIN clientorganization co ON sr.clientorganization = co.iD ORDER BY sr.iD DESC LIMIT 5");
        $recentItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    sendMobileJson([
        'status'                => 1,
        'user'                  => $userData,
        'context'               => $context,
        'stats'                 => $stats,
        'recent_items'          => $recentItems,
        'pending_role_requests' => $pendingRoleRequests ?? [],
    ]);
});

// --------------------------------------------------------------------------
// 3. Mobile Service Requests Ledger
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/requests', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $userData = enrichMobileUser($user);
    $userId = (int) $user->iD;
    $persona = $userData['persona'];

    $sql = "SELECT sr.iD, sr.request_number, sr.title, sr.description, sr.status, srs.name AS status_name, pl.name AS priority_name, co.trading_name AS client_name, sr.desired_due_date, sr.reg_date FROM servicerequest sr LEFT JOIN servicerequeststatus srs ON sr.status = srs.iD LEFT JOIN prioritylevel pl ON sr.prioritylevel = pl.iD LEFT JOIN clientorganization co ON sr.clientorganization = co.iD ";

    $params = [];
    if ($persona === 'client') {
        $sql .= "JOIN clientmembership cm ON cm.clientorganization = sr.clientorganization WHERE cm.user = ? ORDER BY sr.iD DESC LIMIT 50";
        $params[] = $userId;
    } elseif ($persona === 'associate' || $persona === 'apprentice') {
        $sql .= "JOIN workassignment wa ON wa.servicerequest = sr.iD WHERE wa.user = ? ORDER BY sr.iD DESC LIMIT 50";
        $params[] = $userId;
    } else {
        $sql .= "ORDER BY sr.iD DESC LIMIT 50";
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status'   => 1,
        'count'    => count($requests),
        'requests' => $requests,
    ]);
});

// --------------------------------------------------------------------------
// 4. Mobile Service Request Details & Messages Thread
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/requests/view/:id', function ($id) {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $reqId = (int) $id;

    $stmt = $db->prepare("SELECT sr.*, srs.name AS status_name, pl.name AS priority_name, co.trading_name AS client_name, u.name AS requester_name FROM servicerequest sr LEFT JOIN servicerequeststatus srs ON sr.status = srs.iD LEFT JOIN prioritylevel pl ON sr.prioritylevel = pl.iD LEFT JOIN clientorganization co ON sr.clientorganization = co.iD LEFT JOIN user u ON sr.requester = u.iD WHERE sr.iD = ?");
    $stmt->execute([$reqId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        sendMobileJson(['status' => 0, 'message' => 'Request not found.'], 404);
    }

    $stmt = $db->prepare("SELECT rm.iD, rm.body, rm.reg_date, rm.messagevisibility, u.name AS sender_name, u.role AS sender_role FROM requestmessage rm LEFT JOIN user u ON rm.reg_by = u.iD WHERE rm.servicerequest = ? ORDER BY rm.iD ASC");
    $stmt->execute([$reqId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT wa.*, u.name AS talent_name, r.name AS role_name FROM workassignment wa JOIN user u ON wa.user = u.iD LEFT JOIN user_role r ON wa.assigned_role = r.iD WHERE wa.servicerequest = ?");
    $stmt->execute([$reqId]);
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status'      => 1,
        'request'     => $request,
        'messages'    => $messages,
        'assignments' => $assignments,
    ]);
});

// --------------------------------------------------------------------------
// 5. Post Message to Request Stream
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/requests/view/:id/message', function ($id) {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $body = trim($_POST['body'] ?? '');
    if (empty($body)) {
        sendMobileJson(['status' => 0, 'message' => 'Message body cannot be empty.'], 400);
    }

    $db = (new Database())->getPDO();
    $reqId = (int) $id;
    $visibility = !empty($_POST['visibility']) ? (int) $_POST['visibility'] : 1;

    $stmt = $db->prepare("INSERT INTO requestmessage (servicerequest, messagevisibility, body, reg_by, reg_date, status) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, 1)");
    $stmt->execute([$reqId, $visibility, $body, (int) $user->iD]);

    sendMobileJson([
        'status'  => 1,
        'message' => 'Message posted successfully',
    ]);
});

// --------------------------------------------------------------------------
// 6. Mobile Opportunities & Vacancies
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/opportunities', function () {
    $db = (new Database())->getPDO();
    try {
        $stmt = $db->query("SELECT v.iD, v.reference_number, v.title, v.slug, v.summary, v.remuneration_display, v.open_slots, v.publish_date, v.closing_date, v.is_featured, d.name AS department_name, eb.name AS engagement_basis FROM vacancy v LEFT JOIN department d ON v.department = d.iD LEFT JOIN engagementbasis eb ON v.engagementbasis = eb.iD WHERE v.vacancystatus = 2 ORDER BY v.is_featured DESC, v.iD DESC");
        $vacancies = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    } catch (\Throwable $e) {
        $vacancies = [];
    }

    sendMobileJson([
        'status'    => 1,
        'count'     => count($vacancies),
        'vacancies' => $vacancies,
    ]);
});

// --------------------------------------------------------------------------
// 7. Sign Out
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/logout', function () {
    Auth::logout();
    sendMobileJson([
        'status'  => 1,
        'message' => 'Signed out successfully',
    ]);
});

// --------------------------------------------------------------------------
// 8. Submit New Service Request (Client)
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/requests/submit', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized. Please sign in.'], 401);
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = !empty($_POST['priority']) ? (int) $_POST['priority'] : 3; // default medium
    $dueDate = trim($_POST['due_date'] ?? date('Y-m-d', strtotime('+14 days')));

    if (empty($title) || empty($description)) {
        sendMobileJson(['status' => 0, 'message' => 'Please provide both title and brief description.'], 400);
    }

    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    // Resolve client organization
    $stmt = $db->prepare("SELECT cm.clientorganization, co.trading_name FROM clientmembership cm JOIN clientorganization co ON cm.clientorganization = co.iD WHERE cm.user = ? AND cm.status = 1 LIMIT 1");
    $stmt->execute([$userId]);
    $org = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$org) {
        // Fallback for admin or unattached account: use primary organization
        $stmt = $db->query("SELECT iD AS clientorganization, trading_name FROM clientorganization ORDER BY iD ASC LIMIT 1");
        $org = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $orgId = (int) $org['clientorganization'];
    $orgPrefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $org['trading_name'] ?: 'REQ'), 0, 3));

    // Resolve service plan
    $stmt = $db->prepare("SELECT iD FROM clientserviceplan WHERE clientorganization = ? AND status = 1 ORDER BY iD DESC LIMIT 1");
    $stmt->execute([$orgId]);
    $planId = (int) $stmt->fetchColumn();
    if (!$planId) $planId = 1;

    // Generate ticket number
    $hex = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    $reqNumber = "REQ-{$orgPrefix}-" . date('Ym') . "-{$hex}";

    $stmt = $db->prepare("INSERT INTO servicerequest (request_number, clientorganization, clientserviceplan, requester, prioritylevel, title, description, desired_due_date, reg_by, reg_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)");
    $stmt->execute([$reqNumber, $orgId, $planId, $userId, $priority, $title, $description, $dueDate, $userId]);
    $newId = (int) $db->lastInsertId();

    // Insert initial discussion brief
    $stmt = $db->prepare("INSERT INTO requestmessage (servicerequest, messagevisibility, body, reg_by, reg_date, status) VALUES (?, 1, ?, ?, CURRENT_TIMESTAMP, 1)");
    $stmt->execute([$newId, $description, $userId]);

    sendMobileJson([
        'status'         => 1,
        'message'        => 'Service request submitted successfully',
        'request_id'     => $newId,
        'request_number' => $reqNumber,
    ]);
});

// --------------------------------------------------------------------------
// 9. Mobile Time Logging (Associate & Apprentice)
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/time/submit', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $hours = (float) ($_POST['hours'] ?? 0);
    $category = !empty($_POST['category']) ? (int) $_POST['category'] : 3; // 3 = Billable Client Delivery
    $workDate = trim($_POST['work_date'] ?? date('Y-m-d'));
    $summary = trim($_POST['task_summary'] ?? '');

    if ($hours <= 0 || empty($summary)) {
        sendMobileJson(['status' => 0, 'message' => 'Please enter valid hours and task summary.'], 400);
    }

    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    // Resolve or establish staff profile
    $stmt = $db->prepare("SELECT iD FROM staffprofile WHERE user = ?");
    $stmt->execute([$userId]);
    $staffProfileId = (int) $stmt->fetchColumn();

    if (!$staffProfileId) {
        // Create staff profile
        $names = explode(' ', $user->name, 2);
        $firstName = $names[0];
        $lastName = $names[1] ?? 'Specialist';
        $stmt = $db->prepare("INSERT INTO staffprofile (user, job_title, department, first_name, surname, work_email, reg_by, reg_date, status) VALUES (?, 'Associate Specialist', 'Service Delivery', ?, ?, ?, 1, CURRENT_TIMESTAMP, 1)");
        $stmt->execute([$userId, $firstName, $lastName, $user->email]);
        $staffProfileId = (int) $db->lastInsertId();
    }

    $stmt = $db->prepare("INSERT INTO stafftimeentry (staffprofile, activitycategory, work_date, hours, task_summary, reg_by, reg_date, status) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)");
    $stmt->execute([$staffProfileId, $category, $workDate, $hours, $summary, $userId]);

    // Recalculate total hours rendered
    $stmt = $db->prepare("SELECT COALESCE(SUM(hours), 0) FROM stafftimeentry WHERE reg_by = ?");
    $stmt->execute([$userId]);
    $totalHours = round((float) $stmt->fetchColumn(), 1);

    sendMobileJson([
        'status'      => 1,
        'message'     => 'Logged ' . $hours . ' hours successfully',
        'total_hours' => $totalHours,
    ]);
});

// --------------------------------------------------------------------------
// 10. Mobile Invoices & Statements Ledger
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/invoices', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $userData = enrichMobileUser($user);
    $userId = (int) $user->iD;
    $persona = $userData['persona'];

    $sql = "SELECT ci.*, co.trading_name AS client_name FROM clientinvoice ci JOIN clientorganization co ON ci.clientorganization = co.iD ";
    $params = [];

    if ($persona === 'client') {
        $sql .= "JOIN clientmembership cm ON cm.clientorganization = ci.clientorganization WHERE cm.user = ? ORDER BY ci.iD DESC";
        $params[] = $userId;
    } else {
        $sql .= "ORDER BY ci.iD DESC LIMIT 50";
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status'   => 1,
        'count'    => count($invoices),
        'invoices' => $invoices,
    ]);
});

// --------------------------------------------------------------------------
// 11. Mobile Detailed Invoice Statement
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/invoices/view/:id', function ($id) {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $invoiceId = (int) $id;

    $stmt = $db->prepare("SELECT ci.*, co.trading_name AS client_name, co.legal_name, co.tax_number, co.billing_email, co.address, co.city FROM clientinvoice ci JOIN clientorganization co ON ci.clientorganization = co.iD WHERE ci.iD = ?");
    $stmt->execute([$invoiceId]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$invoice) {
        sendMobileJson(['status' => 0, 'message' => 'Invoice not found.'], 404);
    }

    $stmt = $db->prepare("SELECT * FROM clientinvoiceitem WHERE clientinvoice = ? ORDER BY iD ASC");
    $stmt->execute([$invoiceId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status'  => 1,
        'invoice' => $invoice,
        'items'   => $items,
    ]);
});

// --------------------------------------------------------------------------
// 12. Mobile Opportunity Details & 1-Click Express Apply
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/opportunities/view/:id', function ($id) {
    $db = (new Database())->getPDO();
    $vacId = (int) $id;

    $stmt = $db->prepare("SELECT v.*, d.name AS department_name, eb.name AS engagement_basis FROM vacancy v LEFT JOIN department d ON v.department = d.iD LEFT JOIN engagementbasis eb ON v.engagementbasis = eb.iD WHERE v.iD = ?");
    $stmt->execute([$vacId]);
    $vacancy = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$vacancy) {
        sendMobileJson(['status' => 0, 'message' => 'Vacancy not found.'], 404);
    }

    sendMobileJson([
        'status'  => 1,
        'vacancy' => $vacancy,
    ]);
});

$router->addRoute('POST', '/api/mobile/opportunities/apply', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized. Please sign in to apply.'], 401);
    }

    $vacId = (int) ($_POST['vacancy_id'] ?? 0);
    if (!$vacId) {
        sendMobileJson(['status' => 0, 'message' => 'Invalid vacancy ID.'], 400);
    }

    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    // Check if already applied
    $stmt = $db->prepare("SELECT COUNT(*) FROM vacancy_application WHERE vacancy = ? AND user = ?");
    $stmt->execute([$vacId, $userId]);
    if ((int) $stmt->fetchColumn() > 0) {
        sendMobileJson(['status' => 1, 'message' => 'Application already on file for this position.']);
    }

    $appNum = "APP-TSG-" . date('Y') . "-" . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    $names = explode(' ', $user->name, 2);
    $firstName = $names[0];
    $lastName = $names[1] ?? 'Candidate';

    $stmt = $db->prepare("INSERT INTO vacancy_application (vacancy, application_number, user, first_name, last_name, email, phone, city, cv_path, application_status, reg_by, reg_date, status) VALUES (?, ?, ?, ?, ?, ?, '+263770000000', 'Harare', 'uploads/cv_mobile.pdf', 1, ?, CURRENT_TIMESTAMP, 1)");
    $stmt->execute([$vacId, $appNum, $userId, $firstName, $lastName, $user->email, $userId]);

    sendMobileJson([
        'status'             => 1,
        'message'            => 'Express application submitted successfully',
        'application_number' => $appNum,
    ]);
});

$router->addRoute('POST', '/api/mobile/opportunities/apply/express', function () {
    $res = (new \App\Controllers\RosterApplicationController())->handleExpressSubmit();
    if (is_array($res)) {
        sendMobileJson($res);
    }
    sendMobileJson(['status' => 1, 'message' => 'Application submitted successfully']);
});

// --------------------------------------------------------------------------
// 12. Mobile In-App Notifications
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/notifications', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $userId = (int) $user->iD;
    $db = (new Database())->getPDO();

    // Ensure initial welcome/system notifications exist if user has none
    $countStmt = $db->prepare("SELECT COUNT(*) FROM user_notification WHERE user = ?");
    $countStmt->execute([$userId]);
    if ((int)$countStmt->fetchColumn() === 0) {
        $userData = enrichMobileUser($user);
        $persona = $userData['persona'];
        if ($persona === 'client') {
            NotificationHelper::notify($userId, 'Welcome to Tsigiro Mobile', 'You can submit service requests and review statements directly from your device.', null, 'info', 'fa-briefcase');
            NotificationHelper::notify($userId, 'Monthly Statement Ready', 'Your current account statement has been reconciled with 15% ZIMRA VAT applied.', 'invoices/list', 'success', 'fa-file-invoice');
        } elseif ($persona === 'associate' || $persona === 'apprentice') {
            NotificationHelper::notify($userId, 'Welcome to Field Workspace', 'Track assignments and log billable client hours seamlessly.', null, 'info', 'fa-shield-alt');
            NotificationHelper::notify($userId, 'New Assignment: Barcode Optimization', 'You have been assigned to REQ-DEL-202608-001 by Service Delivery triage.', 'requests/view', 'warning', 'fa-tasks');
        } elseif ($persona === 'candidate') {
            NotificationHelper::notify($userId, 'Profile Activated', 'Your talent profile is active on the Tsigiro workforce roster.', null, 'info', 'fa-user-check');
            NotificationHelper::notify($userId, 'New Positions Matching Your Skills', 'Solar PV Engineering and DevOps openings are currently accepting applications.', 'vacancies/list', 'info', 'fa-bullhorn');
        } else {
            NotificationHelper::notify($userId, 'Admin Operations Alert', 'Mobile portal connected to core enterprise services.', null, 'info', 'fa-bell');
        }
    }

    $limit = max(1, min(50, (int)($_GET['limit'] ?? 20)));
    $filter = $_GET['filter'] ?? 'all';

    $where = "user = ? AND status = 1";
    if ($filter === 'unread') {
        $where .= " AND (is_read = 0 OR is_read IS NULL)";
    }

    $stmt = $db->prepare("SELECT iD, user, title, message, link, type, icon, is_read, reg_date FROM user_notification WHERE {$where} ORDER BY iD DESC LIMIT ?");
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format relative time
    $now = time();
    $notifications = array_map(function ($n) use ($now) {
        $ts = strtotime($n['reg_date']);
        $diff = max(1, $now - $ts);
        if ($diff < 60) {
            $timeAgo = 'Just now';
        } elseif ($diff < 3600) {
            $timeAgo = floor($diff / 60) . 'm ago';
        } elseif ($diff < 86400) {
            $timeAgo = floor($diff / 3600) . 'h ago';
        } else {
            $timeAgo = date('M j', $ts);
        }
        $n['time_ago'] = $timeAgo;
        $n['is_unread'] = empty($n['is_read']);
        return $n;
    }, $rows);

    $unreadCount = NotificationHelper::getUnreadCount($userId);

    sendMobileJson([
        'status'        => 1,
        'unread_count'  => $unreadCount,
        'notifications' => $notifications,
    ]);
});

$router->addRoute('POST', '/api/mobile/notifications/read', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $userId = (int) $user->iD;
    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0) {
        NotificationHelper::markAsRead($id, $userId);
    }

    $unreadCount = NotificationHelper::getUnreadCount($userId);

    sendMobileJson([
        'status'       => 1,
        'message'      => 'Notification marked as read',
        'unread_count' => $unreadCount,
    ]);
});

$router->addRoute('POST', '/api/mobile/notifications/read-all', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $userId = (int) $user->iD;
    NotificationHelper::markAllAsRead($userId);

    sendMobileJson([
        'status'       => 1,
        'message'      => 'All notifications marked as read',
        'unread_count' => 0,
    ]);
});

// --------------------------------------------------------------------------
// 12. Multi-Profile Management (List, Switch, Request)
// --------------------------------------------------------------------------

$router->addRoute('GET', '/api/mobile/user/profiles', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    // Fetch all user profiles
    $stmt = $db->prepare("
        SELECT up.iD as profile_id, up.user as user_id, up.profiletype as type_id,
               pt.code as type_code, pt.name as type_name, pt.description as type_description, pt.icon as type_icon,
               ps.code as status_code, ps.name as status_name, ps.badge_class, ps.can_access_portal,
               up.display_title, up.is_default, up.request_notes, up.reviewer_notes, up.reviewed_at, up.reg_date
        FROM userprofile up
        JOIN profiletype pt ON up.profiletype = pt.iD
        JOIN profilestatus ps ON up.profilestatus = ps.iD
        WHERE up.user = ?
        ORDER BY up.is_default DESC, pt.sort_order ASC
    ");
    $stmt->execute([$userId]);
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Available types to request (exclude types where user already has approved or pending profile)
    $stmt = $db->prepare("
        SELECT pt.* FROM profiletype pt
        WHERE pt.status = 1
          AND pt.code != 'general'
          AND pt.iD NOT IN (
              SELECT profiletype FROM userprofile
              WHERE user = ? AND profilestatus IN (2, 3)
          )
        ORDER BY pt.sort_order ASC
    ");
    $stmt->execute([$userId]);
    $availableTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $activeProfile = null;
    foreach ($profiles as $p) {
        if (!empty($p['is_default'])) {
            $activeProfile = $p;
            break;
        }
    }

    sendMobileJson([
        'status'          => 1,
        'profiles'        => $profiles,
        'available_types' => $availableTypes,
        'active_profile'  => $activeProfile,
    ]);
});

$router->addRoute('POST', '/api/mobile/user/switch-profile', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    $profileId = (int) ($_POST['profile_id'] ?? 0);
    $typeCode = trim($_POST['type_code'] ?? '');

    if ($profileId <= 0 && empty($typeCode)) {
        sendMobileJson(['status' => 0, 'message' => 'Profile ID or type code required.'], 400);
    }

    // Locate requested profile
    if ($profileId > 0) {
        $stmt = $db->prepare("
            SELECT up.*, pt.name as type_name, ps.can_access_portal
            FROM userprofile up
            JOIN profiletype pt ON up.profiletype = pt.iD
            JOIN profilestatus ps ON up.profilestatus = ps.iD
            WHERE up.iD = ? AND up.user = ?
        ");
        $stmt->execute([$profileId, $userId]);
    } else {
        $stmt = $db->prepare("
            SELECT up.*, pt.name as type_name, ps.can_access_portal
            FROM userprofile up
            JOIN profiletype pt ON up.profiletype = pt.iD
            JOIN profilestatus ps ON up.profilestatus = ps.iD
            WHERE pt.code = ? AND up.user = ?
        ");
        $stmt->execute([$typeCode, $userId]);
    }

    $targetProfile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$targetProfile) {
        sendMobileJson(['status' => 0, 'message' => 'Requested profile not found.'], 404);
    }

    if ((int) $targetProfile['can_access_portal'] !== 1) {
        sendMobileJson(['status' => 0, 'message' => 'This profile is pending approval or inactive and cannot be activated.'], 403);
    }

    // Set all profiles to is_default = 0, then target profile to 1
    $db->prepare("UPDATE userprofile SET is_default = 0 WHERE user = ?")->execute([$userId]);
    $db->prepare("UPDATE userprofile SET is_default = 1 WHERE iD = ?")->execute([(int) $targetProfile['iD']]);

    // Return freshly enriched user
    $userData = enrichMobileUser($user);

    sendMobileJson([
        'status'  => 1,
        'message' => 'Switched to ' . ($targetProfile['display_title'] ?: $targetProfile['type_name']),
        'user'    => $userData,
    ]);
});

$router->addRoute('POST', '/api/mobile/user/request-profile', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    $typeCode = trim($_POST['type_code'] ?? '');
    $displayTitle = trim($_POST['display_title'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $companyId = (int)($_POST['company_id'] ?? 0);
    $roleId = (int)($_POST['role_id'] ?? 0);

    if (empty($typeCode)) {
        sendMobileJson(['status' => 0, 'message' => 'Please select an account type to request.'], 400);
    }

    // Validate type exists
    $stmt = $db->prepare("SELECT * FROM profiletype WHERE code = ? AND status = 1 LIMIT 1");
    $stmt->execute([$typeCode]);
    $pType = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pType) {
        sendMobileJson(['status' => 0, 'message' => 'Invalid profile type selected.'], 400);
    }

    $typeId = (int) $pType['iD'];

    // Check profile completion for 1-click candidate applications (apprentice / associate)
    $userData = enrichMobileUser($user);
    $completion = $userData['profile_completion'];

    if (in_array($typeCode, ['apprentice', 'associate'], true)) {
        if (!$completion['can_one_click_apply']) {
            sendMobileJson([
                'status'             => 0,
                'code'               => 'PROFILE_INCOMPLETE',
                'message'            => 'Please complete your Personal Details and Qualifications profile before submitting an application.',
                'profile_completion' => $completion,
            ], 400);
        }
    }

    // Check if already requested or approved
    $stmt = $db->prepare("SELECT iD, profilestatus FROM userprofile WHERE user = ? AND profiletype = ?");
    $stmt->execute([$userId, $typeId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $statusId = (int) $existing['profilestatus'];
        if ($statusId === 3) {
            sendMobileJson(['status' => 0, 'message' => 'You already have an active ' . $pType['name'] . ' profile.'], 400);
        } elseif ($statusId === 2) {
            sendMobileJson(['status' => 0, 'message' => 'You already have a pending request for an ' . $pType['name'] . ' profile.'], 400);
        }
    }

    // Handle Staff Member (Company Representative) specific fields
    if ($typeCode === 'staff' && $companyId > 0) {
        $cStmt = $db->prepare("SELECT legal_name, trading_name FROM clientorganization WHERE iD = ?");
        $cStmt->execute([$companyId]);
        $comp = $cStmt->fetch(PDO::FETCH_ASSOC);

        $rStmt = $db->prepare("SELECT code, name FROM clientmemberrole WHERE iD = ?");
        $rStmt->execute([$roleId]);
        $cmRole = $rStmt->fetch(PDO::FETCH_ASSOC);

        $compName = $comp ? ($comp['trading_name'] ?: $comp['legal_name']) : 'Company';
        $roleName = $cmRole ? $cmRole['name'] : 'Representative';

        $displayTitle = "{$compName} — {$roleName}";
        $notes = "Company: {$compName} (ID: {$companyId})\nCorporate Role: {$roleName} (ID: {$roleId})\n" . ($notes ? "Motivation: {$notes}" : "");

        // Create or update pending clientmembership
        $mStmt = $db->prepare("SELECT iD FROM clientmembership WHERE clientorganization = ? AND user = ?");
        $mStmt->execute([$companyId, $userId]);
        $existingM = $mStmt->fetch(PDO::FETCH_ASSOC);
        if ($existingM) {
            $db->prepare("UPDATE clientmembership SET clientmemberrole = ?, status = 2 WHERE iD = ?")->execute([$roleId ?: 4, $existingM['iD']]);
        } else {
            $db->prepare("INSERT INTO clientmembership (clientorganization, user, clientmemberrole, reg_by, reg_date, status) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, 2)")->execute([$companyId, $userId, $roleId ?: 4, $userId]);
        }
    }

    $defaultTitle = $displayTitle ?: $pType['name'];

    if ($existing) {
        // Re-activate previously rejected or cancelled record
        $stmt = $db->prepare("
            UPDATE userprofile
            SET profilestatus = 2, display_title = ?, request_notes = ?, reviewer_notes = NULL, reviewed_by = NULL, reviewed_at = NULL, reg_date = CURRENT_TIMESTAMP, status = 1
            WHERE iD = ?
        ");
        $stmt->execute([$defaultTitle, $notes, $existing['iD']]);
        $newProfileId = (int) $existing['iD'];
    } else {
        // Insert userprofile as Pending (status 2)
        $stmt = $db->prepare("
            INSERT INTO userprofile (user, profiletype, profilestatus, display_title, is_default, request_notes, reg_by, reg_date, status)
            VALUES (?, ?, 2, ?, 0, ?, ?, CURRENT_TIMESTAMP, 1)
        ");
        $stmt->execute([$userId, $typeId, $defaultTitle, $notes, $userId]);
        $newProfileId = (int) $db->lastInsertId();
    }

    // Insert audit record
    $stmt = $db->prepare("
        INSERT INTO profilerequestaudit (userprofile, action, from_status, to_status, performed_by, notes, reg_by, reg_date, status)
        VALUES (?, 'request_submitted', NULL, 2, ?, ?, ?, CURRENT_TIMESTAMP, 1)
    ");
    $stmt->execute([$newProfileId, $userId, $notes, $userId]);

    // Send in-app notification to the applicant
    NotificationHelper::notify(
        $userId,
        'Profile Request Submitted',
        "Your request for a {$pType['name']} account has been received and is pending vetting and review.",
        'profile/view',
        'info',
        'fa-user-clock'
    );

    // Send in-app notification to administrators
    NotificationHelper::notifyAdmins(
        'New Profile Application',
        "{$user->name} has requested an {$pType['name']} profile ({$defaultTitle}).",
        'userprofiles',
        'info',
        'fa-id-card'
    );

    // Refresh user & profiles
    $freshUser = enrichMobileUser($user);

    sendMobileJson([
        'status'   => 1,
        'message'  => 'Your request for ' . $pType['name'] . ' has been submitted for review.',
        'profiles' => $freshUser['profiles'],
        'user'     => $freshUser,
    ]);
});

// Revoke / Cancel Profile Application
$handleRevokeProfile = function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    $profileId = (int)($_POST['profile_id'] ?? 0);
    $typeCode = strtolower(trim($_POST['type_code'] ?? ($_POST['track_code'] ?? '')));
    $appId = (int)($_POST['application_id'] ?? 0);
    $reason = trim($_POST['reason'] ?? 'Application withdrawn by applicant');

    if ($profileId <= 0 && empty($typeCode) && $appId <= 0) {
        sendMobileJson(['status' => 0, 'message' => 'Profile or application identifier required.'], 400);
    }

    // Locate target profile record if available
    $profile = null;
    if ($profileId > 0) {
        $stmt = $db->prepare("
            SELECT up.*, pt.name as type_name, pt.code as type_code, ps.code as status_code
            FROM userprofile up
            JOIN profiletype pt ON up.profiletype = pt.iD
            JOIN profilestatus ps ON up.profilestatus = ps.iD
            WHERE up.iD = ? AND up.user = ?
        ");
        $stmt->execute([$profileId, $userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif (!empty($typeCode)) {
        $stmt = $db->prepare("
            SELECT up.*, pt.name as type_name, pt.code as type_code, ps.code as status_code
            FROM userprofile up
            JOIN profiletype pt ON up.profiletype = pt.iD
            JOIN profilestatus ps ON up.profilestatus = ps.iD
            WHERE up.user = ? AND pt.code = ?
        ");
        $stmt->execute([$userId, $typeCode]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Also check for rosterapplication if appId given or typeCode is apprentice/associate
    $rosterApp = null;
    if ($appId > 0) {
        $stmt = $db->prepare("SELECT ra.*, at.code as track_code, at.name as track_name FROM rosterapplication ra JOIN applicationtrack at ON ra.applicationtrack = at.iD WHERE ra.iD = ? AND ra.user = ?");
        $stmt->execute([$appId, $userId]);
        $rosterApp = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($typeCode === 'apprentice' || $typeCode === 'associate') {
        $trackId = ($typeCode === 'associate') ? 2 : 1;
        $stmt = $db->prepare("SELECT ra.*, at.code as track_code, at.name as track_name FROM rosterapplication ra JOIN applicationtrack at ON ra.applicationtrack = at.iD WHERE ra.user = ? AND ra.applicationtrack = ? AND ra.applicationstatus != 5 ORDER BY ra.iD DESC LIMIT 1");
        $stmt->execute([$userId, $trackId]);
        $rosterApp = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$profile && !$rosterApp) {
        sendMobileJson(['status' => 0, 'message' => 'Application record not found.'], 404);
    }

    $typeName = $profile ? $profile['type_name'] : ($rosterApp ? $rosterApp['track_name'] : ucfirst($typeCode));
    $profileStatus = $profile ? (int) $profile['profilestatus'] : 2;

    // Prevent revoking active/approved accounts directly from here
    if ($profileStatus === 3 || ($rosterApp && (int)$rosterApp['applicationstatus'] === 5)) {
        sendMobileJson(['status' => 0, 'message' => 'Active approved accounts cannot be withdrawn directly. Please contact administration.'], 400);
    }

    // 1. Delete userprofile & related audits if found
    if ($profile) {
        $targetProfileId = (int) $profile['iD'];
        $db->prepare("DELETE FROM profilerequestaudit WHERE userprofile = ?")->execute([$targetProfileId]);
        $db->prepare("DELETE FROM userprofile WHERE iD = ?")->execute([$targetProfileId]);
    }

    // 2. If staff/client membership is pending, remove it as well
    if ($typeCode === 'staff' || $typeCode === 'client' || ($profile && in_array((int)$profile['profiletype'], [4, 5]))) {
        $db->prepare("DELETE FROM clientmembership WHERE user = ? AND status = 2")->execute([$userId]);
    }

    // 3. If Apprentice or Associate, thoroughly clean up roster application and all child tables
    if ($typeCode === 'apprentice' || $typeCode === 'associate' || $rosterApp || ($profile && in_array((int)$profile['profiletype'], [2, 3]))) {
        $trackId = ($typeCode === 'associate' || ($profile && (int)$profile['profiletype'] === 3)) ? 2 : 1;
        
        $rosterAppsQuery = $db->prepare("SELECT iD FROM rosterapplication WHERE user = ? AND (applicationtrack = ? OR iD = ?) AND applicationstatus != 5");
        $rosterAppsQuery->execute([$userId, $trackId, $appId]);
        $appRows = $rosterAppsQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($appRows as $appRow) {
            $rAppId = (int)$appRow['iD'];
            $db->prepare("DELETE FROM rosterdocument WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM rosterskill WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM rosterqualification WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM rosterworkhistory WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM rosterreferee WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM rosterjudgementresponse WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM rosterassessment WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM rosterstatusevent WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM apprenticeprofile WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM associateprofile WHERE rosterapplication = ?")->execute([$rAppId]);
            $db->prepare("DELETE FROM rosterapplication WHERE iD = ?")->execute([$rAppId]);
        }
    }

    // In-app notification for applicant
    NotificationHelper::notify(
        $userId,
        'Application Withdrawn',
        "Your application for {$typeName} account has been withdrawn and removed.",
        'dashboard/home',
        'info',
        'fa-times-circle'
    );

    // Refresh user & profiles
    $freshUser = enrichMobileUser($user);

    sendMobileJson([
        'status'   => 1,
        'message'  => "Your application for {$typeName} has been revoked and removed.",
        'profiles' => $freshUser['profiles'],
        'user'     => $freshUser,
    ]);
};

$router->addRoute('POST', '/api/mobile/user/revoke-profile', $handleRevokeProfile);
$router->addRoute('POST', '/api/mobile/user/cancel-profile-request', $handleRevokeProfile);
$router->addRoute('POST', '/api/mobile/roster/revoke-application', $handleRevokeProfile);

// --------------------------------------------------------------------------
// 14b. Update Basic User Profile (Name, Email)
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/user/update-profile', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized. Please sign in.'], 401);
    }

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $currentPassword = $_POST['current_password'] ?? '';

    if (empty($name)) {
        sendMobileJson(['status' => 0, 'message' => 'Full name cannot be empty.'], 400);
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendMobileJson(['status' => 0, 'message' => 'Please provide a valid email address.'], 400);
    }

    // If current password provided, verify it
    if (!empty($currentPassword)) {
        if (!$user->validate($currentPassword)) {
            sendMobileJson(['status' => 0, 'message' => 'Current password verification failed.'], 400);
        }
    }

    // Check if email taken by another user
    $existing = User::findByQuery("SELECT * FROM user WHERE email = ? AND iD != ?", [$email, $user->iD]);
    if (!empty($existing)) {
        sendMobileJson(['status' => 0, 'message' => 'The email address is already in use by another account.'], 400);
    }

    $db = (new Database())->getPDO();
    $db->prepare("UPDATE user SET name = ?, email = ? WHERE iD = ?")->execute([$name, $email, $user->iD]);
    $user->name = $name;
    $user->email = $email;

    // Also update associated rosterapplication legal_name if exists
    $db->prepare("UPDATE rosterapplication SET legal_name = ?, email = ? WHERE user = ?")->execute([$name, $email, $user->iD]);

    // Also update staffprofile if user is staff
    $nameParts = explode(' ', $name, 2);
    $firstName = $nameParts[0];
    $surname = $nameParts[1] ?? '';
    $db->prepare("UPDATE staffprofile SET first_name = ?, surname = ?, work_email = ? WHERE user = ?")->execute([$firstName, $surname, $email, $user->iD]);

    $enriched = enrichMobileUser($user);

    sendMobileJson([
        'status' => 1,
        'message' => 'Profile details updated successfully.',
        'user' => $enriched
    ]);
});

// --------------------------------------------------------------------------
// 14c. Change User Password
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/user/change-password', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized. Please sign in.'], 401);
    }

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword)) {
        sendMobileJson(['status' => 0, 'message' => 'Please enter your current password.'], 400);
    }

    if (!$user->validate($currentPassword)) {
        sendMobileJson(['status' => 0, 'message' => 'Current password is incorrect.'], 400);
    }

    if (strlen($newPassword) < 6) {
        sendMobileJson(['status' => 0, 'message' => 'New password must be at least 6 characters long.'], 400);
    }

    if ($newPassword !== $confirmPassword) {
        sendMobileJson(['status' => 0, 'message' => 'New password and confirmation do not match.'], 400);
    }

    $db = (new Database())->getPDO();
    $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $upd = $db->prepare("UPDATE user_login SET password = ?, failed_login = 0 WHERE user = ?");
    $upd->execute([$newHash, $user->iD]);

    $check = $db->prepare("SELECT iD FROM user_login WHERE user = ?");
    $check->execute([$user->iD]);
    if (!$check->fetch()) {
        $ins = $db->prepare("INSERT INTO user_login (user, password, reg_by, status) VALUES (?, ?, ?, 1)");
        $ins->execute([$user->iD, $newHash, $user->iD]);
    }

    \App\Helpers\PasswordResume::enroll((int) $user->iD, $newPassword);

    sendMobileJson([
        'status' => 1,
        'message' => 'Password changed successfully.'
    ]);
});

// --------------------------------------------------------------------------
// 15. User Personal Details Profile
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/user/personal-details', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }
    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    $stmt = $db->prepare("SELECT * FROM rosterapplication WHERE user = ? ORDER BY iD DESC LIMIT 1");
    $stmt->execute([$userId]);
    $details = $stmt->fetch(PDO::FETCH_ASSOC);

    // Resolve province name if numeric ID or open string
    if ($details) {
        if (empty($details['province_name'])) {
            if (!empty($details['zimprovince']) && is_numeric($details['zimprovince'])) {
                $pStmt = $db->prepare("SELECT name FROM zimprovince WHERE iD = ?");
                $pStmt->execute([(int)$details['zimprovince']]);
                $pRow = $pStmt->fetch(PDO::FETCH_ASSOC);
                $details['province_name'] = $pRow ? $pRow['name'] : (string)$details['zimprovince'];
            } else {
                $details['province_name'] = !empty($details['zimprovince']) ? (string)$details['zimprovince'] : '';
            }
        }
    }

    $genders = $db->query("SELECT iD, name FROM gender")->fetchAll(PDO::FETCH_ASSOC);
    $provinces = $db->query("SELECT iD, name FROM zimprovince ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status'    => 1,
        'details'   => $details ?: [
            'legal_name'         => $user->name,
            'preferred_name'     => '',
            'email'              => $user->email,
            'mobile_number'      => '',
            'whatsapp_number'    => '',
            'date_of_birth'      => '',
            'gender'             => '',
            'city'               => '',
            'suburb'             => '',
            'zimprovince'        => '',
            'province_name'      => '',
            'country'            => 'Zimbabwe',
            'nationality'        => 'Zimbabwean',
            'work_permit_number' => '',
        ],
        'genders'   => $genders,
        'provinces' => $provinces,
    ]);
});

$router->addRoute('POST', '/api/mobile/user/personal-details', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }
    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    $legalName = trim($_POST['legal_name'] ?? '');
    $preferredName = trim($_POST['preferred_name'] ?? '');
    $mobile = trim($_POST['mobile_number'] ?? '');
    $whatsapp = trim($_POST['whatsapp_number'] ?? '');
    $dob = trim($_POST['date_of_birth'] ?? '');
    $gender = !empty($_POST['gender']) ? (int)$_POST['gender'] : null;
    $city = trim($_POST['city'] ?? '');
    $suburb = trim($_POST['suburb'] ?? '');
    
    // Open Province handling: accepts ID or open text name safely satisfying foreign keys
    $provinceRaw = trim($_POST['zimprovince'] ?? '');
    $provinceId = null;
    $provinceName = $provinceRaw;

    if (is_numeric($provinceRaw) && (int)$provinceRaw > 0) {
        $provinceId = (int)$provinceRaw;
        $stmtProv = $db->prepare("SELECT name FROM zimprovince WHERE iD = ?");
        $stmtProv->execute([$provinceId]);
        $rowProv = $stmtProv->fetch(PDO::FETCH_ASSOC);
        if ($rowProv) {
            $provinceName = $rowProv['name'];
        }
    } elseif (!empty($provinceRaw)) {
        $stmtProv = $db->prepare("SELECT iD, name FROM zimprovince WHERE LOWER(TRIM(name)) = LOWER(TRIM(?)) LIMIT 1");
        $stmtProv->execute([$provinceRaw]);
        $rowProv = $stmtProv->fetch(PDO::FETCH_ASSOC);
        if ($rowProv) {
            $provinceId = (int)$rowProv['iD'];
            $provinceName = $rowProv['name'];
        } else {
            // Open string for non-Zimbabwe or customized province/state (keep provinceId null so FK is satisfied)
            $provinceId = null;
            $provinceName = $provinceRaw;
        }
    }

    $country = trim($_POST['country'] ?? 'Zimbabwe');
    $nationality = trim($_POST['nationality'] ?? 'Zimbabwean');
    $idNumber = trim($_POST['work_permit_number'] ?? '');

    if (empty($legalName)) {
        sendMobileJson(['status' => 0, 'message' => 'Legal name is required.'], 400);
    }
    if (empty($mobile)) {
        sendMobileJson(['status' => 0, 'message' => 'Mobile number is required.'], 400);
    }
    if (empty($city)) {
        sendMobileJson(['status' => 0, 'message' => 'City is required.'], 400);
    }

    // Check existing record
    $stmt = $db->prepare("SELECT iD FROM rosterapplication WHERE user = ? ORDER BY iD DESC LIMIT 1");
    $stmt->execute([$userId]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $stmt = $db->prepare("
            UPDATE rosterapplication SET 
                legal_name = ?, preferred_name = ?, mobile_number = ?, whatsapp_number = ?,
                date_of_birth = ?, gender = ?, city = ?, suburb = ?, zimprovince = ?, province_name = ?,
                country = ?, nationality = ?, work_permit_number = ?
            WHERE iD = ?
        ");
        $stmt->execute([
            $legalName, $preferredName, $mobile, $whatsapp,
            $dob ?: null, $gender, $city, $suburb, $provinceId, $provinceName,
            $country, $nationality, $idNumber,
            $existing['iD']
        ]);
    } else {
        $stmt = $db->prepare("
            INSERT INTO rosterapplication (
                user, applicationtrack, applicationstatus, primaryfunction, legal_name, preferred_name, email,
                mobile_number, whatsapp_number, date_of_birth, gender, city, suburb, zimprovince, province_name,
                country, nationality, work_permit_number, reg_by, reg_date, status
            ) VALUES (
                ?, 1, 1, 7, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, CURRENT_TIMESTAMP, 1
            )
        ");
        $stmt->execute([
            $userId, $legalName, $preferredName, $user->email,
            $mobile, $whatsapp, $dob ?: null, $gender, $city, $suburb, $provinceId, $provinceName,
            $country, $nationality, $idNumber, $userId
        ]);
    }

    // Synchronize base user name if updated
    if ($legalName !== $user->name) {
        $db->prepare("UPDATE user SET name = ? WHERE iD = ?")->execute([$legalName, $userId]);
        $user->name = $legalName;
    }

    $userData = enrichMobileUser($user);

    sendMobileJson([
        'status'  => 1,
        'message' => 'Personal details updated successfully.',
        'user'    => $userData,
    ]);
});

// --------------------------------------------------------------------------
// 16. User Qualifications Profile
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/user/qualifications', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }
    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    $stmt = $db->prepare("
        SELECT rq.*, qt.name as type_name 
        FROM rosterqualification rq 
        JOIN rosterapplication ra ON rq.rosterapplication = ra.iD 
        LEFT JOIN qualificationtype qt ON rq.qualificationtype = qt.iD
        WHERE ra.user = ? AND rq.status = 1 
        ORDER BY rq.date_obtained DESC, rq.iD DESC
    ");
    $stmt->execute([$userId]);
    $quals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $types = $db->query("SELECT iD, code, name FROM qualificationtype WHERE status = 1 ORDER BY sort_order ASC")->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status'              => 1,
        'qualifications'      => $quals,
        'qualification_types' => $types,
    ]);
});

$router->addRoute('POST', '/api/mobile/user/qualifications/save', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }
    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    $qualId = !empty($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $institution = trim($_POST['institution_name'] ?? '');
    $field = trim($_POST['field_of_study'] ?? '');
    $typeId = !empty($_POST['qualificationtype']) ? (int)$_POST['qualificationtype'] : 6;
    $dateObtained = trim($_POST['date_obtained'] ?? '');

    if (empty($title)) {
        sendMobileJson(['status' => 0, 'message' => 'Qualification title is required.'], 400);
    }
    if (empty($institution)) {
        sendMobileJson(['status' => 0, 'message' => 'Institution name is required.'], 400);
    }

    // Ensure rosterapplication exists for user
    $stmt = $db->prepare("SELECT iD FROM rosterapplication WHERE user = ? ORDER BY iD DESC LIMIT 1");
    $stmt->execute([$userId]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$app) {
        $stmt = $db->prepare("
            INSERT INTO rosterapplication (user, applicationtrack, applicationstatus, primaryfunction, legal_name, email, mobile_number, reg_by, reg_date, status)
            VALUES (?, 1, 1, 7, ?, ?, '+263770000000', ?, CURRENT_TIMESTAMP, 1)
        ");
        $stmt->execute([$userId, $user->name, $user->email, $userId]);
        $appId = (int)$db->lastInsertId();
    } else {
        $appId = (int)$app['iD'];
    }

    if ($qualId > 0) {
        // Update existing (verifying ownership)
        $checkStmt = $db->prepare("
            SELECT rq.iD FROM rosterqualification rq 
            JOIN rosterapplication ra ON rq.rosterapplication = ra.iD 
            WHERE rq.iD = ? AND ra.user = ?
        ");
        $checkStmt->execute([$qualId, $userId]);
        if (!$checkStmt->fetch()) {
            sendMobileJson(['status' => 0, 'message' => 'Qualification record not found.'], 404);
        }

        $stmt = $db->prepare("
            UPDATE rosterqualification SET 
                title = ?, institution_name = ?, field_of_study = ?, qualificationtype = ?, date_obtained = ?
            WHERE iD = ?
        ");
        $stmt->execute([$title, $institution, $field, $typeId, $dateObtained ?: null, $qualId]);
    } else {
        $stmt = $db->prepare("
            INSERT INTO rosterqualification (
                rosterapplication, qualificationtype, title, institution_name, field_of_study, date_obtained, reg_by, reg_date, status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1
            )
        ");
        $stmt->execute([$appId, $typeId, $title, $institution, $field, $dateObtained ?: null, $userId]);
    }

    $userData = enrichMobileUser($user);

    sendMobileJson([
        'status'  => 1,
        'message' => 'Qualification record saved successfully.',
        'user'    => $userData,
    ]);
});

$router->addRoute('POST', '/api/mobile/user/qualifications/delete', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }
    $db = (new Database())->getPDO();
    $userId = (int) $user->iD;

    $qualId = (int)($_POST['id'] ?? 0);
    if (!$qualId) {
        sendMobileJson(['status' => 0, 'message' => 'Invalid qualification ID.'], 400);
    }

    $stmt = $db->prepare("
        DELETE FROM rosterqualification 
        WHERE iD = ? AND rosterapplication IN (SELECT iD FROM rosterapplication WHERE user = ?)
    ");
    $stmt->execute([$qualId, $userId]);

    $userData = enrichMobileUser($user);

    sendMobileJson([
        'status'  => 1,
        'message' => 'Qualification deleted successfully.',
        'user'    => $userData,
    ]);
});

// --------------------------------------------------------------------------
// 17. Client Organizations & Member Roles for Company Representative
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/companies', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }
    $db = (new Database())->getPDO();

    $companies = $db->query("
        SELECT iD, legal_name, trading_name, city, country 
        FROM clientorganization 
        WHERE status = 1 
        ORDER BY COALESCE(NULLIF(trading_name, ''), legal_name) ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $roles = $db->query("
        SELECT iD, code, name, description 
        FROM clientmemberrole 
        WHERE status = 1 
        ORDER BY iD ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status'    => 1,
        'companies' => $companies,
        'roles'     => $roles,
    ]);
});

// --------------------------------------------------------------------------
// 18. Profile Completion Status
// --------------------------------------------------------------------------
// 19. Admin Profile Requests Ledger
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/admin/profile-requests', function () {
    $admin = getMobileUser();
    if (!$admin) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }
    $role = (int) $admin->role;
    if ($role !== 1 && $role !== 8 && $role !== 6) {
        sendMobileJson(['status' => 0, 'message' => 'Forbidden. Admin privileges required.'], 403);
    }

    $db = (new Database())->getPDO();
    $stmt = $db->query("
        SELECT up.iD as profile_id, up.user as user_id, u.name as user_name, u.email as user_email,
               pt.code as type_code, pt.name as type_name, pt.icon as type_icon,
               ps.code as status_code, ps.name as status_name,
               up.display_title, up.request_notes, up.reg_date
        FROM userprofile up
        JOIN user u ON up.user = u.iD
        JOIN profiletype pt ON up.profiletype = pt.iD
        JOIN profilestatus ps ON up.profilestatus = ps.iD
        WHERE up.profilestatus = 2
        ORDER BY up.iD DESC
    ");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($requests as &$req) {
        $uId = (int) $req['user_id'];
        $pStmt = $db->prepare("SELECT legal_name, mobile_number, city, suburb FROM rosterapplication WHERE user = ? ORDER BY iD DESC LIMIT 1");
        $pStmt->execute([$uId]);
        $req['personal'] = $pStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        $qStmt = $db->prepare("
            SELECT rq.title, rq.institution_name, rq.field_of_study, qt.name as type_name 
            FROM rosterqualification rq 
            JOIN rosterapplication ra ON rq.rosterapplication = ra.iD 
            LEFT JOIN qualificationtype qt ON rq.qualificationtype = qt.iD 
            WHERE ra.user = ? AND rq.status = 1 
            ORDER BY rq.date_obtained DESC
        ");
        $qStmt->execute([$uId]);
        $req['qualifications'] = $qStmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    sendMobileJson([
        'status'   => 1,
        'requests' => $requests,
    ]);
});

// --------------------------------------------------------------------------
// 20. Admin Review & Approve/Reject Profile Request
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/admin/review-profile', function () {
    $admin = getMobileUser();
    if (!$admin) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }
    $role = (int) $admin->role;
    if ($role !== 1 && $role !== 8 && $role !== 6) {
        sendMobileJson(['status' => 0, 'message' => 'Forbidden. Admin privileges required.'], 403);
    }

    $db = (new Database())->getPDO();
    $profileId = (int) ($_POST['profile_id'] ?? 0);
    $action = trim($_POST['action'] ?? '');
    $notes = trim($_POST['reviewer_notes'] ?? '');

    $stmt = $db->prepare("SELECT * FROM userprofile WHERE iD = ?");
    $stmt->execute([$profileId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        sendMobileJson(['status' => 0, 'message' => 'Profile request not found.'], 404);
    }

    $applicantUserId = (int) $profile['user'];
    $typeId = (int) $profile['profiletype'];
    $adminId = (int) $admin->iD;
    $now = date('Y-m-d H:i:s');

    if ($action === 'approve') {
        $stmt = $db->prepare("UPDATE userprofile SET profilestatus = 3, reviewer_notes = ?, reviewed_by = ?, reviewed_at = ? WHERE iD = ?");
        $stmt->execute([$notes, $adminId, $now, $profileId]);

        // Sync user role if candidate
        if ($typeId === 2) { // Apprentice
            $db->prepare("UPDATE user SET role = 5 WHERE iD = ? AND role = 2")->execute([$applicantUserId]);
        } elseif ($typeId === 3) { // Associate
            $db->prepare("UPDATE user SET role = 4 WHERE iD = ? AND role = 2")->execute([$applicantUserId]);
        } elseif ($typeId === 4 || $typeId === 5) { // Staff or Client
            $db->prepare("UPDATE clientmembership SET status = 1 WHERE user = ? AND status = 2")->execute([$applicantUserId]);
        }

        // Audit log
        $stmt = $db->prepare("INSERT INTO profilerequestaudit (userprofile, action, from_status, to_status, performed_by, notes, reg_by, reg_date, status) VALUES (?, 'request_approved', ?, 3, ?, ?, ?, CURRENT_TIMESTAMP, 1)");
        $stmt->execute([$profileId, $profile['profilestatus'], $adminId, $notes ?: 'Approved via mobile command', $adminId]);

        // Notification to applicant
        NotificationHelper::notify(
            $applicantUserId,
            'Account Application Approved!',
            "Your request for a {$profile['display_title']} account has been approved and activated.",
            'dashboard/home',
            'success',
            'fa-check-circle'
        );

        sendMobileJson(['status' => 1, 'message' => 'Account request successfully approved!']);
    } elseif ($action === 'reject') {
        $stmt = $db->prepare("UPDATE userprofile SET profilestatus = 4, reviewer_notes = ?, reviewed_by = ?, reviewed_at = ? WHERE iD = ?");
        $stmt->execute([$notes, $adminId, $now, $profileId]);

        $stmt = $db->prepare("INSERT INTO profilerequestaudit (userprofile, action, from_status, to_status, performed_by, notes, reg_by, reg_date, status) VALUES (?, 'request_rejected', ?, 4, ?, ?, ?, CURRENT_TIMESTAMP, 1)");
        $stmt->execute([$profileId, $profile['profilestatus'], $adminId, $notes ?: 'Declined by reviewer', $adminId]);

        NotificationHelper::notify(
            $applicantUserId,
            'Account Request Update',
            "Your request for a {$profile['display_title']} account has been declined. Notes: " . ($notes ?: 'Criteria not met'),
            'profile/view',
            'warning',
            'fa-times-circle'
        );

        sendMobileJson(['status' => 1, 'message' => 'Account request declined.']);
    } else {
        sendMobileJson(['status' => 0, 'message' => 'Invalid action. Must be approve or reject.'], 400);
    }
});

// --------------------------------------------------------------------------
// OTA Auto-Update Check Endpoint
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/ota/check', function () {
    global $siteConfig;
    $configFile = dirname(__DIR__) . '/config/mobile_ota.json';
    if (!file_exists($configFile)) {
        sendMobileJson([
            'status' => 1,
            'update_available' => false,
            'message' => 'No OTA updates configured.'
        ]);
    }

    $config = json_decode(file_get_contents($configFile), true) ?: [];
    $latestVersion = $config['bundle_version'] ?? '1.0.0';
    $bundleHash = $config['bundle_hash'] ?? '';
    $bundleUrl = $config['bundle_url'] ?? '/public/downloads/ota_bundle.zip';
    $minApkVersion = (int)($config['min_apk_version'] ?? 1);
    $releaseNotes = $config['release_notes'] ?? '';

    $clientBundleVersion = trim($_GET['bundle_version'] ?? '0.0.0');
    $clientApkVersion = (int)($_GET['apk_version'] ?? 1);

    // Build absolute URL for bundle download
    $host = $_SERVER['HTTP_HOST'] ?? 'portal.tsigiro.co.zw';
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    if (strpos($host, 'portal.tsigiro.co.zw') !== false || strpos($host, 'trainit.co.zw') !== false) {
        $downloadBase = "https://$host";
    } elseif (strpos($host, '10.0.2.2') !== false || strpos($host, 'localhost') !== false) {
        $downloadBase = "$protocol://$host/trainit";
    } else {
        $downloadBase = $siteConfig->siteUrl ?? 'https://portal.tsigiro.co.zw';
    }
    $fullDownloadUrl = rtrim($downloadBase, '/') . '/' . ltrim($bundleUrl, '/');

    // Compare versions (supports semantic versioning e.g. 1.0.1 > 1.0.0 or string inequality)
    $updateAvailable = version_compare($latestVersion, $clientBundleVersion, '>');

    sendMobileJson([
        'status' => 1,
        'update_available' => $updateAvailable,
        'latest_bundle_version' => $latestVersion,
        'client_bundle_version' => $clientBundleVersion,
        'bundle_url' => $fullDownloadUrl,
        'bundle_hash' => $bundleHash,
        'bundle_size' => $config['bundle_size_bytes'] ?? 0,
        'release_notes' => $releaseNotes,
        'min_apk_version' => $minApkVersion,
        'apk_update_required' => ($minApkVersion > $clientApkVersion),
        'apk_url' => rtrim($downloadBase, '/') . '/public/downloads/tsigiro-mobile.apk',
        'updated_at' => $config['updated_at'] ?? ''
    ]);
});
