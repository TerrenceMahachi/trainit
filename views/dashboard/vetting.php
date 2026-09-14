@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'];

// Gather Vetting & Talent Metrics
$totalApplications = App\Models\Rosterapplication::countAll();
$apprenticeCount = count(App\Models\Rosterapplication::findByQuery("SELECT iD FROM rosterapplication WHERE applicationtrack = 1"));
$associateCount = count(App\Models\Rosterapplication::findByQuery("SELECT iD FROM rosterapplication WHERE applicationtrack = 2"));
$pendingReviewCount = count(App\Models\Rosterapplication::findByQuery("SELECT iD FROM rosterapplication WHERE applicationstatus = 2"));
$onRosterCount = count(App\Models\Rosterapplication::findByQuery("SELECT iD FROM rosterapplication WHERE applicationstatus = 5"));
$activeVacancies = count(App\Models\Vacancy::findByQuery("SELECT iD FROM vacancy WHERE vacancystatus = 2 AND status = 1"));

// Compliance Stats
$complianceStats = App\Helpers\ComplianceExpiryService::getComplianceStats();

// Applications Awaiting Vetting / In Review
$pendingApps = App\Models\Rosterapplication::findByQuery(
    "SELECT * FROM rosterapplication WHERE applicationstatus IN (1, 2) ORDER BY iD DESC LIMIT 8"
);
if (empty($pendingApps)) {
    $pendingApps = App\Models\Rosterapplication::findByQuery(
        "SELECT * FROM rosterapplication ORDER BY iD DESC LIMIT 8"
    );
}

// Track Dictionary
$tracks = [
    1 => ['name' => 'Apprentice (WRL)', 'badge' => 'bg-info text-dark'],
    2 => ['name' => 'Associate Specialist', 'badge' => 'bg-primary text-white'],
];
?>

