@extends('layouts.main')

<?php
global $siteConfig;
$vacancy       = $data['vacancy'] ?? null;
$applications  = $data['applications'] ?? [];
$statuses      = $data['statuses'] ?? [];
$targetRoleObj = $data['targetRoleObj'] ?? null;
$deptObj       = $vacancy ? $vacancy->department() : null;
?>

<main class="portal-dashboard">
    <!-- Hero Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="text-warning text-decoration-none">Vacancies</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">Applicant Dossiers</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                    <h1 class="h2 fw-bold text-white mb-0"><?= htmlspecialchars($vacancy->title ?? 'Applicant Review'); ?></h1>
                    <span class="badge bg-warning text-dark fw-bold"><?= htmlspecialchars($vacancy->reference_number ?? ''); ?></span>
                </div>
                <p class="mb-0 text-white-50">
                    Target Role: <strong class="text-white"><?= htmlspecialchars($targetRoleObj ? $targetRoleObj->name : 'Staff Member'); ?></strong> &bull;
                    Department: <strong class="text-white"><?= htmlspecialchars($deptObj ? $deptObj->name : 'General'); ?></strong> &bull;
                    Submissions: <strong class="text-white"><?= count($applications); ?> Candidates</strong>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/edit/<?= (int)($vacancy->iD ?? 0); ?>" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3 shadow-sm">
                    <i class="fa fa-pencil me-1"></i> Edit Vacancy
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="btn btn-outline-warning text-white px-3 py-2 fw-semibold rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-1"></i> All Vacancies
                </a>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div id="applicationsAlert" class="alert d-none mb-4" role="alert"></div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold" style="color: #1C0D30;">
                        <i class="fa fa-folder-open text-primary me-2"></i> Candidate Submissions (<?= count($applications); ?>)
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Legend:</span>
                        <span class="badge bg-info text-dark">Intake</span>
                        <span class="badge bg-primary text-white">Shortlisted</span>
                        <span class="badge bg-success text-white">Appointed</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Applicant Details</th>
                                <th>Experience &amp; Education</th>
                                <th>Compensation &amp; Notice</th>
                                <th>Curriculum Vitae</th>
                                <th>Score / Status</th>
                                <th class="text-end" style="min-width: 220px;">Staff Appointment Bridge</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($applications)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa fa-inbox fa-3x mb-3 text-secondary opacity-50"></i>
                                        <p class="mb-1 fw-bold fs-5">No applications submitted yet</p>
                                        <p class="small">Candidates applying through the public Opportunities portal will appear here in real-time.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($applications as $app): 
                                    $stRec = $app->statusRecord();
                                    $isAppointed = !empty($app->staff_invite) || (int)$app->application_status === 5;
                                ?>
                                    <tr id="app_row_<?= $app->iD; ?>">
                                        <td>
                                            <div class="fw-bold fs-6 text-dark"><?= htmlspecialchars($app->fullName()); ?></div>
                                            <div class="small text-muted"><i class="fa fa-envelope me-1"></i> <a href="mailto:<?= htmlspecialchars($app->email); ?>" class="text-decoration-none"><?= htmlspecialchars($app->email); ?></a></div>
                                            <div class="small text-muted"><i class="fa fa-phone me-1"></i> <?= htmlspecialchars($app->phone); ?></div>
                                            <div class="small text-muted"><i class="fa fa-map-marker-alt me-1 text-danger"></i> <?= htmlspecialchars($app->city . ', ' . $app->country); ?></div>
                                            <code class="small text-secondary mt-1 d-inline-block"><?= htmlspecialchars($app->application_number); ?></code>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-dark"><i class="fa fa-briefcase me-1 text-primary"></i> <?= (int)$app->years_of_experience; ?> yrs exp</div>
                                            <div class="small text-muted"><?= htmlspecialchars($app->highest_qualification ?: 'Not stated'); ?></div>
                                            <?php if ($app->current_job_title): ?>
                                                <div class="small text-muted mt-1"><i class="fa fa-id-card me-1"></i> <?= htmlspecialchars($app->current_job_title . ' at ' . ($app->current_employer ?: 'Confidential')); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="small text-dark fw-semibold"><?= htmlspecialchars($app->expected_salary ?: 'Negotiable'); ?></div>
                                            <div class="small text-muted"><i class="fa fa-clock me-1 text-secondary"></i> Notice: <?= (int)$app->notice_period_days; ?> days</div>
                                        </td>
                                        <td>
                                            <?php if ($app->cv_path): ?>
                                                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/application/<?= $app->iD; ?>/cv" class="btn btn-sm btn-outline-danger rounded-pill px-3" target="_blank" title="Download CV File">
                                                    <i class="fa fa-file-pdf me-1"></i> View CV
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">No file</span>
                                            <?php endif; ?>

                                            <?php if ($app->cover_letter): ?>
                                                <button type="button" class="btn btn-sm btn-link text-primary p-0 d-block mt-1 small" data-bs-toggle="modal" data-bs-target="#coverLetterModal_<?= $app->iD; ?>">
                                                    <i class="fa fa-file-lines me-1"></i> Cover Letter
                                                </button>
                                                <!-- Cover Letter Modal -->
                                                <div class="modal fade" id="coverLetterModal_<?= $app->iD; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content rounded-4 border-0">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title fw-bold">Cover Letter: <?= htmlspecialchars($app->fullName()); ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <p class="text-muted" style="white-space: pre-wrap; font-size: 0.95rem;"><?= htmlspecialchars($app->cover_letter); ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- Status Dropdown -->
                                            <div class="dropdown mb-2">
                                                <button class="btn btn-sm <?= htmlspecialchars($stRec ? $stRec->badge_class : 'bg-secondary text-white'); ?> dropdown-toggle py-1 px-2 fw-semibold" type="button" data-bs-toggle="dropdown" id="status_btn_<?= $app->iD; ?>">
                                                    <?= htmlspecialchars($stRec ? $stRec->name : 'Intake'); ?>
                                                </button>
                                                <ul class="dropdown-menu shadow-sm">
                                                    <?php foreach ($statuses as $st): ?>
                                                        <li>
                                                            <a class="dropdown-item small app-status-btn" href="#" data-app="<?= $app->iD; ?>" data-status="<?= $st->iD; ?>">
                                                                <span class="badge <?= htmlspecialchars($st->badge_class); ?> me-1">&bull;</span>
                                                                <?= htmlspecialchars($st->name); ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>

                                            <div class="d-flex align-items-center gap-1">
                                                <span class="small text-muted">Score:</span>
                                                <input type="number" class="form-control form-control-sm text-center py-0 px-1 app-score-input" data-app="<?= $app->iD; ?>" value="<?= htmlspecialchars($app->rating_score ?? ''); ?>" placeholder="--" min="0" max="100" style="width: 55px;">
                                                <span class="small text-muted">/100</span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <?php if ($isAppointed): ?>
                                                <span class="badge bg-success-subtle text-success border border-success p-2 rounded-3 d-inline-block text-start">
                                                    <i class="fa fa-user-check me-1"></i> <strong>Appointed to Staff</strong><br>
                                                    <small class="text-muted">Invite issued <?= htmlspecialchars($app->appointed_at ?? ''); ?></small>
                                                </span>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-success fw-bold rounded-pill px-3 shadow-sm appoint-staff-btn" data-app="<?= $app->iD; ?>" data-name="<?= htmlspecialchars($app->fullName()); ?>" data-role="<?= htmlspecialchars($targetRoleObj ? $targetRoleObj->name : 'Staff'); ?>">
                                                    <i class="fa fa-user-plus me-1"></i> Appoint &amp; Onboard
                                                </button>
                                            <?php endif; ?>
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
    const alertBox = document.getElementById('applicationsAlert');

    function showAlert(msg, isSuccess) {
        alertBox.className = 'alert alert-' + (isSuccess ? 'success' : 'danger') + ' alert-dismissible fade show';
        alertBox.innerHTML = msg + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        alertBox.classList.remove('d-none');
        window.scrollTo({ top: alertBox.offsetTop - 80, behavior: 'smooth' });
    }

    // Status change
    document.querySelectorAll('.app-status-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const appId = this.dataset.app;
            const statusId = this.dataset.status;

            const fd = new FormData();
            fd.append('application_id', appId);
            fd.append('application_status', statusId);

            fetch('<?= $siteConfig->siteUrl; ?>/admin/vacancies/application/update', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 1) {
                    const btnEl = document.getElementById('status_btn_' + appId);
                    btnEl.className = 'btn btn-sm ' + data.badge_class + ' dropdown-toggle py-1 px-2 fw-semibold';
                    btnEl.innerHTML = data.status_name;
                    showAlert(data.msg, true);
                } else {
                    showAlert(data.msg || 'Update failed', false);
                }
            })
            .catch(err => {
                console.error(err);
                showAlert('Network or server error updating applicant status.', false);
            });
        });
    });

    // Score update on change
    document.querySelectorAll('.app-score-input').forEach(inp => {
        inp.addEventListener('change', function() {
            const appId = this.dataset.app;
            const score = this.value;

            const fd = new FormData();
            fd.append('application_id', appId);
            fd.append('rating_score', score);

            fetch('<?= $siteConfig->siteUrl; ?>/admin/vacancies/application/update', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 1) {
                    showAlert('Rating score updated to ' + score + '/100.', true);
                }
            });
        });
    });

    // Appoint candidate to staff
    document.querySelectorAll('.appoint-staff-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const appId = this.dataset.app;
            const name = this.dataset.name;
            const role = this.dataset.role;

            if (!confirm(`Are you sure you want to appoint ${name} as ${role} and dispatch a statutory staff onboarding invitation?`)) {
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Provisioning...';

            const fd = new FormData();
            fd.append('application_id', appId);

            fetch('<?= $siteConfig->siteUrl; ?>/admin/vacancies/application/appoint', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 1) {
                    showAlert(data.msg + (data.invite_url ? ` <a href="${data.invite_url}" target="_blank" class="fw-bold text-dark text-decoration-underline ms-1">View Onboarding Link</a>` : ''), true);
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-user-plus me-1"></i> Appoint &amp; Onboard';
                    showAlert(data.msg || 'Appointment failed.', false);
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-user-plus me-1"></i> Appoint &amp; Onboard';
                showAlert('Network error executing staff appointment.', false);
            });
        });
    });
});
</script>
