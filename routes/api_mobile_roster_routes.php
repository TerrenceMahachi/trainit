<?php

use App\Models\Database;
use App\Models\User;
use App\Models\Applicationtrack;
use App\Models\Applicationstatus;
use App\Models\Servicefunction;
use App\Models\Skillitem;
use App\Models\Proficiencylevel;
use App\Models\Documenttype;
use App\Models\Zimprovince;
use App\Models\Rosterapplication;
use App\Models\Rosterdocument;
use App\Models\Rosterskill;
use App\Models\Rosterworkhistory;
use App\Models\Rosterreferee;
use App\Models\Rosterstatusevent;
use App\Helpers\Auth;
use App\Helpers\NotificationHelper;

global $router;

if (!function_exists('sendMobileJson')) {
    function sendMobileJson($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

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
// 1. Roster Intake Configuration & Catalogs
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/roster/config', function () {
    $db = (new Database())->getPDO();

    // 1. Application Tracks
    $tracks = [
        [
            'id' => 1,
            'code' => 'apprentice',
            'name' => 'Apprentice Roster',
            'category' => 'Early Career & Attachment',
            'tagline' => 'Work-Related Learning (WRL) & Early Professional Placement',
            'badge' => 'Early Career',
            'color' => '#059669',
            'summary' => 'Designed for tertiary students on industrial attachment / WRL, recent graduates (< 24 months), or early career professionals seeking supervised institutional delivery.',
            'benefits' => [
                'Supervised placement under senior Associates',
                'Logbook support & institutional evaluation',
                'Shared (up to 5 clients at 20%) or Dedicated placement',
                'Monthly transport & meal stipend support',
                'Transition pathway to Associate Roster upon graduation'
            ],
            'requirements' => [
                'Enrolled student or graduate from recognized institution',
                'Institutional attachment letter (if student)',
                'Curriculum Vitae & National ID',
                'Availability for at least 6 months placement'
            ]
        ],
        [
            'id' => 2,
            'code' => 'associate',
            'name' => 'Associate Roster',
            'category' => 'Expert & Specialist Layer',
            'tagline' => 'Senior Technical Oversight & Consulting Deliverables',
            'badge' => 'Specialist / Expert',
            'color' => '#2563eb',
            'summary' => 'Designed for experienced specialists, established consultants, and senior practitioners providing advisory oversight, technical sign-off, and quality assurance.',
            'benefits' => [
                'On-demand calls for client assignments',
                'Competitive indicative USD day rates',
                'Supervision & QA of Apprentice delivery teams',
                'Direct inclusion in high-value bids & tenders'
            ],
            'requirements' => [
                'At least 3 years relevant professional experience',
                'Proven track record in selected service function',
                'Professional body membership (if applicable)',
                'Comprehensive CV, academic certificates & verified referees'
            ]
        ]
    ];

    // 2. Service Functions / Specialties
    $stmt = $db->query("SELECT iD as id, code, name, description FROM servicefunction WHERE status = 1 ORDER BY sort_order ASC, name ASC");
    $functions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Zimbabwean Provinces
    $stmt = $db->query("SELECT iD as id, code, name FROM zimprovince WHERE status = 1 ORDER BY sort_order ASC, name ASC");
    $provinces = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Proficiency Levels
    $stmt = $db->query("SELECT iD as id, level_number, code, name, definition as description FROM proficiencylevel WHERE status = 1 ORDER BY level_number ASC");
    $profLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Document Types
    $stmt = $db->query("SELECT iD as id, code, name, description, requires_expiry FROM documenttype WHERE status = 1 ORDER BY iD ASC");
    $docTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 6. Common Qualification Levels
    $qualifications = [
        'Certificate / Diploma',
        'Higher National Diploma (HND)',
        'Bachelor\'s Degree',
        'Honours Degree',
        'Postgraduate Diploma',
        'Master\'s Degree',
        'Doctorate / PhD',
        'Professional / Board Certification'
    ];

    // 7. Employment / Availability Statuses
    $employmentStatuses = [
        'Full-time student (Seeking attachment)',
        'Recent graduate (Immediately available)',
        'Employed full-time (Seeking part-time / associate calls)',
        'Self-employed / Independent consultant',
        'Unemployed / Available immediately'
    ];

    sendMobileJson([
        'status' => 1,
        'tracks' => $tracks,
        'functions' => $functions,
        'provinces' => $provinces,
        'proficiency_levels' => $profLevels,
        'document_types' => $docTypes,
        'qualification_options' => $qualifications,
        'employment_options' => $employmentStatuses
    ]);
});

