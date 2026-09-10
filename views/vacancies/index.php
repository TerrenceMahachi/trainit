@extends('layouts.main')

<?php
global $siteConfig;
$vacancies         = $data['vacancies'] ?? [];
$statuses          = $data['statuses'] ?? [];
$departments       = $data['departments'] ?? [];
$totalVacancies    = $data['totalVacancies'] ?? 0;
$publishedCount    = $data['publishedCount'] ?? 0;
$draftCount        = $data['draftCount'] ?? 0;
$closedCount       = $data['closedCount'] ?? 0;
$totalApplications = $data['totalApplications'] ?? 0;
$appointedCount    = $data['appointedCount'] ?? 0;
$selectedStatus    = $data['selectedStatus'] ?? 0;
$selectedDept      = $data['selectedDept'] ?? 0;
$search            = $data['search'] ?? '';
?>

<main class="portal-dashboard">
    <!-- Hero Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <p class="portal-kicker text-warning mb-1" style="font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;">
                    <i class="fa fa-bullhorn me-1"></i> Talent Acquisition &amp; Staff Onboarding
                </p>
                <h1 class="h2 fw-bold text-white mb-2">Recruitment &amp; Vacancy Management</h1>
                <p class="mb-0 text-white-50" style="max-width: 650px;">
                    Advertise openings, screen candidate dossiers, manage competencies, and seamlessly appoint successful applicants directly into internal staff onboarding.
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3 shadow-sm" target="_blank">
                    <i class="fa fa-arrow-up-right-from-square me-1"></i> View Opportunities Page
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="btn btn-outline-warning text-white px-3 py-2 fw-semibold rounded-3 shadow-sm">
                    <i class="fa fa-users me-1"></i> Staff Directory
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/create" class="btn btn-warning text-dark fw-bold px-3 py-2 rounded-3 shadow-sm">
                    <i class="fa fa-plus-circle me-1"></i> Post New Vacancy
                </a>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">

            <!-- KPI Metric Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #2A114B !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small text-uppercase fw-semibold">Total Vacancies</span>
                                <i class="fa fa-briefcase text-secondary"></i>
                            </div>
                            <h3 class="fw-bold mb-0" style="color: #1C0D30;"><?= $totalVacancies; ?></h3>
                            <div class="small text-muted mt-1"><?= $draftCount; ?> Draft &bull; <?= $closedCount; ?> Closed</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #10B981 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small text-uppercase fw-semibold">Published &amp; Active</span>
                                <i class="fa fa-globe text-success"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-success"><?= $publishedCount; ?></h3>
                            <div class="small text-success mt-1"><i class="fa fa-check-circle me-1"></i>Live on Opportunities</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #3B82F6 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small text-uppercase fw-semibold">Applications</span>
                                <i class="fa fa-file-lines text-primary"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-primary"><?= $totalApplications; ?></h3>
                            <div class="small text-muted mt-1">Direct applicant submissions</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #FFCC00 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small text-uppercase fw-semibold">Staff Appointed</span>
                                <i class="fa fa-user-check text-warning"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-dark"><?= $appointedCount; ?></h3>
                            <div class="small text-muted mt-1"><i class="fa fa-signature me-1 text-warning"></i>Transitioned to Staff</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters & Search Toolbar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3">
                    <form method="GET" action="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="row g-2 align-items-center">
                        <div class="col-12 col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                                <input type="search" name="search" value="<?= htmlspecialchars($search); ?>" class="form-control border-start-0" placeholder="Search title, ref number, keywords...">
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <select name="dept" class="form-select" onchange="this.form.submit()">
                                <option value="0">All Departments</option>
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d->iD; ?>" <?= $selectedDept === (int)$d->iD ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($d->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="0">All Statuses</option>
                                <?php foreach ($statuses as $st): ?>
                                    <option value="<?= $st->iD; ?>" <?= $selectedStatus === (int)$st->iD ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($st->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 fw-semibold">Filter</button>
                            <?php if ($search || $selectedDept || $selectedStatus): ?>
                                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="btn btn-outline-secondary" title="Reset Filters"><i class="fa fa-rotate-left"></i></a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alert Box for AJAX actions -->
            <div id="vacanciesAlert" class="alert d-none mb-4" role="alert"></div>

            <!-- Vacancies Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold" style="color: #1C0D30;">
                        <i class="fa fa-list me-2 text-primary"></i> Vacancy Records (<?= count($vacancies); ?>)
                    </h5>
                    <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/create" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                        <i class="fa fa-plus me-1"></i> New Position
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 140px;">Reference</th>
                                <th>Position Title &amp; Target Role</th>
                                <th>Department</th>
                                <th>Terms / Location</th>
                                <th>Closing</th>
                                <th>Status</th>
                                <th class="text-center">Applicants</th>
                                <th class="text-end" style="width: 160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($vacancies)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fa fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-1 fw-bold fs-5">No vacancies found</p>
                                        <p class="small">Try adjusting your search criteria or create a new job opening.</p>
                                        <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/create" class="btn btn-warning btn-sm text-dark fw-bold mt-2">
                                            <i class="fa fa-plus-circle me-1"></i> Post Vacancy Now
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($vacancies as $v):
                                    $dept = $v->department();
                                    $engagement = $v->engagementbasis();
                                    $location = $v->worklocationpreference();
                                    $targetRole = $v->targetRole();
                                    $statusRec = $v->vacancyStatus();
                                    $appCount = $v->applicationCount();
                                    $daysRemaining = $v->daysRemaining();
                                    $isClosed = $v->isClosed();
                                ?>
                                    <tr>
                                        <td>
                                            <code class="fw-bold text-dark fs-6"><?= htmlspecialchars($v->reference_number); ?></code>
                                            <?php if ($v->is_featured): ?>
                                                <span class="badge bg-warning text-dark d-block mt-1" style="font-size: 0.7rem;">
                                                    <i class="fa fa-star me-1"></i> Featured
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark fs-6 mb-1">
                                                <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancy/<?= urlencode($v->slug); ?>" target="_blank" class="text-decoration-none text-dark hover-primary">
                                                    <?= htmlspecialchars($v->title); ?>
                                                </a>
                                            </div>
                                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill small">
                                                <i class="fa fa-id-badge me-1"></i> Target: <?= htmlspecialchars($targetRole ? $targetRole->name : 'Staff'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-secondary">
                                                <?= htmlspecialchars($dept ? $dept->name : 'General'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark"><?= htmlspecialchars($engagement ? $engagement->name : 'N/A'); ?></div>
                                            <div class="small text-muted"><i class="fa fa-location-dot me-1 text-danger"></i> <?= htmlspecialchars($location ? $location->name : 'N/A'); ?></div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold"><?= htmlspecialchars($v->closing_date); ?></div>
                                            <?php if ($isClosed): ?>
                                                <span class="badge bg-danger-subtle text-danger rounded-pill small">Closed</span>
                                            <?php elseif ($daysRemaining <= 5): ?>
                                                <span class="badge bg-warning text-dark rounded-pill small"><?= $daysRemaining; ?> days left</span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success rounded-pill small"><?= $daysRemaining; ?> days left</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm <?= htmlspecialchars($statusRec ? $statusRec->badge_class : 'bg-secondary text-white'); ?> dropdown-toggle py-1 px-2 fw-semibold" type="button" data-bs-toggle="dropdown">
                                                    <?= htmlspecialchars($statusRec ? $statusRec->name : 'Draft'); ?>
                                                </button>
                                                <ul class="dropdown-menu shadow-sm">
                                                    <?php foreach ($statuses as $st): ?>
                                                        <li>
                                                            <a class="dropdown-item small status-toggle-btn" href="#" data-id="<?= $v->iD; ?>" data-status="<?= $st->iD; ?>">
                                                                <span class="badge <?= htmlspecialchars($st->badge_class); ?> me-1">&bull;</span>
                                                                <?= htmlspecialchars($st->name); ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/applications/<?= $v->iD; ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3 position-relative">
                                                <i class="fa fa-users me-1"></i>
                                                <span class="fw-bold"><?= $appCount; ?></span>
                                            </a>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/applications/<?= $v->iD; ?>" class="btn btn-outline-info" title="Review Applications">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/edit/<?= $v->iD; ?>" class="btn btn-outline-secondary" title="Edit Vacancy">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancy/<?= urlencode($v->slug); ?>" target="_blank" class="btn btn-outline-dark" title="Preview Public Page">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const alertBox = document.getElementById('vacanciesAlert');

    function showAlert(msg, isSuccess) {
        alertBox.className = 'alert alert-' + (isSuccess ? 'success' : 'danger') + ' alert-dismissible fade show';
        alertBox.innerHTML = msg + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        alertBox.classList.remove('d-none');
        window.scrollTo({ top: alertBox.offsetTop - 80, behavior: 'smooth' });
    }

    document.querySelectorAll('.status-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const vacancyId = this.dataset.id;
            const statusId = this.dataset.status;

            const fd = new FormData();
            fd.append('id', vacancyId);
            fd.append('status', statusId);

            fetch('<?= $siteConfig->siteUrl; ?>/admin/vacancies/toggle-status', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 1) {
                    showAlert(data.msg, true);
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAlert(data.msg || 'Could not update status.', false);
                }
            })
            .catch(err => {
                console.error(err);
                showAlert('Network or server error updating status.', false);
            });
        });
    });
});
</script>
