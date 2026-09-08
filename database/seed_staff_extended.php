<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Documenttype;
use App\Models\Verificationstatus;
use App\Models\Leavetype;
use App\Models\Leavestatus;
use App\Models\Activitycategory;
use App\Models\Department;
use App\Models\Staffdocument;
use App\Models\Staffleave;
use App\Models\Stafftimeentry;
use App\Models\Documentverification;
use App\Models\Documentvalidity;
use App\Models\Staffleaveapproval;
use App\Models\Staffleaveattachment;
use App\Models\Stafftimeapproval;
use App\Models\Staffdepartmentassignment;
use App\Models\Staffprofile;
use App\Models\User;

echo "=== Initializing and Seeding Extended Staff Entities ===\n";

// 1. Instantiate all models so tables are auto-created in database
$modelsToInit = [
    new Documenttype(),
    new Verificationstatus(),
    new Leavetype(),
    new Leavestatus(),
    new Activitycategory(),
    new Department(),
    new Staffdocument(),
    new Staffleave(),
    new Stafftimeentry(),
    new Documentverification(),
    new Documentvalidity(),
    new Staffleaveapproval(),
    new Staffleaveattachment(),
    new Stafftimeapproval(),
    new Staffdepartmentassignment(),
];

echo "All 15 tables created or confirmed.\n";

// 2. Seed Document Types
$docTypes = [
    ['code' => 'NAT_ID', 'name' => 'National Identity Card', 'description' => 'Zimbabwe National ID card or registration slip', 'requires_expiry' => 0],
    ['code' => 'DRV_LIC', 'name' => "Driver's Licence", 'description' => 'Valid Zimbabwe or international driver licence', 'requires_expiry' => 1],
    ['code' => 'DEF_DRV', 'name' => 'Defensive Driving Certificate', 'description' => 'Traffic Safety Council of Zimbabwe (TSCZ) certificate', 'requires_expiry' => 1],
    ['code' => 'MED_CERT', 'name' => 'Medical Fitness Certificate', 'description' => 'Certified occupational health / medical examination', 'requires_expiry' => 1],
    ['code' => 'EMP_CON', 'name' => 'Employment Contract', 'description' => 'Signed permanent or fixed-term employment agreement', 'requires_expiry' => 0],
    ['code' => 'ACA_CERT', 'name' => 'Academic / Professional Qualification', 'description' => 'Degree, diploma, or recognized professional certification', 'requires_expiry' => 0],
    ['code' => 'RES_PRF', 'name' => 'Proof of Residence', 'description' => 'Utility bill, bank statement, or verified lease agreement', 'requires_expiry' => 1],
    ['code' => 'POL_CLR', 'name' => 'Police Clearance', 'description' => 'Zimbabwe Republic Police (ZRP) CID vetting certificate', 'requires_expiry' => 1],
];

foreach ($docTypes as $dt) {
    $existing = Documenttype::where('code', $dt['code']);
    if (empty($existing)) {
        Documenttype::create($dt);
        echo "  + DocumentType created: {$dt['code']} ({$dt['name']})\n";
    }
}

// 3. Seed Verification Statuses
$verStatuses = [
    ['code' => 'PENDING', 'name' => 'Pending Review', 'description' => 'Awaiting audit and verification by compliance officer'],
    ['code' => 'VERIFIED', 'name' => 'Verified & Genuine', 'description' => 'Document inspected and confirmed authentic'],
    ['code' => 'REJECTED', 'name' => 'Rejected', 'description' => 'Document rejected due to illegibility, alteration, or invalidity'],
    ['code' => 'EXPIRED', 'name' => 'Expired', 'description' => 'Document has surpassed its legal validity date'],
];

foreach ($verStatuses as $vs) {
    $existing = Verificationstatus::where('code', $vs['code']);
    if (empty($existing)) {
        Verificationstatus::create($vs);
        echo "  + VerificationStatus created: {$vs['code']} ({$vs['name']})\n";
    }
}