// --------------------------------------------------------------------------
// 2. Skills Catalog by Service Function
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/roster/skills-catalog', function () {
    $db = (new Database())->getPDO();
    $funcId = !empty($_GET['function_id']) ? (int)$_GET['function_id'] : 0;

    if ($funcId > 0) {
        $stmt = $db->prepare("SELECT iD as id, servicefunction as function_id, code, name FROM skillitem WHERE servicefunction = ? AND status = 1 ORDER BY sort_order ASC, name ASC");
        $stmt->execute([$funcId]);
    } else {
        $stmt = $db->query("SELECT iD as id, servicefunction as function_id, code, name FROM skillitem WHERE status = 1 ORDER BY servicefunction ASC, sort_order ASC, name ASC");
    }
    $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status' => 1,
        'skills' => $skills
    ]);
});

// --------------------------------------------------------------------------
// 3. User's Roster Applications
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/roster/my-applications', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized. Please sign in.'], 401);
    }

    $db = (new Database())->getPDO();
    $userId = (int)$user->iD;

    $stmt = $db->prepare("
        SELECT ra.iD as id, ra.applicationtrack as track_id,
               at.code as track_code, at.name as track_name,
               ra.applicationstatus as status_id,
               ast.code as status_code, ast.name as status_name,
               sf.iD as function_id, sf.name as function_name,
               ra.legal_name, ra.email, ra.mobile_number, ra.city,
               ra.reg_date as created_at, ra.consent_timestamp as submitted_at
        FROM rosterapplication ra
        LEFT JOIN applicationtrack at ON ra.applicationtrack = at.iD
        LEFT JOIN applicationstatus ast ON ra.applicationstatus = ast.iD
        LEFT JOIN servicefunction sf ON ra.primaryfunction = sf.iD
        WHERE ra.user = ?
        ORDER BY ra.iD DESC
    ");
    $stmt->execute([$userId]);
    $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($apps as &$app) {
        $appId = (int)$app['id'];
        $prefix = ((int)$app['track_id'] === 2) ? 'TSG-ASC' : 'TSG-APP';
        $year = !empty($app['created_at']) ? date('Y', strtotime($app['created_at'])) : date('Y');
        $app['application_number'] = $prefix . '-' . $year . '-' . str_pad($appId, 4, '0', STR_PAD_LEFT);

        // Counts
        $docCount = (int)$db->query("SELECT COUNT(*) FROM rosterdocument WHERE rosterapplication = {$appId}")->fetchColumn();
        $skillCount = (int)$db->query("SELECT COUNT(*) FROM rosterskill WHERE rosterapplication = {$appId}")->fetchColumn();
        $expCount = (int)$db->query("SELECT COUNT(*) FROM rosterworkhistory WHERE rosterapplication = {$appId}")->fetchColumn();
        $refCount = (int)$db->query("SELECT COUNT(*) FROM rosterreferee WHERE rosterapplication = {$appId}")->fetchColumn();

        $app['documents_count'] = $docCount;
        $app['skills_count'] = $skillCount;
        $app['experience_count'] = $expCount;
        $app['referees_count'] = $refCount;
        $app['is_draft'] = ((int)$app['status_id'] === 1);
        $app['is_submitted'] = ((int)$app['status_id'] >= 2);

        // Badge classes for mobile styling
        $badgeClass = 'bg-secondary';
        switch ($app['status_code']) {
            case 'draft': $badgeClass = 'bg-warning text-dark'; break;
            case 'submitted': $badgeClass = 'bg-primary text-white'; break;
            case 'screened': $badgeClass = 'bg-info text-dark'; break;
            case 'interviewed': $badgeClass = 'bg-indigo text-white'; break;
            case 'on_roster': $badgeClass = 'bg-success text-white'; break;
            case 'rejected': $badgeClass = 'bg-danger text-white'; break;
        }
        $app['badge_class'] = $badgeClass;
    }

    sendMobileJson([
        'status' => 1,
        'applications' => $apps
    ]);
});

