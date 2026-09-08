<?php
$title = "Staff Payroll Cycles & Remuneration — " . _SITE;
$navUser = \App\Helpers\Auth::user();
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
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl ?>/admin/staff" class="text-decoration-none" style="color: #2A114B;">Staff</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Payroll</li>
                    </ol>
                </nav>
                <h2 class="h3 fw-bold mb-0" style="color: #1C0D30;">
                    <i class="fa fa-money-check-dollar me-2" style="color: #FFCC00;"></i>Staff Payroll & Remuneration
                </h2>
                <p class="text-muted small mb-0">Monthly salary calculation, Zimbabwean NSSA/PAYE deductions, employee payslips, and statutory filings.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl ?>/admin/staff" class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fa fa-id-badge me-1"></i> Staff Directory
                </a>
                <button type="button" class="btn text-white rounded-pill px-4 shadow-sm" style="background-color: #2A114B;" data-bs-toggle="modal" data-bs-target="#modalNewPeriod">
                    <i class="fa fa-plus-circle me-1"></i> Initialize Payroll Period
                </button>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #2A114B 0%, #431E76 100%); color: #fff;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase small fw-bold" style="opacity: 0.85;">Payroll Cycles</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1"><?= count($periods) ?></h3>
                        </div>
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.15);">
                            <i class="fa fa-calendar-days fa-lg text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3 small" style="opacity: 0.9;">
                        <span>Historical & active cycles</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold">Active Personnel</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1 text-dark"><?= $activeStaffCount ?></h3>
                        </div>
                        <div class="rounded-circle p-2 bg-light">
                            <i class="fa fa-users fa-lg text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        <span>Staff enrolled on payroll</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold">Total Disbursed</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1 text-success">$<?= number_format($totalDisbursedOverall, 2) ?></h3>
                        </div>
                        <div class="rounded-circle p-2 bg-light">
                            <i class="fa fa-building-columns fa-lg text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        <span>Net salaries paid to date</span>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold">Statutory Compliance</span>
                            <h3 class="display-6 fw-bold mb-0 mt-1" style="color: #431E76;">100%</h3>
                        </div>
                        <div class="rounded-circle p-2 bg-light">
                            <i class="fa fa-shield-check fa-lg text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3 small text-muted">
                        <span>NSSA Form P4 & ZIMRA PAYE</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Periods Ledger -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0" style="color: #2A114B;">Monthly Payroll Periods</h5>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNewPeriod">
                    <i class="fa fa-plus me-1"></i> New Cycle
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Period Code</th>
                            <th>Cycle Name</th>
                            <th>Pay Date</th>
                            <th>Personnel Count</th>
                            <th>Total Gross Remuneration</th>
                            <th>Net Salaries</th>
                            <th>Cycle Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($periods)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa fa-calculator fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                    <p class="mb-2">No payroll cycles created yet.</p>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalNewPeriod">
                                        Initialize First Payroll Run
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($periods as $pItem): ?>
                                <?php
                                $p = $pItem['period'];
                                $pId = is_object($p) ? $p->iD : $p['iD'];
                                ?>
                                <tr>
                                    <td class="ps-4 fw-bold font-monospace text-dark">
                                        <?= htmlspecialchars(is_object($p) ? $p->period_code : $p['period_code']) ?>
                                    </td>
                                    <td>
                                        <a href="<?= $siteConfig->siteUrl ?>/admin/payroll/view/<?= $pId ?>" class="fw-bold text-decoration-none" style="color: #1C0D30;">
                                            <?= htmlspecialchars(is_object($p) ? $p->period_name : $p['period_name']) ?>
                                        </a>
                                        <div class="text-muted small">
                                            <?= htmlspecialchars(is_object($p) ? $p->start_date : $p['start_date']) ?> to <?= htmlspecialchars(is_object($p) ? $p->end_date : $p['end_date']) ?>
                                        </div>
                                    </td>
                                    <td class="text-muted small">
                                        <i class="fa fa-calendar-check me-1 text-success"></i><?= htmlspecialchars(is_object($p) ? $p->pay_date : $p['pay_date']) ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <?= $pItem['payslips_count'] ?> Staff Payslips
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">$<?= number_format($pItem['total_gross'], 2) ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">$<?= number_format($pItem['total_net'], 2) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $pItem['is_disbursed'] ? 'bg-success' : 'bg-primary-subtle text-primary border' ?>">
                                            <?= htmlspecialchars($pItem['status_name']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= $siteConfig->siteUrl ?>/admin/payroll/view/<?= $pId ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            View Ledger <i class="fa fa-chevron-right ms-1"></i>
                                        </a>
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

<!-- Modal: Initialize New Payroll Period -->
<div class="modal fade" id="modalNewPeriod" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="<?= $siteConfig->siteUrl ?>/admin/payroll/create" method="POST">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold" style="color: #2A114B;">
                        <i class="fa fa-calendar-plus me-2 text-warning"></i>Initialize Payroll Period
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Period Code <span class="text-danger">*</span></label>
                        <input type="text" name="period_code" class="form-control rounded-3 font-monospace" value="PAY-<?= date('Y-m') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Period Descriptive Name <span class="text-danger">*</span></label>
                        <input type="text" name="period_name" class="form-control rounded-3" value="<?= date('F Y') ?> Monthly Payroll Run" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Cycle Start Date</label>
                            <input type="date" name="start_date" class="form-control rounded-3" value="<?= date('Y-m-01') ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Cycle End Date</label>
                            <input type="date" name="end_date" class="form-control rounded-3" value="<?= date('Y-m-t') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Scheduled Salary Pay Date</label>
                        <input type="date" name="pay_date" class="form-control rounded-3" value="<?= date('Y-m-25') ?>" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #2A114B;">Create Cycle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include _BASE_PATH . '/views/partials/footer.php'; ?>
