<?php
$pCode = htmlspecialchars(is_object($period) ? $period->period_code : $period['period_code']);
$title = "{$pCode} — Payroll Period Ledger — " . _SITE;
$navUser = \App\Helpers\Auth::user();
$periodId = is_object($period) ? $period->iD : $period['iD'];
?>
<?php include _BASE_PATH . '/views/partials/header.php'; ?>
<?php include _BASE_PATH . '/views/partials/nav.php'; ?>

<main class="py-4" style="background-color: #fcfbfe; min-height: 85vh;">
    <div class="container-xl">

        <!-- Top Header & Breadcrumbs -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted small">
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/dashboard" class="text-decoration-none" style="color: #2A114B;">Admin</a></li>
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/admin/payroll" class="text-decoration-none" style="color: #2A114B;">Payroll</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= $pCode ?></li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h3 fw-bold mb-0" style="color: #1C0D30;"><?= htmlspecialchars(is_object($period) ? $period->period_name : $period['period_name']) ?></h2>
                    <span class="badge font-monospace bg-light text-dark border"><?= $pCode ?></span>
                </div>
                <div class="text-muted small mt-1">
                    Cycle: <?= htmlspecialchars(is_object($period) ? $period->start_date : $period['start_date']) ?> to <?= htmlspecialchars(is_object($period) ? $period->end_date : $period['end_date']) ?> &bull; Pay Date: <strong class="text-dark"><?= htmlspecialchars(is_object($period) ? $period->pay_date : $period['pay_date']) ?></strong>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= $siteConfig->siteUrl ?>/admin/payroll" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa fa-arrow-left me-1"></i> All Periods
                </a>
                <form action="<?= $siteConfig->siteUrl ?>/admin/payroll/calculate" method="POST" class="d-inline">
                    <input type="hidden" name="period_id" value="<?= $periodId ?>">
                    <button type="submit" class="btn text-white rounded-pill px-3 shadow-sm" style="background-color: #2A114B;">
                        <i class="fa fa-calculator me-1"></i> Run Calculations
                    </button>
                </form>
                <button type="button" class="btn btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalApprovePayroll">
                    <i class="fa fa-stamp me-1"></i> Executive Approve
                </button>
                <button type="button" class="btn btn-success rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDisbursePayroll">
                    <i class="fa fa-building-columns me-1"></i> Disburse Salaries
                </button>
                <button type="button" class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalStatutoryReturn">
                    <i class="fa fa-file-invoice me-1"></i> File Statutory Return
                </button>
            </div>
        </div>

        <?php if (!empty($_GET['msg'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="fa fa-check-circle me-2"></i>
                <?php if ($_GET['msg'] === 'calculated'): ?>Payroll calculations executed across all active personnel.<?php endif; ?>
                <?php if ($_GET['msg'] === 'approved'): ?>Payroll run officially approved by executive sign-off.<?php endif; ?>
                <?php if ($_GET['msg'] === 'disbursed'): ?>Salaries recorded as disbursed via electronic bank transfer.<?php endif; ?>
                <?php if ($_GET['msg'] === 'return_filed'): ?>Statutory filing recorded and archived in compliance vault.<?php endif; ?>
                <?php if ($_GET['msg'] === 'period_created'): ?>Payroll period successfully initialized.<?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Summary Totals -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <span class="text-uppercase text-muted small fw-bold">Total Gross Pay</span>
                    <h3 class="display-6 fw-bold mb-0 mt-1 text-dark">$<?= number_format($totalGross, 2) ?></h3>
                    <div class="small text-muted mt-2">Contractual remuneration</div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <span class="text-uppercase text-muted small fw-bold">NSSA Pension Scheme</span>
                    <h3 class="display-6 fw-bold mb-0 mt-1 text-primary">$<?= number_format($totalNssa, 2) ?></h3>
                    <div class="small text-muted mt-2">Employee 4.5% statutory pool</div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <span class="text-uppercase text-muted small fw-bold">ZIMRA PAYE & AIDS Levy</span>
                    <h3 class="display-6 fw-bold mb-0 mt-1 text-danger">$<?= number_format($totalPaye + $totalAidsLevy, 2) ?></h3>
                    <div class="small text-muted mt-2">Tax ($<?= number_format($totalPaye, 2) ?>) + AIDS ($<?= number_format($totalAidsLevy, 2) ?>)</div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 p-3" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff;">
                    <span class="text-uppercase small fw-bold" style="opacity: 0.85;">Total Net Disbursement</span>
                    <h3 class="display-6 fw-bold mb-0 mt-1">$<?= number_format($totalNet, 2) ?></h3>
                    <div class="small mt-2" style="opacity: 0.9;">Payable to bank accounts</div>
                </div>
            </div>
        </div>

        <!-- Staff Payslips Ledger -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="color: #2A114B;">
                    <i class="fa fa-users me-2 text-primary"></i>Employee Remuneration Payslips (<?= count($payslips) ?>)
                </h5>
                <?php if (empty($payslips)): ?>
                    <form action="<?= $siteConfig->siteUrl ?>/admin/payroll/calculate" method="POST">
                        <input type="hidden" name="period_id" value="<?= $periodId ?>">
                        <button type="submit" class="btn btn-sm text-white rounded-pill px-3" style="background-color: #2A114B;">
                            <i class="fa fa-play me-1"></i> Generate Payslips
                        </button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Employee</th>
                            <th>Works / NSSA SSR</th>
                            <th>Gross Pay</th>
                            <th>Statutory Deductions</th>
                            <th>Net Pay</th>
                            <th>Disbursement</th>
                            <th class="text-end pe-4">Payslip Document</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payslips)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa fa-calculator fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                    <p class="mb-2">No payslips calculated for this cycle yet.</p>
                                    <form action="<?= $siteConfig->siteUrl ?>/admin/payroll/calculate" method="POST">
                                        <input type="hidden" name="period_id" value="<?= $periodId ?>">
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
                                            Run Automated Calculations
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payslips as $item): ?>
                                <?php
                                $ps = $item['payslip'];
                                $psId = is_object($ps) ? $ps->iD : $ps['iD'];
                                $staff = $item['staff'];
                                $disb = $item['disbursement'];
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($staff ? (is_object($staff) ? ($staff->firstname . ' ' . $staff->surname) : ($staff['firstname'] . ' ' . $staff['surname'])) : 'Staff Member') ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($staff ? (is_object($staff) ? $staff->occupation : $staff['occupation']) : 'Personnel') ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($staff ? (is_object($staff) ? $staff->current_works_number : $staff['current_works_number']) : 'N/A') ?></span>
                                        <div class="text-muted small">SSR: <?= htmlspecialchars($staff ? (is_object($staff) ? $staff->ssr_number : $staff['ssr_number']) : 'N/A') ?></div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">$<?= number_format(is_object($ps) ? $ps->gross_pay : $ps['gross_pay'], 2) ?></span>
                                    </td>
                                    <td>
                                        <span class="text-danger fw-bold">-$<?= number_format(is_object($ps) ? $ps->total_deductions : $ps['total_deductions'], 2) ?></span>
                                    </td>
                                    <td>
                                        <span class="h6 fw-bold text-success mb-0">$<?= number_format(is_object($ps) ? $ps->net_pay : $ps['net_pay'], 2) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($disb): ?>
                                            <span class="badge bg-success-subtle text-success border">
                                                <i class="fa fa-check me-1"></i>DISBURSED
                                            </span>
                                            <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars(is_object($disb) ? $disb->transaction_reference : $disb['transaction_reference']) ?></div>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning-emphasis border">PENDING</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= $siteConfig->siteUrl ?>/admin/payroll/payslip/<?= $psId ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fa fa-print me-1"></i> View Payslip
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statutory Returns Ledger -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="color: #2A114B;">
                    <i class="fa fa-file-contract me-2 text-warning"></i>Statutory Returns & Filings (NSSA P4 & ZIMRA P2)
                </h5>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalStatutoryReturn">
                    <i class="fa fa-plus me-1"></i> Record Filing
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Return Type</th>
                            <th>Reference Number</th>
                            <th>Total Remittance</th>
                            <th>Filing Date</th>
                            <th class="text-end pe-4">File / Confirmation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($returns)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted small">
                                    No statutory returns logged for this period yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($returns as $ret): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        <?= htmlspecialchars(is_object($ret) ? $ret->return_type : $ret['return_type']) ?>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars(is_object($ret) ? $ret->reference_number : $ret['reference_number']) ?></span></td>
                                    <td class="fw-bold text-success">$<?= number_format(is_object($ret) ? $ret->total_contribution : $ret['total_contribution'], 2) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars(is_object($ret) ? $ret->submission_date : $ret['submission_date']) ?></td>
                                    <td class="text-end pe-4">
                                        <span class="badge bg-success-subtle text-success border"><i class="fa fa-shield-check me-1"></i>FILING CONFIRMED</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

<!-- Modal: Executive Approval -->
<div class="modal fade" id="modalApprovePayroll" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/payroll/approve" method="POST">
                <input type="hidden" name="period_id" value="<?= $periodId ?>">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-success">
                        <i class="fa fa-stamp me-2"></i>Executive Approval of Payroll
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="small text-muted mb-3">You are granting executive sign-off for period <strong><?= $pCode ?></strong> with total net payout of <strong>$<?= number_format($totalNet, 2) ?></strong>.</p>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Sign-Off Notes</label>
                        <textarea name="approval_notes" class="form-control rounded-3" rows="2" placeholder="Audited and approved for bank disbursement..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Approve Run</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Disburse Salaries -->
<div class="modal fade" id="modalDisbursePayroll" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/payroll/disburse" method="POST">
                <input type="hidden" name="period_id" value="<?= $periodId ?>">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" style="color: #2A114B;">
                        <i class="fa fa-building-columns me-2 text-warning"></i>Disburse Salaries via Bank Transfer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Payment Method</label>
                        <select name="payment_method" class="form-select rounded-3">
                            <option value="STANBIC_BANK_TRANSFER">Stanbic Bank Corporate Direct Transfer</option>
                            <option value="CBZ_BANK_TRANSFER">CBZ Bank Transfer</option>
                            <option value="CABS_ELECTRONIC_FUNDS">CABS Electronic Funds Transfer</option>
                            <option value="ECOCASH_PAYROLL">EcoCash Payroll Disbursement</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Corporate Batch Reference</label>
                        <input type="text" name="batch_reference" class="form-control rounded-3" value="STANBIC-BATCH-<?= date('Ymd') ?>-01" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">Confirm Disbursement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: File Statutory Return -->
<div class="modal fade" id="modalStatutoryReturn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/payroll/statutory-return" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="period_id" value="<?= $periodId ?>">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" style="color: #2A114B;">
                        <i class="fa fa-file-invoice me-2 text-primary"></i>Record Statutory Return Filing
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Return Type</label>
                        <select name="return_type" class="form-select rounded-3">
                            <option value="NSSA_P4_MONTHLY">NSSA Form P4 Monthly Remittance (4.5% + 4.5%)</option>
                            <option value="ZIMRA_PAYE_P2">ZIMRA P2 Monthly PAYE & AIDS Levy Remittance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Filing Reference / Receipt Number <span class="text-danger">*</span></label>
                        <input type="text" name="reference_number" class="form-control rounded-3" placeholder="e.g. NSSA-RCPT-202609-001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Total Statutory Amount Remitted ($ USD) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total_contribution" class="form-control rounded-3" value="<?= number_format($totalNssa * 2, 2, '.', '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Filing Date</label>
                        <input type="date" name="submission_date" class="form-control rounded-3" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Signed Return / Proof of Payment (PDF)</label>
                        <input type="file" name="return_file" class="form-control rounded-3">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">Save Filing</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