// --------------------------------------------------------------------------
// 4. Stage 1: Express Intake & Personal Details
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/roster/apply/stage1-intake', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized. Please sign in.'], 401);
    }

    $db = (new Database())->getPDO();
    $userId = (int)$user->iD;

    $appId = !empty($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
    $trackRaw = trim($_POST['track'] ?? 'apprentice');
    $trackId = ($trackRaw === 'associate' || $trackRaw === '2') ? 2 : 1;

    $primaryFunction = (int)($_POST['primaryfunction'] ?? 0);
    $legalName = trim($_POST['legal_name'] ?? ($user->name ?? ''));
    $preferredName = trim($_POST['preferred_name'] ?? '');
    $email = trim($_POST['email'] ?? ($user->email ?? ''));
    $mobile = trim($_POST['mobile_number'] ?? '');
    $whatsapp = trim($_POST['whatsapp_number'] ?? $mobile);
    $dob = trim($_POST['date_of_birth'] ?? '');
    $gender = !empty($_POST['gender']) ? (int)$_POST['gender'] : null;
    $nationalId = trim($_POST['national_id'] ?? '');
    $city = trim($_POST['city'] ?? 'Harare');
    $suburb = trim($_POST['suburb'] ?? '');
    $province = !empty($_POST['zimprovince']) ? (int)$_POST['zimprovince'] : 1;
    $country = trim($_POST['country'] ?? 'Zimbabwe');
    $nationality = trim($_POST['nationality'] ?? 'Zimbabwean');
    $workRight = !empty($_POST['workrightstatus']) ? (int)$_POST['workrightstatus'] : 1;
    $howHeard = trim($_POST['how_heard'] ?? 'Mobile App');

    if (empty($legalName)) {
        sendMobileJson(['status' => 0, 'message' => 'Full legal name is required.'], 400);
    }
    if (empty($email)) {
        sendMobileJson(['status' => 0, 'message' => 'Email address is required.'], 400);
    }
    if (empty($mobile)) {
        sendMobileJson(['status' => 0, 'message' => 'Mobile phone number is required.'], 400);
    }
    if ($primaryFunction <= 0) {
        sendMobileJson(['status' => 0, 'message' => 'Please select a primary service function / specialty.'], 400);
    }

    if ($appId > 0) {
        // Update existing draft application
        $checkStmt = $db->prepare("SELECT iD, applicationstatus, reg_date FROM rosterapplication WHERE iD = ? AND user = ?");
        $checkStmt->execute([$appId, $userId]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$existing) {
            sendMobileJson(['status' => 0, 'message' => 'Application not found or unauthorized.'], 404);
        }
        if ((int)$existing['applicationstatus'] !== 1) {
            sendMobileJson(['status' => 0, 'message' => 'Cannot edit an application that has already been submitted.'], 400);
        }

        $stmt = $db->prepare("
            UPDATE rosterapplication SET
                applicationtrack = ?, primaryfunction = ?, legal_name = ?, preferred_name = ?,
                email = ?, mobile_number = ?, whatsapp_number = ?, date_of_birth = ?,
                gender = ?, city = ?, suburb = ?, zimprovince = ?, country = ?, nationality = ?,
                workrightstatus = ?, how_heard = ?
            WHERE iD = ?
        ");
        $stmt->execute([
            $trackId, $primaryFunction, $legalName, $preferredName,
            $email, $mobile, $whatsapp, $dob ?: null,
            $gender, $city, $suburb, $province, $country, $nationality,
            $workRight, $howHeard, $appId
        ]);
        $prefix = ($trackId === 2) ? 'TSG-ASC' : 'TSG-APP';
        $year = !empty($existing['reg_date']) ? date('Y', strtotime($existing['reg_date'])) : date('Y');
        $appNumber = $prefix . '-' . $year . '-' . str_pad($appId, 4, '0', STR_PAD_LEFT);
    } else {
        // Create new draft application
        $stmt = $db->prepare("
            INSERT INTO rosterapplication (
                user, applicationtrack, applicationstatus, primaryfunction, legal_name, preferred_name,
                email, mobile_number, whatsapp_number, date_of_birth, gender, city, suburb,
                zimprovince, country, nationality, workrightstatus, how_heard, reg_by, reg_date, status
            ) VALUES (
                ?, ?, 1, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1
            )
        ");
        $stmt->execute([
            $userId, $trackId, $primaryFunction, $legalName, $preferredName,
            $email, $mobile, $whatsapp, $dob ?: null, $gender, $city, $suburb,
            $province, $country, $nationality, $workRight, $howHeard, $userId
        ]);
        $appId = (int)$db->lastInsertId();

        $prefix = ($trackId === 2) ? 'TSG-ASC' : 'TSG-APP';
        $appNumber = $prefix . '-' . date('Y') . '-' . str_pad($appId, 4, '0', STR_PAD_LEFT);

        // Audit status event: Draft started
        $auditStmt = $db->prepare("INSERT INTO rosterstatusevent (rosterapplication, applicationstatus, remarks, reg_by, reg_date, status) VALUES (?, 1, 'Application started via mobile app', ?, CURRENT_TIMESTAMP, 1)");
        $auditStmt->execute([$appId, $userId]);
    }

    sendMobileJson([
        'status' => 1,
        'message' => 'Personal details saved successfully.',
        'application_id' => $appId,
        'application_number' => $appNumber,
        'track_id' => $trackId,
        'next_stage' => 'stage2-upload'
    ]);
});

// --------------------------------------------------------------------------
// 5. Stage 2: Document Upload & List
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/roster/apply/stage2-upload', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $appId = !empty($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
    if (!$appId) {
        sendMobileJson(['status' => 0, 'message' => 'Application ID is required.'], 400);
    }

    $db = (new Database())->getPDO();
    $userId = (int)$user->iD;

    // Verify ownership
    $check = $db->prepare("SELECT iD, applicationstatus FROM rosterapplication WHERE iD = ? AND user = ?");
    $check->execute([$appId, $userId]);
    $app = $check->fetch(PDO::FETCH_ASSOC);
    if (!$app) {
        sendMobileJson(['status' => 0, 'message' => 'Application not found.'], 404);
    }
    if ((int)$app['applicationstatus'] !== 1) {
        sendMobileJson(['status' => 0, 'message' => 'Cannot modify documents on a submitted application.'], 400);
    }

    $file = $_FILES['file'] ?? ($_FILES['document'] ?? null);
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        sendMobileJson(['status' => 0, 'message' => 'Please select a valid file to upload.'], 400);
    }

    // Size limit 10MB for mobile scans
    if ($file['size'] > 10 * 1024 * 1024) {
        sendMobileJson(['status' => 0, 'message' => 'File size exceeds 10MB limit.'], 400);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'docx', 'doc'];
    if (!in_array($ext, $allowed, true)) {
        sendMobileJson(['status' => 0, 'message' => 'Invalid file format. Allowed: PDF, JPG, PNG, DOCX.'], 400);
    }

    $docTypeCode = trim($_POST['doc_type_code'] ?? 'CV_RESUME');
    $dtStmt = $db->prepare("SELECT iD, name FROM documenttype WHERE code = ?");
    $dtStmt->execute([$docTypeCode]);
    $docType = $dtStmt->fetch(PDO::FETCH_ASSOC);
    $docTypeId = $docType ? (int)$docType['iD'] : 1;

    $destDir = _BASE_PATH . '/storage/roster_uploads';
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0777, true);
    }

    $safePrefix = strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '_', $docTypeCode));
    $newFilename = "roster_{$appId}_{$safePrefix}_" . bin2hex(random_bytes(6)) . '.' . $ext;
    $targetPath = $destDir . '/' . $newFilename;

    $moved = move_uploaded_file($file['tmp_name'], $targetPath);
    if (!$moved && (php_sapi_name() === 'cli' || is_file($file['tmp_name']))) {
        $moved = copy($file['tmp_name'], $targetPath);
    }

    if (!$moved) {
        sendMobileJson(['status' => 0, 'message' => 'Failed to write uploaded file to storage.'], 500);
    }

    $fileSizeKb = (int)round($file['size'] / 1024);
    $originalName = $file['name'];

    // If a document of this type already exists for this draft, update it, otherwise insert
    $existDoc = $db->prepare("SELECT iD FROM rosterdocument WHERE rosterapplication = ? AND documenttype = ?");
    $existDoc->execute([$appId, $docTypeId]);
    $docRow = $existDoc->fetch(PDO::FETCH_ASSOC);

    if ($docRow) {
        $upStmt = $db->prepare("UPDATE rosterdocument SET file_path = ?, original_name = ?, file_size_kb = ?, reg_date = CURRENT_TIMESTAMP WHERE iD = ?");
        $upStmt->execute([$newFilename, $originalName, $fileSizeKb, $docRow['iD']]);
        $docId = (int)$docRow['iD'];
    } else {
        $inStmt = $db->prepare("INSERT INTO rosterdocument (rosterapplication, documenttype, original_name, file_path, file_size_kb, reg_by, reg_date, status) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)");
        $inStmt->execute([$appId, $docTypeId, $originalName, $newFilename, $fileSizeKb, $userId]);
        $docId = (int)$db->lastInsertId();
    }

    // Return updated documents list
    $listStmt = $db->prepare("
        SELECT rd.iD as id, rd.documenttype as doc_type_id, dt.code as doc_type_code, dt.name as doc_type_name,
               rd.original_name, rd.file_path, rd.file_size_kb, rd.reg_date
        FROM rosterdocument rd
        JOIN documenttype dt ON rd.documenttype = dt.iD
        WHERE rd.rosterapplication = ?
        ORDER BY rd.iD ASC
    ");
    $listStmt->execute([$appId]);
    $docs = $listStmt->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status' => 1,
        'message' => ($docType ? $docType['name'] : 'Document') . ' uploaded successfully.',
        'uploaded_document_id' => $docId,
        'documents' => $docs
    ]);
});

