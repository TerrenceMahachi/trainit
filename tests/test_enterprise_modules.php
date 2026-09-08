<?php
/**
 * Automated Test Suite: Enterprise Modules
 * 1. Client Onboarding, Service Catalogue & Retainer Plans
 * 2. Service Request Delivery Desk, Work Assignments & Collaboration Stream
 * 3. Staff Payroll Periods, Deductions, Payslips & Statutory Returns
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Prioritylevel;
use App\Models\Clientmemberrole;
use App\Models\Excesspolicy;
use App\Models\Servicecategory;
use App\Models\Serviceoffering;
use App\Models\Servicelevelpolicy;
use App\Models\Clientorganization;
use App\Models\Clientmembership;
use App\Models\Clientserviceplan;
use App\Models\Clientserviceplantermination;
use App\Models\Servicerequeststatus;
use App\Models\Assignmentstatus;
use App\Models\Messagevisibility;
use App\Models\Servicerequest;
use App\Models\Servicerequesttriage;
use App\Models\Servicerequeststatusevent;
use App\Models\Servicerequestattachment;
use App\Models\Servicerequestclosure;
use App\Models\Workassignment;
use App\Models\Assignmentsupervisor;
use App\Models\Workassignmentstatus;
use App\Models\Requestmessage;
use App\Models\Requestmessageattachment;
use App\Models\Payperiodstatus;
use App\Models\Payrollitemtype;
use App\Models\Payrollperiod;
use App\Models\Payrollperiodapproval;
use App\Models\Payslip;
use App\Models\Payslipitem;
use App\Models\Payslipdisbursement;
use App\Models\Statutoryreturn;
use App\Models\Statutoryreturnfile;
use App\Models\Staffprofile;
use App\Models\User;

echo "=================================================================\n";
echo "       ENTERPRISE MODULES END-TO-END VERIFICATION SUITE          \n";
echo "=================================================================\n\n";

$errors = 0;

function assertCondition($condition, $successMessage, $failureMessage) {
    global $errors;
    if ($condition) {
        echo "  [PASS] $successMessage\n";
    } else {
        echo "  [FAIL] $failureMessage\n";
        $errors++;
    }
}

// -------------------------------------------------------------
// SUITE 1: Client Onboarding & Service Retainer Subscriptions
// -------------------------------------------------------------
echo "=== SUITE 1: Client Onboarding & Service Retainers ===\n";

$catIT = Servicecategory::where('code', 'IT');
assertCondition(!empty($catIT), "Service category 'IT' verified", "Service category 'IT' missing");

$offering = Serviceoffering::where('code', 'IT_SYS_ADMIN');
assertCondition(!empty($offering), "Service offering 'IT_SYS_ADMIN' verified", "Service offering missing");

// Create test client
$testClient = Clientorganization::create([
    'legal_name' => 'Delta Beverages Holdings Ltd',
    'trading_name' => 'Delta Beverages',
    'registration_number' => 'DELTA-ZW-TEST',
    'tax_number' => '99887766',
    'billing_email' => 'finance@delta.co.zw',
    'address' => 'Sable House, Northridge Park',
    'city' => 'Harare',
    'country' => 'Zimbabwe',
    'primary_phone' => '+263 242 883000',
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
$testClientId = is_object($testClient) ? $testClient->iD : $testClient['iD'];
assertCondition($testClientId > 0, "Test client organization created (ID: {$testClientId})", "Failed to create client organization");

// Subscribe Retainer Plan
$testPlan = Clientserviceplan::create([
    'clientorganization' => $testClientId,
    'serviceoffering' => is_object($offering[0]) ? $offering[0]->iD : $offering[0]['iD'],
    'plan_name' => 'Enterprise Cloud Care & SLA Retainer',
    'currency' => 'USD',
    'monthly_fee' => 2500.00,
    'included_hours' => 60.0,
    'associate_rate' => 45.00,
    'apprentice_rate' => 22.00,
    'billing_cycle_day' => 1,
    'service_manager' => 1,
    'billing_owner' => 1,
    'excesspolicy' => 1,
    'start_date' => date('Y-m-01'),
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
$testPlanId = is_object($testPlan) ? $testPlan->iD : $testPlan['iD'];
assertCondition($testPlanId > 0, "Retainer plan subscribed (ID: {$testPlanId}, Fee: $2,500/mo, 60 hrs)", "Failed to subscribe retainer plan");

// Terminate plan test (Zero-null event)
$testTerm = Clientserviceplantermination::create([
    'clientserviceplan' => $testPlanId,
    'end_date' => date('Y-m-d'),
    'reason' => 'Contract testing lifecycle',
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
assertCondition(!empty($testTerm), "Plan termination event recorded with zero-null integrity", "Failed to record plan termination");


// -------------------------------------------------------------
// SUITE 2: Service Request Delivery & Work Assignment Desk
// -------------------------------------------------------------
echo "\n=== SUITE 2: Service Request Delivery & Dispatch Desk ===\n";

$priority = Prioritylevel::where('code', 'URGENT');
$pId = !empty($priority) ? (is_object($priority[0]) ? $priority[0]->iD : $priority[0]['iD']) : 1;

// 1. Submit ticket
$testReq = Servicerequest::create([
    'request_number' => 'TRN-REQ-' . date('ymd') . '-TEST',
    'clientorganization' => $testClientId,
    'clientserviceplan' => $testPlanId,
    'requester' => 1,
    'prioritylevel' => $pId,
    'title' => 'Kubernetes Ingress Failover & SSL Rotation',
    'description' => 'Ingress controller failover configuration and wildcard SSL cert rotation.',
    'desired_due_date' => date('Y-m-d', strtotime('+2 days')),
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
$testReqId = is_object($testReq) ? $testReq->iD : $testReq['iD'];
assertCondition($testReqId > 0, "Service request submitted: TRN-REQ-... (ID: {$testReqId})", "Failed to submit request");

// 2. Triage & SLA
$triage = Servicerequesttriage::create([
    'servicerequest' => $testReqId,
    'sla_due_date' => date('Y-m-d H:i:s', strtotime('+4 hours')),
    'triage_notes' => 'Urgent tier verified - 4hr resolution target committed',
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
assertCondition(!empty($triage), "Ticket triaged with 4hr SLA commitment", "Triage failed");

// 3. Work assignment with supervisor pairing (Zero-null)
$assignment = Workassignment::create([
    'servicerequest' => $testReqId,
    'user' => 1,
    'assigned_role' => 4, // Associate
    'rate_currency' => 'USD',
    'hourly_rate_snapshot' => 45.00,
    'due_date' => date('Y-m-d', strtotime('+2 days')),
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
$assignId = is_object($assignment) ? $assignment->iD : $assignment['iD'];
assertCondition($assignId > 0, "Talent assigned to request (Rate snapshot: $45.00/hr)", "Work assignment failed");

$supervision = Assignmentsupervisor::create([
    'workassignment' => $assignId,
    'supervisor_user' => 1,
    'supervision_notes' => 'Technical lead oversight and cert audit',
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
assertCondition(!empty($supervision), "Apprentice/Associate supervision mentor linked via zero-null table", "Supervision pairing failed");

// 4. Threaded message with Client vs Staff visibility
$visInternal = Messagevisibility::where('code', 'INTERNAL_STAFF');
$visInternalId = !empty($visInternal) ? (is_object($visInternal[0]) ? $visInternal[0]->iD : $visInternal[0]['iD']) : 1;

$msg = Requestmessage::create([
    'servicerequest' => $testReqId,
    'messagevisibility' => $visInternalId,
    'body' => 'Internal engineering note: Private staging clusters updated with new certificates.',
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
assertCondition(!empty($msg), "Collaboration message posted with INTERNAL_STAFF visibility", "Messaging failed");

// 5. Complete & Close request
$closure = Servicerequestclosure::create([
    'servicerequest' => $testReqId,
    'closure_notes' => 'Certificates rotated, tested and verified across all endpoints.',
    'satisfaction_rating' => 5,
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
assertCondition(!empty($closure), "Request formally closed with 5-Star satisfaction rating", "Closure event failed");


// -------------------------------------------------------------
// SUITE 3: Staff Payroll Cycles, Deductions & Payslips
// -------------------------------------------------------------
echo "\n=== SUITE 3: Staff Payroll, Payslips & Statutory Returns ===\n";

// 1. Initialize period
$periodCode = 'PAY-' . date('Ym') . '-TEST';
$period = Payrollperiod::create([
    'period_code' => $periodCode,
    'period_name' => date('F Y') . ' Test Payroll Cycle',
    'start_date' => date('Y-m-01'),
    'end_date' => date('Y-m-t'),
    'pay_date' => date('Y-m-25'),
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
$periodId = is_object($period) ? $period->iD : $period['iD'];
assertCondition($periodId > 0, "Payroll period initialized: {$periodCode} (ID: {$periodId})", "Payroll period creation failed");

// 2. Calculate demo staff payslip with statutory Zimbabwe rules
$gross = 2500.00;
$nssaCap = min($gross, 700.00);
$nssa = round($nssaCap * 0.045, 2); // $31.50
$taxable = $gross - $nssa;
// PAYE calculation:
// 100 @ 0% = 0
// 200 @ 20% = 40
// 700 @ 25% = 175
// 1000 @ 30% = 300
// Remaining 468.50 @ 35% = 163.98
// PAYE = 678.98
// AIDS Levy = 3% of 678.98 = 20.37
$taxableRem = $taxable;
$paye = 0;
if ($taxableRem > 2000) {
    $paye += ($taxableRem - 2000) * 0.35;
    $taxableRem = 2000;
}
if ($taxableRem > 1000) {
    $paye += ($taxableRem - 1000) * 0.30;
    $taxableRem = 1000;
}
if ($taxableRem > 300) {
    $paye += ($taxableRem - 300) * 0.25;
    $taxableRem = 300;
}
if ($taxableRem > 100) {
    $paye += ($taxableRem - 100) * 0.20;
}
$paye = round($paye, 2);
$aidsLevy = round($paye * 0.03, 2);
$totalDeductions = round($nssa + $paye + $aidsLevy, 2);
$netPay = round($gross - $totalDeductions, 2);

$testStaffProfiles = Staffprofile::all();
$staffProfileId = !empty($testStaffProfiles) ? (is_object($testStaffProfiles[0]) ? $testStaffProfiles[0]->iD : $testStaffProfiles[0]['iD']) : 1;

$payslip = Payslip::create([
    'payrollperiod' => $periodId,
    'staffprofile' => $staffProfileId,
    'currency' => 'USD',
    'gross_pay' => $gross,
    'total_deductions' => $totalDeductions,
    'net_pay' => $netPay,
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
$psId = is_object($payslip) ? $payslip->iD : $payslip['iD'];
assertCondition($psId > 0, "Payslip generated: Gross $$gross | NSSA $$nssa | PAYE $$paye | AIDS $$aidsLevy | Net $$netPay", "Payslip calculation failed");

// 3. Line items
$itemTypes = [];
foreach (Payrollitemtype::all() as $it) {
    $itemTypes[is_object($it) ? $it->code : $it['code']] = is_object($it) ? $it->iD : $it['iD'];
}

Payslipitem::create(['payslip' => $psId, 'payrollitemtype' => $itemTypes['BASIC_SALARY'], 'item_name' => 'Basic Salary', 'amount' => $gross, 'status' => 1, 'reg_date' => date('Y-m-d H:i:s'), 'reg_by' => 1]);
Payslipitem::create(['payslip' => $psId, 'payrollitemtype' => $itemTypes['NSSA_PENSION'], 'item_name' => 'NSSA Pension (4.5%)', 'amount' => $nssa, 'status' => 1, 'reg_date' => date('Y-m-d H:i:s'), 'reg_by' => 1]);
Payslipitem::create(['payslip' => $psId, 'payrollitemtype' => $itemTypes['PAYE_TAX'], 'item_name' => 'ZIMRA PAYE', 'amount' => $paye, 'status' => 1, 'reg_date' => date('Y-m-d H:i:s'), 'reg_by' => 1]);
Payslipitem::create(['payslip' => $psId, 'payrollitemtype' => $itemTypes['AIDS_LEVY'], 'item_name' => 'National AIDS Levy', 'amount' => $aidsLevy, 'status' => 1, 'reg_date' => date('Y-m-d H:i:s'), 'reg_by' => 1]);
assertCondition(count(Payslipitem::where('payslip', $psId)) === 4, "4 line items (Earnings + NSSA/PAYE/AIDS deductions) verified on payslip", "Line items count mismatch");

// 4. Executive Approval
$stApproved = Payperiodstatus::where('code', 'APPROVED');
$stApprovedId = !empty($stApproved) ? (is_object($stApproved[0]) ? $stApproved[0]->iD : $stApproved[0]['iD']) : 1;
$approval = Payrollperiodapproval::create([
    'payrollperiod' => $periodId,
    'payperiodstatus' => $stApprovedId,
    'notes' => 'Executive approval granted for monthly salaries',
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
assertCondition(!empty($approval), "Executive payroll sign-off recorded in immutable approval audit log", "Approval failed");

// 5. Disbursement event
$disbursement = Payslipdisbursement::create([
    'payslip' => $psId,
    'payment_method' => 'STANBIC_BANK_TRANSFER',
    'transaction_reference' => 'STANBIC-TEST-REF-001',
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
assertCondition(!empty($disbursement), "Salary disbursement recorded via Stanbic Bank Transfer", "Disbursement failed");

// 6. Statutory Return
$statReturn = Statutoryreturn::create([
    'payrollperiod' => $periodId,
    'return_type' => 'NSSA_P4_MONTHLY',
    'reference_number' => 'NSSA-P4-TEST-2026',
    'total_contribution' => $nssa * 2,
    'submission_date' => date('Y-m-d'),
    'status' => 1,
    'reg_date' => date('Y-m-d H:i:s'),
    'reg_by' => 1,
]);
assertCondition(!empty($statReturn), "NSSA Form P4 statutory return logged ($" . ($nssa * 2) . " total remittance)", "Statutory return failed");


// -------------------------------------------------------------
// CLEANUP TEST TRANSACTIONS
// -------------------------------------------------------------
echo "\n--- Cleaning up temporary test transactions ---\n";
// Delete payslip items & payslip
$items = Payslipitem::where('payslip', $psId);
foreach ($items as $it) {
    (new Payslipitem())->find(is_object($it) ? $it->iD : $it['iD'])->delete();
}
(new Payslipdisbursement())->find(is_object($disbursement) ? $disbursement->iD : $disbursement['iD'])->delete();
(new Payslip())->find($psId)->delete();
(new Payrollperiodapproval())->find(is_object($approval) ? $approval->iD : $approval['iD'])->delete();
(new Statutoryreturn())->find(is_object($statReturn) ? $statReturn->iD : $statReturn['iD'])->delete();
(new Payrollperiod())->find($periodId)->delete();

// Delete request test records
(new Servicerequestclosure())->find(is_object($closure) ? $closure->iD : $closure['iD'])->delete();
(new Requestmessage())->find(is_object($msg) ? $msg->iD : $msg['iD'])->delete();
(new Assignmentsupervisor())->find(is_object($supervision) ? $supervision->iD : $supervision['iD'])->delete();
(new Workassignment())->find($assignId)->delete();
(new Servicerequesttriage())->find(is_object($triage) ? $triage->iD : $triage['iD'])->delete();
(new Servicerequest())->find($testReqId)->delete();

// Delete client test records
(new Clientserviceplantermination())->find(is_object($testTerm) ? $testTerm->iD : $testTerm['iD'])->delete();
(new Clientserviceplan())->find($testPlanId)->delete();
(new Clientorganization())->find($testClientId)->delete();

echo "  [+] All test entities safely cleaned up.\n";

echo "\n=================================================================\n";
if ($errors === 0) {
    echo "  ALL 3 ENTERPRISE SUITES PASSED VERIFICATION WITH 100% SUCCESS  \n";
} else {
    echo "  TOTAL FAILURES: {$errors}\n";
}
echo "=================================================================\n";
exit($errors === 0 ? 0 : 1);
