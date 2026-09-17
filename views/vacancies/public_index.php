@extends('layouts.main')

<?php
global $siteConfig;
$vacancies   = $data['vacancies'] ?? [];
$departments = $data['departments'] ?? [];
$search      = $data['search'] ?? '';
$selectedDept= $data['selectedDept'] ?? 0;
?>

<main class="trainit-page">
    <section class="opportunity-hero py-5" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 60%, #3B1B66 100%); color: #ffffff;">
        <div class="container py-4 text-center">
            <p class="text-warning text-uppercase fw-bold letter-spacing-1 mb-2" style="font-size: 0.85rem;">
                <i class="fa fa-bullhorn me-1"></i> Tsigiro Talent Network
            </p>
            <h1 class="display-5 fw-bold text-white mb-3">Published Job Vacancies &amp; Open Positions</h1>
            <p class="lead text-white-50 mx-auto mb-4" style="max-width: 720px;">
                Explore current career openings across Operations, IT &amp; Engineering, Finance &amp; Compliance, and Project Advisory.
            </p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-light rounded-pill px-4">
                    <i class="fa fa-arrow-left me-1"></i> Back to Opportunities Overview
                </a>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: #f8fafc;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold text-dark mb-0"><i class="fa fa-briefcase text-warning me-2"></i> Current Positions</h4>
                </div>
                <div>
                    <button type="button" class="btn btn-warning text-dark fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#jobAlertModal">
                        <i class="fa fa-bell me-1"></i> Save Search &amp; Get Job Alerts
                    </button>
                </div>
            </div>

            <!-- Search & Filters -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
                <form method="GET" action="<?= $siteConfig->siteUrl; ?>/opportunities/vacancies" class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                            <input type="search" name="search" value="<?= htmlspecialchars($search); ?>" class="form-control border-start-0" placeholder="Search job title, skills, keywords...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="dept" class="form-select" onchange="this.form.submit()">
                            <option value="0">All Departments</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d->iD; ?>" <?= $selectedDept === (int)$d->iD ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($d->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 fw-semibold rounded-3">Search</button>
                        <?php if ($search || $selectedDept): ?>
                            <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancies" class="btn btn-outline-secondary rounded-3" title="Clear Filters"><i class="fa fa-rotate-left"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Vacancy Cards Grid -->
            <div class="row g-4">
                <?php if (empty($vacancies)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-briefcase fa-3x text-secondary opacity-50 mb-3"></i>
                        <h4 class="fw-bold text-dark">No Published Openings Found</h4>
                        <p class="text-muted">We do not have active openings matching your criteria right now. Check back soon or register your interest on our general roster.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-warning text-dark fw-bold rounded-pill px-4 mt-2">
                            Explore General Roster Tracks
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($vacancies as $v):
                        $dept = $v->department();
                        $engagement = $v->engagementbasis();
                        $location = $v->worklocationpreference();
                        $skills = $v->skills();
                        $days = $v->daysRemaining();
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white d-flex flex-column justify-content-between" style="border-top: 4px solid <?= $v->is_featured ? '#FFCC00' : '#2A114B'; ?> !important;">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <code class="small text-muted fw-bold"><?= htmlspecialchars($v->reference_number); ?></code>
                                        <?php if ($v->is_featured): ?>
                                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.7rem;">Featured</span>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="fw-bold mb-2">
                                        <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancy/<?= urlencode($v->slug); ?>" class="text-dark text-decoration-none hover-primary">
                                            <?= htmlspecialchars($v->title); ?>
                                        </a>
                                    </h5>
                                    <div class="small text-secondary fw-semibold mb-2">
                                        <i class="fa fa-building me-1"></i> <?= htmlspecialchars($dept ? $dept->name : 'Operations'); ?>
                                    </div>
                                    <p class="small text-muted mb-3" style="line-height: 1.6;">
                                        <?= htmlspecialchars(mb_strimwidth($v->summary, 0, 140, '...')); ?>
                                    </p>

                                    <?php if (!empty($skills)): ?>
                                        <div class="d-flex flex-wrap gap-1 mb-3">
                                            <?php foreach (array_slice($skills, 0, 3) as $sk): ?>
                                                <span class="badge bg-light text-dark border small"><?= htmlspecialchars($sk['name']); ?></span>
                                            <?php endforeach; ?>
                                            <?php if (count($skills) > 3): ?>
                                                <span class="badge bg-light text-muted border small">+<?= count($skills) - 3; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="pt-3 border-top mt-2">
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                                        <span><i class="fa fa-location-dot text-danger me-1"></i> <?= htmlspecialchars($location ? $location->name : 'Harare'); ?></span>
                                        <span class="text-success fw-semibold"><i class="fa fa-clock me-1"></i> <?= $days; ?> days left</span>
                                    </div>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancy/<?= urlencode($v->slug); ?>" class="btn btn-warning text-dark fw-bold w-100 rounded-pill shadow-sm">
                                        View Details &amp; Apply <i class="fa fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<!-- Job Alert Subscription Modal -->
<div class="modal fade" id="jobAlertModal" tabindex="-1" aria-labelledby="jobAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-1">
                        <i class="fa fa-bell me-1"></i> Vacancy Match Alerts
                    </span>
                    <h5 class="modal-title fw-bold text-dark" id="jobAlertModalLabel">Save Search &amp; Never Miss a Role</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="text-muted small mb-4">
                    Receive instant notifications directly to your inbox and candidate portal when verified vacancies matching your skills and preferences are published.
                </p>
                <form id="jobAlertForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Target Keywords or Role Title</label>
                        <input type="text" name="keywords" class="form-control rounded-3" value="<?= htmlspecialchars($search); ?>" placeholder="e.g. Finance Officer, Full Stack, Project Manager">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Department Preference</label>
                        <select name="department" class="form-select rounded-3">
                            <option value="">Any Department</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d->iD; ?>" <?= $selectedDept === (int)$d->iD ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($d->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-dark">Your Name</label>
                            <input type="text" name="name" class="form-control rounded-3" placeholder="Full name">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold text-dark">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-3" required placeholder="name@example.com">
                        </div>
                    </div>
                    <div id="alertFeedback" class="alert d-none rounded-3 small"></div>
                    <div class="d-grid mt-4">
                        <button type="submit" id="btnSubmitAlert" class="btn btn-warning text-dark fw-bold rounded-pill py-2">
                            <i class="fa fa-bell me-1"></i> Subscribe to Job Alerts
                        </button>
                    </div>
                    <div class="text-center mt-3">
                        <span class="text-muted" style="font-size: 0.75rem;">
                            <i class="fa fa-lock me-1"></i> 1-click unsubscribe at any time. We never spam.
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const alertForm = document.getElementById('jobAlertForm');
    if (!alertForm) return;

    alertForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitAlert');
        const fb = document.getElementById('alertFeedback');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
        fb.className = 'alert d-none rounded-3 small';

        const formData = new FormData(alertForm);

        fetch('<?= $siteConfig->siteUrl; ?>/opportunities/alerts/subscribe', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-bell me-1"></i> Subscribe to Job Alerts';
            fb.classList.remove('d-none');
            if (data.status === 1) {
                fb.classList.add('alert-success');
                fb.innerHTML = '<i class="fa fa-check-circle me-1"></i> ' + data.msg;
                setTimeout(() => {
                    const modalEl = document.getElementById('jobAlertModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    alertForm.reset();
                }, 2000);
            } else {
                fb.classList.add('alert-danger');
                fb.innerHTML = '<i class="fa fa-triangle-exclamation me-1"></i> ' + (data.msg || 'Subscription failed.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-bell me-1"></i> Subscribe to Job Alerts';
            fb.classList.remove('d-none');
            fb.classList.add('alert-danger');
            fb.innerHTML = 'Network error: ' + err;
        });
    });
});
</script>

