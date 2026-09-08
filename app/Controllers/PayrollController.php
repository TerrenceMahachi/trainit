<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Payrollperiod;
use App\Models\Payrollperiodapproval;
use App\Models\Payperiodstatus;
use App\Models\Payrollitemtype;
use App\Models\Payslip;
use App\Models\Payslipitem;
use App\Models\Payslipdisbursement;
use App\Models\Statutoryreturn;
use App\Models\Statutoryreturnfile;
use App\Models\Staffprofile;
use App\Models\User;

class PayrollController extends Controller
{
    /**
     * Admin: Payroll Cycles & Summary Console
     */
    public function index()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $periods = Payrollperiod::all();
        $enrichedPeriods = [];
        $totalDisbursedOverall = 0;

        foreach ($periods as $p) {
            $pId = is_object($p) ? $p->iD : $p['iD'];
            $payslips = Payslip::where('payrollperiod', $pId);
            $approvals = Payrollperiodapproval::where('payrollperiod', $pId);
            $returns = Statutoryreturn::where('payrollperiod', $pId);

            $periodGross = 0;
            $periodDeductions = 0;
            $periodNet = 0;
            $disbursedCount = 0;

            foreach ($payslips as $ps) {
                $periodGross += (float)(is_object($ps) ? $ps->gross_pay : $ps['gross_pay']);
                $periodDeductions += (float)(is_object($ps) ? $ps->total_deductions : $ps['total_deductions']);
                $periodNet += (float)(is_object($ps) ? $ps->net_pay : $ps['net_pay']);

                $disb = Payslipdisbursement::where('payslip', is_object($ps) ? $ps->iD : $ps['iD']);
                if (!empty($disb)) {
                    $disbursedCount++;
                }
            }

            if ($disbursedCount > 0) {
                $totalDisbursedOverall += $periodNet;
            }

            $latestApproval = !empty($approvals) ? end($approvals) : null;
            $approvalStatus = null;
            if ($latestApproval) {
                $stId = is_object($latestApproval) ? $latestApproval->payperiodstatus : $latestApproval['payperiodstatus'];
                $approvalStatus = Payperiodstatus::find($stId);
            }

            $enrichedPeriods[] = [
                'period' => $p,
                'payslips_count' => count($payslips),
                'total_gross' => $periodGross,
                'total_deductions' => $periodDeductions,
                'total_net' => $periodNet,
                'is_disbursed' => ($disbursedCount === count($payslips) && count($payslips) > 0),
                'disbursed_count' => $disbursedCount,
                'status_name' => $approvalStatus ? (is_object($approvalStatus) ? $approvalStatus->name : $approvalStatus['name']) : 'Draft Run',
                'returns_count' => count($returns),
            ];
        }

        $activeStaffCount = count(Staffprofile::where('employment_status', 'active'));

