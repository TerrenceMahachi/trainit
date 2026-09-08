<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Login;
use App\Models\Staffprofile;
use App\Models\StaffInvite;
use App\Helpers\Auth;
use App\Helpers\Mailer;
use DateTime;

class StaffController
{
    /**
     * Internal staff role IDs.
     */
    public const STAFF_ROLES = [1, 6, 7, 8];

    /**
     * Staff Directory Console.
     */
    public function index()
    {
        global $siteConfig;
        if (!Auth::check() || !Auth::isAdmin()) {
            header("Location: " . $siteConfig->siteUrl . "/dashboard");
            exit;
        }

        $rolesIn = implode(',', self::STAFF_ROLES);
        $search = trim($_GET['search'] ?? '');
        
        $sql = "SELECT u.* FROM user u WHERE u.role IN ({$rolesIn})";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY u.role ASC, u.name ASC";
        $users = User::findByQuery($sql, $params);

        // Map users with their staff profile
        $staffRecords = [];
        foreach ($users as $u) {
            $profiles = Staffprofile::where('user', $u->iD);
            $staffRecords[] = [
                'user'    => $u,
                'profile' => !empty($profiles) ? $profiles[0] : null,
                'role'    => $u->role()
            ];
        }

        // Pending Invitations
        $pendingInvites = StaffInvite::findByQuery(
            "SELECT * FROM staff_invite WHERE used = 0 AND expires_at > datetime('now') ORDER BY iD DESC"
        );

        $data = [
            'title'          => 'Staff & Operations Directory',
            'user'           => Auth::user(),
            'staffRecords'   => $staffRecords,
            'pendingInvites' => $pendingInvites,
            'search'         => $search
        ];

        echo view('staff.index', compact('data'));
        exit;
    }

    /**
     * Show Admin Direct Creation Form.
     */
    public function create()
    {
        global $siteConfig;
        if (!Auth::check() || !Auth::isAdmin()) {
            header("Location: " . $siteConfig->siteUrl . "/dashboard");
            exit;
        }

        $roles = Role::findByQuery("SELECT * FROM user_role WHERE iD IN (1, 6, 7, 8)");
        $data = [
            'title' => 'Register New Staff Member',
            'user'  => Auth::user(),
            'roles' => $roles
        ];

        echo view('staff.create', compact('data'));
        exit;
    }

