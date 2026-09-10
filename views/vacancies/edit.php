@extends('layouts.main')

<?php
global $siteConfig;
$vacancy         = $data['vacancy'] ?? null;
$departments     = $data['departments'] ?? [];
$engagementBases = $data['engagementBases'] ?? [];
$locations       = $data['locations'] ?? [];
$roles           = $data['roles'] ?? [];
$skills          = $data['skills'] ?? [];
$statuses        = $data['statuses'] ?? [];
$linkedSkills    = $data['linkedSkills'] ?? [];
?>

<main class="portal-dashboard">
    <!-- Header -->
    <section class="portal-dashboard-header" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 50%, #3B1B66 100%); color: #fff; padding: 2.5rem 0;">
        <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="text-warning text-decoration-none">Vacancies</a></li>
                        <li class="breadcrumb-item active text-white-50" aria-current="page"><?= htmlspecialchars($vacancy->reference_number ?? 'Edit'); ?></li>
                    </ol>
                </nav>
                <h1 class="h2 fw-bold text-white mb-1">Edit Vacancy: <?= htmlspecialchars($vacancy->title ?? ''); ?></h1>
                <p class="mb-0 text-white-50">Ref: <code><?= htmlspecialchars($vacancy->reference_number ?? ''); ?></code> &bull; Slug: <code><?= htmlspecialchars($vacancy->slug ?? ''); ?></code></p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancy/<?= urlencode($vacancy->slug ?? ''); ?>" target="_blank" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3 shadow-sm">
                    <i class="fa fa-external-link me-1"></i> Preview Public
                </a>
                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies" class="btn btn-outline-light px-3 py-2 fw-semibold rounded-3 shadow-sm">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div id="editVacancyAlert" class="alert d-none mb-4" role="alert"></div>

            <form id="editVacancyForm" method="POST" action="<?= $siteConfig->siteUrl; ?>/admin/vacancies/update" class="row g-4">
                <input type="hidden" name="id" value="<?= (int)($vacancy->iD ?? 0); ?>">

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
                                <input type="text" name="title" id="title" class="form-control form-control-lg" value="<?= htmlspecialchars($vacancy->title ?? ''); ?>" required>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="department" class="form-label fw-bold">Department <span class="text-danger">*</span></label>
                                    <select name="department" id="department" class="form-select" required>
                                        <option value="">-- Select Department --</option>
                                        <?php foreach ($departments as $d): ?>
                                            <option value="<?= $d->iD; ?>" <?= (int)$d->iD === (int)$vacancy->department ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($d->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="target_role" class="form-label fw-bold">Target Staff System Role <span class="text-danger">*</span></label>
                                    <select name="target_role" id="target_role" class="form-select" required>
                                        <?php foreach ($roles as $r): 
                                            if (in_array((int)$r->iD, [1, 5, 6, 7, 8], true)):
                                        ?>
                                            <option value="<?= $r->iD; ?>" <?= (int)$r->iD === (int)$vacancy->target_role ? 'selected' : ''; ?>>
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
                                            <option value="<?= $b->iD; ?>" <?= (int)$b->iD === (int)$vacancy->engagementbasis ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($b->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="worklocationpreference" class="form-label fw-bold">Work Location Preference <span class="text-danger">*</span></label>
                                    <select name="worklocationpreference" id="worklocationpreference" class="form-select" required>
                                        <option value="">-- Select Location --</option>
                                        <?php foreach ($locations as $l): ?>
                                            <option value="<?= $l->iD; ?>" <?= (int)$l->iD === (int)$vacancy->worklocationpreference ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($l->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="summary" class="form-label fw-bold">Executive Summary <span class="text-danger">*</span></label>
                                <textarea name="summary" id="summary" rows="3" class="form-control" required><?= htmlspecialchars($vacancy->summary ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Role Description &amp; Strategic Context <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" rows="6" class="form-control" required><?= htmlspecialchars($vacancy->description ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="responsibilities" class="form-label fw-bold">Key Responsibilities &amp; Deliverables</label>
                                <textarea name="responsibilities" id="responsibilities" rows="5" class="form-control"><?= htmlspecialchars($vacancy->responsibilities ?? ''); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="requirements" class="form-label fw-bold">Required Qualifications &amp; Prior Experience</label>
                                <textarea name="requirements" id="requirements" rows="5" class="form-control"><?= htmlspecialchars($vacancy->requirements ?? ''); ?></textarea>
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
                            <p class="text-muted small mb-3">Select catalogued skills. Check "Mandatory" if the skill is a non-negotiable prerequisite.</p>
                            <div class="row g-2" style="max-height: 320px; overflow-y: auto;">
                                <?php foreach ($skills as $sk): 
                                    $isLinked = isset($linkedSkills[(int)$sk->iD]);
                                    $isMand = $isLinked && $linkedSkills[(int)$sk->iD] === 1;
                                ?>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input" type="checkbox" name="skills[]" value="<?= $sk->iD; ?>" id="skill_<?= $sk->iD; ?>" <?= $isLinked ? 'checked' : ''; ?>>
                                                <label class="form-check-label small fw-semibold" for="skill_<?= $sk->iD; ?>">
                                                    <?= htmlspecialchars($sk->name); ?>
                                                </label>
                                            </div>
                                            <div class="form-check form-switch mb-0" title="Mark as Mandatory">
                                                <input class="form-check-input" type="checkbox" name="mandatory_skills[]" value="<?= $sk->iD; ?>" id="mand_<?= $sk->iD; ?>" <?= $isMand ? 'checked' : ''; ?>>
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
                                <label for="vacancystatus" class="form-label fw-bold">Vacancy Status</label>
                                <select name="vacancystatus" id="vacancystatus" class="form-select">
                                    <?php foreach ($statuses as $st): ?>
                                        <option value="<?= $st->iD; ?>" <?= (int)$st->iD === (int)$vacancy->vacancystatus ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($st->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="publish_date" class="form-label fw-bold">Publication Date</label>
                                <input type="date" name="publish_date" id="publish_date" class="form-control" value="<?= htmlspecialchars($vacancy->publish_date ?? date('Y-m-d')); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="closing_date" class="form-label fw-bold">Application Closing Date <span class="text-danger">*</span></label>
                                <input type="date" name="closing_date" id="closing_date" class="form-control" value="<?= htmlspecialchars($vacancy->closing_date ?? date('Y-m-d', strtotime('+30 days'))); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="open_slots" class="form-label fw-bold">Available Open Slots</label>
                                <input type="number" name="open_slots" id="open_slots" class="form-control" value="<?= (int)($vacancy->open_slots ?? 1); ?>" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="remuneration_display" class="form-label fw-bold">Remuneration / Band (Public)</label>
                                <input type="text" name="remuneration_display" id="remuneration_display" class="form-control" value="<?= htmlspecialchars($vacancy->remuneration_display ?? ''); ?>" placeholder="e.g. Competitive USD + Benefits">
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" <?= !empty($vacancy->is_featured) ? 'checked' : ''; ?>>
                                <label class="form-check-label fw-semibold" for="is_featured">
                                    Feature on Opportunities Top Banner
                                </label>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" id="updateVacancyBtn" class="btn btn-warning text-dark fw-bold py-2 fs-6 rounded-3 shadow-sm">
                                    <i class="fa fa-save me-1"></i> Update Vacancy Details
                                </button>
                                <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/applications/<?= $vacancy->iD; ?>" class="btn btn-outline-primary py-2 rounded-3">
                                    <i class="fa fa-users me-1"></i> View Applications (<?= $vacancy->applicationCount(); ?>)
                                </a>
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
    const form = document.getElementById('editVacancyForm');
    const alertBox = document.getElementById('editVacancyAlert');
    const btn = document.getElementById('updateVacancyBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Updating...';

        const fd = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-save me-1"></i> Update Vacancy Details';

            if (data.status === 1) {
                alertBox.className = 'alert alert-success alert-dismissible fade show';
                alertBox.innerHTML = data.msg;
                alertBox.classList.remove('d-none');
                window.scrollTo({ top: alertBox.offsetTop - 80, behavior: 'smooth' });
                setTimeout(() => {
                    window.location.href = data.redirect || '<?= $siteConfig->siteUrl; ?>/admin/vacancies';
                }, 1000);
            } else {
                alertBox.className = 'alert alert-danger alert-dismissible fade show';
                alertBox.innerHTML = data.msg || 'An error occurred while updating the vacancy.';
                alertBox.classList.remove('d-none');
                window.scrollTo({ top: alertBox.offsetTop - 80, behavior: 'smooth' });
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-save me-1"></i> Update Vacancy Details';
            alertBox.className = 'alert alert-danger alert-dismissible fade show';
            alertBox.innerHTML = 'Network or server error updating vacancy.';
            alertBox.classList.remove('d-none');
        });
    });
});
</script>
