<?php
/**
 * Seeder script for enterprise modules:
 * 1. Service Catalogue, SLA Policies & Client Retainers
 * 2. Service Requests, Triage, Work Assignments & Messaging
 * 3. Staff Payroll Periods, Payslip Line Items & Statutory Returns
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
use App\Models\User;
use App\Models\Role;
use App\Models\Staffprofile;

echo "=== Initializing and Ensuring all 32 Enterprise Tables Exist ===\n";

$models = [
    new Prioritylevel(),
    new Clientmemberrole(),
    new Excesspolicy(),
    new Servicecategory(),
    new Serviceoffering(),
    new Servicelevelpolicy(),
    new Clientorganization(),
    new Clientmembership(),
    new Clientserviceplan(),
    new Clientserviceplantermination(),
    new Servicerequeststatus(),
    new Assignmentstatus(),
    new Messagevisibility(),
    new Servicerequest(),
    new Servicerequesttriage(),
    new Servicerequeststatusevent(),
    new Servicerequestattachment(),
    new Servicerequestclosure(),
    new Workassignment(),
    new Assignmentsupervisor(),
    new Workassignmentstatus(),
    new Requestmessage(),
    new Requestmessageattachment(),
    new Payperiodstatus(),
    new Payrollitemtype(),
    new Payrollperiod(),
    new Payrollperiodapproval(),
    new Payslip(),
    new Payslipitem(),
    new Payslipdisbursement(),
    new Statutoryreturn(),
    new Statutoryreturnfile(),
];

echo "All " . count($models) . " models initialized successfully.\n";

function seedLookupRecord($modelClass, $data) {
    $existing = $modelClass::where('code', $data['code']);
    if (empty($existing)) {
        $data['status'] = 1;
        $data['reg_date'] = date('Y-m-d H:i:s');
        $data['reg_by'] = 1;
        $res = $modelClass::create($data);
        echo "  [+] Seeded $modelClass: {$data['code']} ({$data['name']})\n";
        return $res;
    }
    return $existing[0];
}

echo "\n--- 1. Seeding Priority Levels ---\n";
seedLookupRecord(Prioritylevel::class, ['code' => 'URGENT', 'name' => 'Urgent / Critical', 'description' => 'Mission-critical outage or legal deadline', 'response_minutes' => 15, 'resolution_hours' => 4.0]);
seedLookupRecord(Prioritylevel::class, ['code' => 'HIGH', 'name' => 'High Priority', 'description' => 'Substantial operational disruption', 'response_minutes' => 30, 'resolution_hours' => 8.0]);
seedLookupRecord(Prioritylevel::class, ['code' => 'MEDIUM', 'name' => 'Medium / Standard', 'description' => 'Standard operational service task', 'response_minutes' => 120, 'resolution_hours' => 24.0]);
seedLookupRecord(Prioritylevel::class, ['code' => 'LOW', 'name' => 'Low / Routine', 'description' => 'Routine inquiry or long-term enhancement', 'response_minutes' => 240, 'resolution_hours' => 48.0]);

echo "\n--- 2. Seeding Client Member Roles ---\n";
seedLookupRecord(Clientmemberrole::class, ['code' => 'OWNER', 'name' => 'Organization Owner / Signatory', 'description' => 'Full governance over organization, billing, and all requests']);
seedLookupRecord(Clientmemberrole::class, ['code' => 'BILLING_CONTACT', 'name' => 'Billing & Financial Officer', 'description' => 'Receives invoices, makes payments, and reviews retainers']);
seedLookupRecord(Clientmemberrole::class, ['code' => 'REQUESTER', 'name' => 'Authorized Requester', 'description' => 'Can submit and collaborate on service requests']);
seedLookupRecord(Clientmemberrole::class, ['code' => 'MEMBER', 'name' => 'General Team Member', 'description' => 'Read-only visibility into organization workspace']);

echo "\n--- 3. Seeding Excess Policies ---\n";
seedLookupRecord(Excesspolicy::class, ['code' => 'ALLOW_BILLED', 'name' => 'Allow & Bill Excess', 'description' => 'Automatically dispatch work and bill excess at tiered hourly rate']);
seedLookupRecord(Excesspolicy::class, ['code' => 'REQUIRE_PREAPPROVAL', 'name' => 'Pre-Approval Required', 'description' => 'Notify client and require sign-off before exceeding included hours']);
seedLookupRecord(Excesspolicy::class, ['code' => 'CAP_AT_LIMIT', 'name' => 'Strict Cap at Limit', 'description' => 'Work pauses when included hours are depleted']);

echo "\n--- 4. Seeding Service Categories & Offerings ---\n";
$catIT = seedLookupRecord(Servicecategory::class, ['code' => 'IT', 'name' => 'Information Technology & Cloud', 'description' => 'Cloud infrastructure, application engineering and tech support', 'sort_order' => 1]);
$catHR = seedLookupRecord(Servicecategory::class, ['code' => 'HR', 'name' => 'Human Resources & Talent', 'description' => 'Talent acquisition, vetting, placement, and statutory compliance', 'sort_order' => 2]);
$catFIN = seedLookupRecord(Servicecategory::class, ['code' => 'FIN', 'name' => 'Finance, Accounting & Tax', 'description' => 'Bookkeeping, management reporting, and statutory tax returns', 'sort_order' => 3]);
$catMKT = seedLookupRecord(Servicecategory::class, ['code' => 'MKT', 'name' => 'Marketing & Communications', 'description' => 'Brand strategy, social media, and digital content', 'sort_order' => 4]);

$catITId = is_object($catIT) ? $catIT->iD : $catIT['iD'];
$catHRId = is_object($catHR) ? $catHR->iD : $catHR['iD'];
$catFINId = is_object($catFIN) ? $catFIN->iD : $catFIN['iD'];
$catMKTId = is_object($catMKT) ? $catMKT->iD : $catMKT['iD'];

$offeringIT1 = seedLookupRecord(Serviceoffering::class, ['code' => 'IT_SYS_ADMIN', 'name' => 'Cloud & Systems Engineering', 'description' => 'Managed server infrastructure, security, and cloud devops', 'servicecategory' => $catITId]);
$offeringIT2 = seedLookupRecord(Serviceoffering::class, ['code' => 'IT_WEB_APP', 'name' => 'Web & Application Development', 'description' => 'Custom application engineering, APIs, and bug resolution', 'servicecategory' => $catITId]);
$offeringHR1 = seedLookupRecord(Serviceoffering::class, ['code' => 'HR_COMPLIANCE', 'name' => 'Statutory Onboarding & HR Desk', 'description' => 'Employee registration, NSSA filings, and contract administration', 'servicecategory' => $catHRId]);
$offeringFIN1 = seedLookupRecord(Serviceoffering::class, ['code' => 'FIN_ACCOUNTING', 'name' => 'Management Accounting & Bookkeeping', 'description' => 'Monthly reconciliation, VAT/PAYE returns, and management accounts', 'servicecategory' => $catFINId]);

$offeringIT1Id = is_object($offeringIT1) ? $offeringIT1->iD : $offeringIT1['iD'];

echo "\n--- 5. Seeding Service Request & Assignment Statuses ---\n";
seedLookupRecord(Servicerequeststatus::class, ['code' => 'NEW', 'name' => 'Newly Submitted', 'description' => 'Awaiting service manager triage']);
seedLookupRecord(Servicerequeststatus::class, ['code' => 'TRIAGED', 'name' => 'Triaged & Scheduled', 'description' => 'SLA target assigned; talent matched']);
seedLookupRecord(Servicerequeststatus::class, ['code' => 'IN_PROGRESS', 'name' => 'In Progress', 'description' => 'Active delivery by assigned Associate/Apprentice']);
seedLookupRecord(Servicerequeststatus::class, ['code' => 'WAITING_CLIENT', 'name' => 'Waiting on Client', 'description' => 'Clarification or asset required from client']);
seedLookupRecord(Servicerequeststatus::class, ['code' => 'RESOLVED', 'name' => 'Resolved / Delivered', 'description' => 'Deliverables uploaded; awaiting client verification']);
seedLookupRecord(Servicerequeststatus::class, ['code' => 'CLOSED', 'name' => 'Closed & Satisfied', 'description' => 'Engagement signed off and closed']);
seedLookupRecord(Servicerequeststatus::class, ['code' => 'CANCELLED', 'name' => 'Cancelled', 'description' => 'Request withdrawn or voided']);

seedLookupRecord(Assignmentstatus::class, ['code' => 'ASSIGNED', 'name' => 'Assigned', 'description' => 'Work allocated to candidate/talent']);
seedLookupRecord(Assignmentstatus::class, ['code' => 'ACCEPTED', 'name' => 'Accepted', 'description' => 'Talent confirmed capacity and accepted assignment']);
seedLookupRecord(Assignmentstatus::class, ['code' => 'IN_PROGRESS', 'name' => 'In Progress', 'description' => 'Actively working on deliverable']);
seedLookupRecord(Assignmentstatus::class, ['code' => 'SUBMITTED', 'name' => 'Submitted for Review', 'description' => 'Work submitted for supervisor or manager review']);
seedLookupRecord(Assignmentstatus::class, ['code' => 'COMPLETED', 'name' => 'Approved & Completed', 'description' => 'Work accepted as complete']);
seedLookupRecord(Assignmentstatus::class, ['code' => 'REVOKED', 'name' => 'Revoked', 'description' => 'Assignment reassigned or cancelled']);

echo "\n--- 6. Seeding Message Visibility Types ---\n";
seedLookupRecord(Messagevisibility::class, ['code' => 'PUBLIC_CLIENT', 'name' => 'Public (Client Visible)', 'description' => 'Visible to client organization and internal staff']);
seedLookupRecord(Messagevisibility::class, ['code' => 'INTERNAL_STAFF', 'name' => 'Internal Staff Only', 'description' => 'Confidential to Trainit delivery and management staff']);

echo "\n--- 7. Seeding Payroll Statuses & Item Types ---\n";
seedLookupRecord(Payperiodstatus::class, ['code' => 'DRAFT', 'name' => 'Draft Calculations', 'description' => 'Initial calculation stage']);
seedLookupRecord(Payperiodstatus::class, ['code' => 'CALCULATED', 'name' => 'Calculated & Audited', 'description' => 'Ready for executive sign-off']);
seedLookupRecord(Payperiodstatus::class, ['code' => 'APPROVED', 'name' => 'Executive Approved', 'description' => 'Approved for disbursement']);
seedLookupRecord(Payperiodstatus::class, ['code' => 'DISBURSED', 'name' => 'Salaries Disbursed', 'description' => 'Payments dispatched to bank accounts']);
seedLookupRecord(Payperiodstatus::class, ['code' => 'CLOSED', 'name' => 'Period Closed', 'description' => 'Statutory filings and records locked']);

seedLookupRecord(Payrollitemtype::class, ['code' => 'BASIC_SALARY', 'name' => 'Basic Monthly Salary', 'description' => 'Contractual basic remuneration', 'is_deduction' => 0]);
seedLookupRecord(Payrollitemtype::class, ['code' => 'HOUSING_ALLOWANCE', 'name' => 'Housing Allowance', 'description' => 'Standard monthly accommodation allowance', 'is_deduction' => 0]);
seedLookupRecord(Payrollitemtype::class, ['code' => 'TRANSPORT_ALLOWANCE', 'name' => 'Transport Allowance', 'description' => 'Commuting & mobility stipend', 'is_deduction' => 0]);
seedLookupRecord(Payrollitemtype::class, ['code' => 'NSSA_PENSION', 'name' => 'NSSA Pension (Employee 4.5%)', 'description' => 'Statutory National Social Security Authority pension contribution', 'is_deduction' => 1]);
seedLookupRecord(Payrollitemtype::class, ['code' => 'PAYE_TAX', 'name' => 'ZIMRA PAYE Income Tax', 'description' => 'Statutory Pay As You Earn employee tax deduction', 'is_deduction' => 1]);
seedLookupRecord(Payrollitemtype::class, ['code' => 'AIDS_LEVY', 'name' => 'National AIDS Council Levy (3%)', 'description' => '3% surcharge on PAYE tax payable', 'is_deduction' => 1]);
seedLookupRecord(Payrollitemtype::class, ['code' => 'UNPAID_LEAVE', 'name' => 'Unpaid Leave Deduction', 'description' => 'Pro-rated deduction for unpaid absence', 'is_deduction' => 1]);
seedLookupRecord(Payrollitemtype::class, ['code' => 'PERFORMANCE_BONUS', 'name' => 'Performance Bonus', 'description' => 'Discretionary merit bonus', 'is_deduction' => 0]);

echo "\n--- 8. Seeding Demo Client Organization & Active Retainer Plan ---\n";
$demoClients = Clientorganization::where('registration_number', 'CO-ZW-2026-001');
if (empty($demoClients)) {
    $demoClient = Clientorganization::create([
        'legal_name' => 'Acme Global Logistics (Pvt) Ltd',
        'trading_name' => 'Acme Logistics',
        'registration_number' => 'CO-ZW-2026-001',
        'tax_number' => '10045892',
        'billing_email' => 'accounts@acmelogistics.co.zw',
        'address' => '14 Samora Machel Avenue',
        'city' => 'Harare',
        'country' => 'Zimbabwe',
        'primary_phone' => '+263 242 700100',
        'status' => 1,
        'reg_date' => date('Y-m-d H:i:s'),
        'reg_by' => 1,
    ]);
    $clientId = is_object($demoClient) ? $demoClient->iD : $demoClient['iD'];
    echo "  [+] Created Demo Client: Acme Logistics (ID: {$clientId})\n";
} else {
    $demoClient = $demoClients[0];
    $clientId = is_object($demoClient) ? $demoClient->iD : $demoClient['iD'];
    echo "  [*] Demo Client Acme Logistics already exists (ID: {$clientId})\n";
}

// Ensure an active service plan
$activePlans = Clientserviceplan::where('clientorganization', $clientId);
if (empty($activePlans)) {
    $excessPolicies = Excesspolicy::where('code', 'ALLOW_BILLED');
    $excessId = !empty($excessPolicies) ? (is_object($excessPolicies[0]) ? $excessPolicies[0]->iD : $excessPolicies[0]['iD']) : 1;

    $activePlan = Clientserviceplan::create([
        'clientorganization' => $clientId,
        'serviceoffering' => $offeringIT1Id,
        'plan_name' => 'Cloud & Infrastructure Care Retainer',
        'currency' => 'USD',
        'monthly_fee' => 1200.00,
        'included_hours' => 40.0,
        'associate_rate' => 35.00,
        'apprentice_rate' => 18.00,
        'billing_cycle_day' => 1,
        'service_manager' => 1, // Admin / Service Manager
        'billing_owner' => 1,   // Admin / Billing Owner
        'excesspolicy' => $excessId,
        'start_date' => date('Y-m-01'),
        'status' => 1,
        'reg_date' => date('Y-m-d H:i:s'),
        'reg_by' => 1,
    ]);
    $planId = is_object($activePlan) ? $activePlan->iD : $activePlan['iD'];
    echo "  [+] Created Demo Service Plan: Cloud & Infrastructure Care Retainer (ID: {$planId})\n";
} else {
    $planId = is_object($activePlans[0]) ? $activePlans[0]->iD : $activePlans[0]['iD'];
    echo "  [*] Demo Service Plan already exists (ID: {$planId})\n";
}

echo "\n=== Seeding of Enterprise Modules Complete with 100% Success ===\n";