        $this->render('payroll.index', [
            'periods' => $enrichedPeriods,
            'totalDisbursedOverall' => $totalDisbursedOverall,
            'activeStaffCount' => $activeStaffCount,
        ]);
    }

    /**
     * Admin: Detailed Payroll Period Ledger & Employee Payslips
     */
    public function viewPeriod($id)
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $period = Payrollperiod::find($id);
        if (!$period) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll?error=not_found');
            exit;
        }

        $payslips = Payslip::where('payrollperiod', $id);
        $enrichedPayslips = [];
        $totalGross = 0;
        $totalDeductions = 0;
        $totalNet = 0;
        $totalNssa = 0;
        $totalPaye = 0;
        $totalAidsLevy = 0;

        foreach ($payslips as $ps) {
            $psId = is_object($ps) ? $ps->iD : $ps['iD'];
            $staffId = is_object($ps) ? $ps->staffprofile : $ps['staffprofile'];
            $staff = Staffprofile::find($staffId);
            $disb = Payslipdisbursement::where('payslip', $psId);
            $items = Payslipitem::where('payslip', $psId);

            $g = (float)(is_object($ps) ? $ps->gross_pay : $ps['gross_pay']);
            $d = (float)(is_object($ps) ? $ps->total_deductions : $ps['total_deductions']);
            $n = (float)(is_object($ps) ? $ps->net_pay : $ps['net_pay']);

            $totalGross += $g;
            $totalDeductions += $d;
            $totalNet += $n;

            // Extract item totals
            foreach ($items as $it) {
                $typeId = is_object($it) ? $it->payrollitemtype : $it['payrollitemtype'];
                $itemType = Payrollitemtype::find($typeId);
                $code = $itemType ? (is_object($itemType) ? $itemType->code : $itemType['code']) : '';
                $amt = (float)(is_object($it) ? $it->amount : $it['amount']);

                if ($code === 'NSSA_PENSION') $totalNssa += $amt;
                if ($code === 'PAYE_TAX') $totalPaye += $amt;
                if ($code === 'AIDS_LEVY') $totalAidsLevy += $amt;
            }

            $enrichedPayslips[] = [
                'payslip' => $ps,
                'staff' => $staff,
                'disbursement' => !empty($disb) ? $disb[0] : null,
                'items_count' => count($items),
            ];
        }

        $approvals = Payrollperiodapproval::where('payrollperiod', $id);
        $returns = Statutoryreturn::where('payrollperiod', $id);

        $this->render('payroll.view_period', [
            'period' => $period,
            'payslips' => $enrichedPayslips,
            'totalGross' => $totalGross,
            'totalDeductions' => $totalDeductions,
            'totalNet' => $totalNet,
            'totalNssa' => $totalNssa,
            'totalPaye' => $totalPaye,
            'totalAidsLevy' => $totalAidsLevy,
            'approvals' => $approvals,
            'returns' => $returns,
        ]);
    }

    /**
     * Admin: Create New Payroll Period
     */
    public function createPeriodAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $periodCode = trim($_POST['period_code'] ?? '');
        $periodName = trim($_POST['period_name'] ?? '');
        $startDate = $_POST['start_date'] ?? date('Y-m-01');
        $endDate = $_POST['end_date'] ?? date('Y-m-t');
        $payDate = $_POST['pay_date'] ?? date('Y-m-25');

        if (!$periodCode || !$periodName) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll?error=missing_fields');
            exit;
        }

        $period = Payrollperiod::create([
            'period_code' => $periodCode,
            'period_name' => $periodName,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'pay_date' => $payDate,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => Auth::id() ?? 1,
        ]);

        $pId = is_object($period) ? $period->iD : $period['iD'];

        // Initial status event: DRAFT
        $st = Payperiodstatus::where('code', 'DRAFT');
        if (!empty($st)) {
            $stId = is_object($st[0]) ? $st[0]->iD : $st[0]['iD'];
            Payrollperiodapproval::create([
                'payrollperiod' => $pId,
                'payperiodstatus' => $stId,
                'notes' => 'Period initialized in Draft state',
                'status' => 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'reg_by' => Auth::id() ?? 1,
            ]);
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll/view/' . $pId . '?msg=period_created');
        exit;
    }

    /**
     * Admin: Compute Payroll Calculations & Line Items for All Staff
     */
    public function calculateAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $periodId = (int)($_POST['period_id'] ?? 0);
        if (!$periodId) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll?error=missing_id');
            exit;
        }

        $period = Payrollperiod::find($periodId);
        if (!$period) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll?error=not_found');
            exit;
        }

        // Get all active staff profiles
        $staffProfiles = Staffprofile::where('employment_status', 'active');
        if (empty($staffProfiles)) {
            $staffProfiles = Staffprofile::all();
        }

        // Reference items
        $itemTypes = [];
        foreach (Payrollitemtype::all() as $it) {
            $c = is_object($it) ? $it->code : $it['code'];
            $itemTypes[$c] = is_object($it) ? $it->iD : $it['iD'];
        }

        foreach ($staffProfiles as $staff) {
            $staffId = is_object($staff) ? $staff->iD : $staff['iD'];
            $baseSalary = (float)(is_object($staff) ? $staff->salary : $staff['salary']);
            if ($baseSalary <= 0) {
                $baseSalary = 1500.00; // fallback standard demo salary if not entered
            }

            // Check if payslip already exists for this staff in this period
            $existing = Payslip::where('payrollperiod', $periodId);
            $alreadyExists = false;
            foreach ($existing as $e) {
                if ((int)(is_object($e) ? $e->staffprofile : $e['staffprofile']) === $staffId) {
                    $alreadyExists = true;
                    break;
                }
            }
            if ($alreadyExists) {
                continue;
            }

            // 1. Calculate Gross
            $gross = $baseSalary;

            // 2. Zimbabwe NSSA Pension: 4.5% up to max ceiling ($700) = max $31.50
            $insurableCap = min($gross, 700.00);
            $nssaPension = round($insurableCap * 0.045, 2);

            // 3. Zimbabwe PAYE (Standard Graduated Brackets on Taxable Income = Gross - NSSA)
            $taxableIncome = max(0, $gross - $nssaPension);
            $paye = 0;
            if ($taxableIncome > 3000) {
                $paye += ($taxableIncome - 3000) * 0.40;
                $taxableIncome = 3000;
            }
            if ($taxableIncome > 2000) {
                $paye += ($taxableIncome - 2000) * 0.35;
                $taxableIncome = 2000;
            }
            if ($taxableIncome > 1000) {
                $paye += ($taxableIncome - 1000) * 0.30;
                $taxableIncome = 1000;
            }
            if ($taxableIncome > 300) {
                $paye += ($taxableIncome - 300) * 0.25;
                $taxableIncome = 300;
            }
            if ($taxableIncome > 100) {
                $paye += ($taxableIncome - 100) * 0.20;
            }
            $paye = round($paye, 2);

            // 4. AIDS Levy: 3% of PAYE
            $aidsLevy = round($paye * 0.03, 2);

            // 5. Net Remuneration
            $totalDeductions = round($nssaPension + $paye + $aidsLevy, 2);
            $netPay = round($gross - $totalDeductions, 2);

            // Create Payslip record
            $payslip = Payslip::create([
                'payrollperiod' => $periodId,
                'staffprofile' => $staffId,
                'currency' => 'USD',
                'gross_pay' => $gross,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
                'status' => 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'reg_by' => Auth::id() ?? 1,
            ]);

            $psId = is_object($payslip) ? $payslip->iD : $payslip['iD'];

            // Insert line items
            if (isset($itemTypes['BASIC_SALARY'])) {
                Payslipitem::create([
                    'payslip' => $psId,
                    'payrollitemtype' => $itemTypes['BASIC_SALARY'],
                    'item_name' => 'Basic Monthly Salary',
                    'amount' => $gross,
                    'status' => 1,
                    'reg_date' => date('Y-m-d H:i:s'),
                    'reg_by' => Auth::id() ?? 1,
                ]);
            }
            if (isset($itemTypes['NSSA_PENSION'])) {
                Payslipitem::create([
                    'payslip' => $psId,
                    'payrollitemtype' => $itemTypes['NSSA_PENSION'],
                    'item_name' => 'NSSA Pension Scheme (Employee 4.5%)',
                    'amount' => $nssaPension,
                    'status' => 1,
                    'reg_date' => date('Y-m-d H:i:s'),
                    'reg_by' => Auth::id() ?? 1,
                ]);
            }
            if (isset($itemTypes['PAYE_TAX'])) {
                Payslipitem::create([
                    'payslip' => $psId,
                    'payrollitemtype' => $itemTypes['PAYE_TAX'],
                    'item_name' => 'ZIMRA PAYE Tax Deduction',
                    'amount' => $paye,
                    'status' => 1,
                    'reg_date' => date('Y-m-d H:i:s'),
                    'reg_by' => Auth::id() ?? 1,
                ]);
            }
            if (isset($itemTypes['AIDS_LEVY'])) {
                Payslipitem::create([
                    'payslip' => $psId,
                    'payrollitemtype' => $itemTypes['AIDS_LEVY'],
                    'item_name' => 'National AIDS Council Levy (3%)',
                    'amount' => $aidsLevy,
                    'status' => 1,
                    'reg_date' => date('Y-m-d H:i:s'),
                    'reg_by' => Auth::id() ?? 1,
                ]);
            }
        }

        // Transition status to CALCULATED
        $st = Payperiodstatus::where('code', 'CALCULATED');
        if (!empty($st)) {
            $stId = is_object($st[0]) ? $st[0]->iD : $st[0]['iD'];
            Payrollperiodapproval::create([
                'payrollperiod' => $periodId,
                'payperiodstatus' => $stId,
                'notes' => 'Payroll calculations executed for active personnel',
                'status' => 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'reg_by' => Auth::id() ?? 1,
            ]);
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll/view/' . $periodId . '?msg=calculated');
        exit;
    }

    /**
     * Admin: Approve Payroll Run
     */
    public function approveAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $periodId = (int)($_POST['period_id'] ?? 0);
        $notes = trim($_POST['approval_notes'] ?? 'Approved by Executive Leadership');

        if (!$periodId) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll?error=missing_id');
            exit;
        }

        $st = Payperiodstatus::where('code', 'APPROVED');
        if (!empty($st)) {
            $stId = is_object($st[0]) ? $st[0]->iD : $st[0]['iD'];
            Payrollperiodapproval::create([
                'payrollperiod' => $periodId,
                'payperiodstatus' => $stId,
                'notes' => $notes,
                'status' => 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'reg_by' => Auth::id() ?? 1,
            ]);
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll/view/' . $periodId . '?msg=approved');
        exit;
    }

    /**
     * Admin: Disburse Salaries (Record Bank Transfer Event)
     */
    public function disburseAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $periodId = (int)($_POST['period_id'] ?? 0);
        $paymentMethod = trim($_POST['payment_method'] ?? 'STANBIC_BANK_TRANSFER');
        $batchReference = trim($_POST['batch_reference'] ?? ('PAY-' . date('Ymd') . '-BATCH'));

        if (!$periodId) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll?error=missing_id');
            exit;
        }

        $payslips = Payslip::where('payrollperiod', $periodId);
        foreach ($payslips as $ps) {
            $psId = is_object($ps) ? $ps->iD : $ps['iD'];
            $existing = Payslipdisbursement::where('payslip', $psId);
            if (empty($existing)) {
                Payslipdisbursement::create([
                    'payslip' => $psId,
                    'payment_method' => $paymentMethod,
                    'transaction_reference' => $batchReference,
                    'status' => 1,
                    'reg_date' => date('Y-m-d H:i:s'),
                    'reg_by' => Auth::id() ?? 1,
                ]);
            }
        }

        // Transition status to DISBURSED
        $st = Payperiodstatus::where('code', 'DISBURSED');
        if (!empty($st)) {
            $stId = is_object($st[0]) ? $st[0]->iD : $st[0]['iD'];
            Payrollperiodapproval::create([
                'payrollperiod' => $periodId,
                'payperiodstatus' => $stId,
                'notes' => 'Salaries disbursed via ' . $paymentMethod . ' (Ref: ' . $batchReference . ')',
                'status' => 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'reg_by' => Auth::id() ?? 1,
            ]);
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll/view/' . $periodId . '?msg=disbursed');
        exit;
    }

    /**
     * Printable Staff Payslip Document
     */
    public function viewPayslip($id)
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $payslip = Payslip::find($id);
        if (!$payslip) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll?error=not_found');
            exit;
        }

        $period = Payrollperiod::find(is_object($payslip) ? $payslip->payrollperiod : $payslip['payrollperiod']);
        $staff = Staffprofile::find(is_object($payslip) ? $payslip->staffprofile : $payslip['staffprofile']);
        $items = Payslipitem::where('payslip', $id);
        $disbursement = Payslipdisbursement::where('payslip', $id);

        $earnings = [];
        $deductions = [];

        foreach ($items as $it) {
            $typeId = is_object($it) ? $it->payrollitemtype : $it['payrollitemtype'];
            $itemType = Payrollitemtype::find($typeId);
            $isDeduction = $itemType ? (int)(is_object($itemType) ? $itemType->is_deduction : $itemType['is_deduction']) : 0;

            if ($isDeduction) {
                $deductions[] = [
                    'name' => is_object($it) ? $it->item_name : $it['item_name'],
                    'amount' => (float)(is_object($it) ? $it->amount : $it['amount']),
                ];
            } else {
                $earnings[] = [
                    'name' => is_object($it) ? $it->item_name : $it['item_name'],
                    'amount' => (float)(is_object($it) ? $it->amount : $it['amount']),
                ];
            }
        }

        $this->render('payroll.payslip', [
            'payslip' => $payslip,
            'period' => $period,
            'staff' => $staff,
            'earnings' => $earnings,
            'deductions' => $deductions,
            'disbursement' => !empty($disbursement) ? $disbursement[0] : null,
        ]);
    }

    /**
     * File Statutory Return (NSSA Form P4 / ZIMRA P2)
     */
    public function statutoryReturnAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $periodId = (int)($_POST['period_id'] ?? 0);
        $returnType = trim($_POST['return_type'] ?? 'NSSA_P4_MONTHLY');
        $refNumber = trim($_POST['reference_number'] ?? '');
        $totalContribution = (float)($_POST['total_contribution'] ?? 0);
        $submissionDate = $_POST['submission_date'] ?? date('Y-m-d');

        if (!$periodId || !$refNumber) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll/view/' . $periodId . '?error=missing_return_data');
            exit;
        }

        $return = Statutoryreturn::create([
            'payrollperiod' => $periodId,
            'return_type' => $returnType,
            'reference_number' => $refNumber,
            'total_contribution' => $totalContribution,
            'submission_date' => $submissionDate,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => Auth::id() ?? 1,
        ]);

        $retId = is_object($return) ? $return->iD : $return['iD'];

        // File upload if present
        if (!empty($_FILES['return_file']['name']) && $_FILES['return_file']['error'] === UPLOAD_ERR_OK) {
            $destDir = _BASE_PATH . '/uploads/statutory/' . $periodId;
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $fileName = basename($_FILES['return_file']['name']);
            $targetPath = $destDir . '/' . time() . '_' . $fileName;
            if (move_uploaded_file($_FILES['return_file']['tmp_name'], $targetPath)) {
                Statutoryreturnfile::create([
                    'statutoryreturn' => $retId,
                    'file_path' => str_replace(_BASE_PATH . '/', '', $targetPath),
                    'file_name' => $fileName,
                    'file_size' => (int)$_FILES['return_file']['size'],
                    'mime_type' => $_FILES['return_file']['type'] ?? 'application/pdf',
                    'status' => 1,
                    'reg_date' => date('Y-m-d H:i:s'),
                    'reg_by' => Auth::id() ?? 1,
                ]);
            }
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/payroll/view/' . $periodId . '?msg=return_filed');
        exit;
    }
}