$router->addRoute('GET', '/api/mobile/roster/apply/documents/:appId', function ($appId) {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $userId = (int)$user->iD;

    // Check ownership
    $check = $db->prepare("SELECT iD FROM rosterapplication WHERE iD = ? AND user = ?");
    $check->execute([(int)$appId, $userId]);
    if (!$check->fetch()) {
        sendMobileJson(['status' => 0, 'message' => 'Application not found.'], 404);
    }

    $stmt = $db->prepare("
        SELECT rd.iD as id, rd.documenttype as doc_type_id, dt.code as doc_type_code, dt.name as doc_type_name,
               rd.original_name, rd.file_path, rd.file_size_kb, rd.reg_date
        FROM rosterdocument rd
        JOIN documenttype dt ON rd.documenttype = dt.iD
        WHERE rd.rosterapplication = ?
        ORDER BY rd.iD ASC
    ");
    $stmt->execute([(int)$appId]);
    $docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendMobileJson([
        'status' => 1,
        'documents' => $docs
    ]);
});

$router->addRoute('POST', '/api/mobile/roster/apply/document-delete', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $docId = (int)($_POST['document_id'] ?? 0);
    $appId = (int)($_POST['application_id'] ?? 0);
    $db = (new Database())->getPDO();

    $stmt = $db->prepare("
        SELECT rd.iD, rd.file_path, ra.applicationstatus
        FROM rosterdocument rd
        JOIN rosterapplication ra ON rd.rosterapplication = ra.iD
        WHERE rd.iD = ? AND ra.iD = ? AND ra.user = ?
    ");
    $stmt->execute([$docId, $appId, (int)$user->iD]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        sendMobileJson(['status' => 0, 'message' => 'Document not found or access denied.'], 404);
    }
    if ((int)$row['applicationstatus'] !== 1) {
        sendMobileJson(['status' => 0, 'message' => 'Cannot remove documents from a submitted application.'], 400);
    }

    // Delete record
    $db->prepare("DELETE FROM rosterdocument WHERE iD = ?")->execute([$docId]);

    // Remove file if exists
    $filePath = _BASE_PATH . '/storage/roster_uploads/' . $row['file_path'];
    if (file_exists($filePath)) {
        @unlink($filePath);
    }

    sendMobileJson([
        'status' => 1,
        'message' => 'Document removed.'
    ]);
});

// --------------------------------------------------------------------------
// 6. Stage 3: Skills & Competency Matrix
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/roster/apply/stage3-skills', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $appId = (int)($_POST['application_id'] ?? 0);
    if (!$appId) {
        sendMobileJson(['status' => 0, 'message' => 'Application ID is required.'], 400);
    }

    $db = (new Database())->getPDO();
    $userId = (int)$user->iD;

    // Verify ownership and draft status
    $check = $db->prepare("SELECT iD, primaryfunction, applicationstatus FROM rosterapplication WHERE iD = ? AND user = ?");
    $check->execute([$appId, $userId]);
    $app = $check->fetch(PDO::FETCH_ASSOC);
    if (!$app || (int)$app['applicationstatus'] !== 1) {
        sendMobileJson(['status' => 0, 'message' => 'Application not editable.'], 400);
    }

    $primaryFuncId = (int)$app['primaryfunction'];

    // Skills can be passed as JSON string or POST array
    $rawSkills = $_POST['skills'] ?? null;
    $skillsData = [];
    if (is_string($rawSkills)) {
        $skillsData = json_decode($rawSkills, true) ?: [];
    } elseif (is_array($rawSkills)) {
        $skillsData = $rawSkills;
    }

    if (empty($skillsData)) {
        sendMobileJson(['status' => 0, 'message' => 'Please rate at least one skill in your competency matrix.'], 400);
    }

    // Clean existing skills for this application
    $db->prepare("DELETE FROM rosterskill WHERE rosterapplication = ?")->execute([$appId]);

    $insStmt = $db->prepare("INSERT INTO rosterskill (rosterapplication, servicefunction, skillitem, proficiencylevel, reg_by, reg_date, status) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)");

    $count = 0;
    foreach ($skillsData as $s) {
        $skillItemId = (int)($s['skill_id'] ?? ($s['skillitem_id'] ?? 0));
        $profLevel = (int)($s['proficiency'] ?? ($s['proficiencylevel'] ?? 1));

        if ($skillItemId > 0) {
            // Find servicefunction for this skillitem
            $sfStmt = $db->prepare("SELECT servicefunction FROM skillitem WHERE iD = ?");
            $sfStmt->execute([$skillItemId]);
            $sfRow = $sfStmt->fetch(PDO::FETCH_ASSOC);
            $sfId = $sfRow ? (int)$sfRow['servicefunction'] : $primaryFuncId;

            $insStmt->execute([$appId, $sfId, $skillItemId, $profLevel, $userId]);
            $count++;
        }
    }

    sendMobileJson([
        'status' => 1,
        'message' => "Successfully saved {$count} competency matrix ratings.",
        'skills_count' => $count,
        'next_stage' => 'stage4-experience'
    ]);
});

