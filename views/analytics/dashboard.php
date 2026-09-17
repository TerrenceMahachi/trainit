@extends('layouts.main')

<?php
global $siteConfig;
$user             = $data['user'] ?? null;
$totalApps        = $data['totalApps'] ?? 0;
$apprenticeApps   = $data['apprenticeApps'] ?? 0;
$associateApps    = $data['associateApps'] ?? 0;
$funnelData       = $data['funnelData'] ?? [];
$scoreStats       = $data['scoreStats'] ?? [];
$scoreTiers       = $data['scoreTiers'] ?? [];
$totalRequests    = $data['totalRequests'] ?? 0;
$closedRequests   = $data['closedRequests'] ?? 0;
$slaStats         = $data['slaStats'] ?? [];
$priorityRows     = $data['priorityRows'] ?? [];
$payrollSummary   = $data['payrollSummary'] ?? [];
$recentPeriods    = $data['recentPeriods'] ?? [];
$activeClients    = $data['activeClientsCount'] ?? 0;
$activePlans      = $data['activePlansCount'] ?? 0;
$retainerRevenue  = $data['retainerRevenue'] ?? [];
$plansByOffering  = $data['plansByOffering'] ?? [];

$avgScore = !empty($scoreStats['avg_total']) ? number_format((float)$scoreStats['avg_total'], 1) : 'N/A';
$mrr = !empty($retainerRevenue['monthly_mrr']) ? '$' . number_format((float)$retainerRevenue['monthly_mrr'], 0) : '$0';
$avgRating = !empty($slaStats['avg_rating']) ? number_format((float)$slaStats['avg_rating'], 1) : '5.0';
?>

