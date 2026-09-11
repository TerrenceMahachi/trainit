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
    <!-- Success / Error Alert Notifications -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'payment_recorded'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="fa fa-check-circle fs-5"></i>
            <div><strong>Payment Proof Submitted!</strong> Your settlement reference has been recorded and this invoice is now marked as settled.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'invoice_settled'): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="fa fa-check-double fs-5"></i>
            <div><strong>Billing Desk Certified:</strong> Bank settlement verified and invoice reconciled.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'invoice_reopened'): ?>
        <div class="alert alert-warning alert-dismissible fade show rounded-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="fa fa-exclamation-triangle fs-5"></i>
            <div><strong>Invoice Reopened:</strong> Status reverted to Payment Due for review or audit adjustment.</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 d-print-none">
        <a href="<?= $siteConfig->siteUrl ?>/client/invoices" class="btn btn-outline-dark rounded-pill px-3 py-2 fw-semibold">
            <i class="fa fa-arrow-left me-1"></i> Back to Invoices
        </a>
        <div class="d-flex gap-2">
            <a href="<?= $siteConfig->siteUrl ?>/client/invoices/pdf/<?= $invoice->iD ?>" target="_blank" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold shadow-sm">
                <i class="fa fa-file-pdf me-1 text-danger"></i> Download Official PDF
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                <i class="fa fa-print me-1"></i> Print Statement
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
                    <div class="small text-dark mb-1">
                        Settled via: <strong><?= htmlspecialchars($p->payment_method) ?></strong> &bull; 
                        Ref: <code><?= htmlspecialchars($p->transaction_reference) ?></code> &bull; 
                        Amount: <strong>$<?= number_format((float)$p->amount, 2) ?></strong> &bull; 
                        Date: <?= date('d M Y, H:i', strtotime($p->paid_at)) ?>
                        <?php if (!empty($p->notes)): ?>
                            <span class="text-muted ps-1">(<?= htmlspecialchars($p->notes) ?>)</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Client Settle / Submit Proof of Payment (POP) Card (If not settled) -->
        <?php if ((int)$invoice->payment_status !== 2): ?>
            <div class="card border-warning border-2 rounded-4 p-4 mb-4 bg-warning bg-opacity-10 d-print-none">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><i class="fa fa-credit-card me-2 text-warning"></i> Settle Invoice / Submit Proof of Payment (POP)</h5>
                        <p class="text-muted small mb-0">After initiating payment to our Stanbic Bank Nostro account, submit your bank transaction reference and optional receipt proof below to certify settlement.</p>
                    </div>
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold fs-6">
                        Amount Due: $<?= number_format((float)$invoice->total_amount, 2) ?> USD
                    </span>
                </div>

                <form method="POST" action="<?= $siteConfig->siteUrl ?>/client/invoices/submit-payment" enctype="multipart/form-data" class="row g-3">
                    <input type="hidden" name="invoice_id" value="<?= $invoice->iD ?>">

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark">Payment Method</label>
                        <select name="payment_method" class="form-select rounded-3" required>
                            <option value="Stanbic Bank Nostro Transfer" selected>Stanbic Bank Nostro Transfer</option>
                            <option value="CABS Nostro Transfer">CABS Nostro Transfer</option>
                            <option value="CBZ Bank Transfer">CBZ Bank Transfer</option>
                            <option value="EcoBank Zimbabwe Transfer">EcoBank Zimbabwe Transfer</option>
                            <option value="EcoCash FCA Business">EcoCash FCA Business</option>
                            <option value="Direct RTGS / ZIPIT">Direct RTGS / ZIPIT</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark">Bank Transaction / Trace Reference</label>
                        <input type="text" name="transaction_reference" class="form-control rounded-3" placeholder="e.g. TXN-STB-<?= date('Ymd') ?>-9102" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark">Settlement Date</label>
                        <input type="date" name="paid_at" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark">Amount Settled (USD)</label>
                        <input type="number" step="0.01" name="amount" class="form-control rounded-3 fw-bold" value="<?= number_format((float)$invoice->total_amount, 2, '.', '') ?>" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark">Attach Bank POP Receipt (PDF/Image)</label>
                        <input type="file" name="receipt_file" class="form-control rounded-3" accept=".pdf,.png,.jpg,.jpeg">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-dark">Notes / Payer Reference</label>
                        <input type="text" name="notes" class="form-control rounded-3" placeholder="Optional notes for Tsigiro Finance Desk">
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm">
                            <i class="fa fa-check me-1"></i> Submit Payment Proof &amp; Certify
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Billing Desk Financial Controls (Staff / Finance Role Only) -->
        <?php if (\App\Helpers\Auth::isStaff()): ?>
            <div class="card border-primary border-2 rounded-4 p-4 mb-4 bg-light d-print-none">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-primary text-uppercase mb-1" style="letter-spacing: 0.5px;">
                            <i class="fa fa-shield-alt me-1"></i> Billing Desk Financial Reconciliation Controls (Internal Staff)
                        </h6>
                        <div class="text-muted small">Manage official billing ledger status, certify direct bank settlement, or reopen for audit review.</div>
                    </div>
                    <div>
                        <?php if ((int)$invoice->payment_status === 2): ?>
                            <span class="badge bg-success rounded-pill px-3 py-1">Audited Status: Settled</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1">Audited Status: Pending Settlement</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <?php if ((int)$invoice->payment_status !== 2): ?>
                        <form method="POST" action="<?= $siteConfig->siteUrl ?>/admin/invoices/reconcile-payment" class="d-flex flex-wrap gap-2 align-items-center w-100">
                            <input type="hidden" name="invoice_id" value="<?= $invoice->iD ?>">
                            <input type="hidden" name="action" value="settle">
                            <input type="hidden" name="amount" value="<?= $invoice->total_amount ?>">
                            <input type="text" name="transaction_reference" class="form-control form-control-sm rounded-pill" style="max-width: 260px;" placeholder="Bank Ref (e.g. TXN-REC-<?= date('Ymd') ?>-001)" value="TXN-STB-<?= date('Ymd') ?>-<?= rand(1000, 9999) ?>">
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold">
                                <i class="fa fa-stamp me-1"></i> Certify &amp; Mark as Settled
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?= $siteConfig->siteUrl ?>/admin/invoices/reconcile-payment" onsubmit="return confirm('Are you sure you want to reopen this invoice to Payment Due?');">
                            <input type="hidden" name="invoice_id" value="<?= $invoice->iD ?>">
                            <input type="hidden" name="action" value="reopen">
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold">
                                <i class="fa fa-undo me-1"></i> Reopen Invoice / Set to Payment Due
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
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
