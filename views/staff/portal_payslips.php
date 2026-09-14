@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'] ?? null;
$profile = $data['profile'] ?? null;
$payslips = $data['payslips'] ?? [];
$activeTab = 'payslips';

// Calculate totals
$totalNet = 0;
$latestNet = 0;
if (!empty($payslips)) {
    $latestNet = (float)$payslips[0]['payslip']->net_pay;
    foreach ($payslips as $item) {
        $totalNet += (float)$item['payslip']->net_pay;
    }
}
?>

<main class="portal-dashboard">
    <?php include __DIR__ . '/portal_nav.php'; ?>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Latest Net Pay</span>
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small">Current</span>
                </div>
                <h4 class="fw-bold mb-0 text-success">$<?= number_format($latestNet, 2); ?></h4>
                <small class="text-muted" style="font-size: 0.78rem;">Last settled pay period</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Total Remuneration</span>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small">Cumulative</span>
                </div>
                <h4 class="fw-bold mb-0 text-dark">$<?= number_format($totalNet, 2); ?></h4>
                <small class="text-muted" style="font-size: 0.78rem;">Net salary disbursed to date</small>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold">Settled Periods</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-1 small">Cycles</span>
                </div>
                <h4 class="fw-bold mb-0" style="color: #2A114B;"><?= count($payslips); ?></h4>
                <small class="text-muted" style="font-size: 0.78rem;">Monthly payroll statements</small>
            </div>
        </div>
    </div>

    <!-- Section Content: My Remuneration & Payslips -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-money-check-dollar text-warning me-2"></i> My Remuneration & Payslips</h6>
                <small class="text-muted">Itemized monthly compensation, statutory deductions, and disbursement records</small>
            </div>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                <i class="fa fa-receipt text-primary me-1"></i> <?= count($payslips); ?> Available Statements
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($payslips)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="fa fa-file-invoice-dollar fa-3x mb-3 text-light-purple" style="color: #D6C2EB;"></i>
                    <h6 class="fw-bold text-dark">No Payslips Generated Yet</h6>
                    <p class="small text-muted mb-3">Official salary statements will appear here once each monthly payroll cycle is calculated and disbursed by Finance.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                        <thead class="bg-light text-muted">
                            <tr>
                                <th class="ps-4">Pay Period & Cycle</th>
                                <th>Pay Date</th>
                                <th class="text-end">Gross Pay</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Net Take-Home</th>
                                <th>Disbursement Status</th>
                                <th class="text-center pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payslips as $item): 
                                $ps = $item['payslip'];
                                $period = $item['period'];
                                $disb = $item['disbursement'];
                                $curr = $ps->currency ?: 'USD';
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars($period ? $period->period_name : ('Pay Period #' . $ps->payrollperiod)); ?>
                                        </div>
                                        <small class="text-muted font-monospace">
                                            <?= htmlspecialchars($period ? $period->period_code : ('PS-' . str_pad($ps->iD, 5, '0', STR_PAD_LEFT))); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="small fw-semibold text-dark">
                                            <?= $period && $period->pay_date ? date('d M Y', strtotime($period->pay_date)) : date('d M Y', strtotime($ps->reg_date)); ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        $<?= number_format((float)$ps->gross_pay, 2); ?>
                                    </td>
                                    <td class="text-end text-danger fw-semibold">
                                        -$<?= number_format((float)$ps->total_deductions, 2); ?>
                                    </td>
                                    <td class="text-end fw-bold text-success" style="font-size: 0.95rem;">
                                        $<?= number_format((float)$ps->net_pay, 2); ?>
                                    </td>
                                    <td>
                                        <?php if ($disb): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="fa fa-check-circle me-1"></i> Paid / Disbursed
                                            </span>
                                            <div class="text-muted" style="font-size: 0.72rem; margin-top: 2px;">
                                                Ref: <?= htmlspecialchars($disb->transaction_reference); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                                <i class="fa fa-hourglass-half me-1"></i> Processing
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="<?= $siteConfig->siteUrl; ?>/admin/payroll/payslip/<?= $ps->iD; ?>" 
                                           class="btn btn-sm text-white rounded-pill px-3 shadow-sm"
                                           style="background-color: #2A114B; font-size: 0.8rem;"
                                           title="View full printable payslip with statutory breakdown">
                                            <i class="fa fa-print me-1" style="color: #FFCC00;"></i> View Payslip
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

            </div><!-- /.col-lg-8 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section><!-- /.portal-dashboard-body -->
</main>
