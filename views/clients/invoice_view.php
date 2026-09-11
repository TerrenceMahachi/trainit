@extends('layouts.main')

<?php
global $siteConfig;
$client = $data['client'] ?? null;
$invoice = $data['invoice'] ?? null;
$items = $data['items'] ?? [];
$payments = $data['payments'] ?? [];
$plan = $data['plan'] ?? null;
$activeTab = 'invoices';
?>

<div class="container py-4 my-2">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 d-print-none">
        <a href="<?= $siteConfig->siteUrl ?>/client/invoices" class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold">
            <i class="fa fa-arrow-left me-1"></i> Back to Invoices
        </a>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                <i class="fa fa-print me-1"></i> Print / Save PDF
            </button>
        </div>
    </div>

    <!-- Invoice Statement Paper Card -->
    <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5 bg-white mb-4">
        <!-- Brand Header -->
        <div class="row align-items-start border-bottom pb-4 mb-4">
            <div class="col-sm-7">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <img src="<?= $siteConfig->assetsUrl ?>/img/logo-badge.svg?v=<?= _ASSET_VERSION ?>" alt="Tsigiro" style="height: 48px;">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">TSIGIRO SERVICES</h4>
                        <span class="badge bg-dark text-white rounded-pill px-2 py-0" style="font-size: 0.7rem;">Verified Talent &amp; Managed Services</span>
                    </div>
                </div>
                <div class="text-muted small" style="line-height: 1.4;">
                    <strong>Tsigiro Operations (Pvt) Ltd</strong><br>
                    Harare Technology Park, Borrowdale, Harare, Zimbabwe<br>
                    ZIMRA Tax BP: <strong>BP20088921</strong> &bull; VAT No: <strong>10049281</strong><br>
                    Billing Desk: <a href="mailto:billing@tsigiro.co.zw" class="text-decoration-none">billing@tsigiro.co.zw</a>
                </div>
            </div>
            <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                <h2 class="h3 fw-bold text-primary mb-1">TAX INVOICE</h2>
                <div class="fs-5 fw-bold text-dark mb-1"><?= htmlspecialchars($invoice->invoice_number) ?></div>
                <div class="mb-2">
                    <?php if ((int)$invoice->payment_status === 2): ?>
                        <span class="badge bg-success text-white rounded-pill px-3 py-1 fs-6 fw-bold">
                            <i class="fa fa-check-circle me-1"></i> PAID &amp; SETTLED
                        </span>
                    <?php elseif ((int)$invoice->payment_status === 3): ?>
                        <span class="badge bg-danger text-white rounded-pill px-3 py-1 fs-6 fw-bold">
                            <i class="fa fa-exclamation-triangle me-1"></i> OVERDUE
                        </span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fs-6 fw-bold">
                            <i class="fa fa-clock me-1"></i> PAYMENT DUE
                        </span>
                    <?php endif; ?>
                </div>
                <div class="small text-muted">
                    Issue Date: <strong><?= date('d M Y', strtotime($invoice->issue_date)) ?></strong><br>
                    Due Date: <strong><?= date('d M Y', strtotime($invoice->due_date)) ?></strong>
                </div>
            </div>
        </div>

        <!-- Bill-To and Billing Period Metadata -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6">
                <div class="p-3 rounded-4 bg-light h-100 border">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Invoiced To:</span>
                    <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($client->legal_name ?? 'Client Organization') ?></h5>
                    <div class="small text-muted" style="line-height: 1.4;">
                        <?= htmlspecialchars($client->address ?? '') ?><br>
                        <?= htmlspecialchars($client->city ?? '') ?>, <?= htmlspecialchars($client->country ?? 'Zimbabwe') ?><br>
                        Reg Number: <strong><?= htmlspecialchars($client->registration_number ?? 'N/A') ?></strong><br>
                        ZIMRA BP: <strong><?= htmlspecialchars($client->tax_number ?? 'N/A') ?></strong><br>
                        Billing Email: <?= htmlspecialchars($client->billing_email ?? 'billing@client.co.zw') ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="p-3 rounded-4 bg-light h-100 border">
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Service Retainer Coverage:</span>
                    <h5 class="fw-bold text-primary mb-1"><?= htmlspecialchars($plan->plan_name ?? 'Enterprise Service Retainer') ?></h5>
                    <div class="small text-muted" style="line-height: 1.4;">
                        Billing Cycle: <strong><?= date('d M Y', strtotime($invoice->billing_period_start)) ?> &ndash; <?= date('d M Y', strtotime($invoice->billing_period_end)) ?></strong><br>
                        Monthly Retainer Capacity: <strong><?= number_format((float)($plan->included_hours ?? 60), 1) ?> Hours</strong><br>
                        Blended Senior Associate Rate: <strong>$<?= number_format((float)($plan->associate_rate ?? 45), 2) ?>/hr</strong><br>
                        Supported Apprentice Rate: <strong>$<?= number_format((float)($plan->apprentice_rate ?? 18), 2) ?>/hr</strong><br>
                        Currency: <strong>USD (Nostro Settlement)</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Itemized Line Items Table -->
        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 50%;">Description / Service Deliverable</th>
                        <th class="text-center" style="width: 15%;">Qty / Units</th>
                        <th class="text-end" style="width: 15%;">Unit Rate (USD)</th>
                        <th class="text-end" style="width: 15%;">Line Total (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">No line items recorded for this invoice.</td>
                        </tr>
                    <?php else: ?>
                        <?php $idx = 1; foreach ($items as $it): ?>
                            <tr>
                                <td class="text-center text-muted small"><?= $idx++ ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($it->description) ?></div>
                                    <small class="text-muted">
                                        Type: <span class="badge bg-light text-secondary border"><?= htmlspecialchars($it->item_type) ?></span>
                                    </small>
                                </td>
                                <td class="text-center fw-semibold"><?= number_format((float)$it->quantity, 1) ?></td>
                                <td class="text-end text-muted">$<?= number_format((float)$it->unit_price, 2) ?></td>
                                <td class="text-end fw-bold text-dark">$<?= number_format((float)$it->total_price, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end text-muted fw-semibold">Net Subtotal:</th>
                        <th class="text-end fw-bold text-dark">$<?= number_format((float)$invoice->subtotal, 2) ?></th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-end text-muted fw-semibold">ZIMRA VAT (<?= number_format((float)$invoice->vat_rate, 1) ?>%):</th>
                        <th class="text-end fw-bold text-dark">$<?= number_format((float)$invoice->vat_amount, 2) ?></th>
                    </tr>
                    <tr class="table-light">
                        <th colspan="4" class="text-end text-dark fw-bold fs-5">Total Payable (USD):</th>
                        <th class="text-end text-primary fw-bold fs-5">$<?= number_format((float)$invoice->total_amount, 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Settlement Proof (if Paid) -->
        <?php if (!empty($payments)): ?>
            <div class="p-3 rounded-4 bg-success bg-opacity-10 border border-success mb-4">
                <div class="d-flex align-items-center gap-2 mb-1 text-success fw-bold">
                    <i class="fa fa-receipt fa-lg"></i> Electronic Payment Settlement Confirmation
                </div>
                <?php foreach ($payments as $p): ?>
                    <div class="small text-dark">
                        Settled via: <strong><?= htmlspecialchars($p->payment_method) ?></strong> &bull; 
                        Ref: <code><?= htmlspecialchars($p->transaction_reference) ?></code> &bull; 
                        Amount: <strong>$<?= number_format((float)$p->amount, 2) ?></strong> &bull; 
                        Date: <?= date('d M Y, H:i', strtotime($p->paid_at)) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Banking Settlement Information -->
        <div class="row pt-3 border-top g-3 text-muted small">
            <div class="col-sm-6">
                <h6 class="fw-bold text-dark small text-uppercase mb-1"><i class="fa fa-university me-1 text-primary"></i> Settlement Account Details</h6>
                <div><strong>Bank:</strong> Stanbic Bank Zimbabwe</div>
                <div><strong>Account Name:</strong> Tsigiro Operations (Pvt) Ltd</div>
                <div><strong>Account No (Nostro USD):</strong> 9140003882910</div>
                <div><strong>Branch:</strong> Borrowdale Branch &bull; Swift: <strong>SBICZWHX</strong></div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="fw-bold text-dark small text-uppercase mb-1"><i class="fa fa-shield-alt me-1 text-success"></i> Regulatory Notes</h6>
                <div>This is an official Tax Invoice issued in terms of the Zimbabwe Value Added Tax Act [Chapter 23:12].</div>
                <div>Tsigiro talent deliverables are verified and supervised under contract SLA standards.</div>
            </div>
        </div>
    </div>
</div>
