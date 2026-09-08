<?php
$title = "Payslip — " . htmlspecialchars($staff ? (is_object($staff) ? ($staff->firstname . ' ' . $staff->surname) : ($staff['firstname'] . ' ' . $staff['surname'])) : 'Staff');
$navUser = \App\Helpers\Auth::user();
?>
<?php include _BASE_PATH . '/views/partials/header.php'; ?>

<style>
@media print {
    .no-print, header, footer, nav, .trainit-navbar {
        display: none !important;
    }
    body {
        background-color: #fff !important;
    }
    .payslip-container {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
}
</style>

<div class="container py-4">
    <!-- Screen Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fa fa-arrow-left me-1"></i> Back to Ledger
        </a>
        <button type="button" class="btn text-white rounded-pill px-4 shadow-sm" style="background-color: #2A114B;" onclick="window.print()">
            <i class="fa fa-print me-1"></i> Print / Save PDF
        </button>
    </div>

    <!-- Printable Payslip Document -->
    <div class="card border shadow-sm rounded-4 p-5 payslip-container bg-white mx-auto" style="max-width: 850px;">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="rounded-3 p-2 text-white fw-bold d-flex align-items-center justify-content-center" style="background-color: #2A114B; width: 44px; height: 44px;">
                        TT
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0" style="color: #1C0D30;">TRAINIT TECHNOLOGIES (PVT) LTD</h4>
                        <div class="text-muted small">Trading as <strong>Tsigiro</strong> &bull; Company Reg: 1423/98</div>
                    </div>
                </div>
                <div class="text-muted small">
                    14 Samora Machel Avenue, Harare, Zimbabwe &bull; Email: payroll@trainit.co.zw
                </div>
            </div>
            <div class="text-end">
                <span class="badge px-3 py-2 text-uppercase fw-bold rounded-pill text-white" style="background-color: #2A114B; font-size: 0.85rem;">
                    CONFIDENTIAL PAYSLIP
                </span>
                <div class="h5 fw-bold text-dark mt-2 mb-0 font-monospace"><?= htmlspecialchars(is_object($period) ? $period->period_code : $period['period_code']) ?></div>
                <div class="text-muted small">Pay Date: <?= htmlspecialchars(is_object($period) ? $period->pay_date : $period['pay_date']) ?></div>
            </div>
        </div>

        <!-- Employee Info Grid -->
        <div class="row g-3 mb-4 pb-4 border-bottom">
            <div class="col-sm-6">
                <div class="small text-muted text-uppercase fw-bold mb-1">Employee Details</div>
                <h5 class="fw-bold text-dark mb-1">
                    <?= htmlspecialchars($staff ? (is_object($staff) ? ($staff->title . ' ' . $staff->firstname . ' ' . $staff->surname) : ($staff['title'] . ' ' . $staff['firstname'] . ' ' . $staff['surname'])) : 'Staff Member') ?>
                </h5>
                <div class="text-muted small"><strong>Designation:</strong> <?= htmlspecialchars($staff ? (is_object($staff) ? $staff->occupation : $staff['occupation']) : 'Personnel') ?></div>
                <div class="text-muted small"><strong>Department:</strong> <?= htmlspecialchars($staff ? (is_object($staff) ? $staff->department : $staff['department']) : 'Operations') ?></div>
                <div class="text-muted small"><strong>Station:</strong> <?= htmlspecialchars($staff ? (is_object($staff) ? $staff->station : $staff['station']) : 'Harare HQ') ?></div>
            </div>

            <div class="col-sm-6 text-sm-end">
                <div class="small text-muted text-uppercase fw-bold mb-1">Statutory & Banking</div>
                <div class="text-dark small"><strong>Works Number:</strong> <span class="badge bg-light text-dark border"><?= htmlspecialchars($staff ? (is_object($staff) ? $staff->current_works_number : $staff['current_works_number']) : 'N/A') ?></span></div>
                <div class="text-dark small"><strong>National ID:</strong> <?= htmlspecialchars($staff ? (is_object($staff) ? $staff->national_id_number : $staff['national_id_number']) : 'N/A') ?></div>
                <div class="text-dark small"><strong>NSSA SSR:</strong> <?= htmlspecialchars($staff ? (is_object($staff) ? $staff->ssr_number : $staff['ssr_number']) : 'N/A') ?></div>
                <div class="text-muted small mt-1">
                    <strong>Bank:</strong> <?= htmlspecialchars($staff ? (is_object($staff) ? $staff->bank_name : $staff['bank_name']) : 'Stanbic Bank') ?> &bull;
                    <strong>Acc:</strong> <?= htmlspecialchars($staff ? (is_object($staff) ? $staff->account_number : $staff['account_number']) : '••••••••') ?>
                </div>
            </div>
        </div>

        <!-- Remuneration & Deductions Breakdown -->
        <div class="row g-4 mb-4">
            <!-- Earnings -->
            <div class="col-sm-6">
                <div class="border rounded-4 p-3 bg-light h-100">
                    <h6 class="fw-bold text-uppercase small text-dark border-bottom pb-2 mb-3">
                        <i class="fa fa-plus-circle text-success me-1"></i> Gross Remuneration
                    </h6>
                    <div class="list-group list-group-flush bg-transparent">
                        <?php foreach ($earnings as $e): ?>
                            <div class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between small">
                                <span><?= htmlspecialchars($e['name']) ?></span>
                                <span class="fw-bold text-dark">$<?= number_format($e['amount'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top fw-bold text-dark">
                        <span>Total Gross</span>
                        <span>$<?= number_format(is_object($payslip) ? $payslip->gross_pay : $payslip['gross_pay'], 2) ?></span>
                    </div>
                </div>
            </div>

            <!-- Deductions -->
            <div class="col-sm-6">
                <div class="border rounded-4 p-3 bg-light h-100">
                    <h6 class="fw-bold text-uppercase small text-dark border-bottom pb-2 mb-3">
                        <i class="fa fa-minus-circle text-danger me-1"></i> Statutory Deductions
                    </h6>
                    <div class="list-group list-group-flush bg-transparent">
                        <?php foreach ($deductions as $d): ?>
                            <div class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between small">
                                <span><?= htmlspecialchars($d['name']) ?></span>
                                <span class="fw-bold text-danger">-$<?= number_format($d['amount'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 mt-3 border-top fw-bold text-danger">
                        <span>Total Deductions</span>
                        <span>-$<?= number_format(is_object($payslip) ? $payslip->total_deductions : $payslip['total_deductions'], 2) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Remuneration Banner -->
        <div class="p-4 rounded-4 mb-4 text-white d-flex justify-content-between align-items-center shadow-sm" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 100%);">
            <div>
                <span class="text-uppercase small fw-bold" style="color: #FFCC00; letter-spacing: 0.5px;">Net Take-Home Remuneration</span>
                <div class="small opacity-75">Currency: <?= htmlspecialchars(is_object($payslip) ? $payslip->currency : $payslip['currency']) ?> &bull; Transferred to bank</div>
            </div>
            <div class="text-end">
                <span class="display-6 fw-bold">$<?= number_format(is_object($payslip) ? $payslip->net_pay : $payslip['net_pay'], 2) ?></span>
            </div>
        </div>

        <!-- Footer Audit Note -->
        <div class="d-flex justify-content-between align-items-center pt-3 border-top text-muted small">
            <div>
                <?php if ($disbursement): ?>
                    <i class="fa fa-circle-check text-success me-1"></i> Disbursed via <?= htmlspecialchars(is_object($disbursement) ? $disbursement->payment_method : $disbursement['payment_method']) ?>
                    (Ref: <?= htmlspecialchars(is_object($disbursement) ? $disbursement->transaction_reference : $disbursement['transaction_reference']) ?>)
                <?php else: ?>
                    <i class="fa fa-clock text-warning me-1"></i> Awaiting electronic bank transfer
                <?php endif; ?>
            </div>
            <div class="text-end" style="font-size: 0.72rem;">
                Generated by Tsigiro Payroll Engine &bull; <?= date('Y-m-d H:i') ?>
            </div>
        </div>

    </div>
</div>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