// --------------------------------------------------------------------------
// 7. Stage 4: Experience & Referees
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/roster/apply/stage4-experience', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $appId = (int)($_POST['application_id'] ?? 0);
    if (!$appId) {
        sendMobileJson(['status' => 0, 'message' => 'Application ID is required.'], 400);
    }

    $db = (new Database())->getPDO();
    $userId = (int)$user->iD;

    // Verify ownership and draft status
    $check = $db->prepare("SELECT iD, applicationstatus FROM rosterapplication WHERE iD = ? AND user = ?");
    $check->execute([$appId, $userId]);
    $app = $check->fetch(PDO::FETCH_ASSOC);
    if (!$app || (int)$app['applicationstatus'] !== 1) {
        sendMobileJson(['status' => 0, 'message' => 'Application not editable.'], 400);
    }

    // 1. Work history
    $rawHistory = $_POST['work_history'] ?? null;
    $historyData = is_string($rawHistory) ? (json_decode($rawHistory, true) ?: []) : (is_array($rawHistory) ? $rawHistory : []);

    if (!empty($historyData)) {
        $db->prepare("DELETE FROM rosterworkhistory WHERE rosterapplication = ?")->execute([$appId]);
        $whStmt = $db->prepare("INSERT INTO rosterworkhistory (rosterapplication, organization_name, position_title, start_date, end_date, is_current, key_deliverables, reg_by, reg_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)");

        foreach ($historyData as $wh) {
            $org = trim($wh['organization_name'] ?? '');
            $title = trim($wh['job_title'] ?? ($wh['position_title'] ?? ''));
            if (!empty($org) && !empty($title)) {
                $start = trim($wh['start_date'] ?? date('Y-m-d'));
                $end = !empty($wh['end_date']) ? trim($wh['end_date']) : null;
                $isCurrent = !empty($wh['is_current']) ? 1 : 0;
                $achieve = trim($wh['key_achievements'] ?? ($wh['key_deliverables'] ?? ''));
                $whStmt->execute([$appId, $org, $title, $start, $end, $isCurrent, $achieve, $userId]);
            }
        }
    }

    // 2. Referees
    $rawReferees = $_POST['referees'] ?? null;
    $refData = is_string($rawReferees) ? (json_decode($rawReferees, true) ?: []) : (is_array($rawReferees) ? $rawReferees : []);

    if (!empty($refData)) {
        $db->prepare("DELETE FROM rosterreferee WHERE rosterapplication = ?")->execute([$appId]);
        $refStmt = $db->prepare("INSERT INTO rosterreferee (rosterapplication, referee_name, organization, position, relationship, email, phone, refereecontacttiming, reg_by, reg_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)");

        foreach ($refData as $ref) {
            $name = trim($ref['referee_name'] ?? '');
            if (!empty($name)) {
                $org = trim($ref['organization'] ?? '');
                $pos = trim($ref['position'] ?? ($ref['designation'] ?? 'Supervisor'));
                $rel = trim($ref['relationship'] ?? 'Professional');
                $email = trim($ref['email'] ?? '');
                $phone = trim($ref['phone'] ?? '');
                $timing = !empty($ref['timing_id']) ? (int)$ref['timing_id'] : 1;
                $refStmt->execute([$appId, $name, $org, $pos, $rel, $email, $phone, $timing, $userId]);
            }
        }
    }

    sendMobileJson([
        'status' => 1,
        'message' => 'Experience & referee details saved.',
        'next_stage' => 'stage5-submit'
    ]);
});

