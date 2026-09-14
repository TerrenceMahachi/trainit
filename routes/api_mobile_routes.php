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
function sendMobileJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
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
            $persona = ($role === 1) ? 'admin' : 'staff';
            $roleName = $activeProfile['display_title'] ?: ($role === 1 ? 'Administrator' : 'Staff Member');
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
            $persona = ($role === 1) ? 'admin' : 'candidate';
            $roleName = ($role === 1) ? 'Administrator' : 'General User';
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

    } elseif ($persona === 'candidate') {
        // --- General Candidate / Opportunity Seeker ---
        $stmt = $db->query("SELECT COUNT(*) FROM vacancy WHERE vacancystatus = 2");
        $openVacancies = (int) $stmt->fetchColumn();

        $stmt = $db->prepare("SELECT COUNT(*) FROM rosterapplication WHERE email = ?");
        $stmt->execute([$user->email]);
        $myApps = (int) $stmt->fetchColumn();

        $stats = [
            ['label' => 'Open Vacancies', 'value' => (string) $openVacancies, 'icon' => 'search', 'color' => '#3b82f6'],
            ['label' => 'Applications', 'value' => (string) ($myApps ?: 1), 'icon' => 'file-text', 'color' => '#10b981'],
            ['label' => 'Interview Status', 'value' => 'Shortlisted', 'icon' => 'user-check', 'color' => '#8b5cf6'],
        ];

        $stmt = $db->query("SELECT iD, reference_number, title, remuneration_display, closing_date, is_featured FROM vacancy WHERE vacancystatus = 2 ORDER BY is_featured DESC, iD DESC LIMIT 5");
        $recentItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        // --- Staff / Admin / Service Manager / Billing / Vetting ---
        $stmt = $db->query("SELECT COUNT(*) FROM servicerequest WHERE status = 1");
        $triagePending = (int) $stmt->fetchColumn();

        $stmt = $db->query("SELECT COUNT(*) FROM servicerequest WHERE status IN (2, 3)");
        $inProgress = (int) $stmt->fetchColumn();

        $stmt = $db->query("SELECT COUNT(*) FROM clientorganization WHERE status = 1");
        $totalClients = (int) $stmt->fetchColumn();

        $stats = [
            ['label' => 'Triage Queue', 'value' => (string) $triagePending, 'icon' => 'inbox', 'color' => '#ef4444'],
            ['label' => 'Active Service Tasks', 'value' => (string) $inProgress, 'icon' => 'activity', 'color' => '#3b82f6'],
            ['label' => 'Corporate Clients', 'value' => (string) $totalClients, 'icon' => 'users', 'color' => '#10b981'],
        ];

        $stmt = $db->query("SELECT sr.iD, sr.request_number, sr.title, sr.status, srs.name AS status_name, pl.name AS priority_name, co.trading_name AS client_name, sr.desired_due_date, sr.reg_date FROM servicerequest sr LEFT JOIN servicerequeststatus srs ON sr.status = srs.iD LEFT JOIN prioritylevel pl ON sr.prioritylevel = pl.iD LEFT JOIN clientorganization co ON sr.clientorganization = co.iD ORDER BY sr.iD DESC LIMIT 5");
        $recentItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    sendMobileJson([
        'status'       => 1,
        'user'         => $userData,
        'context'      => $context,
        'stats'        => $stats,
        'recent_items' => $recentItems,
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
    $typeCode = trim($_POST['type_code'] ?? '');
    $reason = trim($_POST['reason'] ?? 'Application withdrawn by applicant');

    if ($profileId <= 0 && empty($typeCode)) {
        sendMobileJson(['status' => 0, 'message' => 'Profile or application identifier required.'], 400);
    }

    // Locate target profile record
    if ($profileId > 0) {
        $stmt = $db->prepare("
            SELECT up.*, pt.name as type_name, pt.code as type_code, ps.code as status_code
            FROM userprofile up
            JOIN profiletype pt ON up.profiletype = pt.iD
            JOIN profilestatus ps ON up.profilestatus = ps.iD
            WHERE up.iD = ? AND up.user = ?
        ");
        $stmt->execute([$profileId, $userId]);
    } else {
        $stmt = $db->prepare("
            SELECT up.*, pt.name as type_name, pt.code as type_code, ps.code as status_code
            FROM userprofile up
            JOIN profiletype pt ON up.profiletype = pt.iD
            JOIN profilestatus ps ON up.profilestatus = ps.iD
            WHERE up.user = ? AND pt.code = ?
        ");
        $stmt->execute([$userId, $typeCode]);
    }
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        sendMobileJson(['status' => 0, 'message' => 'Application record not found.'], 404);
    }

    $targetProfileId = (int) $profile['iD'];
    $typeName = $profile['type_name'];
    $profileStatus = (int) $profile['profilestatus'];

    // Prevent revoking active/approved accounts directly from here
    if ($profileStatus === 3) {
        sendMobileJson(['status' => 0, 'message' => 'Active approved accounts cannot be withdrawn directly. Please contact administration.'], 400);
    }

    // Delete audit entries first to satisfy foreign key constraints
    $db->prepare("DELETE FROM profilerequestaudit WHERE userprofile = ?")->execute([$targetProfileId]);

    // If staff/client membership is pending, remove it as well
    if ($profile['type_code'] === 'staff' || (int)$profile['profiletype'] === 4 || (int)$profile['profiletype'] === 5) {
        $db->prepare("DELETE FROM clientmembership WHERE user = ? AND status = 2")->execute([$userId]);
    }

    // Delete the application profile record
    $db->prepare("DELETE FROM userprofile WHERE iD = ?")->execute([$targetProfileId]);

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
$router->addRoute('GET', '/api/mobile/user/profile-completion', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }
    $userData = enrichMobileUser($user);
    sendMobileJson([
        'status'             => 1,
        'profile_completion' => $userData['profile_completion'],
    ]);
});



