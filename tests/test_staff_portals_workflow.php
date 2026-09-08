<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;
use App\Models\Staffprofile;
use App\Models\Staffdocument;
use App\Models\Documenttype;
use App\Models\Verificationstatus;
use App\Models\Documentverification;
use App\Models\Documentvalidity;
use App\Models\Staffleave;
use App\Models\Leavetype;
use App\Models\Leavestatus;
use App\Models\Staffleaveapproval;
use App\Models\Staffleaveattachment;
use App\Models\Stafftimeentry;
use App\Models\Activitycategory;
use App\Models\Stafftimeapproval;
use App\Controllers\StaffController;

echo "=== 1. Testing Staff Profile & Dossier Data Readiness ===\n";
$profiles = Staffprofile::all();
assert(count($profiles) > 0, "Staff profiles exist");
$staff = $profiles[0];
$user = $staff->user();
echo "Staff: {$staff->name} (User ID: {$user->iD}, Employee No: {$staff->employee_number})\n";

echo "\n=== 2. Testing Document Vault & Verification Workflow ===\n";
$licenceType = Documenttype::where('code', 'DRV_LIC')[0];
$doc = Staffdocument::create([
    'staffprofile' => $staff->iD,
    'documenttype' => $licenceType->iD,
    'title'        => 'Class 4 Drivers Licence',
    'file_path'    => 'uploads/staff/docs/test_licence.pdf',
    'file_size'    => 512000,
    'mime_type'    => 'application/pdf',
    'reg_by'       => $user->iD
]);
assert($doc && $doc->iD > 0, "StaffDocument created");
echo "PASS: Document uploaded: {$doc->title} (ID: {$doc->iD})\n";

// Add Validity record with expiry date
$validity = Documentvalidity::create([
    'staffdocument' => $doc->iD,
    'issue_date'    => '2024-01-15',
    'expiry_date'   => date('Y-m-d', strtotime('+45 days')),
    'reg_by'        => $user->iD
]);
assert($validity && $validity->iD > 0, "Documentvalidity created");
echo "PASS: Validity filed: Expires on {$validity->expiry_date}\n";

// Audit verification
$verStatus = Verificationstatus::where('code', 'VERIFIED')[0];
$audit = Documentverification::create([
    'staffdocument'      => $doc->iD,
    'verificationstatus' => $verStatus->iD,
    'notes'              => 'Licence inspected and authentic.',
    'reg_by'             => 1
]);
assert($audit && $audit->iD > 0, "Documentverification created");
echo "PASS: Document verified by auditor (Audit ID: {$audit->iD})\n";

echo "\n=== 3. Testing Staff Leave & Manager Decision Workflow ===\n";
$annualType = Leavetype::where('code', 'ANNUAL')[0];
$leave = Staffleave::create([
    'staffprofile'   => $staff->iD,
    'leavetype'      => $annualType->iD,
    'start_date'     => date('Y-m-d', strtotime('+10 days')),
    'end_date'       => date('Y-m-d', strtotime('+14 days')),
    'days_requested' => 5.0,
    'reason'         => 'Scheduled annual leave with team handover complete.',
    'reg_by'         => $user->iD
]);
assert($leave && $leave->iD > 0, "Staffleave created");
echo "PASS: Leave application submitted for {$leave->days_requested} days (ID: {$leave->iD})\n";

// Medical / Supporting attachment
$att = Staffleaveattachment::create([
    'staffleave' => $leave->iD,
    'file_path'  => 'uploads/staff/leaves/test_handover.pdf',
    'file_size'  => 128000,
    'mime_type'  => 'application/pdf',
    'reg_by'     => $user->iD
]);
assert($att && $att->iD > 0, "Staffleaveattachment created");
echo "PASS: Attachment linked to leave request (Attachment ID: {$att->iD})\n";

// Manager Approval Event
$apprStatus = Leavestatus::where('code', 'APPROVED')[0];
$leaveAppr = Staffleaveapproval::create([
    'staffleave'     => $leave->iD,
    'leavestatus'    => $apprStatus->iD,
    'decision_notes' => 'Authorized. Handover approved by line manager.',
    'reg_by'         => 1
]);
assert($leaveAppr && $leaveAppr->iD > 0, "Staffleaveapproval created");
echo "PASS: Leave request approved (Approval ID: {$leaveAppr->iD})\n";

echo "\n=== 4. Testing Operational Time Logging & Sign-Off Workflow ===\n";
$infraCat = Activitycategory::where('code', 'INFRA')[0];
$timeLog = Stafftimeentry::create([
    'staffprofile'     => $staff->iD,
    'activitycategory' => $infraCat->iD,
    'work_date'        => date('Y-m-d'),
    'hours'            => 6.5,
    'task_summary'     => 'Server hardening, database indexing, and deployment scripts verification.',
    'reg_by'           => $user->iD
]);
assert($timeLog && $timeLog->iD > 0, "Stafftimeentry created");
echo "PASS: Operational hours logged: {$timeLog->hours} hrs (ID: {$timeLog->iD})\n";

// Supervisor Sign-Off Event
$timeSignoff = Stafftimeapproval::create([
    'stafftimeentry' => $timeLog->iD,
    'is_approved'    => 1,
    'review_notes'   => 'Infrastructure maintenance deliverables verified.',
    'reg_by'         => 1
]);
assert($timeSignoff && $timeSignoff->iD > 0, "Stafftimeapproval created");
echo "PASS: Time entry signed off by supervisor (Sign-Off ID: {$timeSignoff->iD})\n";

echo "\n=== 5. Testing Queue Filters & Expiry Detection ===\n";
$today = date('Y-m-d');
$radarDate = date('Y-m-d', strtotime('+90 days'));
$allExpiring = Documentvalidity::findByQuery("SELECT * FROM documentvalidity WHERE expiry_date <= ?", [$radarDate]);
assert(count($allExpiring) > 0, "Expiring credentials detected");
echo "PASS: Radar detected " . count($allExpiring) . " expiring document(s) within 90-day window.\n";

// Clean up test rows
$timeSignoff->delete();
$timeLog->delete();
$leaveAppr->delete();
$att->delete();
$leave->delete();
$audit->delete();
$validity->delete();
$doc->delete();
echo "PASS: Test transactions safely cleaned up.\n";

echo "\n=== ALL 3 STAFF PORTAL SUITES PASSED VERIFICATION WITH 100% SUCCESS ===\n";