// --------------------------------------------------------------------------
// 8. Stage 5: Review, Declarations & Final Submission
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/roster/apply/stage5-submit', function () {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $appId = (int)($_POST['application_id'] ?? 0);
    if (!$appId) {
        sendMobileJson(['status' => 0, 'message' => 'Application ID is required.'], 400);
    }

    $db = (new Database())->getPDO();
    $userId = (int)$user->iD;

    // Verify ownership and draft status
    $check = $db->prepare("SELECT * FROM rosterapplication WHERE iD = ? AND user = ?");
    $check->execute([$appId, $userId]);
    $app = $check->fetch(PDO::FETCH_ASSOC);

    if (!$app) {
        sendMobileJson(['status' => 0, 'message' => 'Application not found.'], 404);
    }
    if ((int)$app['applicationstatus'] !== 1) {
        sendMobileJson(['status' => 0, 'message' => 'This application has already been submitted.'], 400);
    }

    // Verify declarations
    $consentAccuracy = !empty($_POST['consent_accuracy']);
    $consentVetting = !empty($_POST['consent_vetting']);
    $eSignature = trim($_POST['e_signature'] ?? '');

    if (!$consentAccuracy || !$consentVetting) {
        sendMobileJson(['status' => 0, 'message' => 'You must agree to the accuracy and background check declarations.'], 400);
    }
    if (empty($eSignature)) {
        sendMobileJson(['status' => 0, 'message' => 'Please provide your full legal name as an electronic signature.'], 400);
    }

    // Verify minimum required documents (CV is mandatory)
    $cvCount = (int)$db->query("SELECT COUNT(*) FROM rosterdocument rd JOIN documenttype dt ON rd.documenttype = dt.iD WHERE rd.rosterapplication = {$appId} AND dt.code = 'CV_RESUME'")->fetchColumn();
    if ($cvCount === 0) {
        sendMobileJson(['status' => 0, 'message' => 'A Curriculum Vitae (CV) must be uploaded before submission.'], 400);
    }

    // Transition status to Submitted (2)
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $subStmt = $db->prepare("
        UPDATE rosterapplication SET
            applicationstatus = 2,
            e_signature = ?,
            consent_version = '2026.1',
            consent_timestamp = CURRENT_TIMESTAMP,
            consent_ip_address = ?
        WHERE iD = ?
    ");
    $subStmt->execute([$eSignature, $clientIp, $appId]);

    // Audit Event
    $auditStmt = $db->prepare("INSERT INTO rosterstatusevent (rosterapplication, applicationstatus, remarks, reg_by, reg_date, status) VALUES (?, 2, 'Candidate completed and submitted roster application via mobile app', ?, CURRENT_TIMESTAMP, 1)");
    $auditStmt->execute([$appId, $userId]);

    // Put the applicant's track profile into "under review" so the submission
    // reaches the admin approvals queue. The web intake already did this; the
    // mobile wizard previously created the application only, which left mobile
    // applicants with no account request for an admin to ever approve.
    $trackTypeId = \App\Helpers\AccountElevation::trackTypeFor($app['applicationtrack']);
    if ($trackTypeId) {
        \App\Helpers\AccountElevation::markUnderReview($userId, $trackTypeId, $userId);
    }

    $prefix = ((int)$app['applicationtrack'] === 2) ? 'TSG-ASC' : 'TSG-APP';
    $year = !empty($app['reg_date']) ? date('Y', strtotime($app['reg_date'])) : date('Y');
    $appNumber = $prefix . '-' . $year . '-' . str_pad($appId, 4, '0', STR_PAD_LEFT);

    // Create In-App Notification if user_notification table exists
    $trackName = ((int)$app['applicationtrack'] === 2) ? 'Associate Roster' : 'Apprentice Roster';
    $msg = "Your application ({$appNumber}) for the {$trackName} has been received and queued for screening.";
    try {
        $db->prepare("INSERT INTO user_notification (user, type, title, message, is_read, reg_date) VALUES (?, 'roster_submission', 'Application Submitted', ?, 0, CURRENT_TIMESTAMP)")->execute([$userId, $msg]);
    } catch (\Throwable $e) {
        // Notification table may be optional
    }

    sendMobileJson([
        'status' => 1,
        'message' => 'Congratulations! Your roster application has been submitted successfully.',
        'application_number' => $appNumber,
        'application_id' => $appId,
        'review_timeline' => 'Initial screening is completed within 3 business days.'
    ]);
});