    /**
     * Handle Admin Direct Staff Creation (POST).
     */
    public function store()
    {
        header('Content-Type: application/json');
        if (!Auth::check() || !Auth::isAdmin()) {
            echo json_encode(['status' => 0, 'msg' => 'Unauthorized. Administrator access required.']);
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $name = trim($_POST['name'] ?? ($firstName . ' ' . $surname));
        $roleId = (int)($_POST['role'] ?? 0);
        $jobTitle = trim($_POST['job_title'] ?? '');
        $department = trim($_POST['department'] ?? 'Operations');
        $employeeNumber = trim($_POST['employee_number'] ?? '');
        $station = trim($_POST['station'] ?? 'Harare HQ');
        $dateOfEmployment = trim($_POST['date_of_employment'] ?? date('Y-m-d'));
        $natureOfEmployment = trim($_POST['nature_of_employment'] ?? 'ORDINARY');
        $salary = trim($_POST['salary'] ?? '');
        $sendInvite = isset($_POST['send_invite_toggle']) && $_POST['send_invite_toggle'] === '1';

        // Basic validation
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 0, 'msg' => 'A valid work email address is required.']);
            exit;
        }

        if (empty($name)) {
            echo json_encode(['status' => 0, 'msg' => 'Staff full name is required.']);
            exit;
        }

        if (!in_array($roleId, self::STAFF_ROLES, true)) {
            echo json_encode(['status' => 0, 'msg' => 'Please select an authorized staff role.']);
            exit;
        }

        if (empty($jobTitle)) {
            echo json_encode(['status' => 0, 'msg' => 'Job title/occupation is required.']);
            exit;
        }

        // Check if email already registered
        $existingUsers = User::where('email', $email);
        if (!empty($existingUsers)) {
            echo json_encode(['status' => 0, 'msg' => 'A user account with this email already exists on the platform.']);
            exit;
        }

        // If admin chose the "Send Invitation" option instead of direct password
        if ($sendInvite) {
            return $this->dispatchInviteInternal($email, $name, $roleId, $department, $jobTitle);
        }

        $password = trim($_POST['password'] ?? '');
        if (strlen($password) < 6) {
            // Generate a secure temporary password if none provided
            $password = bin2hex(random_bytes(4)) . '!Aa';
        }

        // Create User
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->role = $roleId;
        $user->reg_by = Auth::id() ?? 1;
        $user->status = 1;
        $user->save();

        // Create Login credentials
        $login = new Login();
        $login->user = $user->iD;
        $login->reg_by = Auth::id() ?? 1;
        $login->password = password_hash($password, PASSWORD_BCRYPT);
        $login->status = 1;
        $login->save();

        // Create Staff Profile
        $profile = new Staffprofile();
        $profile->user = $user->iD;
        $profile->employee_number = $employeeNumber ?: 'TRN-' . str_pad((string)$user->iD, 3, '0', STR_PAD_LEFT);
        $profile->job_title = $jobTitle;
        $profile->department = $department;
        $profile->nature_of_employment = $natureOfEmployment;
        $profile->employee_type = 'Employee';
        $profile->date_of_employment = $dateOfEmployment;
        $profile->station = $station;
        $profile->salary = $salary;
        $profile->employment_status = 'active';

        $profile->title = trim($_POST['title'] ?? 'Mr');
        $profile->first_name = $firstName ?: explode(' ', $name)[0];
        $profile->other_names = trim($_POST['other_names'] ?? '');
        $profile->surname = $surname ?: (explode(' ', $name)[1] ?? '');
        $profile->national_id_number = trim($_POST['national_id_number'] ?? '');
        $profile->ssr_number = trim($_POST['ssr_number'] ?? '');
        $profile->birth_certificate_number = trim($_POST['birth_certificate_number'] ?? '');
        $profile->drivers_licence_number = trim($_POST['drivers_licence_number'] ?? '');
        $profile->passport_number = trim($_POST['passport_number'] ?? '');
        $profile->date_of_birth = trim($_POST['date_of_birth'] ?? null);
        $profile->gender = trim($_POST['gender'] ?? 'Male');
        $profile->marital_status = trim($_POST['marital_status'] ?? 'Single');
        $profile->nationality = trim($_POST['nationality'] ?? 'Zimbabwean');
        $profile->citizenship = trim($_POST['citizenship'] ?? 'ZW');

        $profile->street_number = trim($_POST['street_number'] ?? '');
        $profile->street_name = trim($_POST['street_name'] ?? '');
        $profile->suburb = trim($_POST['suburb'] ?? '');
        $profile->town = trim($_POST['town'] ?? 'HARARE');
        $profile->region = trim($_POST['region'] ?? 'HRE');
        $profile->country = trim($_POST['country'] ?? 'Zimbabwe');
        $profile->postal_code = trim($_POST['postal_code'] ?? '');
        $profile->telephone_number = trim($_POST['telephone_number'] ?? '');
        $profile->work_email = $email;
        $profile->personal_email = trim($_POST['personal_email'] ?? '');

        $profile->bank_name = trim($_POST['bank_name'] ?? '');
        $profile->bank_branch = trim($_POST['bank_branch'] ?? '');
        $profile->account_name = trim($_POST['account_name'] ?? '');
        $profile->account_number = trim($_POST['account_number'] ?? '');
        $profile->bank_currency = trim($_POST['bank_currency'] ?? 'USD');

        $profile->emergency_contact_name = trim($_POST['emergency_contact_name'] ?? '');
        $profile->emergency_contact_phone = trim($_POST['emergency_contact_phone'] ?? '');
        $profile->emergency_contact_relationship = trim($_POST['emergency_contact_relationship'] ?? '');

        $profile->reg_by = Auth::id() ?? 1;
        $profile->status = 1;
        $profile->save();

        // Send Welcome email
        $roleObj = Role::find($roleId);
        $roleName = $roleObj ? $roleObj->name : 'Staff Member';
        Mailer::sendStaffWelcome($user, $jobTitle, $roleName, $password);

        echo json_encode([
            'status' => 1,
            'msg'    => "Staff member {$name} registered successfully. Welcome email dispatched.",
            'id'     => $user->iD
        ]);
        exit;
    }

    /**
     * Admin Send Staff Invitation (POST).
     */
    public function sendInvite()
    {
        header('Content-Type: application/json');
        if (!Auth::check() || !Auth::isAdmin()) {
            echo json_encode(['status' => 0, 'msg' => 'Unauthorized.']);
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $roleId = (int)($_POST['role'] ?? 0);
        $department = trim($_POST['department'] ?? 'Operations');
        $jobTitle = trim($_POST['job_title'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 0, 'msg' => 'Valid work email required.']);
            exit;
        }

        if (empty($name)) {
            echo json_encode(['status' => 0, 'msg' => 'Staff name required.']);
            exit;
        }

        if (!in_array($roleId, self::STAFF_ROLES, true)) {
            echo json_encode(['status' => 0, 'msg' => 'Valid staff role required.']);
            exit;
        }

        if (empty($jobTitle)) {
            echo json_encode(['status' => 0, 'msg' => 'Job title required.']);
            exit;
        }

        // Check existing user
        $existing = User::where('email', $email);
        if (!empty($existing)) {
            echo json_encode(['status' => 0, 'msg' => 'An active user already exists with this email address.']);
            exit;
        }

        return $this->dispatchInviteInternal($email, $name, $roleId, $department, $jobTitle);
    }

    /**
     * Helper to dispatch invitation token & email.
     */
    private function dispatchInviteInternal($email, $name, $roleId, $department, $jobTitle)
    {
        global $siteConfig;
        $siteUrl = $siteConfig->siteUrl ?? 'https://trainit.co.zw';

        // Token generation
        $token = bin2hex(random_bytes(24));
        $expires = (new DateTime())->modify('+7 days')->format('Y-m-d H:i:s');

        // Check if there was an unused invite for this email, delete or replace it
        $oldInvites = StaffInvite::findByQuery("SELECT * FROM staff_invite WHERE email = ? AND used = 0", [$email]);
        foreach ($oldInvites as $old) {
            $old->delete();
        }

        $invite = new StaffInvite();
        $invite->email = $email;
        $invite->name = $name;
        $invite->role = $roleId;
        $invite->department = $department;
        $invite->job_title = $jobTitle;
        $invite->token = $token;
        $invite->expires_at = $expires;
        $invite->invited_by = Auth::id() ?? 1;
        $invite->used = 0;
        $invite->save();

        $roleObj = Role::find($roleId);
        $roleName = $roleObj ? $roleObj->name : 'Staff Member';
        $inviteUrl = "{$siteUrl}/staff/onboard?token={$token}";

        Mailer::sendStaffInvitation($email, $name, $roleName, $jobTitle, $department, $inviteUrl);

        echo json_encode([
            'status' => 1,
            'msg'    => "Staff onboarding invitation sent to {$email}. Link valid for 7 days."
        ]);
        exit;
    }

    /**
     * Show Public Staff Onboarding Wizard (GET).
     */
    public function showOnboarding()
    {
        global $siteConfig;
        $token = trim($_GET['token'] ?? '');

        if (empty($token)) {
            $data = ['title' => 'Invalid Link', 'message' => 'Missing invitation token. Please check your invitation email.'];
            echo view('errors.404', compact('data'));
            exit;
        }

        $invites = StaffInvite::findByQuery(
            "SELECT * FROM staff_invite WHERE token = ? AND used = 0 AND expires_at > datetime('now') LIMIT 1",
            [$token]
        );

        if (empty($invites)) {
            $data = [
                'title'   => 'Invitation Expired or Invalid',
                'message' => 'This staff onboarding invitation link is invalid, has already been used, or has expired. Please contact your system administrator.'
            ];
            echo view('errors.404', compact('data'));
            exit;
        }

        $invite = $invites[0];
        $roleObj = Role::find($invite->role);
        $roleName = $roleObj ? $roleObj->name : 'Staff Member';

        $data = [
            'title'    => 'Staff Statutory Onboarding',
            'invite'   => $invite,
            'roleName' => $roleName
        ];

        echo view('staff.onboard', compact('data'));
        exit;
    }

    /**
     * Handle Staff Onboarding Submission (POST).
     */
    public function handleOnboardingSubmit()
    {
        header('Content-Type: application/json');
        $token = trim($_POST['token'] ?? '');

        if (empty($token)) {
            echo json_encode(['status' => 0, 'msg' => 'Missing invitation token.']);
            exit;
        }

        $invites = StaffInvite::findByQuery(
            "SELECT * FROM staff_invite WHERE token = ? AND used = 0 AND expires_at > datetime('now') LIMIT 1",
            [$token]
        );

        if (empty($invites)) {
            echo json_encode(['status' => 0, 'msg' => 'Invitation expired or already used.']);
            exit;
        }

        $invite = $invites[0];

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (strlen($password) < 6) {
            echo json_encode(['status' => 0, 'msg' => 'Password must be at least 6 characters long.']);
            exit;
        }

        if ($password !== $confirmPassword) {
            echo json_encode(['status' => 0, 'msg' => 'Passwords do not match.']);
            exit;
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $nationalId = trim($_POST['national_id_number'] ?? '');
        $dob = trim($_POST['date_of_birth'] ?? '');

        if (empty($firstName) || empty($surname)) {
            echo json_encode(['status' => 0, 'msg' => 'First name and surname are required.']);
            exit;
        }

        if (empty($nationalId)) {
            echo json_encode(['status' => 0, 'msg' => 'National ID Number is required for statutory NSSA registration.']);
            exit;
        }

        $fullName = $firstName . ' ' . $surname;

        // Check if user account already exists with this email
        $existing = User::where('email', $invite->email);
        if (!empty($existing)) {
            echo json_encode(['status' => 0, 'msg' => 'A user with this email address has already completed registration.']);
            exit;
        }

        // Create User
        $user = new User();
        $user->name = $fullName;
        $user->email = $invite->email;
        $user->role = $invite->role;
        $user->reg_by = $invite->invited_by;
        $user->status = 1;
        $user->save();

        // Create Login
        $login = new Login();
        $login->user = $user->iD;
        $login->reg_by = $invite->invited_by;
        $login->password = password_hash($password, PASSWORD_BCRYPT);
        $login->status = 1;
        $login->save();

        // Handle File Uploads (Optional ID Scan, CV)
        $uploadDir = _BASE_PATH . '/storage/uploads/staff/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $nationalIdDoc = $this->uploadDocument('national_id_doc', $uploadDir, 'nid_' . $user->iD);
        $cvDoc = $this->uploadDocument('cv_doc', $uploadDir, 'cv_' . $user->iD);

        // Create Staff Profile
        $profile = new Staffprofile();
        $profile->user = $user->iD;
        $profile->employee_number = 'TRN-' . str_pad((string)$user->iD, 3, '0', STR_PAD_LEFT);
        $profile->job_title = $invite->job_title;
        $profile->department = $invite->department;
        $profile->nature_of_employment = trim($_POST['nature_of_employment'] ?? 'ORDINARY');
        $profile->employee_type = 'Employee';
        $profile->date_of_employment = date('Y-m-d');
        $profile->station = trim($_POST['station'] ?? 'Harare HQ');
        $profile->salary = trim($_POST['salary'] ?? '');
        $profile->employment_status = 'active';

        $profile->title = trim($_POST['title'] ?? 'Mr');
        $profile->first_name = $firstName;
        $profile->other_names = trim($_POST['other_names'] ?? '');
        $profile->surname = $surname;
        $profile->national_id_number = $nationalId;
        $profile->ssr_number = trim($_POST['ssr_number'] ?? '');
        $profile->birth_certificate_number = trim($_POST['birth_certificate_number'] ?? '');
        $profile->drivers_licence_number = trim($_POST['drivers_licence_number'] ?? '');
        $profile->passport_number = trim($_POST['passport_number'] ?? '');
        $profile->date_of_birth = $dob ?: null;
        $profile->gender = trim($_POST['gender'] ?? 'Male');
        $profile->marital_status = trim($_POST['marital_status'] ?? 'Single');
        $profile->nationality = trim($_POST['nationality'] ?? 'Zimbabwean');
        $profile->citizenship = trim($_POST['citizenship'] ?? 'ZW');

        $profile->street_number = trim($_POST['street_number'] ?? '');
        $profile->street_name = trim($_POST['street_name'] ?? '');
        $profile->suburb = trim($_POST['suburb'] ?? '');
        $profile->town = trim($_POST['town'] ?? 'HARARE');
        $profile->region = trim($_POST['region'] ?? 'HRE');
        $profile->country = trim($_POST['country'] ?? 'Zimbabwe');
        $profile->postal_code = trim($_POST['postal_code'] ?? '');
        $profile->telephone_number = trim($_POST['telephone_number'] ?? '');
        $profile->work_email = $invite->email;
        $profile->personal_email = trim($_POST['personal_email'] ?? '');

        $profile->bank_name = trim($_POST['bank_name'] ?? '');
        $profile->bank_branch = trim($_POST['bank_branch'] ?? '');
        $profile->account_name = trim($_POST['account_name'] ?? '');
        $profile->account_number = trim($_POST['account_number'] ?? '');
        $profile->bank_currency = trim($_POST['bank_currency'] ?? 'USD');

        $profile->emergency_contact_name = trim($_POST['emergency_contact_name'] ?? '');
        $profile->emergency_contact_phone = trim($_POST['emergency_contact_phone'] ?? '');
        $profile->emergency_contact_relationship = trim($_POST['emergency_contact_relationship'] ?? '');

        $profile->national_id_doc = $nationalIdDoc;
        $profile->cv_doc = $cvDoc;

        $profile->reg_by = $invite->invited_by;
        $profile->status = 1;
        $profile->save();

        // Mark invite used
        $invite->used = 1;
        $invite->used_at = date('Y-m-d H:i:s');
        $invite->update();

        // Log the user in
        Auth::login($user->iD);

        echo json_encode([
            'status' => 1,
            'msg'    => 'Statutory onboarding completed successfully! Welcome to the team.',
            'redirect' => '/dashboard'
        ]);
        exit;
    }

    /**
     * View Staff Dossier (GET).
     */
    public function view($id)
    {
        global $siteConfig;
        $targetId = (int)$id;

        if (!Auth::check() || (!Auth::isAdmin() && Auth::id() !== $targetId)) {
            header("Location: " . $siteConfig->siteUrl . "/dashboard");
            exit;
        }

        $user = User::find($targetId);
        if (!$user) {
            $data = ['title' => 'Staff Not Found', 'message' => 'Staff account not found.'];
            echo view('errors.404', compact('data'));
            exit;
        }

        $profiles = Staffprofile::where('user', $user->iD);
        $profile = !empty($profiles) ? $profiles[0] : null;

        $data = [
            'title'   => "Staff Profile – {$user->name}",
            'user'    => $user,
            'profile' => $profile,
            'role'    => $user->role()
        ];

        echo view('staff.view', compact('data'));
        exit;
    }

    /**
     * Edit Staff Profile (GET).
     */
    public function edit($id)
    {
        global $siteConfig;
        $targetId = (int)$id;

        if (!Auth::check() || (!Auth::isAdmin() && Auth::id() !== $targetId)) {
            header("Location: " . $siteConfig->siteUrl . "/dashboard");
            exit;
        }

        $user = User::find($targetId);
        if (!$user) {
            $data = ['title' => 'Staff Not Found', 'message' => 'Staff account not found.'];
            echo view('errors.404', compact('data'));
            exit;
        }

        $profiles = Staffprofile::where('user', $user->iD);
        $profile = !empty($profiles) ? $profiles[0] : null;
        $roles = Role::findByQuery("SELECT * FROM user_role WHERE iD IN (1, 6, 7, 8)");

        $data = [
            'title'   => "Edit Staff Profile – {$user->name}",
            'user'    => $user,
            'profile' => $profile,
            'roles'   => $roles
        ];

        echo view('staff.edit', compact('data'));
        exit;
    }

    /**
     * Update Staff Profile (POST).
     */
    public function update()
    {
        header('Content-Type: application/json');
        $userId = (int)($_POST['user_id'] ?? 0);

        if (!Auth::check() || (!Auth::isAdmin() && Auth::id() !== $userId)) {
            echo json_encode(['status' => 0, 'msg' => 'Unauthorized.']);
            exit;
        }

        $user = User::find($userId);
        if (!$user) {
            echo json_encode(['status' => 0, 'msg' => 'User not found.']);
            exit;
        }

        // Only admin can change role
        if (Auth::isAdmin() && isset($_POST['role'])) {
            $roleId = (int)$_POST['role'];
            if (in_array($roleId, self::STAFF_ROLES, true)) {
                $user->role = $roleId;
            }
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $surname = trim($_POST['surname'] ?? '');
        $fullName = trim($_POST['name'] ?? ($firstName . ' ' . $surname));
        if (!empty($fullName)) {
            $user->name = $fullName;
        }
        $user->update();

        $profiles = Staffprofile::where('user', $user->iD);
        $profile = !empty($profiles) ? $profiles[0] : new Staffprofile();
        $profile->user = $user->iD;

        if (!empty($_POST['job_title'])) $profile->job_title = trim($_POST['job_title']);
        if (!empty($_POST['department'])) $profile->department = trim($_POST['department']);
        if (!empty($_POST['employee_number'])) $profile->employee_number = trim($_POST['employee_number']);
        if (!empty($_POST['station'])) $profile->station = trim($_POST['station']);
        if (!empty($_POST['date_of_employment'])) $profile->date_of_employment = trim($_POST['date_of_employment']);
        if (!empty($_POST['nature_of_employment'])) $profile->nature_of_employment = trim($_POST['nature_of_employment']);
        if (isset($_POST['salary'])) $profile->salary = trim($_POST['salary']);

        if (!empty($_POST['title'])) $profile->title = trim($_POST['title']);
        if (!empty($firstName)) $profile->first_name = $firstName;
        if (isset($_POST['other_names'])) $profile->other_names = trim($_POST['other_names']);
        if (!empty($surname)) $profile->surname = $surname;
        if (isset($_POST['national_id_number'])) $profile->national_id_number = trim($_POST['national_id_number']);
        if (isset($_POST['ssr_number'])) $profile->ssr_number = trim($_POST['ssr_number']);
        if (isset($_POST['drivers_licence_number'])) $profile->drivers_licence_number = trim($_POST['drivers_licence_number']);
        if (isset($_POST['passport_number'])) $profile->passport_number = trim($_POST['passport_number']);
        if (!empty($_POST['date_of_birth'])) $profile->date_of_birth = trim($_POST['date_of_birth']);
        if (isset($_POST['gender'])) $profile->gender = trim($_POST['gender']);
        if (isset($_POST['marital_status'])) $profile->marital_status = trim($_POST['marital_status']);

        if (isset($_POST['street_number'])) $profile->street_number = trim($_POST['street_number']);
        if (isset($_POST['street_name'])) $profile->street_name = trim($_POST['street_name']);
        if (isset($_POST['suburb'])) $profile->suburb = trim($_POST['suburb']);
        if (isset($_POST['town'])) $profile->town = trim($_POST['town']);
        if (isset($_POST['region'])) $profile->region = trim($_POST['region']);
        if (isset($_POST['telephone_number'])) $profile->telephone_number = trim($_POST['telephone_number']);

        if (isset($_POST['bank_name'])) $profile->bank_name = trim($_POST['bank_name']);
        if (isset($_POST['bank_branch'])) $profile->bank_branch = trim($_POST['bank_branch']);
        if (isset($_POST['account_number'])) $profile->account_number = trim($_POST['account_number']);

        if (isset($_POST['emergency_contact_name'])) $profile->emergency_contact_name = trim($_POST['emergency_contact_name']);
        if (isset($_POST['emergency_contact_phone'])) $profile->emergency_contact_phone = trim($_POST['emergency_contact_phone']);
        if (isset($_POST['emergency_contact_relationship'])) $profile->emergency_contact_relationship = trim($_POST['emergency_contact_relationship']);

        if ($profile->iD) {
            $profile->update();
        } else {
            $profile->reg_by = Auth::id() ?? 1;
            $profile->save();
        }

        echo json_encode([
            'status' => 1,
            'msg'    => 'Staff profile updated successfully.'
        ]);
        exit;
    }

    /**
     * Export all staff records in NSSA Form P4 CSV structure.
     */
    public function exportP4()
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            http_response_code(403);
            echo "Unauthorized.";
            exit;
        }

        $filename = "Trainit_NSSA_P4_Staff_Export_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');

        // Write P4 Header exactly matching docs/P4 Example.xlsx
        fputcsv($out, [
            'NationalIdNumber',
            'SSRNumber',
            'Title',
            'Firstname',
            'OtherName',
            'Surname',
            'MaritalStatus',
            'Nationality',
            'Citizenship',
            'BirthDate',
            'StreetNumber',
            'StreetName',
            'Suburb',
            'Town',
            'Region',
            'Country',
            'PostalCode',
            'TelephoneNumber',
            'EmailAddress',
            'BirthCertificateNumber',
            'DriverslicenceNumber',
            'PassportNumber',
            'NatureofEmployment',
            'CurrentWorksNumber',
            'EmployeeType',
            'DateOfEmployment',
            'Station',
            'Salary',
            'Ministry',
            'Occupation'
        ]);

        $profiles = Staffprofile::all();
        foreach ($profiles as $p) {
            $u = $p->user();
            $email = $p->work_email ?: ($u ? $u->email : '');
            
            fputcsv($out, [
                $p->national_id_number,
                $p->ssr_number,
                $p->title ?: 'MR',
                $p->first_name,
                $p->other_names,
                $p->surname,
                $p->marital_status ?: 'Single',
                $p->nationality ?: 'Zimbabwean',
                $p->citizenship ?: 'ZW',
                $p->date_of_birth ? date('d.m.Y', strtotime($p->date_of_birth)) : '',
                $p->street_number,
                $p->street_name,
                $p->suburb,
                $p->town ?: 'HARARE',
                $p->region ?: 'HRE',
                $p->country ?: 'Zimbabwe',
                $p->postal_code,
                $p->telephone_number,
                $email,
                $p->birth_certificate_number,
                $p->drivers_licence_number,
                $p->passport_number,
                $p->nature_of_employment ?: 'ORDINARY',
                $p->employee_number,
                $p->employee_type ?: 'Employee',
                $p->date_of_employment ? date('d.m.Y', strtotime($p->date_of_employment)) : '',
                $p->station ?: 'Harare HQ',
                $p->salary,
                $p->department ?: 'Operations',
                $p->job_title
            ]);
        }

        fclose($out);
        exit;
    }

    /**
     * File upload helper.
     */
    private function uploadDocument($fileKey, $targetDir, $prefix)
    {
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $tmp = $_FILES[$fileKey]['tmp_name'];
        $orig = $_FILES[$fileKey]['name'];
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        if (!in_array($ext, $allowed, true)) {
            return null;
        }

        $safeName = $prefix . '_' . time() . '.' . $ext;
        $dest = $targetDir . $safeName;

        if (move_uploaded_file($tmp, $dest)) {
            return 'uploads/staff/' . $safeName;
        }

        return null;
    }
}
