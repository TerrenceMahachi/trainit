<?php
/**
 * Database Migration & Seeder for Tsigiro Vacancies & Staff Onboarding
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Models\Database;

$pdo = Database::sharedPdo();

echo "=== Migrating Vacancy & Staff Recruitment Tables ===\n";

// 1. vacancystatus
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `vacancystatus` (
        `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
        `code` TEXT NOT NULL UNIQUE,
        `name` TEXT NOT NULL,
        `description` TEXT NOT NULL,
        `badge_class` TEXT NOT NULL DEFAULT 'bg-secondary',
        `sort_order` INTEGER NOT NULL DEFAULT 1,
        `reg_by` INTEGER NOT NULL DEFAULT 1,
        `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `status` INTEGER NOT NULL DEFAULT 1
    );
");

// 2. vacancyapplicationstatus
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `vacancyapplicationstatus` (
        `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
        `code` TEXT NOT NULL UNIQUE,
        `name` TEXT NOT NULL,
        `badge_class` TEXT NOT NULL DEFAULT 'bg-secondary',
        `sort_order` INTEGER NOT NULL DEFAULT 1,
        `reg_by` INTEGER NOT NULL DEFAULT 1,
        `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `status` INTEGER NOT NULL DEFAULT 1
    );
");

// 3. vacancy
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `vacancy` (
        `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
        `reference_number` TEXT NOT NULL UNIQUE,
        `title` TEXT NOT NULL,
        `slug` TEXT NOT NULL UNIQUE,
        `department` INTEGER NOT NULL,
        `engagementbasis` INTEGER NOT NULL,
        `worklocationpreference` INTEGER NOT NULL,
        `target_role` INTEGER NOT NULL,
        `summary` TEXT NOT NULL,
        `description` TEXT NOT NULL,
        `responsibilities` TEXT NOT NULL,
        `requirements` TEXT NOT NULL,
        `remuneration_display` TEXT DEFAULT NULL,
        `open_slots` INTEGER NOT NULL DEFAULT 1,
        `publish_date` DATE NOT NULL,
        `closing_date` DATE NOT NULL,
        `is_featured` BOOLEAN NOT NULL DEFAULT 0,
        `vacancystatus` INTEGER NOT NULL DEFAULT 1,
        `reg_by` INTEGER NOT NULL DEFAULT 1,
        `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `status` INTEGER NOT NULL DEFAULT 1,
        FOREIGN KEY(`department`) REFERENCES `department`(`iD`),
        FOREIGN KEY(`engagementbasis`) REFERENCES `engagementbasis`(`iD`),
        FOREIGN KEY(`worklocationpreference`) REFERENCES `worklocationpreference`(`iD`),
        FOREIGN KEY(`target_role`) REFERENCES `user_role`(`iD`),
        FOREIGN KEY(`vacancystatus`) REFERENCES `vacancystatus`(`iD`),
        FOREIGN KEY(`reg_by`) REFERENCES `user`(`iD`)
    );
");

// 4. vacancy_skill
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `vacancy_skill` (
        `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
        `vacancy` INTEGER NOT NULL,
        `skillitem` INTEGER NOT NULL,
        `is_mandatory` BOOLEAN NOT NULL DEFAULT 1,
        `reg_by` INTEGER NOT NULL DEFAULT 1,
        `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `status` INTEGER NOT NULL DEFAULT 1,
        FOREIGN KEY(`vacancy`) REFERENCES `vacancy`(`iD`) ON DELETE CASCADE,
        FOREIGN KEY(`skillitem`) REFERENCES `skillitem`(`iD`) ON DELETE CASCADE,
        UNIQUE(`vacancy`, `skillitem`)
    );
");

// 5. vacancy_application
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `vacancy_application` (
        `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
        `vacancy` INTEGER NOT NULL,
        `application_number` TEXT NOT NULL UNIQUE,
        `user` INTEGER DEFAULT NULL,
        `first_name` TEXT NOT NULL,
        `last_name` TEXT NOT NULL,
        `email` TEXT NOT NULL,
        `phone` TEXT NOT NULL,
        `city` TEXT NOT NULL,
        `country` TEXT NOT NULL DEFAULT 'Zimbabwe',
        `years_of_experience` INTEGER NOT NULL DEFAULT 0,
        `highest_qualification` TEXT DEFAULT NULL,
        `current_employer` TEXT DEFAULT NULL,
        `current_job_title` TEXT DEFAULT NULL,
        `expected_salary` TEXT DEFAULT NULL,
        `notice_period_days` INTEGER DEFAULT 30,
        `cover_letter` TEXT DEFAULT NULL,
        `cv_path` TEXT NOT NULL,
        `application_status` INTEGER NOT NULL DEFAULT 1,
        `rating_score` INTEGER DEFAULT NULL,
        `admin_notes` TEXT DEFAULT NULL,
        `interview_at` DATETIME DEFAULT NULL,
        `staff_invite` INTEGER DEFAULT NULL,
        `appointed_at` DATETIME DEFAULT NULL,
        `reg_by` INTEGER NOT NULL DEFAULT 1,
        `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `status` INTEGER NOT NULL DEFAULT 1,
        FOREIGN KEY(`vacancy`) REFERENCES `vacancy`(`iD`),
        FOREIGN KEY(`user`) REFERENCES `user`(`iD`),
        FOREIGN KEY(`application_status`) REFERENCES `vacancyapplicationstatus`(`iD`),
        FOREIGN KEY(`staff_invite`) REFERENCES `staff_invite`(`iD`)
    );
");

echo "Tables created successfully.\n";

// Seed vacancystatus
$vacancyStatuses = [
    [1, 'draft', 'Draft', 'Vacancy created but not visible publicly', 'bg-secondary', 1],
    [2, 'published', 'Published', 'Active position published on Opportunities page', 'bg-success', 2],
    [3, 'under_review', 'Under Review', 'Application deadline reached; screening underway', 'bg-warning text-dark', 3],
    [4, 'closed', 'Closed / Filled', 'Position filled and closed', 'bg-dark', 4],
    [5, 'archived', 'Archived', 'Historical vacancy archive', 'bg-secondary', 5],
];

$stmt = $pdo->prepare("INSERT OR IGNORE INTO `vacancystatus` (iD, code, name, description, badge_class, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($vacancyStatuses as $s) {
    $stmt->execute($s);
}

// Seed vacancyapplicationstatus
$appStatuses = [
    [1, 'submitted', 'Application Received', 'bg-info text-dark', 1],
    [2, 'shortlisted', 'Shortlisted', 'bg-primary text-white', 2],
    [3, 'interview', 'Interview Scheduled', 'bg-warning text-dark', 3],
    [4, 'offer', 'Offer Extended', 'bg-purple text-white', 4],
    [5, 'appointed', 'Appointed to Staff', 'bg-success text-white', 5],
    [6, 'regretted', 'Not Shortlisted', 'bg-danger text-white', 6],
];

$stmt = $pdo->prepare("INSERT OR IGNORE INTO `vacancyapplicationstatus` (iD, code, name, badge_class, sort_order) VALUES (?, ?, ?, ?, ?)");
foreach ($appStatuses as $s) {
    $stmt->execute($s);
}

// Seed initial Published Vacancies
$vacancies = [
    [
        'iD' => 1,
        'reference_number' => 'TSG-VAC-2026-001',
        'title' => 'Talent Operations & Vetting Officer',
        'slug' => 'talent-operations-vetting-officer',
        'department' => 3, // OPS (Talent Operations & Vetting)
        'engagementbasis' => 1, // Full-time
        'worklocationpreference' => 3, // Hybrid (Harare HQ + Remote)
        'target_role' => 8, // Vetting Officer
        'summary' => 'Lead candidate verification, referee checking, and technical competency scoring across our expanding Apprentice and Associate talent networks.',
        'description' => 'Tsigiro is hiring a Talent Operations & Vetting Officer to spearhead candidate credential screening, 100-point rubric scoring, and statutory onboarding compliance across Africa and regional client briefs.',
        'responsibilities' => "• Review incoming express intake applications across Apprentice and Associate tracks.\n• Validate submitted academic credentials, university attachment logbooks, and national IDs.\n• Conduct structured reference checks with previous supervisors and academic deans.\n• Execute statutory onboarding compliance files for ZIMRA tax numbers and USD bank accounts.\n• Maintain candidate status workflows and dispatch magic link verification dossiers.",
        'requirements' => "• Bachelor's Degree in Human Resource Management, Psychology, Business Administration, or related.\n• Minimum 3 years in talent acquisition, background screening, or operational vetting.\n• Strong grasp of Zimbabwean labor statutory compliance (ZIMRA, NSSA, NEC, PRAZ).\n• Exceptional written communication and confidentiality ethics.",
        'remuneration_display' => '$900 - $1,300 / month (USD) + Health Cover',
        'open_slots' => 1,
        'publish_date' => '2026-09-01',
        'closing_date' => '2026-10-15',
        'is_featured' => 1,
        'vacancystatus' => 2, // Published
        'skills' => [13, 14, 98] // Recruitment admin, Contract admin, Records management
    ],
    [
        'iD' => 2,
        'reference_number' => 'TSG-VAC-2026-002',
        'title' => 'Senior Financial Compliance & Grants Associate',
        'slug' => 'senior-financial-compliance-grants-associate',
        'department' => 4, // FIN (Finance & Invoicing)
        'engagementbasis' => 1, // Full-time
        'worklocationpreference' => 3, // Hybrid
        'target_role' => 7, // Billing / Finance Officer
        'summary' => 'Oversee shared finance operations, donor budget compliance, statutory returns, and client multi-currency management accounts.',
        'description' => 'We are seeking an experienced Finance Professional to manage shared-services financial controllership for non-profit and corporate clients, including monthly management accounts, audit preparation, and ZIMRA filings.',
        'responsibilities' => "• Prepare monthly client management accounts in compliance with IFRS and local standards.\n• Reconcile multi-currency bank accounts (USD and ZWG) with strict exchange control compliance.\n• File monthly and quarterly statutory returns (PAYE, VAT, QPDs, NSSA, ZIMDEF).\n• Review donor acquittals, grant Budget-vs-Actual (BVA) matrices, and audit working papers.\n• Provide senior financial oversight and mentor junior finance apprentices on live client briefs.",
        'requirements' => "• Full professional qualification (ACCA, CIMA, CAZ, or CIS / CGI) or Degree in Accounting.\n• Minimum 5 years of post-qualification financial management or audit experience.\n• Demonstrated track record in donor compliance (USAID, EU, FCDO, or UN agencies) is advantageous.\n• Proficiency in cloud ERPs (QuickBooks, Sage, Odoo) and advanced financial modeling.",
        'remuneration_display' => '$1,600 - $2,200 / month (USD)',
        'open_slots' => 1,
        'publish_date' => '2026-09-05',
        'closing_date' => '2026-10-20',
        'is_featured' => 1,
        'vacancystatus' => 2, // Published
        'skills' => [7, 8, 6, 10, 11] // Mgmt accounts, ZIMRA, Donor BVA, Audit file, Multi-currency
    ],
    [
        'iD' => 3,
        'reference_number' => 'TSG-VAC-2026-003',
        'title' => 'ICT & DevOps Systems Apprentice',
        'slug' => 'ict-devops-systems-apprentice',
        'department' => 2, // IT (IT & Engineering)
        'engagementbasis' => 5, // Work-Related Learning / Attachment
        'worklocationpreference' => 1, // Harare HQ / On-site
        'target_role' => 5, // Apprentice
        'summary' => 'Hands-on industrial attachment for university/polytechnic students in computer science, software engineering, or network administration.',
        'description' => 'Designed for tertiary students seeking a mentored 1-year industrial attachment. Work alongside senior cloud architects and software engineers on live portal infrastructure, server maintenance, and client IT support.',
        'responsibilities' => "• Assist in routine server administration, automated backups, and disaster recovery testing.\n• Support front-end and back-end PHP / MySQL / SQLite application updates.\n• Troubleshoot network connectivity, workstations, and Google Workspace / Microsoft 365 tenants.\n• Document systems architectures and complete weekly institutional attachment logbooks.\n• Participate in client helpdesk ticket triage and technical resolutions.",
        'requirements' => "• Currently registered for a Degree or Higher National Diploma in Computer Science, IT, Software Engineering, or related.\n• Official institutional Work-Related Learning (WRL) approval letter from university or polytechnic.\n• Foundation knowledge in web technologies (HTML, CSS, JavaScript, PHP, relational databases).\n• High curiosity, integrity, and eagerness to build enterprise-grade technical skills.",
        'remuneration_display' => 'Monthly Transport & Meal Stipend (USD) + Mentorship',
        'open_slots' => 2,
        'publish_date' => '2026-09-08',
        'closing_date' => '2026-10-31',
        'is_featured' => 0,
        'vacancystatus' => 2, // Published
        'skills' => [73, 76, 61, 90] // Web frontend, PHP, Backup DR, Web CMS
    ]
];

$vStmt = $pdo->prepare("
    INSERT OR IGNORE INTO `vacancy` (
        iD, reference_number, title, slug, department, engagementbasis, worklocationpreference,
        target_role, summary, description, responsibilities, requirements, remuneration_display,
        open_slots, publish_date, closing_date, is_featured, vacancystatus, reg_by, status
    ) VALUES (
        :iD, :reference_number, :title, :slug, :department, :engagementbasis, :worklocationpreference,
        :target_role, :summary, :description, :responsibilities, :requirements, :remuneration_display,
        :open_slots, :publish_date, :closing_date, :is_featured, :vacancystatus, 1, 1
    )
");

$skillStmt = $pdo->prepare("
    INSERT OR IGNORE INTO `vacancy_skill` (vacancy, skillitem, is_mandatory, reg_by, status)
    VALUES (?, ?, 1, 1, 1)
");

foreach ($vacancies as $v) {
    $skills = $v['skills'];
    unset($v['skills']);
    $vStmt->execute($v);
    
    foreach ($skills as $skillId) {
        $skillStmt->execute([$v['iD'], $skillId]);
    }
}

echo "=== Seeded Vacancy Reference Data & Initial Open Positions Successfully ===\n";