// --------------------------------------------------------------------------
// 9. Detailed Application Status & Dossier View
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/roster/status/:id', function ($id) {
    $user = getMobileUser();
    if (!$user) {
        sendMobileJson(['status' => 0, 'message' => 'Unauthorized.'], 401);
    }

    $db = (new Database())->getPDO();
    $userId = (int)$user->iD;
    $id = (int)$id;

    $stmt = $db->prepare("
        SELECT ra.*,
               at.code as track_code, at.name as track_name,
               ast.code as status_code, ast.name as status_name, ast.description as status_description,
               sf.name as function_name,
               zp.name as province_name
        FROM rosterapplication ra
        LEFT JOIN applicationtrack at ON ra.applicationtrack = at.iD
        LEFT JOIN applicationstatus ast ON ra.applicationstatus = ast.iD
        LEFT JOIN servicefunction sf ON ra.primaryfunction = sf.iD
        LEFT JOIN zimprovince zp ON ra.zimprovince = zp.iD
        WHERE ra.iD = ? AND ra.user = ?
    ");
    $stmt->execute([$id, $userId]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$app) {
        sendMobileJson(['status' => 0, 'message' => 'Application record not found.'], 404);
    }

    $prefix = ((int)$app['applicationtrack'] === 2) ? 'TSG-ASC' : 'TSG-APP';
    $year = !empty($app['reg_date']) ? date('Y', strtotime($app['reg_date'])) : date('Y');
    $app['application_number'] = $prefix . '-' . $year . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);

    // Documents
    $dStmt = $db->prepare("
        SELECT rd.iD as id, dt.code as doc_type_code, dt.name as doc_type_name,
               rd.original_name, rd.file_size_kb, rd.reg_date
        FROM rosterdocument rd
        JOIN documenttype dt ON rd.documenttype = dt.iD
        WHERE rd.rosterapplication = ?
        ORDER BY rd.iD ASC
    ");
    $dStmt->execute([$id]);
    $documents = $dStmt->fetchAll(PDO::FETCH_ASSOC);

    // Skills
    $sStmt = $db->prepare("
        SELECT rs.iD as id, si.name as skill_name, pl.level_number, pl.name as proficiency_name
        FROM rosterskill rs
        JOIN skillitem si ON rs.skillitem = si.iD
        JOIN proficiencylevel pl ON rs.proficiencylevel = pl.iD
        WHERE rs.rosterapplication = ?
        ORDER BY pl.level_number DESC, si.name ASC
    ");
    $sStmt->execute([$id]);
    $skills = $sStmt->fetchAll(PDO::FETCH_ASSOC);

    // Work History
    $whStmt = $db->prepare("SELECT organization_name, position_title, start_date, end_date, is_current, key_deliverables FROM rosterworkhistory WHERE rosterapplication = ? ORDER BY start_date DESC");
    $whStmt->execute([$id]);
    $workHistory = $whStmt->fetchAll(PDO::FETCH_ASSOC);

    // Referees
    $rStmt = $db->prepare("SELECT referee_name, organization, position, relationship, email, phone FROM rosterreferee WHERE rosterapplication = ?");
    $rStmt->execute([$id]);
    $referees = $rStmt->fetchAll(PDO::FETCH_ASSOC);

    // Timeline / Status Events
    $eStmt = $db->prepare("
        SELECT rse.reg_date as event_timestamp, rse.remarks, ast.name as status_name, ast.code as status_code
        FROM rosterstatusevent rse
        JOIN applicationstatus ast ON rse.applicationstatus = ast.iD
        WHERE rse.rosterapplication = ?
        ORDER BY rse.reg_date ASC
    ");
    $eStmt->execute([$id]);
    $events = $eStmt->fetchAll(PDO::FETCH_ASSOC);

    // Milestone calculation for progress bar
    $milestones = [
        ['code' => 'draft', 'name' => 'Draft Started', 'reached' => true],
        ['code' => 'submitted', 'name' => 'Application Submitted', 'reached' => (int)$app['applicationstatus'] >= 2],
        ['code' => 'screened', 'name' => 'Eligibility Screening', 'reached' => (int)$app['applicationstatus'] >= 3],
        ['code' => 'interviewed', 'name' => 'Technical Verification', 'reached' => (int)$app['applicationstatus'] >= 4],
        ['code' => 'on_roster', 'name' => 'Active on Roster', 'reached' => (int)$app['applicationstatus'] >= 5]
    ];

    sendMobileJson([
        'status' => 1,
        'application' => $app,
        'milestones' => $milestones,
        'documents' => $documents,
        'skills' => $skills,
        'work_history' => $workHistory,
        'referees' => $referees,
        'events' => $events
    ]);
});