<main class="portal-dashboard">
    <!-- Hero Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #092019 0%, #134234 100%);">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker" style="color: #32c99a;"><i class="fa fa-gavel me-1"></i> Talent Vetting &amp; Compliance Desk</p>
                <h1>Vetting &amp; Compliance Dashboard</h1>
                <p class="portal-dashboard-intro">Welcome back, <?= htmlspecialchars($user->name ?? 'Officer'); ?>. Oversee candidate dossier evaluation, scoring rubrics, statutory clearances, and recruitment pipelines.</p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/roster" class="btn btn-success fw-bold shadow-sm px-3 py-2" style="background: #32c99a; border: none; color: #090b0b;">
                    <i class="fa fa-clipboard-check me-1"></i> Talent Pipeline Console
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/compliance" class="btn btn-dark border border-secondary text-white fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-shield-halved me-1 text-warning"></i> Compliance Radar
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="btn btn-outline-light fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-briefcase me-1"></i> Vacancies
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/candidate-alerts" class="btn btn-outline-light fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-bell me-1 text-warning"></i> Job Alerts
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
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #3b82f6 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Total Intake</div>
                                    <h3 class="fw-bold mb-0 text-dark"><?= $totalApplications; ?></h3>
                                    <small class="text-muted"><?= $apprenticeCount; ?> Apprentices &bull; <?= $associateCount; ?> Associates</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-primary">
                                    <i class="fa fa-folder-open fa-2x"></i>
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
                                    <div class="text-muted small text-uppercase fw-bold">Pending Vetting</div>
                                    <h3 class="fw-bold mb-0 text-warning"><?= $pendingReviewCount; ?></h3>
                                    <small class="text-muted">Awaiting scoring &amp; assessment</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-warning">
                                    <i class="fa fa-hourglass-half fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; border-left: 5px solid #10b981 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Verified &amp; Admitted</div>
                                    <h3 class="fw-bold mb-0 text-success"><?= $onRosterCount; ?></h3>
                                    <small class="text-muted">Active on Talent Roster</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-success">
                                    <i class="fa fa-user-check fa-2x"></i>
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
                                    <div class="text-muted small text-uppercase fw-bold">Compliance Radar</div>
                                    <h3 class="fw-bold mb-0 text-purple"><?= $complianceStats['critical'] + $complianceStats['warning']; ?></h3>
                                    <small class="text-muted"><?= $complianceStats['critical']; ?> Critical &bull; <?= $complianceStats['expired']; ?> Expired</small>
                                </div>
                                <div class="rounded-circle bg-light p-3 text-purple">
                                    <i class="fa fa-shield-alt fa-2x" style="color: #8b5cf6;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vetting Operational Hub Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #e0f2fe; color: #0284c7;">
                            <i class="fa fa-gavel fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Talent Pipeline Console</h6>
                        <p class="text-muted small mb-3">Review candidate applications, score rubrics, and manage admissions.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/roster" class="btn btn-sm btn-outline-primary fw-bold mt-auto">
                            Open Pipeline <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #fef3c7; color: #d97706;">
                            <i class="fa fa-shield-halved fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Compliance Radar</h6>
                        <p class="text-muted small mb-3">Track expiring police clearances, qualification checks, and statutory documents.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/compliance" class="btn btn-sm btn-outline-warning text-dark fw-bold mt-auto">
                            View Radar <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #dcfce7; color: #15803d;">
                            <i class="fa fa-briefcase fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Recruitment &amp; Vacancies</h6>
                        <p class="text-muted small mb-3">Manage <?= $activeVacancies; ?> active job postings, application submissions, and interviews.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="btn btn-sm btn-outline-success fw-bold mt-auto">
                            Manage Vacancies <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3" style="border-radius: 12px; background: #ffffff;">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #f3e8ff; color: #7e22ce;">
                            <i class="fa fa-bell fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Candidate Job Alerts</h6>
                        <p class="text-muted small mb-3">Monitor candidate search alerts and automated notification preferences.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/candidate-alerts" class="btn btn-sm btn-outline-purple fw-bold mt-auto" style="color: #7e22ce; border-color: #7e22ce;">
                            View Subscriptions <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Priority Intake Queue: Applications Awaiting Review & Scoring -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fa fa-tasks me-2 text-warning"></i> Intake &amp; Vetting Review Queue</h5>
                        <small class="text-muted">Applications awaiting vetting evaluation, police clearance verification, or scoring.</small>
                    </div>
                    <a href="<?= $siteConfig->siteUrl; ?>/admin/roster" class="btn btn-sm btn-outline-dark fw-bold">
                        View Complete Roster <i class="fa fa-chevron-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Candidate / Applicant</th>
                                <th>Application Track</th>
                                <th>Submission Date</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pendingApps)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa fa-check-circle text-success fa-2x mb-2 d-block"></i>
                                        All applications are currently reviewed and up to date!
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pendingApps as $app): 
                                    $appUser = App\Models\User::find($app->user);
                                    $trackInfo = $tracks[$app->applicationtrack] ?? ['name' => 'General Applicant', 'badge' => 'bg-secondary text-white'];
                                    $statusObj = App\Models\Applicationstatus::find($app->applicationstatus);
                                    $statusName = $statusObj ? $statusObj->name : 'Under Review';
                                ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-dark text-white fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 13px;">
                                                    <?= strtoupper(substr($appUser->name ?? 'A', 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($appUser->name ?? 'Candidate #' . $app->iD); ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($appUser->email ?? ''); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge <?= $trackInfo['badge']; ?> px-2 py-1"><?= $trackInfo['name']; ?></span>
                                        </td>
                                        <td>
                                            <div class="small text-dark"><?= date('d M Y', strtotime($app->reg_date ?? 'now')); ?></div>
                                            <small class="text-muted"><?= date('H:i', strtotime($app->reg_date ?? 'now')); ?></small>
                                        </td>
                                        <td>
                                            <?php if ((int)$app->applicationstatus === 5): ?>
                                                <span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="fa fa-check-circle me-1"></i> Admitted</span>
                                            <?php elseif ((int)$app->applicationstatus === 2): ?>
                                                <span class="badge bg-warning-subtle text-warning border border-warning px-2 py-1"><i class="fa fa-hourglass-half me-1"></i> In Review</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><?= htmlspecialchars($statusName); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?= $siteConfig->siteUrl; ?>/admin/roster/review?id=<?= $app->iD; ?>" class="btn btn-sm btn-primary fw-bold px-3">
                                                <i class="fa fa-clipboard-check me-1"></i> Review Dossier
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Officer Profile & Quick Actions Banner -->
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%); border-left: 6px solid #32c99a !important;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px; background: #0f2b23; color: #32c99a; flex-shrink: 0;">
                            <i class="fa fa-id-badge fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Vetting Officer Workspace</h5>
                            <p class="text-muted mb-0 small">Logged in as <strong><?= htmlspecialchars($user->name ?? ''); ?></strong> &bull; Access your leave calendar, staff documents, and profile details.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= $siteConfig->siteUrl; ?>/staff/portal" class="btn btn-dark fw-bold px-3 py-2 shadow-sm" style="background: #0f2b23; border: none;">
                            <i class="fa fa-user-circle me-1 text-success"></i> Open Staff Portal
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary fw-bold px-3 py-2">
                            <i class="fa fa-external-link me-1"></i> View Opportunities Hub
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>
