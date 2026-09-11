<?php
/**
 * Database Seeder: Comprehensive Testing & Demo Accounts
 *
 * Seeds 8 realistic, fully-featured testing accounts representing every key role:
 * 1. Administrator (role 1)
 * 2. Vetting Officer (role 8, Staff)
 * 3. Service Delivery Manager (role 6, Staff)
 * 4. Billing & Finance Officer (role 7, Staff)
 * 5. Apprentice Candidate (role 2, Apprentice Roster)
 * 6. Associate Specialist Candidate (role 2, Associate Roster)
 * 7. Job Vacancy Applicant (role 2, Direct Vacancy Application)
 * 8. Client Organisation User (role 3, Client Workspace)
 *
 * Password for ALL testing accounts: Password123!
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;
use App\Models\Login;
use App\Models\Database;
use App\Models\Staffprofile;
use App\Models\Rosterapplication;
use App\Models\Apprenticeprofile;
use App\Models\Associateprofile;
use App\Models\Rosterstatusevent;
use App\Models\Vacancy;
use App\Models\VacancyApplication;
use App\Models\Clientorganization;

echo "=== Seeding Tsigiro Portal Testing & Demo Accounts ===\n\n";

$db = new Database();
$defaultPassword = 'Password123!';
$passwordHash = password_hash($defaultPassword, PASSWORD_BCRYPT);

$demoAccounts = [
    [
        'key'         => 'admin',
        'email'       => 'admin@tsigiro.co.zw',
        'name'        => 'System Administrator',
        'role'        => 1,
        'role_title'  => 'Administrator',
        'type'        => 'admin',
        'description' => 'Full platform governance, vacancy publishing, review console, and staff appointment',
    ],
    [
        'key'         => 'vetting',
        'email'       => 'vetting@tsigiro.co.zw',
        'name'        => 'Ruvimbo Sithole',
        'role'        => 8,
        'role_title'  => 'Vetting Officer',
        'type'        => 'staff',
        'dept'        => 'Operations',
        'job_title'   => 'Vetting & Compliance Officer',
        'emp_num'     => 'TSG-STF-008',
        'description' => 'Evaluates candidate dossiers, scores rubrics, and schedules interviews',
    ],
    [
        'key'         => 'manager',
        'email'       => 'manager@tsigiro.co.zw',
        'name'        => 'Tafadzwa Mutasa',
        'role'        => 6,
        'role_title'  => 'Service Manager',
        'type'        => 'staff',
        'dept'        => 'Operations',
        'job_title'   => 'Service Delivery Manager',
        'emp_num'     => 'TSG-STF-006',
        'description' => 'Service operations, team assignments, delivery oversight',
    ],
    [
        'key'         => 'finance',
        'email'       => 'finance@tsigiro.co.zw',
        'name'        => 'Nyasha Chidziwa',
        'role'        => 7,
        'role_title'  => 'Billing Officer',
        'type'        => 'staff',
        'dept'        => 'Finance',
        'job_title'   => 'Financial Controller & Billing Officer',
        'emp_num'     => 'TSG-STF-007',
        'description' => 'Manages client retainer plans, disbursements, billing, and contracts',
    ],
    [
        'key'         => 'apprentice',
        'email'       => 'apprentice@tsigiro.co.zw',
        'name'        => 'Kudzai Mapfumo',
        'role'        => 2,
        'role_title'  => 'Apprentice (WRL Student)',
        'type'        => 'apprentice',
        'description' => 'NUST Computer Science student on industrial attachment, shortlisted for placement',
    ],
    [
        'key'         => 'associate',
        'email'       => 'associate@tsigiro.co.zw',
        'name'        => 'Simbarashe Hove',
        'role'        => 2,
        'role_title'  => 'Associate Specialist',
        'type'        => 'associate',
        'description' => 'Senior Cloud & DevOps specialist, $180/day, technical interview stage',
    ],
    [
        'key'         => 'candidate',
        'email'       => 'candidate@tsigiro.co.zw',
        'name'        => 'Farai Chikwanha',
        'role'        => 2,
        'role_title'  => 'Job Vacancy Applicant',
        'type'        => 'vacancy_applicant',
        'description' => 'Applied for Talent Operations & Vetting Officer, interview scheduled',
    ],
    [
        'key'         => 'client',
        'email'       => 'client@tsigiro.co.zw',
        'name'        => 'Tinashe Gumbo',
        'role'        => 3,
        'role_title'  => 'Client Lead (EcoSolutions)',
        'type'        => 'client',
        'description' => 'Managing Director of EcoSolutions Zimbabwe, oversees service retainers',
    ],
];

foreach ($demoAccounts as $acc) {
    echo "--- Provisioning: {$acc['name']} ({$acc['email']}) [{$acc['role_title']}] ---\n";

    // 1. Create or Update User
    $users = User::findByQuery("SELECT * FROM user WHERE email = ? LIMIT 1", [$acc['email']]);
    if (empty($users)) {
        $u = new User();
        $u->name = $acc['name'];
        $u->email = $acc['email'];
        $u->role = $acc['role'];
        $u->status = 1;
        $u->reg_by = 1;
        $u->save();
        $userId = (int)$u->iD;
        echo "  + Created user #{$userId}\n";
    } else {
        $u = $users[0];
        $u->name = $acc['name'];
        $u->role = $acc['role'];
        $u->status = 1;
        $u->update();
        $userId = (int)$u->iD;
        echo "  * Updated existing user #{$userId}\n";
    }

    // 2. Create or Reset Login Credentials
    $logins = Login::findByQuery("SELECT * FROM user_login WHERE user = ? LIMIT 1", [$userId]);
    if (empty($logins)) {
        $l = new Login();
        $l->user = $userId;
        $l->password = $passwordHash;
        $l->status = 1;
        $l->reg_by = 1;
        $l->save();
        echo "  + Created login credentials\n";
    } else {
        $l = $logins[0];
        $l->password = $passwordHash;
        $l->status = 1;
        $l->update();
        echo "  * Reset login password\n";
    }

    // 3. Seed Role-Specific Relational Data
    switch ($acc['type']) {
        case 'staff':
            // Ensure staffprofile exists
            $staffProfiles = $db->query("SELECT * FROM staffprofile WHERE user = ?", [$userId])->fetchAll(PDO::FETCH_ASSOC);
            if (empty($staffProfiles)) {
                $nameParts = explode(' ', $acc['name']);
                $firstName = $nameParts[0];
                $surname = $nameParts[1] ?? 'Staff';
                $db->query(
                    "INSERT INTO staffprofile (user, employee_number, job_title, department, nature_of_employment, employee_type, date_of_employment, work_email, first_name, surname, town, region, country, status)
                     VALUES (?, ?, ?, ?, 'ORDINARY', 'Employee', '2025-01-15', ?, ?, ?, 'HARARE', 'HRE', 'Zimbabwe', 1)",
                    [$userId, $acc['emp_num'], $acc['job_title'], $acc['dept'], $acc['email'], $firstName, $surname]
                );
                echo "  + Created staffprofile ({$acc['emp_num']}, {$acc['job_title']})\n";
            }
            break;

        case 'apprentice':
            // Ensure rosterapplication exists
            $appApps = Rosterapplication::findByQuery("SELECT * FROM rosterapplication WHERE user = ? LIMIT 1", [$userId]);
            if (empty($appApps)) {
                $app = new Rosterapplication();
                $app->user = $userId;
                $app->applicationtrack = 1; // Apprentice
                $app->applicationstatus = 3; // Screened / Shortlisted
                $app->legal_name = 'Kudzai Mapfumo';
                $app->preferred_name = 'Kudzai';
                $app->email = $acc['email'];
                $app->mobile_number = '+263 77 345 6789';
                $app->city = 'Bulawayo';
                $app->zimprovince = 1; // Bulawayo
                $app->primaryfunction = 1; // Software Development
                $app->reg_by = $userId;
                $app->save();
                $appId = (int)$app->iD;

                // Create Apprentice Profile
                $db->query(
                    "INSERT INTO apprenticeprofile (rosterapplication, institution_name, degree_programme, study_level, wrl_start_date, wrl_duration_months, is_wrl_attachment, reg_by, status)
                     VALUES (?, 'National University of Science & Technology (NUST)', 'BSc (Hons) Computer Science & Informatics', 'Seeking Attachment (Year 3/Part 3)', '2026-10-01', 12, 1, ?, 1)",
                    [$appId, $userId]
                );

                // Audit events
                $db->query(
                    "INSERT INTO rosterstatusevent (rosterapplication, applicationstatus, remarks, reg_by)
                     VALUES (?, 2, 'Initial express application and CV submitted via Opportunities portal.', ?)",
                    [$appId, $userId]
                );
                $db->query(
                    "INSERT INTO rosterstatusevent (rosterapplication, applicationstatus, remarks, reg_by)
                     VALUES (?, 3, 'Candidate evaluated and shortlisted by Talent Vetting Committee for academic achievement and coursework.', 1)",
                    [$appId]
                );
                echo "  + Created Apprentice application #{$appId} (Status: Shortlisted) & NUST Profile\n";
            }
            break;

        case 'associate':
            // Ensure rosterapplication exists
            $assocApps = Rosterapplication::findByQuery("SELECT * FROM rosterapplication WHERE user = ? LIMIT 1", [$userId]);
            if (empty($assocApps)) {
                $app = new Rosterapplication();
                $app->user = $userId;
                $app->applicationtrack = 2; // Associate
                $app->applicationstatus = 4; // Interview Scheduled
                $app->legal_name = 'Simbarashe Hove';
                $app->preferred_name = 'Simba';
                $app->email = $acc['email'];
                $app->mobile_number = '+263 71 888 4321';
                $app->city = 'Harare';
                $app->zimprovince = 2; // Harare
                $app->primaryfunction = 1; // Technical Operations / Cloud Architecture
                $app->reg_by = $userId;
                $app->save();
                $appId = (int)$app->iD;

                // Create Associate Profile
                $db->query(
                    "INSERT INTO associateprofile (rosterapplication, years_experience, employmentstatus, day_rate_expectation, capacity_days_per_month, has_tax_clearance_itf263, reg_by, status)
                     VALUES (?, '8+ Years', 1, 180.00, '15 days / month', 1, ?, 1)",
                    [$appId, $userId]
                );

                // Audit events
                $db->query(
                    "INSERT INTO rosterstatusevent (rosterapplication, applicationstatus, remarks, reg_by)
                     VALUES (?, 2, 'Initial express application submitted.', ?)",
                    [$appId, $userId]
                );
                $db->query(
                    "INSERT INTO rosterstatusevent (rosterapplication, applicationstatus, remarks, reg_by)
                     VALUES (?, 3, 'Candidate shortlisted for Specialist Roster.', 1)",
                    [$appId]
                );
                $db->query(
                    "INSERT INTO rosterstatusevent (rosterapplication, applicationstatus, remarks, reg_by)
                     VALUES (?, 4, 'Technical panel interview scheduled with Lead Cloud Architect.', 1)",
                    [$appId]
                );
                echo "  + Created Associate application #{$appId} (Status: Interview Scheduled, $180/day, ITF263 cleared)\n";
            }
            break;

        case 'vacancy_applicant':
            // Check published vacancies
            $vacancies = Vacancy::where('vacancystatus', 2);
            $targetVac = !empty($vacancies) ? $vacancies[0] : null;
            if ($targetVac) {
                $vacApps = VacancyApplication::findByQuery("SELECT * FROM vacancy_application WHERE email = ? LIMIT 1", [$acc['email']]);
                if (empty($vacApps)) {
                    $dummyCv = 'uploads/cvs/CV_Farai_Chikwanha.pdf';
                    if (!file_exists(_BASE_PATH . '/public/' . $dummyCv)) {
                        @file_put_contents(_BASE_PATH . '/public/' . $dummyCv, '%PDF-1.4 Mock CV resume for Farai Chikwanha');
                    }

                    $va = new VacancyApplication();
                    $va->vacancy = $targetVac->iD;
                    $va->application_number = 'APP-TSG-VAC-2026-DEMO';
                    $va->user = $userId;
                    $va->first_name = 'Farai';
                    $va->last_name = 'Chikwanha';
                    $va->email = $acc['email'];
                    $va->phone = '+263 77 987 6543';
                    $va->city = 'Harare';
                    $va->country = 'Zimbabwe';
                    $va->years_of_experience = 4;
                    $va->highest_qualification = 'BSc (Hons) Information Systems & Operations';
                    $va->current_employer = 'Apex Financial Solutions';
                    $va->current_job_title = 'Operations & Systems Analyst';
                    $va->expected_salary = '$1,400 / month';
                    $va->notice_period_days = 30;
                    $va->cover_letter = 'I am passionate about talent vetting, operations, and compliance.';
                    $va->cv_path = $dummyCv;
                    $va->application_status = 3; // Interview Scheduled
                    $va->interview_at = date('Y-m-d 10:30:00', strtotime('+4 days'));
                    $va->rating_score = 88;
                    $va->admin_notes = 'Strong candidate with extensive operations experience. Scheduled for structured panel interview.';
                    $va->reg_by = $userId;
                    $va->status = 1;
                    $va->save();
                    echo "  + Created Vacancy Application '{$va->application_number}' for '{$targetVac->title}' (Interview Scheduled)\n";
                }
            }
            break;

        case 'client':
            // Check client organisation
            $orgs = $db->query("SELECT * FROM clientorganization WHERE legal_name = 'EcoSolutions Zimbabwe (Pvt) Ltd'")->fetchAll(PDO::FETCH_ASSOC);
            if (empty($orgs)) {
                $db->query(
                    "INSERT INTO clientorganization (legal_name, trading_name, registration_number, tax_number, billing_email, address, city, country, primary_phone, status)
                     VALUES ('EcoSolutions Zimbabwe (Pvt) Ltd', 'EcoSolutions', 'ZW-CO-2023-8871', 'BP20098177', 'billing@ecosolutions.co.zw', '12 Enterprise Road, Newlands', 'Harare', 'Zimbabwe', '+263 242 778899', 1)"
                );
                $createdOrg = $db->query("SELECT iD FROM clientorganization WHERE legal_name = 'EcoSolutions Zimbabwe (Pvt) Ltd'")->fetch(PDO::FETCH_ASSOC);
                $orgId = (int)$createdOrg['iD'];
                echo "  + Created Client Organisation 'EcoSolutions Zimbabwe (Pvt) Ltd' (#{$orgId})\n";
            } else {
                $orgId = (int)$orgs[0]['iD'];
            }

            // Ensure client membership
            $memberships = $db->query("SELECT * FROM clientmembership WHERE user = ? AND clientorganization = ?", [$userId, $orgId])->fetchAll(PDO::FETCH_ASSOC);
            if (empty($memberships)) {
                $db->query(
                    "INSERT INTO clientmembership (clientorganization, user, clientmemberrole, status)
                     VALUES (?, ?, 1, 1)",
                    [$orgId, $userId]
                );
                echo "  + Linked client membership as Owner for user #{$userId}\n";
            }
            break;
    }
    echo "\n";
}

echo "=== All 8 Testing Accounts Successfully Seeded! ===\n";
echo "Password for all accounts: {$defaultPassword}\n";
