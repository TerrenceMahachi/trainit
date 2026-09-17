<?php

namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Database;
use App\Models\Rosterapplication;
use App\Models\Rosterassessment;
use App\Models\Servicerequest;
use App\Models\Servicerequestclosure;
use App\Models\Clientserviceplan;
use App\Models\Clientorganization;
use App\Models\Payrollperiod;
use App\Models\Payslip;
use App\Models\Serviceoffering;
use App\Models\Servicefunction;
use PDO;

class AnalyticsController extends Controller
{
    public function index()
    {
        if (!Auth::check() || !in_array((int)Auth::role(), [1, 6, 7, 8], true)) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $pdo = Database::sharedPdo();

        // =========================================================================
        // 1. VETTING FUNNEL & TALENT INTELLIGENCE
        // =========================================================================
        $totalApps = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication")->fetchColumn();
        $apprenticeApps = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationtrack = 1")->fetchColumn();
        $associateApps = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationtrack = 2")->fetchColumn();

        $statusCounts = [];
        $stRows = $pdo->query("
            SELECT s.code, s.name, COUNT(a.iD) as cnt
            FROM applicationstatus s
            LEFT JOIN rosterapplication a ON a.applicationstatus = s.iD
            GROUP BY s.iD, s.code, s.name
            ORDER BY s.sort_order ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($stRows as $row) {
            $statusCounts[$row['code']] = (int)$row['cnt'];
        }

        $submittedCount = $statusCounts['submitted'] ?? 0;
        $screenedCount = $statusCounts['screened'] ?? 0;
        $interviewedCount = $statusCounts['interviewed'] ?? 0;
        $onRosterCount = $statusCounts['on_roster'] ?? 0;
        $deployedCount = $statusCounts['deployed'] ?? 0;
        $rejectedCount = $statusCounts['rejected'] ?? 0;

        // Funnel Step Conversions
        $funnelData = [
            ['stage' => 'Submitted', 'count' => $totalApps, 'pct' => 100],
            ['stage' => 'Screened', 'count' => $screenedCount + $interviewedCount + $onRosterCount + $deployedCount, 'pct' => $totalApps > 0 ? round((($screenedCount + $interviewedCount + $onRosterCount + $deployedCount) / $totalApps) * 100, 1) : 0],
            ['stage' => 'Interviewed / Assessed', 'count' => $interviewedCount + $onRosterCount + $deployedCount, 'pct' => $totalApps > 0 ? round((($interviewedCount + $onRosterCount + $deployedCount) / $totalApps) * 100, 1) : 0],
            ['stage' => 'Admitted to Roster', 'count' => $onRosterCount + $deployedCount, 'pct' => $totalApps > 0 ? round((($onRosterCount + $deployedCount) / $totalApps) * 100, 1) : 0],
            ['stage' => 'Deployed on Brief', 'count' => $deployedCount, 'pct' => $totalApps > 0 ? round(($deployedCount / $totalApps) * 100, 1) : 0],
        ];

        // 100-Point Scoring Benchmarks
        $scoreStats = $pdo->query("
            SELECT 
                COUNT(*) as total_assessed,
                AVG(total_score) as avg_total,
                AVG(technical_fit_score) as avg_tech,
                AVG(evidence_score) as avg_evidence,
                AVG(judgement_score) as avg_judgement,
                AVG(availability_score) as avg_availability,
                AVG(motivation_score) as avg_motivation,
                MAX(total_score) as max_score,
                MIN(total_score) as min_score
            FROM rosterassessment
            WHERE total_score > 0
        ")->fetch(PDO::FETCH_ASSOC);

        // Score Distribution Brackets
        $tier1Count = (int)$pdo->query("SELECT COUNT(*) FROM rosterassessment WHERE total_score >= 85")->fetchColumn();
        $tier2Count = (int)$pdo->query("SELECT COUNT(*) FROM rosterassessment WHERE total_score >= 70 AND total_score < 85")->fetchColumn();
        $tier3Count = (int)$pdo->query("SELECT COUNT(*) FROM rosterassessment WHERE total_score >= 50 AND total_score < 70")->fetchColumn();
        $belowCount = (int)$pdo->query("SELECT COUNT(*) FROM rosterassessment WHERE total_score < 50 AND total_score > 0")->fetchColumn();

        // =========================================================================
        // 2. SLA DELIVERY DESK & CLIENT SATISFACTION
        // =========================================================================
        $totalRequests = (int)$pdo->query("SELECT COUNT(*) FROM servicerequest")->fetchColumn();
        $closedRequests = (int)$pdo->query("SELECT COUNT(*) FROM servicerequestclosure")->fetchColumn();

        $slaStats = $pdo->query("
            SELECT 
                AVG(turnaround_hours) as avg_turnaround,
                AVG(client_rating_stars) as avg_rating,
                SUM(CASE WHEN client_rating_stars = 5 THEN 1 ELSE 0 END) as stars_5,
                SUM(CASE WHEN client_rating_stars = 4 THEN 1 ELSE 0 END) as stars_4,
                SUM(CASE WHEN client_rating_stars <= 3 THEN 1 ELSE 0 END) as stars_below
            FROM servicerequestclosure
        ")->fetch(PDO::FETCH_ASSOC);

        // Active vs Resolved Requests
        $openRequestsCount = (int)$pdo->query("SELECT COUNT(*) FROM servicerequest WHERE servicerequeststatus IN (1, 2, 3, 4)")->fetchColumn();
        $resolvedCount = (int)$pdo->query("SELECT COUNT(*) FROM servicerequest WHERE servicerequeststatus IN (5, 6)")->fetchColumn();

        // Priority Breakdown
        $priorityRows = $pdo->query("
            SELECT p.code, p.name, COUNT(r.iD) as cnt
            FROM prioritylevel p
            LEFT JOIN servicerequest r ON r.prioritylevel = p.iD
            GROUP BY p.iD, p.code, p.name
            ORDER BY p.iD ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // =========================================================================
        // 3. STAFF PAYROLL & STATUTORY OBLIGATIONS
        // =========================================================================
        $payrollSummary = $pdo->query("
            SELECT 
                COUNT(DISTINCT p.iD) as total_periods,
                SUM(ps.gross_salary) as total_gross_all_time,
                SUM(ps.net_salary) as total_net_all_time,
                SUM(ps.nssa_employee) as total_nssa_all_time,
                SUM(ps.paye) as total_paye_all_time,
                SUM(ps.aids_levy) as total_aids_all_time,
                COUNT(ps.iD) as total_payslips
            FROM payslip ps
            LEFT JOIN payrollperiod p ON p.iD = ps.payrollperiod
        ")->fetch(PDO::FETCH_ASSOC);

        $recentPeriods = $pdo->query("
            SELECT p.*, COUNT(ps.iD) as slip_count, SUM(ps.gross_salary) as period_gross
            FROM payrollperiod p
            LEFT JOIN payslip ps ON ps.payrollperiod = p.iD
            GROUP BY p.iD
            ORDER BY p.start_date DESC
            LIMIT 6
        ")->fetchAll(PDO::FETCH_ASSOC);

        // =========================================================================
        // 4. COMMERCIAL RETAINERS & CONTRACT RUN-RATE
        // =========================================================================
        $activeClientsCount = (int)$pdo->query("SELECT COUNT(*) FROM clientorganization WHERE status = 1")->fetchColumn();
        $activePlansCount = (int)$pdo->query("SELECT COUNT(*) FROM clientserviceplan WHERE status = 1")->fetchColumn();

        $retainerRevenue = $pdo->query("
            SELECT 
                SUM(retainer_fee) as monthly_mrr,
                SUM(included_hours) as total_included_hours,
                AVG(retainer_fee) as avg_retainer_fee
            FROM clientserviceplan
            WHERE status = 1
        ")->fetch(PDO::FETCH_ASSOC);

        $plansByOffering = $pdo->query("
            SELECT o.code, o.name, COUNT(p.iD) as plan_count, SUM(p.retainer_fee) as total_fees
            FROM serviceoffering o
            LEFT JOIN clientserviceplan p ON p.serviceoffering = o.iD AND p.status = 1
            GROUP BY o.iD, o.code, o.name
            ORDER BY plan_count DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $data = [
            'title' => 'Executive Analytics & Operational Intelligence',
            'user' => Auth::user(),
            // Vetting
            'totalApps' => $totalApps,
            'apprenticeApps' => $apprenticeApps,
            'associateApps' => $associateApps,
            'funnelData' => $funnelData,
            'scoreStats' => $scoreStats,
            'scoreTiers' => [
                'tier1' => $tier1Count,
                'tier2' => $tier2Count,
                'tier3' => $tier3Count,
                'below' => $belowCount,
            ],
            // SLA
            'totalRequests' => $totalRequests,
            'closedRequests' => $closedRequests,
            'openRequestsCount' => $openRequestsCount,
            'resolvedCount' => $resolvedCount,
            'slaStats' => $slaStats,
            'priorityRows' => $priorityRows,
            // Payroll
            'payrollSummary' => $payrollSummary,
            'recentPeriods' => $recentPeriods,
            // Retainers
            'activeClientsCount' => $activeClientsCount,
            'activePlansCount' => $activePlansCount,
            'retainerRevenue' => $retainerRevenue,
            'plansByOffering' => $plansByOffering,
        ];

        $this->render('analytics.dashboard', $data);
    }

    public function exportCsv()
    {
        if (!Auth::check() || !in_array((int)Auth::role(), [1, 6, 7], true)) {
            http_response_code(403);
            exit;
        }

        $pdo = Database::sharedPdo();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="tsigiro_analytics_summary_' . date('Ymd') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Report', 'Tsigiro Executive Intelligence Export']);
        fputcsv($out, ['Generated', date('Y-m-d H:i:s')]);
        fputcsv($out, []);

        // 1. Talent Funnel
        fputcsv($out, ['--- TALENT FUNNEL & SCORING ---']);
        fputcsv($out, ['Metric', 'Value']);
        fputcsv($out, ['Total Applications', $pdo->query("SELECT COUNT(*) FROM rosterapplication")->fetchColumn()]);
        fputcsv($out, ['Apprentice Applicants', $pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationtrack = 1")->fetchColumn()]);
        fputcsv($out, ['Associate Specialists', $pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationtrack = 2")->fetchColumn()]);
        fputcsv($out, ['Admitted to Roster', $pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationstatus = 5")->fetchColumn()]);
        fputcsv($out, ['Average 100-Point Score', number_format((float)$pdo->query("SELECT AVG(total_score) FROM rosterassessment WHERE total_score > 0")->fetchColumn(), 2)]);
        fputcsv($out, []);

        // 2. SLA Delivery
        fputcsv($out, ['--- SERVICE DELIVERY & SLA ---']);
        fputcsv($out, ['Metric', 'Value']);
        fputcsv($out, ['Total Requests Logged', $pdo->query("SELECT COUNT(*) FROM servicerequest")->fetchColumn()]);
        fputcsv($out, ['Closed Requests', $pdo->query("SELECT COUNT(*) FROM servicerequestclosure")->fetchColumn()]);
        fputcsv($out, ['Average Turnaround (Hours)', number_format((float)$pdo->query("SELECT AVG(turnaround_hours) FROM servicerequestclosure")->fetchColumn(), 1)]);
        fputcsv($out, ['Average Client Rating (Stars)', number_format((float)$pdo->query("SELECT AVG(client_rating_stars) FROM servicerequestclosure")->fetchColumn(), 1)]);
        fputcsv($out, []);

        // 3. Retainers & Payroll
        fputcsv($out, ['--- COMMERCIAL RETAINERS & PAYROLL ---']);
        fputcsv($out, ['Metric', 'Value']);
        fputcsv($out, ['Active Client Organizations', $pdo->query("SELECT COUNT(*) FROM clientorganization WHERE status = 1")->fetchColumn()]);
        fputcsv($out, ['Active Retainer Plans', $pdo->query("SELECT COUNT(*) FROM clientserviceplan WHERE status = 1")->fetchColumn()]);
        fputcsv($out, ['Monthly Contracted MRR ($)', number_format((float)$pdo->query("SELECT SUM(retainer_fee) FROM clientserviceplan WHERE status = 1")->fetchColumn(), 2)]);
        fputcsv($out, ['Total Payroll Gross ($)', number_format((float)$pdo->query("SELECT SUM(gross_salary) FROM payslip")->fetchColumn(), 2)]);

        fclose($out);
        exit;
    }
}