<main class="portal-dashboard">
    <!-- Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="portal-kicker text-warning mb-1" style="font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-chart-pie me-1"></i> Management Information Systems
                </p>
                <h1 class="h2 fw-bold text-white mb-2">Executive Intelligence &amp; Analytics</h1>
                <p class="mb-0 text-white-50" style="max-width: 650px;">
                    Cross-entity visibility across talent vetting conversion, service delivery SLAs, payroll trends, and retainer commercials.
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/analytics/export" class="btn btn-outline-light rounded-pill px-3 shadow-sm">
                    <i class="fa fa-download me-1"></i> Export Metrics (CSV)
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="btn btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm">
                    <i class="fa fa-gauge me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </section>

    <!-- Content Body -->
    <section class="py-4">
        <div class="container">

            <!-- Executive KPI Row -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 p-3 rounded-4 bg-white" style="border-left: 5px solid #2A114B !important;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small text-uppercase fw-semibold">Talent Pipeline Pool</span>
                            <i class="fa fa-users text-primary"></i>
                        </div>
                        <h2 class="fw-bold mb-0 text-dark"><?= $totalApps; ?></h2>
                        <div class="small text-muted mt-1">
                            <?= $apprenticeApps; ?> Apprentices &bull; <?= $associateApps; ?> Associates
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 p-3 rounded-4 bg-white" style="border-left: 5px solid #10B981 !important;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small text-uppercase fw-semibold">Avg Vetting Score</span>
                            <i class="fa fa-award text-success"></i>
                        </div>
                        <h2 class="fw-bold mb-0 text-success"><?= $avgScore; ?><span class="fs-6 text-muted">/100</span></h2>
                        <div class="small text-muted mt-1">
                            <?= $scoreStats['total_assessed'] ?? 0; ?> candidates formally assessed
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 p-3 rounded-4 bg-white" style="border-left: 5px solid #FFCC00 !important;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small text-uppercase fw-semibold">Contracted MRR</span>
                            <i class="fa fa-handshake text-warning"></i>
                        </div>
                        <h2 class="fw-bold mb-0 text-dark"><?= $mrr; ?></h2>
                        <div class="small text-muted mt-1">
                            <?= $activePlans; ?> active service plans across <?= $activeClients; ?> clients
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 p-3 rounded-4 bg-white" style="border-left: 5px solid #3B82F6 !important;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small text-uppercase fw-semibold">Client Satisfaction</span>
                            <i class="fa fa-star text-primary"></i>
                        </div>
                        <h2 class="fw-bold mb-0 text-primary"><?= $avgRating; ?> <span class="fs-6 text-warning"><i class="fa fa-star"></i></span></h2>
                        <div class="small text-muted mt-1">
                            <?= $closedRequests; ?> closed service assignments
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 1: Vetting Funnel & Scoring -->
            <div class="row g-4 mb-4">
                <!-- Funnel Stage Drop-off -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-filter text-primary me-2"></i> Talent Vetting Funnel Conversion
                            </h5>
                            <span class="badge bg-light text-dark border">End-to-End Pipeline</span>
                        </div>
                        <div class="card-body p-4">
                            <div class="vstack gap-3">
                                <?php foreach ($funnelData as $step): 
                                    $pct = $step['pct'];
                                    $barColor = match($step['stage']) {
                                        'Submitted' => 'bg-secondary',
                                        'Screened' => 'bg-info',
                                        'Interviewed / Assessed' => 'bg-warning',
                                        'Admitted to Roster' => 'bg-primary',
                                        default => 'bg-success',
                                    };
                                ?>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-semibold text-dark small"><?= $step['stage']; ?></span>
                                            <span class="fw-bold small"><?= $step['count']; ?> candidates (<?= $pct; ?>%)</span>
                                        </div>
                                        <div class="progress" style="height: 12px; border-radius: 6px; background-color: #f1f5f9;">
                                            <div class="progress-bar <?= $barColor; ?>" role="progressbar" style="width: <?= max(4, $pct); ?>%;" aria-valuenow="<?= $pct; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <hr class="my-4 text-muted">

                            <!-- Vetting Score Dimensions -->
                            <h6 class="fw-bold text-dark mb-3 small text-uppercase">100-Point Vetting Component Scores (Averages)</h6>
                            <div class="row g-2 text-center small">
                                <div class="col">
                                    <div class="p-2 rounded-3 bg-light">
                                        <div class="text-muted" style="font-size: 0.75rem;">Technical Fit (30)</div>
                                        <div class="fw-bold fs-6 text-dark mt-1"><?= !empty($scoreStats['avg_tech']) ? number_format($scoreStats['avg_tech'], 1) : '0'; ?></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="p-2 rounded-3 bg-light">
                                        <div class="text-muted" style="font-size: 0.75rem;">Evidence (20)</div>
                                        <div class="fw-bold fs-6 text-dark mt-1"><?= !empty($scoreStats['avg_evidence']) ? number_format($scoreStats['avg_evidence'], 1) : '0'; ?></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="p-2 rounded-3 bg-light">
                                        <div class="text-muted" style="font-size: 0.75rem;">Judgement (20)</div>
                                        <div class="fw-bold fs-6 text-dark mt-1"><?= !empty($scoreStats['avg_judgement']) ? number_format($scoreStats['avg_judgement'], 1) : '0'; ?></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="p-2 rounded-3 bg-light">
                                        <div class="text-muted" style="font-size: 0.75rem;">Availability (15)</div>
                                        <div class="fw-bold fs-6 text-dark mt-1"><?= !empty($scoreStats['avg_availability']) ? number_format($scoreStats['avg_availability'], 1) : '0'; ?></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="p-2 rounded-3 bg-light">
                                        <div class="text-muted" style="font-size: 0.75rem;">Motivation (15)</div>
                                        <div class="fw-bold fs-6 text-dark mt-1"><?= !empty($scoreStats['avg_motivation']) ? number_format($scoreStats['avg_motivation'], 1) : '0'; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Talent Quality Tiers -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-medal text-warning me-2"></i> Quality Tier Distribution
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-3">Breakdown of assessed candidates based on standard Tsigiro competency gates.</p>

                            <div class="vstack gap-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background: #F0FDF4; border-left: 4px solid #10B981;">
                                    <div>
                                        <strong class="text-success">Tier 1: High Calibre (85-100)</strong>
                                        <div class="small text-muted">Immediate client deployment ready</div>
                                    </div>
                                    <span class="badge bg-success fs-6"><?= $scoreTiers['tier1']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background: #EFF6FF; border-left: 4px solid #3B82F6;">
                                    <div>
                                        <strong class="text-primary">Tier 2: Competent Specialist (70-84)</strong>
                                        <div class="small text-muted">Standard roster admission</div>
                                    </div>
                                    <span class="badge bg-primary fs-6"><?= $scoreTiers['tier2']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background: #FFFBEB; border-left: 4px solid #F59E0B;">
                                    <div>
                                        <strong class="text-warning text-dark">Tier 3: Emerging Talent (50-69)</strong>
                                        <div class="small text-muted">Apprentice track / Supervised delivery</div>
                                    </div>
                                    <span class="badge bg-warning text-dark fs-6"><?= $scoreTiers['tier3']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background: #FEF2F2; border-left: 4px solid #EF4444;">
                                    <div>
                                        <strong class="text-danger">Below Benchmark (&lt;50)</strong>
                                        <div class="small text-muted">Ineligible / Further development needed</div>
                                    </div>
                                    <span class="badge bg-danger fs-6"><?= $scoreTiers['below']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Service Delivery Desk & SLAs -->
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-stopwatch text-danger me-2"></i> Delivery Desk &amp; SLA Performance
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 text-center mb-4">
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded-3">
                                        <div class="small text-muted">Total Requests</div>
                                        <div class="h4 fw-bold text-dark mt-1 mb-0"><?= $totalRequests; ?></div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded-3">
                                        <div class="small text-muted">Avg Turnaround</div>
                                        <div class="h4 fw-bold text-primary mt-1 mb-0"><?= !empty($slaStats['avg_turnaround']) ? number_format($slaStats['avg_turnaround'], 1) : '0'; ?> hrs</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 bg-light rounded-3">
                                        <div class="small text-muted">5-Star Reviews</div>
                                        <div class="h4 fw-bold text-success mt-1 mb-0"><?= $slaStats['stars_5'] ?? 0; ?></div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark small text-uppercase mb-2">Request Priority Tiers</h6>
                            <div class="list-group list-group-flush small">
                                <?php foreach ($priorityRows as $pr): 
                                    $pBadge = match($pr['code']) {
                                        'CRITICAL' => 'bg-danger',
                                        'HIGH' => 'bg-warning text-dark',
                                        'MEDIUM' => 'bg-info text-dark',
                                        default => 'bg-secondary',
                                    };
                                ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><span class="badge <?= $pBadge; ?> me-2"><?= $pr['code']; ?></span> <?= htmlspecialchars($pr['name']); ?></span>
                                        <span class="fw-bold"><?= (int)$pr['cnt']; ?> ticket(s)</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commercial Retainers -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fa fa-briefcase text-success me-2"></i> Retainer Subscriptions &amp; Services
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="text-muted small">Total Included Retainer Capacity</span>
                                    <h4 class="fw-bold mb-0 text-dark"><?= number_format((float)($retainerRevenue['total_included_hours'] ?? 0)); ?> hrs/mo</h4>
                                </div>
                                <span class="badge bg-success fs-6 px-3 py-2"><?= $activePlans; ?> Active Retainers</span>
                            </div>

                            <h6 class="fw-bold text-dark small text-uppercase mb-2">Plans by Service Offering</h6>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Offering</th>
                                            <th class="text-center">Active Plans</th>
                                            <th class="text-end">Contracted Monthly</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        <?php foreach ($plansByOffering as $po): ?>
                                            <tr>
                                                <td class="fw-semibold"><?= htmlspecialchars($po['name']); ?></td>
                                                <td class="text-center"><?= (int)$po['plan_count']; ?></td>
                                                <td class="text-end fw-bold text-success">$<?= number_format((float)$po['total_fees'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Staff Payroll & Statutory Liabilities -->
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-money-check-dollar text-primary me-2"></i> Staff Compensation &amp; Statutory Withholding Trends
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4 text-center">
                        <div class="col-sm-6 col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <div class="small text-muted">Total Gross Disbursed</div>
                                <div class="h4 fw-bold text-dark mt-1 mb-0">$<?= number_format((float)($payrollSummary['total_gross_all_time'] ?? 0), 2); ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <div class="small text-muted">Net Salaries Paid</div>
                                <div class="h4 fw-bold text-success mt-1 mb-0">$<?= number_format((float)($payrollSummary['total_net_all_time'] ?? 0), 2); ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <div class="small text-muted">ZIMRA PAYE Remitted</div>
                                <div class="h4 fw-bold text-danger mt-1 mb-0">$<?= number_format((float)($payrollSummary['total_paye_all_time'] ?? 0), 2); ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-3 bg-light rounded-3">
                                <div class="small text-muted">NSSA Statutory Remitted</div>
                                <div class="h4 fw-bold text-info mt-1 mb-0">$<?= number_format((float)($payrollSummary['total_nssa_all_time'] ?? 0), 2); ?></div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark small text-uppercase mb-2">Recent Processed Payroll Periods</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>Period Code</th>
                                    <th>Cycle Dates</th>
                                    <th>Currency</th>
                                    <th class="text-center">Payslips</th>
                                    <th class="text-end">Gross Total</th>
                                </tr>
                            </thead>
                            <tbody class="small">
                                <?php if (empty($recentPeriods)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-3">No payroll periods processed yet.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($recentPeriods as $period): ?>
                                        <tr>
                                            <td class="fw-bold text-dark"><?= htmlspecialchars($period['period_code']); ?></td>
                                            <td><?= htmlspecialchars($period['start_date']); ?> to <?= htmlspecialchars($period['end_date']); ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($period['currency']); ?></span></td>
                                            <td class="text-center"><?= (int)$period['slip_count']; ?></td>
                                            <td class="text-end fw-bold">$<?= number_format((float)$period['period_gross'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>
