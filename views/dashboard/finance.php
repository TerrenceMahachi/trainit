@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'];

// Gather Financial & Billing Metrics
$totalInvoices = App\Models\Clientinvoice::countAll();
$allInvoices = App\Models\Clientinvoice::all();
$totalBilled = 0;
$pendingInvoicesCount = 0;
$settledInvoicesCount = 0;
foreach ($allInvoices as $inv) {
    $totalBilled += (float)($inv->total_amount ?? 0);
    if ((int)($inv->payment_status ?? 0) === 2) {
        $settledInvoicesCount++;
    } else {
        $pendingInvoicesCount++;
    }
}

$allPayments = App\Models\Clientinvoicepayment::all();
$totalCollected = 0;
foreach ($allPayments as $pmt) {
    $totalCollected += (float)($pmt->amount ?? 0);
}

// Recent Invoices
$recentInvoices = App\Models\Clientinvoice::findByQuery(
    "SELECT * FROM clientinvoice ORDER BY iD DESC LIMIT 8"
);
?>

<main class="portal-dashboard">
    <!-- Hero Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #132e1b 0%, #1e4a2c 100%);">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker" style="color: #4ade80;"><i class="fa fa-coins me-1"></i> Financial Controller &amp; Billing Desk</p>
                <h1>Finance &amp; Billing Dashboard</h1>
                <p class="portal-dashboard-intro">Welcome back, <?= htmlspecialchars($user->name ?? 'Officer'); ?>. Monitor client tax billing, verify proof of payment submissions, process staff payroll cycles, and file statutory returns.</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <a href="<?= $siteConfig->siteUrl; ?>/client/invoices" class="btn btn-success fw-bold shadow-sm px-3 py-2" style="background: #22c55e; border: none; color: #ffffff;">
                    <i class="fa fa-file-invoice-dollar me-1"></i> Client Invoices
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/payroll" class="btn btn-dark border border-secondary text-white fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-money-check-dollar me-1 text-warning"></i> Staff Payroll
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/export-p4" class="btn btn-outline-light fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-file-excel me-1 text-success"></i> NSSA Form P4
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/clients" class="btn btn-outline-light fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-building me-1"></i> Client Retainers
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/staff/portal" class="btn btn-warning text-dark fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-user-circle me-1"></i> Staff Portal
                </a>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                    <i class="fa fa-check-circle me-2"></i> <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 10px;">
                    <i class="fa fa-exclamation-circle me-2"></i> <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Metrics Counters Grid -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #10b981 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Settled Collections</div>
                                    <h3 class="fw-bold mb-0 text-success">$<?= number_format($totalCollected, 2); ?></h3>
                                    <small class="text-muted"><?= $settledInvoicesCount; ?> settled electronic payments</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-success">
                                    <i class="fa fa-circle-check fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #f59e0b !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Payment Due</div>
                                    <h3 class="fw-bold mb-0 text-warning"><?= $pendingInvoicesCount; ?></h3>
                                    <small class="text-muted">Awaiting client POP settlement</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-warning">
                                    <i class="fa fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #3b82f6 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Total Invoiced</div>
                                    <h3 class="fw-bold mb-0 text-primary">$<?= number_format($totalBilled, 2); ?></h3>
                                    <small class="text-muted"><?= $totalInvoices; ?> tax invoices issued</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-primary">
                                    <i class="fa fa-file-invoice-dollar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #8b5cf6 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Payroll &amp; Tax</div>
                                    <h3 class="fw-bold mb-0 text-purple">15% VAT</h3>
                                    <small class="text-muted">ZIMRA &amp; NSSA P4 Compliant</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-purple">
                                    <i class="fa fa-landmark fa-2x" style="color: #8b5cf6;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Finance Hub Operational Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #dcfce7; color: #15803d;">
                            <i class="fa fa-file-invoice-dollar fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tax Invoices &amp; Billing</h6>
                        <p class="text-muted small mb-3">Manage corporate billing, download PDF statements, and reconcile POPs.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/client/invoices" class="btn btn-sm btn-outline-success fw-bold mt-auto">
                            Invoices Ledger <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #fef3c7; color: #d97706;">
                            <i class="fa fa-money-check-dollar fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Staff Payroll Cycles</h6>
                        <p class="text-muted small mb-3">Execute monthly payroll runs, compute PAYE/NSSA, and generate payslips.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/payroll" class="btn btn-sm btn-outline-warning text-dark fw-bold mt-auto">
                            Payroll Desk <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #dbeafe; color: #2563eb;">
                            <i class="fa fa-file-excel fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">NSSA Form P4 Export</h6>
                        <p class="text-muted small mb-3">Export national social security statutory employee onboarding schedules.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/export-p4" class="btn btn-sm btn-outline-primary fw-bold mt-auto">
                            Download P4 <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #ede9fe; color: #6d28d9;">
                            <i class="fa fa-building fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Client Retainers</h6>
                        <p class="text-muted small mb-3">Review contracted monthly retainer plans, billing caps, and excess rates.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/clients" class="btn btn-sm btn-outline-purple fw-bold mt-auto" style="color: #6d28d9; border-color: #6d28d9;">
                            Retainer Plans <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Priority Invoices Ledger -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fa fa-file-invoice-dollar me-2 text-success"></i> Recent Corporate Tax Invoices</h5>
                        <small class="text-muted">Itemized client statements, VAT calculations, and electronic settlement records.</small>
                    </div>
                    <a href="<?= $siteConfig->siteUrl; ?>/client/invoices" class="btn btn-sm btn-outline-dark fw-bold">
                        View All Invoices <i class="fa fa-chevron-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Invoice #</th>
                                <th>Client Organization</th>
                                <th>Billing Period</th>
                                <th>Amount (USD)</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentInvoices)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                        No invoices recorded.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentInvoices as $inv): 
                                    $clientOrg = App\Models\Clientorganization::find($inv->clientorganization);
                                    $isPaid = (int)($inv->payment_status ?? 0) === 2;
                                ?>
                                    <tr>
                                        <td class="ps-4 fw-bold font-monospace text-dark">
                                            <?= htmlspecialchars($inv->invoice_number); ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($clientOrg->legal_name ?? 'Client Organization'); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($clientOrg->city ?? 'Harare'); ?></small>
                                        </td>
                                        <td>
                                            <div class="small text-dark"><?= date('M Y', strtotime($inv->billing_period_start)); ?></div>
                                            <small class="text-muted">Due <?= date('d M Y', strtotime($inv->due_date)); ?></small>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">$<?= number_format((float)($inv->total_amount ?? 0), 2); ?></span>
                                            <small class="d-block text-muted" style="font-size: 11px;">VAT incl.</small>
                                        </td>
                                        <td>
                                            <?php if ($isPaid): ?>
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="fa fa-check-circle me-1"></i> Settled</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1"><i class="fa fa-clock me-1"></i> Payment Due</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= $siteConfig->siteUrl; ?>/client/invoices/pdf/<?= $inv->iD; ?>" target="_blank" class="btn btn-sm btn-outline-danger fw-bold px-2 py-1 me-1" title="Download Official Tax Invoice PDF">
                                                <i class="fa fa-file-pdf"></i> PDF
                                            </a>
                                            <a href="<?= $siteConfig->siteUrl; ?>/client/invoices/view/<?= $inv->iD; ?>" class="btn btn-sm btn-outline-primary fw-bold px-2 py-1">
                                                View &amp; Settle
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Finance Workspace Banner -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-left: 6px solid #10b981 !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; background: #132e1b; color: #4ade80; flex-shrink: 0;">
                            <i class="fa fa-wallet fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Billing &amp; Finance Workspace</h5>
                            <p class="text-muted mb-0 small">Logged in as <strong><?= htmlspecialchars($user->name ?? ''); ?></strong> &bull; Access your leave calendar, staff documents, and profile details.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= $siteConfig->siteUrl; ?>/staff/portal" class="btn btn-success fw-bold px-3 py-2 shadow-sm" style="background: #15803d; border: none;">
                            <i class="fa fa-user-circle me-1"></i> Open Staff Portal
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/client/invoices" class="btn btn-outline-dark fw-bold px-3 py-2">
                            <i class="fa fa-file-invoice me-1"></i> Full Billing Ledger
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>
