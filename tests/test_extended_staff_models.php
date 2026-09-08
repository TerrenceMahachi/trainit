<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Staffprofile;
use App\Models\Documenttype;
use App\Models\Verificationstatus;
use App\Models\Leavetype;
use App\Models\Leavestatus;
use App\Models\Activitycategory;
use App\Models\Department;
use App\Models\Staffdocument;
use App\Models\Documentverification;
use App\Models\Documentvalidity;
use App\Models\Staffleave;
use App\Models\Staffleaveapproval;
use App\Models\Staffleaveattachment;
use App\Models\Stafftimeentry;
use App\Models\Stafftimeapproval;
use App\Models\Staffdepartmentassignment;
use App\Models\User;

echo "--- 1. Testing Reference Lookups ---\n";
$natIdType = Documenttype::where('code', 'NAT_ID')[0] ?? null;
assert($natIdType !== null, "NAT_ID type exists");
echo "PASS: DocumentType found: {$natIdType->name} (requires_expiry: {$natIdType->requires_expiry})\n";

$annualLeave = Leavetype::where('code', 'ANNUAL')[0] ?? null;
assert($annualLeave !== null, "ANNUAL leave exists");
echo "PASS: LeaveType found: {$annualLeave->name} (annual_days: {$annualLeave->annual_days})\n";

$pendingVer = Verificationstatus::where('code', 'PENDING')[0] ?? null;
assert($pendingVer !== null, "PENDING verification status exists");
echo "PASS: VerificationStatus found: {$pendingVer->name}\n";

$pendingLeave = Leavestatus::where('code', 'PENDING')[0] ?? null;
assert($pendingLeave !== null, "PENDING leave status exists");
echo "PASS: LeaveStatus found: {$pendingLeave->name}\n";

$itDept = Department::where('code', 'IT')[0] ?? null;
assert($itDept !== null, "IT department exists");
echo "PASS: Department found: {$itDept->name}\n";

echo "\n--- 2. Testing Staff Profile Association & Document Vault ---\n";
$staffProfiles = Staffprofile::all();
assert(count($staffProfiles) > 0, "Staff profiles exist");
$staff = $staffProfiles[0];
echo "Using Staff Profile: {$staff->name} (ID: {$staff->iD})\n";

// Create Staff Document
$doc = Staffdocument::create([
    'staffprofile' => $staff->iD,
    'documenttype' => $natIdType->iD,
    'title' => 'National ID Card Scan',
    'file_path' => 'uploads/staff_docs/terrence_nat_id.pdf',
    'file_size' => 245000,
    'mime_type' => 'application/pdf',
    'reg_by' => 1
]);
assert($doc !== null && $doc->iD > 0, "StaffDocument created");
echo "PASS: StaffDocument created with ID: {$doc->iD}\n";
echo "  -> Belongs to Staff: " . $doc->staffprofile()->name . "\n";
echo "  -> Document Type: " . $doc->documenttype()->name . "\n";

// Create Document Verification Event (Association Entity)
$verifiedStatus = Verificationstatus::where('code', 'VERIFIED')[0];
$audit = Documentverification::create([
    'staffdocument' => $doc->iD,
    'verificationstatus' => $verifiedStatus->iD,
    'notes' => 'Original physical ID presented and confirmed against P4 SSR record.',
    'reg_by' => 1
]);
assert($audit !== null && $audit->iD > 0, "DocumentVerification created");
echo "PASS: DocumentVerification event recorded (Audit ID: {$audit->iD})\n";
echo "  -> Status: " . $audit->verificationstatus()->name . "\n";
echo "  -> Auditor User: " . $audit->creator()->name . "\n";

echo "\n--- 3. Testing Staff Leave & Approval Event Lifecycle ---\n";
$leaveApp = Staffleave::create([
    'staffprofile' => $staff->iD,
    'leavetype' => $annualLeave->iD,
    'start_date' => '2026-10-01',
    'end_date' => '2026-10-05',
    'days_requested' => 5.0,
    'reason' => 'Annual family vacation leave',
    'reg_by' => 1
]);
assert($leaveApp !== null && $leaveApp->iD > 0, "StaffLeave created");
echo "PASS: StaffLeave submitted (ID: {$leaveApp->iD})\n";
echo "  -> Leave Type: " . $leaveApp->leavetype()->name . "\n";
echo "  -> Days: " . $leaveApp->days_requested . "\n";

// Manager Approval Event (Association Entity)
$approvedStatus = Leavestatus::where('code', 'APPROVED')[0];
$approval = Staffleaveapproval::create([
    'staffleave' => $leaveApp->iD,
    'leavestatus' => $approvedStatus->iD,
    'decision_notes' => 'Approved. Handover note confirmed with IT team.',
    'reg_by' => 1
]);
assert($approval !== null && $approval->iD > 0, "StaffLeaveApproval recorded");
echo "PASS: StaffLeaveApproval recorded (ID: {$approval->iD})\n";
echo "  -> Decision Status: " . $approval->leavestatus()->name . "\n";
echo "  -> Manager User: " . $approval->creator()->name . "\n";

echo "\n--- 4. Testing Operational Time Entry & Sign-Off ---\n";
$vettingCat = Activitycategory::where('code', 'VETTING')[0];
$timeEntry = Stafftimeentry::create([
    'staffprofile' => $staff->iD,
    'activitycategory' => $vettingCat->iD,
    'work_date' => '2026-09-08',
    'hours' => 4.5,
    'task_summary' => 'Vetted candidate intake batch: scored situational judgement & evidence.',
    'reg_by' => 1
]);
assert($timeEntry !== null && $timeEntry->iD > 0, "StaffTimeentry created");
echo "PASS: StaffTimeentry logged (ID: {$timeEntry->iD})\n";

$timeSignoff = Stafftimeapproval::create([
    'stafftimeentry' => $timeEntry->iD,
    'is_approved' => 1,
    'review_notes' => 'Batch evaluation confirmed compliant with 100-pt rubric.',
    'reg_by' => 1
]);
assert($timeSignoff !== null && $timeSignoff->iD > 0, "StaffTimeapproval recorded");
echo "PASS: StaffTimeapproval recorded (ID: {$timeSignoff->iD})\n";

echo "\n=== ALL EXTENDED MODEL INTEGRITY TESTS PASSED SUCCESSFULLY ===\n";
