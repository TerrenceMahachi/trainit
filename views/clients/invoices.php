@extends('layouts.main')

<?php
global $siteConfig;
$client = $data['client'] ?? null;
$invoices = $data['invoices'] ?? [];
$stats = $data['stats'] ?? [
    'total_billed' => 0,
    'total_paid' => 0,
    'total_pending' => 0,
    'invoices_count' => 0
];
$activeTab = $data['activeTab'] ?? 'invoices';
?>

<div class="container py-4 my-2">
    <?php include _BASE_PATH . '/views/clients/nav.php'; ?>

    <!-- Summary KPI Header -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="border-left: 5px solid #2A114B !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Total Invoiced (12 Mo)</span>
                    <i class="fa fa-receipt text-primary fa-lg"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0">$<?= number_format($stats['total_billed'], 2) ?></h3>
                <small class="text-muted"><?= $stats['invoices_count'] ?> Billing Statements</small>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="border-left: 5px solid #28a745 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Total Settled</span>
                    <i class="fa fa-check-double text-success fa-lg"></i>
                </div>
                <h3 class="fw-bold text-success mb-0">$<?= number_format($stats['total_paid'], 2) ?></h3>
                <small class="text-muted">Settled via Electronic Bank Transfer</small>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="border-left: 5px solid #ffc107 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Pending / Current</span>
                    <i class="fa fa-clock text-warning fa-lg"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0">$<?= number_format($stats['total_pending'], 2) ?></h3>
                <small class="text-muted">Due for settlement</small>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="border-left: 5px solid #6f42c1 !important;">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold text-uppercase">Billing Currency</span>
                    <i class="fa fa-coins text-purple fa-lg"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0">USD (NOSTRO)</h3>
                <small class="text-muted">ZIMRA VAT (15%) Compliant</small>
            </div>
        </div>
    </div>

    <!-- Invoices Ledger Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="fa fa-file-invoice-dollar text-primary me-2"></i> Client Billing Statements &amp; Retainer Invoices
                </h5>
                <p class="text-muted small mb-0">Review past monthly statements, itemized associate &amp; apprentice deliverables, and payment receipts.</p>
            </div>
            <div>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small">
                    <i class="fa fa-building me-1"></i> Tax BP: <?= htmlspecialchars($client->tax_number ?? 'BP20098177') ?>
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th class="ps-4">Invoice #</th>
                        <th>Billing Period</th>
                        <th>Retainer Plan</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">VAT (15%)</th>
                        <th class="text-end">Total Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fa fa-folder-open fa-3x mb-3 d-block text-secondary opacity-50"></i>
                                <h6 class="fw-bold text-dark">No Invoices Found</h6>
                                <p class="small text-muted mb-0">Invoices will appear here as your monthly service retainers and work requests are billed.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): 
                            $statusBadge = 'bg-warning text-dark';
                            $statusText = 'Pending';
                            if ((int)$inv['payment_status'] === 2) {
                                $statusBadge = 'bg-success text-white';
                                $statusText = 'Paid';
                            } elseif ((int)$inv['payment_status'] === 3) {
                                $statusBadge = 'bg-danger text-white';
                                $statusText = 'Overdue';
                            }
                        ?>
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <i class="fa fa-file-pdf text-danger me-1"></i>
                                    <?= htmlspecialchars($inv['invoice_number']) ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">
                                        <?= date('M Y', strtotime($inv['billing_period_start'])) ?>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <?= date('d M', strtotime($inv['billing_period_start'])) ?> &ndash; <?= date('d M Y', strtotime($inv['billing_period_end'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= htmlspecialchars($inv['plan_name'] ?? 'Enterprise Retainer') ?>
                                    </span>
                                </td>
                                <td class="small text-muted"><?= date('d M Y', strtotime($inv['issue_date'])) ?></td>
                                <td class="small text-muted"><?= date('d M Y', strtotime($inv['due_date'])) ?></td>
                                <td class="text-end fw-semibold small">$<?= number_format((float)$inv['subtotal'], 2) ?></td>
                                <td class="text-end text-muted small">$<?= number_format((float)$inv['vat_amount'], 2) ?></td>
                                <td class="text-end fw-bold text-dark">$<?= number_format((float)$inv['total_amount'], 2) ?></td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-1 small fw-semibold <?= $statusBadge ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= $siteConfig->siteUrl ?>/client/invoices/view/<?= $inv['iD'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                        <i class="fa fa-eye me-1"></i> View Statement
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Settlement Information Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-light">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h6 class="fw-bold text-dark mb-1"><i class="fa fa-university me-2 text-primary"></i> Banking &amp; Settlement Instructions</h6>
                <p class="text-muted small mb-0">
                    All invoices are payable in USD (Nostro) or ZWG at the prevailing interbank rate. Payments can be settled via Stanbic Bank Zimbabwe or CABS Nostro transfer. Quotations and tax invoices include valid ZIMRA BP registration.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?= $siteConfig->siteUrl ?>/contact?subject=Billing+Inquiry" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold">
                    <i class="fa fa-envelope me-1"></i> Contact Finance Desk
                </a>
            </div>
        </div>
    </div>
</div>