// 4. Seed Leave Types
$leaveTypes = [
    ['code' => 'ANNUAL', 'name' => 'Annual Leave', 'description' => 'Statutory paid annual vacation leave', 'is_paid' => 1, 'annual_days' => 22],
    ['code' => 'SICK', 'name' => 'Sick Leave', 'description' => 'Certified medical illness or injury leave', 'is_paid' => 1, 'annual_days' => 12],
    ['code' => 'COMPASSIONATE', 'name' => 'Compassionate Leave', 'description' => 'Bereavement or severe family emergency', 'is_paid' => 1, 'annual_days' => 5],
    ['code' => 'STUDY', 'name' => 'Study & Exam Leave', 'description' => 'Approved professional sitting or revision', 'is_paid' => 1, 'annual_days' => 10],
    ['code' => 'MATERNITY', 'name' => 'Maternity Leave', 'description' => 'Statutory paid maternity leave', 'is_paid' => 1, 'annual_days' => 98],
    ['code' => 'UNPAID', 'name' => 'Unpaid Leave', 'description' => 'Approved leave of absence without pay', 'is_paid' => 0, 'annual_days' => 0],
];

foreach ($leaveTypes as $lt) {
    $existing = Leavetype::where('code', $lt['code']);
    if (empty($existing)) {
        Leavetype::create($lt);
        echo "  + LeaveType created: {$lt['code']} ({$lt['name']})\n";
    }
}

// 5. Seed Leave Statuses
$leaveStatuses = [
    ['code' => 'PENDING', 'name' => 'Pending Approval', 'description' => 'Application submitted and awaiting line manager decision'],
    ['code' => 'APPROVED', 'name' => 'Approved', 'description' => 'Leave authorized by manager'],
    ['code' => 'REJECTED', 'name' => 'Declined', 'description' => 'Leave request rejected with reasons provided'],
    ['code' => 'CANCELLED', 'name' => 'Cancelled', 'description' => 'Application cancelled by employee before commencement'],
];

foreach ($leaveStatuses as $ls) {
    $existing = Leavestatus::where('code', $ls['code']);
    if (empty($existing)) {
        Leavestatus::create($ls);
        echo "  + LeaveStatus created: {$ls['code']} ({$ls['name']})\n";
    }
}

// 6. Seed Activity Categories
$activityCats = [
    ['code' => 'VETTING', 'name' => 'Talent Intake Vetting', 'description' => 'Screening candidate profiles, evidence audit, and 100-pt scoring', 'is_billable' => 0],
    ['code' => 'TRIAGE', 'name' => 'Client Request Triage', 'description' => 'Reviewing incoming client requests, scoping SLAs, and allocating resources', 'is_billable' => 0],
    ['code' => 'CLIENT_DELIVERY', 'name' => 'Billable Client Delivery', 'description' => 'Direct service delivery executing client assignments', 'is_billable' => 1],
    ['code' => 'INFRA', 'name' => 'Systems & IT Infrastructure', 'description' => 'Server administration, code deployment, and platform maintenance', 'is_billable' => 0],
    ['code' => 'FINANCE', 'name' => 'Billing & Financial Accounting', 'description' => 'Invoicing, payment reconciliations, and statutory returns', 'is_billable' => 0],
    ['code' => 'ADMIN', 'name' => 'Internal Administration', 'description' => 'General organizational meetings, reporting, and operational coordination', 'is_billable' => 0],
];

foreach ($activityCats as $ac) {
    $existing = Activitycategory::where('code', $ac['code']);
    if (empty($existing)) {
        Activitycategory::create($ac);
        echo "  + ActivityCategory created: {$ac['code']} ({$ac['name']})\n";
    }
}

// 7. Seed Departments
$departments = [
    ['code' => 'EXEC', 'name' => 'Executive Leadership', 'description' => 'Governance, strategic direction, and stakeholder management'],
    ['code' => 'IT', 'name' => 'IT & Engineering', 'description' => 'Platform engineering, cloud hosting, security, and digital systems'],
    ['code' => 'OPS', 'name' => 'Talent Operations & Vetting', 'description' => 'Talent recruitment, screening, onboarding, and placement'],
    ['code' => 'FIN', 'name' => 'Finance & Invoicing', 'description' => 'Billing runs, financial control, and statutory compliance'],
    ['code' => 'MKT', 'name' => 'Client Relations & Marketing', 'description' => 'Client acquisition, relationship management, and commercial partnerships'],
];

foreach ($departments as $dept) {
    $existing = Department::where('code', $dept['code']);
    if (empty($existing)) {
        Department::create($dept);
        echo "  + Department created: {$dept['code']} ({$dept['name']})\n";
    }
}

echo "=== Seeding Completed Successfully ===\n";
