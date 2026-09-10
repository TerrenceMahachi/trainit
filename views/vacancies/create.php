@extends('layouts.main')

<?php
global $siteConfig;
$departments     = $data['departments'] ?? [];
$engagementBases = $data['engagementBases'] ?? [];
$locations       = $data['locations'] ?? [];
$roles           = $data['roles'] ?? [];
$skills          = $data['skills'] ?? [];
?>

<main class="portal-dashboard">
    <!-- Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="text-warning text-decoration-none">Vacancies</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page">New Position</li>
                    </ol>
                </nav>
                <h1 class="h2 fw-bold text-white mb-1">Publish New Job Vacancy</h1>
                <p class="mb-0 text-white-50">Configure normalized job parameters, required competencies, and associate with internal staff onboarding.</p>
            </div>
            <div>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-1"></i> Back to Vacancies
                </a>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div id="createVacancyAlert" class="alert d-none mb-4" role="alert"></div>

            <form id="createVacancyForm" method="POST" action="<?= $siteConfig->siteUrl; ?>/admin/vacancies/store" class="row g-4">
                
                <!-- Left Column: Core Details -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold" style="color: #1C0D30;">
                                <i class="fa fa-briefcase text-primary me-2"></i> Position Specification
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Position Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control form-control-lg" placeholder="e.g. Senior Talent Operations Officer" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="department" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                                    <select name="department" id="department" class="form-select" required>
                                        <option value="">-- Select Department --</option>
                                        <?php foreach ($departments as $d): ?>
                                            <option value="<?= $d->iD; ?>"><?= htmlspecialchars($d->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="target_role" class="form-label fw-bold">Target Staff System Role <span class="text-danger">*</span></label>
                                    <select name="target_role" id="target_role" class="form-select" required>
                                        <?php foreach ($roles as $r): 
                                            if (in_array((int)$r->iD, [1, 5, 6, 7, 8], true)):
                                        ?>
                                            <option value="<?= $r->iD; ?>" <?= (int)$r->iD === 8 ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($r->name); ?> (Role #<?= $r->iD; ?>)
                                            </option>
                                        <?php endif; endforeach; ?>
                                    </select>
                                    <small class="text-muted">The internal role assigned when applicant is appointed to staff.</small>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="engagementbasis" class="form-label fw-bold">Engagement Basis <span class="text-danger">*</span></label>
                                    <select name="engagementbasis" id="engagementbasis" class="form-select" required>
                                        <option value="">-- Select Terms --</option>
                                        <?php foreach ($engagementBases as $b): ?>
                                            <option value="<?= $b->iD; ?>"><?= htmlspecialchars($b->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="worklocationpreference" class="form-label fw-bold">Work Location Preference <span class="text-danger">*</span></label>
                                    <select name="worklocationpreference" id="worklocationpreference" class="form-select" required>
                                        <option value="">-- Select Location --</option>
                                        <?php foreach ($locations as $l): ?>
                                            <option value="<?= $l->iD; ?>"><?= htmlspecialchars($l->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="summary" class="form-label fw-bold">Executive Summary <span class="text-danger">*</span></label>
                                <textarea name="summary" id="summary" rows="3" class="form-control" placeholder="Short 2-3 sentence overview displayed on opportunity cards..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Role Description &amp; Strategic Context <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" rows="6" class="form-control" placeholder="Detailed background about the vacancy, unit goals, and delivery standards..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="responsibilities" class="form-label fw-bold">Key Responsibilities &amp; Deliverables</label>
                                <textarea name="responsibilities" id="responsibilities" rows="5" class="form-control" placeholder="List core daily duties, accountability standards, and KPIs (one per line)..."></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="requirements" class="form-label fw-bold">Required Qualifications &amp; Prior Experience</label>
                                <textarea name="requirements" id="requirements" rows="5" class="form-control" placeholder="Specify education degrees, certifications, years of experience, and essential qualities..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Skills Matrix -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold" style="color: #1C0D30;">
                                <i class="fa fa-network-wired text-success me-2"></i> Competencies &amp; Skills Tagging
                            </h5>
                            <span class="badge bg-light text-dark border">Normalized Junction</span>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-3">Select the catalogued skills relevant to this vacancy. Check "Mandatory" if the skill is a non-negotiable prerequisite.</p>
                            <div class="row g-2" style="max-height: 320px; overflow-y: auto;">
                                <?php foreach ($skills as $sk): ?>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" name="skills[]" value="<?= $sk->iD; ?>" id="skill_<?= $sk->iD; ?>">
                                                <label class="form-check-label small fw-semibold" for="skill_<?= $sk->iD; ?>">
                                                    <?= htmlspecialchars($sk->name); ?>
                                                </label>
                                            </div>
                                            <div class="form-check form-switch mb-0" title="Mark as Mandatory">
                                                <input class="form-check-input" type="checkbox" name="mandatory_skills[]" value="<?= $sk->iD; ?>" id="mand_<?= $sk->iD; ?>">
                                                <label class="form-check-label small text-muted" for="mand_<?= $sk->iD; ?>">Req</label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Settings & Publishing -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="mb-0 fw-bold" style="color: #1C0D30;">
                                <i class="fa fa-gear text-secondary me-2"></i> Publication Settings
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="vacancystatus" class="form-label fw-bold">Initial Status</label>
                                <select name="vacancystatus" id="vacancystatus" class="form-select">
                                    <option value="2" selected>Published (Immediately visible)</option>
                                    <option value="1">Draft (Saved privately)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="publish_date" class="form-label fw-bold">Publication Date</label>
                                <input type="date" name="publish_date" id="publish_date" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="closing_date" class="form-label fw-bold">Application Closing Date <span class="text-danger">*</span></label>
                                <input type="date" name="closing_date" id="closing_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="open_slots" class="form-label fw-bold">Available Open Slots</label>
                                <input type="number" name="open_slots" id="open_slots" class="form-control" value="1" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="remuneration_display" class="form-label fw-bold">Remuneration / Band (Public)</label>
                                <input type="text" name="remuneration_display" id="remuneration_display" class="form-control" placeholder="e.g. Market-competitive USD + Benefits">
                                <small class="text-muted">Visible to applicants on the Opportunities page.</small>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                                <label class="form-check-label fw-semibold" for="is_featured">
                                    Feature on Homepage &amp; Opportunities Top Banner
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" id="saveVacancyBtn" class="btn btn-warning text-dark fw-bold py-2 fs-6 rounded-3 shadow-sm">
                                    <i class="fa fa-check-circle me-1"></i> Save &amp; Publish Vacancy
                                </button>
                                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="btn btn-outline-secondary py-2 rounded-3">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createVacancyForm');
    const alertBox = document.getElementById('createVacancyAlert');
    const btn = document.getElementById('saveVacancyBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';

        const fd = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check-circle me-1"></i> Save &amp; Publish Vacancy';

            if (data.status === 1) {
                alertBox.className = 'alert alert-success alert-dismissible fade show';
                alertBox.innerHTML = data.msg;
                alertBox.classList.remove('d-none');
                window.scrollTo({ top: alertBox.offsetTop - 80, behavior: 'smooth' });
                setTimeout(() => {
                    window.location.href = data.redirect || '<?= $siteConfig->siteUrl; ?>/admin/vacancies';
                }, 1200);
            } else {
                alertBox.className = 'alert alert-danger alert-dismissible fade show';
                alertBox.innerHTML = data.msg || 'An error occurred while saving the vacancy.';
                alertBox.classList.remove('d-none');
                window.scrollTo({ top: alertBox.offsetTop - 80, behavior: 'smooth' });
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check-circle me-1"></i> Save &amp; Publish Vacancy';
            alertBox.className = 'alert alert-danger alert-dismissible fade show';
            alertBox.innerHTML = 'Network or server error submitting vacancy form.';
            alertBox.classList.remove('d-none');
        });
    });
});
</script>
