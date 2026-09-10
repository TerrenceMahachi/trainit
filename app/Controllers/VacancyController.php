<?php

namespace App\Controllers;

use App\Models\Vacancy;
use App\Models\VacancySkill;
use App\Models\VacancyApplication;
use App\Models\Vacancystatus;
use App\Models\Vacancyapplicationstatus;
use App\Models\Department;
use App\Models\Engagementbasis;
use App\Models\Worklocationpreference;
use App\Models\Role;
use App\Models\Skillitem;
use App\Models\StaffInvite;
use App\Models\User;
use App\Helpers\Auth;
use App\Helpers\Mailer;
use DateTime;

class VacancyController
{
    /**
     * Public listing of published vacancies.
     */
    public function index()
    {
        global $siteConfig;
        $search = trim($_GET['search'] ?? '');
        $deptId = (int)($_GET['dept'] ?? 0);

        $sql = "SELECT v.* FROM vacancy v WHERE v.vacancystatus = 2 AND v.status = 1";
        $params = [];

        if ($deptId > 0) {
            $sql .= " AND v.department = ?";
            $params[] = $deptId;
        }

        if ($search !== '') {
            $sql .= " AND (v.title LIKE ? OR v.summary LIKE ? OR v.description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY v.is_featured DESC, v.publish_date DESC";
        $vacancies = Vacancy::findByQuery($sql, $params);

        if (isset($_GET['format']) && $_GET['format'] === 'json') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 1, 'data' => $vacancies]);
            return;
        }

        $departments = Department::all();
        $data = [
            'title'        => 'Career Opportunities & Vacancies | ' . ($siteConfig->siteName ?? 'Tsigiro'),
            'vacancies'    => $vacancies,
            'departments'  => $departments,
            'search'       => $search,
            'selectedDept' => $deptId,
        ];

        echo view('vacancies.public_index', compact('data'));
        return;
    }

    /**
     * Public detailed view of a vacancy with application brief.
     */
    public function view(string $slug)
    {
        global $siteConfig;
        $slug = trim($slug);

        $vacancies = Vacancy::where('slug', $slug);
        if (empty($vacancies)) {
            http_response_code(404);
            echo view('errors.404', ['data' => ['title' => 'Vacancy Not Found']]);
            return;
        }

        /** @var Vacancy $vacancy */
        $vacancy = $vacancies[0];

        // Only staff/admin can preview non-published vacancies
        if ((int)$vacancy->vacancystatus !== 2 && !Auth::isStaff()) {
            http_response_code(404);
            echo view('errors.404', ['data' => ['title' => 'Vacancy Closed or Unavailable']]);
            return;
        }

        $dept = $vacancy->department();
        $engagement = $vacancy->engagementbasis();
        $location = $vacancy->worklocationpreference();
        $targetRole = $vacancy->targetRole();
        $skills = $vacancy->skills();

        $data = [
            'title'       => $vacancy->title . ' | Tsigiro Careers',
            'vacancy'     => $vacancy,
            'dept'        => $dept,
            'engagement'  => $engagement,
            'location'    => $location,
            'targetRole'  => $targetRole,
            'skills'      => $skills,
            'isStaff'     => Auth::isStaff(),
        ];

        echo view('vacancies.show', compact('data'));
        return;
    }

    /**
     * Public application submission endpoint (POST).
     */
    public function apply(string $slug)
    {
        global $siteConfig;
        header('Content-Type: application/json');

        $slug = trim($slug);
        $vacancies = Vacancy::where('slug', $slug);
        if (empty($vacancies)) {
            echo json_encode(['status' => 0, 'msg' => 'Vacancy not found.']);
            return;
        }

        /** @var Vacancy $vacancy */
        $vacancy = $vacancies[0];

        if ((int)$vacancy->vacancystatus !== 2 || $vacancy->isClosed()) {
            echo json_encode(['status' => 0, 'msg' => 'Applications for this position are currently closed.']);
            return;
        }

        // Sanitized inputs
        $firstName    = trim($_POST['first_name'] ?? '');
        $lastName     = trim($_POST['last_name'] ?? '');
        $email        = trim($_POST['email'] ?? '');
        $phone        = trim($_POST['phone'] ?? '');
        $city         = trim($_POST['city'] ?? '');
        $country      = trim($_POST['country'] ?? 'Zimbabwe');
        $experience   = max(0, (int)($_POST['years_of_experience'] ?? 0));
        $highestQual  = trim($_POST['highest_qualification'] ?? '');
        $employer     = trim($_POST['current_employer'] ?? '');
        $jobTitle     = trim($_POST['current_job_title'] ?? '');
        $salary       = trim($_POST['expected_salary'] ?? '');
        $noticePeriod = max(0, (int)($_POST['notice_period_days'] ?? 30));
        $coverLetter  = trim($_POST['cover_letter'] ?? '');

        // Validation
        if (empty($firstName) || empty($lastName)) {
            echo json_encode(['status' => 0, 'msg' => 'Please provide your full first and last name.']);
            return;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 0, 'msg' => 'A valid email address is required.']);
            return;
        }

        if (empty($phone)) {
            echo json_encode(['status' => 0, 'msg' => 'A contact phone / WhatsApp number is required.']);
            return;
        }

        if (empty($city)) {
            echo json_encode(['status' => 0, 'msg' => 'Please provide your current city of residence.']);
            return;
        }

        // Check duplicate active application
        $existing = VacancyApplication::findByQuery(
            "SELECT * FROM vacancy_application WHERE vacancy = ? AND email = ? AND status = 1",
            [$vacancy->iD, $email]
        );
        if (!empty($existing)) {
            echo json_encode([
                'status' => 0,
                'msg'    => 'An application from this email address has already been submitted for this position (' . $existing[0]->application_number . ').'
            ]);
            return;
        }

        // Handle CV file upload
        if (empty($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['status' => 0, 'msg' => 'Please upload your Curriculum Vitae (PDF, DOC, or DOCX).']);
            return;
        }

        $file = $_FILES['cv'];
        $maxBytes = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $maxBytes) {
            echo json_encode(['status' => 0, 'msg' => 'Uploaded CV file exceeds the 10MB size limit.']);
            return;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['pdf', 'doc', 'docx'];
        if (!in_array($extension, $allowedExts, true)) {
            echo json_encode(['status' => 0, 'msg' => 'Invalid file format. Please upload your CV as a PDF, DOC, or DOCX file.']);
            return;
        }

        $uploadDir = _BASE_PATH . '/public/uploads/cvs/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        // Mirror storage dir as backup
        $storageUploadDir = _BASE_PATH . '/storage/uploads/cvs/';
        if (!is_dir($storageUploadDir)) {
            @mkdir($storageUploadDir, 0755, true);
        }

        $safeName = 'CV_' . preg_replace('/[^A-Za-z0-9]/', '', $vacancy->reference_number) . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $destPath = $uploadDir . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            echo json_encode(['status' => 0, 'msg' => 'Failed to store your CV file. Please try again.']);
            return;
        }

        // Make mirror copy in storage
        @copy($destPath, $storageUploadDir . $safeName);

        $cvRelativePath = 'uploads/cvs/' . $safeName;

        // Generate application reference number
        $randomSuffix = strtoupper(bin2hex(random_bytes(2)));
        $applicationNumber = 'APP-' . $vacancy->reference_number . '-' . $randomSuffix;

        // Associate user if logged in
        $userId = Auth::id();

        // Create application record
        $app = new VacancyApplication();
        $app->vacancy              = $vacancy->iD;
        $app->application_number   = $applicationNumber;
        $app->user                 = $userId;
        $app->first_name           = $firstName;
        $app->last_name            = $lastName;
        $app->email                = $email;
        $app->phone                = $phone;
        $app->city                 = $city;
        $app->country              = $country;
        $app->years_of_experience  = $experience;
        $app->highest_qualification= $highestQual;
        $app->current_employer     = $employer;
        $app->current_job_title    = $jobTitle;
        $app->expected_salary      = $salary;
        $app->notice_period_days   = $noticePeriod;
        $app->cover_letter         = $coverLetter;
        $app->cv_path              = $cvRelativePath;
        $app->application_status   = 1; // submitted
        $app->reg_by               = $userId ?: 1;
        $app->status               = 1;
        $app->save();

        // Send confirmation email
        $candidateFullName = $firstName . ' ' . $lastName;
        Mailer::sendVacancyApplicationConfirmation(
            $email,
            $candidateFullName,
            $applicationNumber,
            $vacancy->title,
            $vacancy->reference_number
        );

        echo json_encode([
            'status'             => 1,
            'msg'                => 'Your application for ' . htmlspecialchars($vacancy->title) . ' has been received successfully. A confirmation email has been dispatched to ' . htmlspecialchars($email) . '.',
            'application_number' => $applicationNumber,
        ]);
        return;
    }

    /**
     * Secure CV Download action for Staff.
     */
    public function downloadCv(int $applicationId)
    {
        if (!Auth::isStaff()) {
            http_response_code(403);
            echo "Unauthorized access.";
            return;
        }

        $app = VacancyApplication::find($applicationId);
        if (!$app || empty($app->cv_path)) {
            http_response_code(404);
            echo "Application or CV file not found.";
            return;
        }

        $fullPath = _BASE_PATH . '/public/' . ltrim($app->cv_path, '/');
        if (!file_exists($fullPath)) {
            // Check storage backup
            $backupPath = _BASE_PATH . '/storage/' . ltrim($app->cv_path, '/');
            if (file_exists($backupPath)) {
                $fullPath = $backupPath;
            } else {
                http_response_code(404);
                echo "CV file not found on disk.";
                return;
            }
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $contentTypes = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $mime = $contentTypes[$extension] ?? 'application/octet-stream';

        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', $app->first_name . '_' . $app->last_name . '_CV') . '.' . $extension;

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . $safeName . '"');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        return;
    }

    /**
     * Admin Vacancy Management Console (GET).
     */
    public function adminIndex()
    {
        global $siteConfig;
        if (!Auth::isStaff()) {
            header("Location: " . $siteConfig->siteUrl . "/dashboard");
            return;
        }

        $search = trim($_GET['search'] ?? '');
        $statusId = (int)($_GET['status'] ?? 0);
        $deptId = (int)($_GET['dept'] ?? 0);

        $sql = "SELECT v.* FROM vacancy v WHERE v.status = 1";
        $params = [];

        if ($statusId > 0) {
            $sql .= " AND v.vacancystatus = ?";
            $params[] = $statusId;
        }

        if ($deptId > 0) {
            $sql .= " AND v.department = ?";
            $params[] = $deptId;
        }

        if ($search !== '') {
            $sql .= " AND (v.title LIKE ? OR v.reference_number LIKE ? OR v.summary LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $sql .= " ORDER BY v.reg_date DESC";
        $vacancies = Vacancy::findByQuery($sql, $params);

        // Compute KPIs
        $pdo = \App\Models\Database::sharedPdo();
        $totalVacancies = (int)$pdo->query("SELECT COUNT(*) FROM vacancy WHERE status = 1")->fetchColumn();
        $publishedCount = (int)$pdo->query("SELECT COUNT(*) FROM vacancy WHERE vacancystatus = 2 AND status = 1")->fetchColumn();
        $draftCount = (int)$pdo->query("SELECT COUNT(*) FROM vacancy WHERE vacancystatus = 1 AND status = 1")->fetchColumn();
        $closedCount = (int)$pdo->query("SELECT COUNT(*) FROM vacancy WHERE vacancystatus = 4 AND status = 1")->fetchColumn();
        $totalApplications = (int)$pdo->query("SELECT COUNT(*) FROM vacancy_application WHERE status = 1")->fetchColumn();
        $appointedCount = (int)$pdo->query("SELECT COUNT(*) FROM vacancy_application WHERE application_status = 5 AND status = 1")->fetchColumn();

        $statuses = Vacancystatus::all();
        $departments = Department::all();

        $data = [
            'title'              => 'Recruitment & Vacancies Management | ' . ($siteConfig->siteName ?? 'Tsigiro'),
            'vacancies'          => $vacancies,
            'statuses'           => $statuses,
            'departments'        => $departments,
            'totalVacancies'     => $totalVacancies,
            'publishedCount'     => $publishedCount,
            'draftCount'         => $draftCount,
            'closedCount'        => $closedCount,
            'totalApplications'  => $totalApplications,
            'appointedCount'     => $appointedCount,
            'selectedStatus'     => $statusId,
            'selectedDept'       => $deptId,
            'search'             => $search,
        ];

        echo view('vacancies.index', compact('data'));
        return;
    }

    /**
     * Admin Create Vacancy Form (GET).
     */
    public function adminCreate()
    {
        global $siteConfig;
        if (!Auth::isStaff()) {
            header("Location: " . $siteConfig->siteUrl . "/dashboard");
            return;
        }

        $departments = Department::all();
        $engagementBases = Engagementbasis::all();
        $locations = Worklocationpreference::all();
        $roles = Role::all();
        $skills = Skillitem::all();

        $data = [
            'title'           => 'Publish New Vacancy | ' . ($siteConfig->siteName ?? 'Tsigiro'),
            'departments'     => $departments,
            'engagementBases' => $engagementBases,
            'locations'       => $locations,
            'roles'           => $roles,
            'skills'          => $skills,
        ];

        echo view('vacancies.create', compact('data'));
        return;
    }

    /**
     * Admin Store Vacancy (POST).
     */
    public function adminStore()
    {
        global $siteConfig;
        header('Content-Type: application/json');

        if (!Auth::isStaff()) {
            echo json_encode(['status' => 0, 'msg' => 'Unauthorized.']);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $deptId = (int)($_POST['department'] ?? 0);
        $engagementId = (int)($_POST['engagementbasis'] ?? 0);
        $locationId = (int)($_POST['worklocationpreference'] ?? 0);
        $targetRole = (int)($_POST['target_role'] ?? 8);
        $summary = trim($_POST['summary'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $responsibilities = trim($_POST['responsibilities'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');
        $remuneration = trim($_POST['remuneration_display'] ?? '');
        $slots = max(1, (int)($_POST['open_slots'] ?? 1));
        $publishDate = trim($_POST['publish_date'] ?? date('Y-m-d'));
        $closingDate = trim($_POST['closing_date'] ?? date('Y-m-d', strtotime('+30 days')));
        $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;
        $vacancyStatus = (int)($_POST['vacancystatus'] ?? 2); // default published
        $skillIds = $_POST['skills'] ?? [];
        $mandatorySkills = $_POST['mandatory_skills'] ?? [];

        if (empty($title)) {
            echo json_encode(['status' => 0, 'msg' => 'Job title is required.']);
            return;
        }

        if ($deptId <= 0 || $engagementId <= 0 || $locationId <= 0) {
            echo json_encode(['status' => 0, 'msg' => 'Department, engagement terms, and work location must be selected.']);
            return;
        }

        if (empty($summary) || empty($description)) {
            echo json_encode(['status' => 0, 'msg' => 'Position summary and description are required.']);
            return;
        }

        if (empty($closingDate)) {
            echo json_encode(['status' => 0, 'msg' => 'Application closing date is required.']);
            return;
        }

        // Generate Reference Number
        $pdo = \App\Models\Database::sharedPdo();
        $nextId = ((int)$pdo->query("SELECT MAX(iD) FROM vacancy")->fetchColumn()) + 1;
        $refNumber = 'TSG-VAC-' . date('Y') . '-' . str_pad((string)$nextId, 3, '0', STR_PAD_LEFT);

        // Generate unique slug
        $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        $slug = $baseSlug;
        $counter = 1;
        while (!empty(Vacancy::where('slug', $slug))) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // Save Vacancy
        $vacancy = new Vacancy();
        $vacancy->reference_number       = $refNumber;
        $vacancy->title                  = $title;
        $vacancy->slug                   = $slug;
        $vacancy->department             = $deptId;
        $vacancy->engagementbasis        = $engagementId;
        $vacancy->worklocationpreference = $locationId;
        $vacancy->target_role            = $targetRole;
        $vacancy->summary                = $summary;
        $vacancy->description            = $description;
        $vacancy->responsibilities       = $responsibilities;
        $vacancy->requirements           = $requirements;
        $vacancy->remuneration_display   = $remuneration ?: null;
        $vacancy->open_slots             = $slots;
        $vacancy->publish_date           = $publishDate;
        $vacancy->closing_date           = $closingDate;
        $vacancy->is_featured            = $isFeatured;
        $vacancy->vacancystatus          = $vacancyStatus;
        $vacancy->reg_by                 = Auth::id() ?? 1;
        $vacancy->status                 = 1;
        $vacancy->save();

        // Save linked skills in normalized junction table vacancy_skill
        if (is_array($skillIds)) {
            foreach ($skillIds as $skillId) {
                $skillId = (int)$skillId;
                if ($skillId <= 0) continue;
                $isMandatory = in_array((string)$skillId, (array)$mandatorySkills, true) ? 1 : 0;

                $vs = new VacancySkill();
                $vs->vacancy      = $vacancy->iD;
                $vs->skillitem    = $skillId;
                $vs->is_mandatory = $isMandatory;
                $vs->reg_by       = Auth::id() ?? 1;
                $vs->status       = 1;
                $vs->save();
            }
        }

        echo json_encode([
            'status'   => 1,
            'msg'      => "Vacancy {$title} [{$refNumber}] created successfully.",
            'id'       => $vacancy->iD,
            'redirect' => $siteConfig->siteUrl . '/admin/vacancies',
        ]);
        return;
    }

    /**
     * Admin Edit Vacancy Form (GET).
     */
    public function adminEdit(int $id)
    {
        global $siteConfig;
        if (!Auth::isStaff()) {
            header("Location: " . $siteConfig->siteUrl . "/dashboard");
            return;
        }

        $vacancy = Vacancy::find($id);
        if (!$vacancy) {
            header("Location: " . $siteConfig->siteUrl . "/admin/vacancies");
            return;
        }

        $departments = Department::all();
        $engagementBases = Engagementbasis::all();
        $locations = Worklocationpreference::all();
        $roles = Role::all();
        $skills = Skillitem::all();
        $statuses = Vacancystatus::all();

        // Get linked skills map [skillitem => is_mandatory]
        $linkedSkills = [];
        $stmt = \App\Models\Database::sharedPdo()->prepare("SELECT skillitem, is_mandatory FROM vacancy_skill WHERE vacancy = ? AND status = 1");
        $stmt->execute([$vacancy->iD]);
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $linkedSkills[(int)$row['skillitem']] = (int)$row['is_mandatory'];
        }

        $data = [
            'title'           => 'Edit Vacancy: ' . $vacancy->title . ' | Tsigiro Admin',
            'vacancy'         => $vacancy,
            'departments'     => $departments,
            'engagementBases' => $engagementBases,
            'locations'       => $locations,
            'roles'           => $roles,
            'skills'          => $skills,
            'statuses'        => $statuses,
            'linkedSkills'    => $linkedSkills,
        ];

        echo view('vacancies.edit', compact('data'));
        return;
    }

    /**
     * Admin Update Vacancy (POST).
     */
    public function adminUpdate()
    {
        global $siteConfig;
        header('Content-Type: application/json');

        if (!Auth::isStaff()) {
            echo json_encode(['status' => 0, 'msg' => 'Unauthorized.']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $vacancy = Vacancy::find($id);
        if (!$vacancy) {
            echo json_encode(['status' => 0, 'msg' => 'Vacancy record not found.']);
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $deptId = (int)($_POST['department'] ?? 0);
        $engagementId = (int)($_POST['engagementbasis'] ?? 0);
        $locationId = (int)($_POST['worklocationpreference'] ?? 0);
        $targetRole = (int)($_POST['target_role'] ?? 8);
        $summary = trim($_POST['summary'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $responsibilities = trim($_POST['responsibilities'] ?? '');
        $requirements = trim($_POST['requirements'] ?? '');
        $remuneration = trim($_POST['remuneration_display'] ?? '');
        $slots = max(1, (int)($_POST['open_slots'] ?? 1));
        $publishDate = trim($_POST['publish_date'] ?? date('Y-m-d'));
        $closingDate = trim($_POST['closing_date'] ?? date('Y-m-d', strtotime('+30 days')));
        $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;
        $vacancyStatus = (int)($_POST['vacancystatus'] ?? 2);
        $skillIds = $_POST['skills'] ?? [];
        $mandatorySkills = $_POST['mandatory_skills'] ?? [];

        if (empty($title)) {
            echo json_encode(['status' => 0, 'msg' => 'Job title is required.']);
            return;
        }

        $vacancy->title                  = $title;
        $vacancy->department             = $deptId;
        $vacancy->engagementbasis        = $engagementId;
        $vacancy->worklocationpreference = $locationId;
        $vacancy->target_role            = $targetRole;
        $vacancy->summary                = $summary;
        $vacancy->description            = $description;
        $vacancy->responsibilities       = $responsibilities;
        $vacancy->requirements           = $requirements;
        $vacancy->remuneration_display   = $remuneration ?: null;
        $vacancy->open_slots             = $slots;
        $vacancy->publish_date           = $publishDate;
        $vacancy->closing_date           = $closingDate;
        $vacancy->is_featured            = $isFeatured;
        $vacancy->vacancystatus          = $vacancyStatus;
        $vacancy->update();

        // Sync vacancy_skill junction table
        $pdo = \App\Models\Database::sharedPdo();
        $pdo->prepare("DELETE FROM vacancy_skill WHERE vacancy = ?")->execute([$vacancy->iD]);

        if (is_array($skillIds)) {
            foreach ($skillIds as $skillId) {
                $skillId = (int)$skillId;
                if ($skillId <= 0) continue;
                $isMandatory = in_array((string)$skillId, (array)$mandatorySkills, true) ? 1 : 0;

                $vs = new VacancySkill();
                $vs->vacancy      = $vacancy->iD;
                $vs->skillitem    = $skillId;
                $vs->is_mandatory = $isMandatory;
                $vs->reg_by       = Auth::id() ?? 1;
                $vs->status       = 1;
                $vs->save();
            }
        }

        echo json_encode([
            'status'   => 1,
            'msg'      => "Vacancy {$title} updated successfully.",
            'redirect' => $siteConfig->siteUrl . '/admin/vacancies',
        ]);
        return;
    }

    /**
     * Quick status toggle (POST AJAX).
     */
    public function adminToggleStatus()
    {
        header('Content-Type: application/json');
        if (!Auth::isStaff()) {
            echo json_encode(['status' => 0, 'msg' => 'Unauthorized.']);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $newStatus = (int)($_POST['status'] ?? 0);

        $vacancy = Vacancy::find($id);
        if (!$vacancy) {
            echo json_encode(['status' => 0, 'msg' => 'Vacancy not found.']);
            return;
        }

        $statusObj = Vacancystatus::find($newStatus);
        if (!$statusObj) {
            echo json_encode(['status' => 0, 'msg' => 'Invalid vacancy status code.']);
            return;
        }

        $vacancy->vacancystatus = $newStatus;
        $vacancy->update();

        echo json_encode([
            'status'      => 1,
            'msg'         => "Vacancy status updated to {$statusObj->name}.",
            'status_name' => $statusObj->name,
            'badge_class' => $statusObj->badge_class,
        ]);
        return;
    }

    /**
     * Admin Review Applicants for a Vacancy (GET).
     */
    public function adminApplications(int $id)
    {
        global $siteConfig;
        if (!Auth::isStaff()) {
            header("Location: " . $siteConfig->siteUrl . "/dashboard");
            return;
        }

        $vacancy = Vacancy::find($id);
        if (!$vacancy) {
            header("Location: " . $siteConfig->siteUrl . "/admin/vacancies");
            return;
        }

        $applications = VacancyApplication::findByQuery(
            "SELECT * FROM vacancy_application WHERE vacancy = ? AND status = 1 ORDER BY reg_date DESC",
            [$vacancy->iD]
        );

        $statuses = Vacancyapplicationstatus::all();
        $targetRoleObj = $vacancy->targetRole();

        $data = [
            'title'         => 'Applicants: ' . $vacancy->title . ' | Tsigiro Admin',
            'vacancy'       => $vacancy,
            'applications'  => $applications,
            'statuses'      => $statuses,
            'targetRoleObj' => $targetRoleObj,
        ];

        echo view('vacancies.applications', compact('data'));
        return;
    }

    /**
     * Update application status / scoring / notes (POST AJAX).
     */
    public function adminUpdateApplication()
    {
        header('Content-Type: application/json');
        if (!Auth::isStaff()) {
            echo json_encode(['status' => 0, 'msg' => 'Unauthorized.']);
            return;
        }

        $appId = (int)($_POST['application_id'] ?? 0);
        $statusId = (int)($_POST['application_status'] ?? 0);
        $score = isset($_POST['rating_score']) && $_POST['rating_score'] !== '' ? (int)$_POST['rating_score'] : null;
        $notes = trim($_POST['admin_notes'] ?? '');
        $interviewAt = trim($_POST['interview_at'] ?? '');

        $app = VacancyApplication::find($appId);
        if (!$app) {
            echo json_encode(['status' => 0, 'msg' => 'Application not found.']);
            return;
        }

        if ($statusId > 0) {
            $app->application_status = $statusId;
        }
        if ($score !== null) {
            $app->rating_score = max(0, min(100, $score));
        }
        if ($notes !== '') {
            $app->admin_notes = $notes;
        }
        if ($interviewAt !== '') {
            $app->interview_at = $interviewAt;
        }
        $app->update();

        $stObj = $app->statusRecord();
        echo json_encode([
            'status'      => 1,
            'msg'         => 'Applicant dossier updated successfully.',
            'status_name' => $stObj ? $stObj->name : 'Updated',
            'badge_class' => $stObj ? $stObj->badge_class : 'bg-secondary',
        ]);
        return;
    }

    /**
     * BRIDGE TO STAFF ONBOARDING:
     * Appoint candidate to internal staff and dispatch statutory onboarding invitation (POST AJAX).
     */
    public function adminAppointStaff()
    {
        global $siteConfig;
        header('Content-Type: application/json');

        if (!Auth::isStaff()) {
            echo json_encode(['status' => 0, 'msg' => 'Unauthorized. Only authorized staff may issue appointments.']);
            return;
        }

        $appId = (int)($_POST['application_id'] ?? 0);
        $app = VacancyApplication::find($appId);
        if (!$app) {
            echo json_encode(['status' => 0, 'msg' => 'Application record not found.']);
            return;
        }

        // Get parent vacancy
        $vacancy = $app->vacancy();
        if (!$vacancy) {
            echo json_encode(['status' => 0, 'msg' => 'Linked vacancy not found.']);
            return;
        }

        // Verify not already appointed
        if (!empty($app->staff_invite) && (int)$app->application_status === 5) {
            echo json_encode(['status' => 0, 'msg' => 'This candidate has already been appointed and issued a staff onboarding invitation.']);
            return;
        }

        $candidateEmail = $app->email;
        $candidateName  = $app->fullName();
        $jobTitle       = $vacancy->title;
        $deptObj        = $vacancy->department();
        $departmentName = $deptObj ? $deptObj->name : 'Operations';
        $roleId         = (int)$vacancy->target_role ?: 8;

        // Check if an existing user record already has this email
        $existingUsers = User::where('email', $candidateEmail);
        if (!empty($existingUsers)) {
            echo json_encode([
                'status' => 0,
                'msg'    => "A user account with email {$candidateEmail} already exists in the system (User #{$existingUsers[0]->iD})."
            ]);
            return;
        }

        $siteUrl = $siteConfig->siteUrl ?? 'https://portal.tsigiro.co.zw';

        // Check for an active unused invite for this email, delete old if expired/unused
        $oldInvites = StaffInvite::findByQuery("SELECT * FROM staff_invite WHERE email = ? AND used = 0", [$candidateEmail]);
        foreach ($oldInvites as $old) {
            $old->delete();
        }

        // Generate secure 48-char token
        $token = bin2hex(random_bytes(24));
        $expires = (new DateTime())->modify('+7 days')->format('Y-m-d H:i:s');

        // Create StaffInvite record
        $invite = new StaffInvite();
        $invite->email      = $candidateEmail;
        $invite->name       = $candidateName;
        $invite->role       = $roleId;
        $invite->department = $departmentName;
        $invite->job_title  = $jobTitle;
        $invite->token      = $token;
        $invite->expires_at = $expires;
        $invite->invited_by = Auth::id() ?? 1;
        $invite->used       = 0;
        $invite->save();

        // Update VacancyApplication: status 5 = Appointed to Staff
        $app->staff_invite       = $invite->iD;
        $app->application_status = 5;
        $app->appointed_at       = date('Y-m-d H:i:s');
        $app->update();

        // Dispatch statutory onboarding email
        $roleObj = Role::find($roleId);
        $roleName = $roleObj ? $roleObj->name : 'Staff Member';
        $inviteUrl = "{$siteUrl}/staff/onboard?token={$token}";

        Mailer::sendStaffInvitation($candidateEmail, $candidateName, $roleName, $jobTitle, $departmentName, $inviteUrl);

        echo json_encode([
            'status'     => 1,
            'msg'        => "Candidate {$candidateName} appointed to {$jobTitle}! Statutory staff onboarding invitation dispatched to {$candidateEmail}.",
            'invite_url' => $inviteUrl,
            'token'      => $token,
        ]);
        return;
    }
}
