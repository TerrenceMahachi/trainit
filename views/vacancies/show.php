@extends('layouts.main')

<?php
global $siteConfig;
$vacancy    = $data['vacancy'] ?? null;
$dept       = $data['dept'] ?? null;
$engagement = $data['engagement'] ?? null;
$location   = $data['location'] ?? null;
$targetRole = $data['targetRole'] ?? null;
$skills     = $data['skills'] ?? [];
$isStaff    = $data['isStaff'] ?? false;
$currentUser = $data['currentUser'] ?? null;
$existingApplication = $data['existingApplication'] ?? null;
$daysRemaining = $vacancy ? $vacancy->daysRemaining() : 0;
$isClosed = $vacancy ? $vacancy->isClosed() : true;

// Pre-fill fields if candidate is logged in
$prefillFirstName = '';
$prefillLastName = '';
$prefillEmail = '';
$prefillPhone = '';

if ($currentUser) {
    $parts = explode(' ', trim($currentUser->name ?? ''), 2);
    $prefillFirstName = $parts[0] ?? '';
    $prefillLastName = $parts[1] ?? '';
    $prefillEmail = $currentUser->email ?? '';
    $prefillPhone = $currentUser->phone ?? '';
}
?>

<main class="trainit-page vacancy-detail-page">
    <!-- Hero Header -->
    <section class="opportunity-hero py-5" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 60%, #3B1B66 100%); color: #ffffff;">
        <div class="container py-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="text-warning text-decoration-none">Opportunities</a></li>
                    <li class="breadcrumb-item active text-white-50" aria-current="page"><?= htmlspecialchars($vacancy->reference_number ?? ''); ?></li>
                </ol>
            </nav>

            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-1 fs-6">
                            <?= htmlspecialchars($vacancy->reference_number ?? ''); ?>
                        </span>
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1">
                            <i class="fa fa-building me-1"></i> <?= htmlspecialchars($dept ? $dept->name : 'Operations'); ?>
                        </span>
                        <?php if ($vacancy->is_featured): ?>
                            <span class="badge bg-warning text-dark px-3 py-1">
                                <i class="fa fa-star me-1"></i> Featured Position
                            </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="display-6 fw-bold text-white mb-3"><?= htmlspecialchars($vacancy->title ?? ''); ?></h1>
                    <p class="lead text-white-50 mb-4" style="max-width: 700px; font-size: 1.15rem;">
                        <?= htmlspecialchars($vacancy->summary ?? ''); ?>
                    </p>

                    <div class="d-flex flex-wrap gap-3 text-white small">
                        <div><i class="fa fa-briefcase text-warning me-1"></i> <strong><?= htmlspecialchars($engagement ? $engagement->name : 'Full-Time'); ?></strong></div>
                        <div><i class="fa fa-location-dot text-warning me-1"></i> <strong><?= htmlspecialchars($location ? $location->name : 'Harare'); ?></strong></div>
                        <div><i class="fa fa-users text-warning me-1"></i> <strong><?= (int)$vacancy->open_slots; ?> Position<?= $vacancy->open_slots > 1 ? 's' : ''; ?> Available</strong></div>
                        <div>
                            <i class="fa fa-calendar-check text-warning me-1"></i>
                            <strong>Deadline: <?= htmlspecialchars($vacancy->closing_date); ?></strong>
                            <?php if (!$isClosed): ?>
                                <span class="badge bg-success-subtle text-white rounded-pill ms-1">(<?= $daysRemaining; ?> days left)</span>
                            <?php else: ?>
                                <span class="badge bg-danger rounded-pill ms-1">Applications Closed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <?php if (!$isClosed): ?>
                        <a href="#applySection" class="btn btn-warning text-dark fw-bold btn-lg px-4 py-3 rounded-pill shadow-lg">
                            <i class="fa fa-paper-plane me-2"></i> Apply for this Position
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg px-4 py-3 rounded-pill shadow-sm" disabled>
                            <i class="fa fa-lock me-2"></i> Applications Closed
                        </button>
                    <?php endif; ?>

                    <?php if ($isStaff): ?>
                        <div class="mt-3">
                            <a href="<?= $siteConfig->siteUrl; ?>/admin/vacancies/applications/<?= $vacancy->iD; ?>" class="btn btn-sm btn-outline-light rounded-pill px-3">
                                <i class="fa fa-shield-halved me-1"></i> Admin Console (<?= $vacancy->applicationCount(); ?> Applicants)
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content Grid -->
    <section class="py-5" style="background: #f8fafc;">
        <div class="container">
            <div class="row g-4">
                
                <!-- Left Details -->
                <div class="col-lg-7">
                    
                    <!-- Overview & Context -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <h4 class="fw-bold mb-3" style="color: #1C0D30;">
                            <i class="fa fa-circle-info text-primary me-2"></i> Role Strategic Context &amp; Purpose
                        </h4>
                        <div style="color: #334155; line-height: 1.8; font-size: 1.02rem; white-space: pre-wrap;"><?= htmlspecialchars($vacancy->description ?? ''); ?></div>
                    </div>

                    <!-- Responsibilities -->
                    <?php if (!empty($vacancy->responsibilities)): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                            <h4 class="fw-bold mb-3" style="color: #1C0D30;">
                                <i class="fa fa-list-check text-success me-2"></i> Key Responsibilities &amp; Core Deliverables
                            </h4>
                            <div style="color: #334155; line-height: 1.8; font-size: 1.02rem; white-space: pre-wrap;"><?= htmlspecialchars($vacancy->responsibilities); ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Requirements -->
                    <?php if (!empty($vacancy->requirements)): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                            <h4 class="fw-bold mb-3" style="color: #1C0D30;">
                                <i class="fa fa-graduation-cap text-info me-2"></i> Required Qualifications &amp; Prior Experience
                            </h4>
                            <div style="color: #334155; line-height: 1.8; font-size: 1.02rem; white-space: pre-wrap;"><?= htmlspecialchars($vacancy->requirements); ?></div>
                        </div>
                    <?php endif; ?>

                    <!-- Skills & Competencies Tag Matrix -->
                    <?php if (!empty($skills)): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                            <h4 class="fw-bold mb-3" style="color: #1C0D30;">
                                <i class="fa fa-tags text-warning me-2"></i> Key Competencies &amp; Technical Skills
                            </h4>
                            <p class="text-muted small mb-3">Evaluated in candidate screening and structured assessment stages:</p>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($skills as $sk): ?>
                                    <span class="badge <?= $sk['is_mandatory'] ? 'bg-primary' : 'bg-light text-dark border'; ?> px-3 py-2 rounded-pill fs-6 fw-normal">
                                        <?= htmlspecialchars($sk['name']); ?>
                                        <?php if ($sk['is_mandatory']): ?>
                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Required</span>
                                        <?php endif; ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Right Column: Quick Info & Application Form -->
                <div class="col-lg-5">
                    
                    <!-- Quick Overview Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white" style="border-top: 4px solid #FFCC00 !important;">
                        <h5 class="fw-bold mb-3" style="color: #1C0D30;">Position Summary</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Reference:</span>
                                <code class="fw-bold text-dark"><?= htmlspecialchars($vacancy->reference_number); ?></code>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Department:</span>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($dept ? $dept->name : 'Operations'); ?></span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Engagement:</span>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($engagement ? $engagement->name : 'N/A'); ?></span>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">Location:</span>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($location ? $location->name : 'N/A'); ?></span>
                            </li>
                            <?php if ($vacancy->remuneration_display): ?>
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Compensation:</span>
                                    <span class="fw-bold text-success"><?= htmlspecialchars($vacancy->remuneration_display); ?></span>
                                </li>
                            <?php endif; ?>
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-muted">Application Closing:</span>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($vacancy->closing_date); ?></span>
                            </li>
                        </ul>
                    </div>

                    <!-- Application Form Card -->
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white" id="applySection" style="border-top: 4px solid #2A114B !important;">
                        <h4 class="fw-bold mb-1" style="color: #1C0D30;">Submit Your Application</h4>
                        <p class="text-muted small mb-3">Apply directly with your contact details and CV resume file.</p>

                        <?php if ($isClosed): ?>
                            <div class="alert alert-warning rounded-3 mb-0">
                                <i class="fa fa-lock me-1"></i> The application window for this position closed on <?= htmlspecialchars($vacancy->closing_date); ?>. Please check our other openings.
                            </div>
                        <?php elseif ($existingApplication): ?>
                            <!-- Existing Application Card -->
                            <div class="p-3 bg-light rounded-4 border mb-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                        <i class="fa fa-check-circle fa-lg"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0 text-success">Application Already Received</h5>
                                        <p class="text-muted small mb-0">You have already submitted an application for this position.</p>
                                    </div>
                                </div>

                                <div class="bg-white p-3 rounded-3 border mb-3 small">
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Application Number:</span>
                                        <code class="fw-bold text-dark fs-6"><?= htmlspecialchars($existingApplication->application_number); ?></code>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                        <span class="text-muted">Vetting Stage:</span>
                                        <?php
                                        $stRec = $existingApplication->statusRecord();
                                        $badgeCls = $stRec ? $stRec->badge_class : 'bg-info text-dark';
                                        $stName = $stRec ? $stRec->name : 'Application Received';
                                        ?>
                                        <span class="badge <?= $badgeCls; ?> px-2 py-1"><?= htmlspecialchars($stName); ?></span>
                                    </div>
                                    <?php if (!empty($existingApplication->interview_at)): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <span class="text-muted">Interview Date:</span>
                                            <strong class="text-warning"><?= date('D, d M Y @ H:i', strtotime($existingApplication->interview_at)); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Submitted Date:</span>
                                        <span class="fw-semibold text-dark"><?= date('d M Y, H:i', strtotime($existingApplication->reg_date)); ?></span>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="btn btn-warning text-dark fw-bold py-2 rounded-pill shadow-sm">
                                        <i class="fa fa-tachometer-alt me-1"></i> Track Application on Dashboard
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if ($currentUser): ?>
                                <div class="alert alert-info py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2">
                                    <i class="fa fa-user-check text-primary"></i>
                                    <div>
                                        Applying as signed-in candidate: <strong><?= htmlspecialchars($currentUser->name); ?></strong> (<?= htmlspecialchars($currentUser->email); ?>).
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-light border py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2">
                                    <i class="fa fa-id-badge text-warning"></i>
                                    <div class="text-muted">
                                        Applying as a guest? A candidate account will be automatically generated and logged in so you can track your application.
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div id="applyAlert" class="alert d-none mb-3" role="alert"></div>

                            <form id="vacancyApplyForm" method="POST" action="<?= $siteConfig->siteUrl; ?>/opportunities/vacancy/<?= urlencode($vacancy->slug); ?>/apply" enctype="multipart/form-data">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="first_name" class="form-label small fw-bold">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="first_name" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($prefillFirstName); ?>" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="last_name" class="form-label small fw-bold">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" id="last_name" class="form-control form-control-sm rounded-3" value="<?= htmlspecialchars($prefillLastName); ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control form-control-sm rounded-3" placeholder="you@example.com" value="<?= htmlspecialchars($prefillEmail); ?>" <?= $prefillEmail ? 'readonly' : ''; ?> required>
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label small fw-bold">Phone / WhatsApp <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" id="phone" class="form-control form-control-sm rounded-3" placeholder="+263 7..." value="<?= htmlspecialchars($prefillPhone); ?>" required>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="city" class="form-label small fw-bold">Current City <span class="text-danger">*</span></label>
                                        <input type="text" name="city" id="city" class="form-control form-control-sm rounded-3" placeholder="e.g. Harare" required>
                                    </div>
                                    <div class="col-6">
                                        <label for="country" class="form-label small fw-bold">Country</label>
                                        <input type="text" name="country" id="country" class="form-control form-control-sm rounded-3" value="Zimbabwe" required>
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="years_of_experience" class="form-label small fw-bold">Years Experience</label>
                                        <input type="number" name="years_of_experience" id="years_of_experience" class="form-control form-control-sm rounded-3" value="0" min="0">
                                    </div>
                                    <div class="col-6">
                                        <label for="notice_period_days" class="form-label small fw-bold">Notice Period (Days)</label>
                                        <input type="number" name="notice_period_days" id="notice_period_days" class="form-control form-control-sm rounded-3" value="30" min="0">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="highest_qualification" class="form-label small fw-bold">Highest Qualification</label>
                                    <input type="text" name="highest_qualification" id="highest_qualification" class="form-control form-control-sm rounded-3" placeholder="e.g. Bachelor of Business Studies / CIS / CIMA">
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label for="current_job_title" class="form-label small fw-bold">Current Job Title</label>
                                        <input type="text" name="current_job_title" id="current_job_title" class="form-control form-control-sm rounded-3" placeholder="e.g. Finance Associate">
                                    </div>
                                    <div class="col-6">
                                        <label for="expected_salary" class="form-label small fw-bold">Expected Remuneration</label>
                                        <input type="text" name="expected_salary" id="expected_salary" class="form-control form-control-sm rounded-3" placeholder="e.g. USD / month">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="cv" class="form-label small fw-bold">Upload CV / Resume <span class="text-danger">*</span></label>
                                    <input type="file" name="cv" id="cv" class="form-control form-control-sm rounded-3" accept=".pdf,.doc,.docx" required>
                                    <div class="form-text small">Accepted formats: PDF, DOC, DOCX. Max 10MB.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="cover_letter" class="form-label small fw-bold">Cover Note / Motivation</label>
                                    <textarea name="cover_letter" id="cover_letter" rows="3" class="form-control form-control-sm rounded-3" placeholder="Briefly highlight why you are a strong fit for this vacancy..."></textarea>
                                </div>

                                <button type="submit" id="submitAppBtn" class="btn btn-warning text-dark fw-bold w-100 py-2 fs-6 rounded-pill shadow-sm">
                                    <i class="fa fa-paper-plane me-1"></i> Submit Application
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('vacancyApplyForm');
    if (!form) return;

    const alertBox = document.getElementById('applyAlert');
    const btn = document.getElementById('submitAppBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Uploading &amp; Submitting...';

        const fd = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 1) {
                alertBox.className = 'alert alert-success rounded-3 p-3';
                alertBox.innerHTML = `
                    <h5 class="fw-bold mb-1"><i class="fa fa-circle-check text-success me-1"></i> Application Submitted!</h5>
                    <p class="mb-2 small">${data.msg}</p>
                    <div class="p-2 bg-white rounded border small mb-2">
                        <strong>Application Reference:</strong> <code>${data.application_number}</code>
                    </div>
                    ${data.redirect ? `
                    <div class="d-flex align-items-center gap-2 text-dark small fw-bold mt-2 pt-2 border-top">
                        <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                        <span>Redirecting to your dashboard to track your application...</span>
                    </div>
                    ` : ''}
                `;
                alertBox.classList.remove('d-none');
                form.reset();
                btn.style.display = 'none';
                window.scrollTo({ top: alertBox.offsetTop - 80, behavior: 'smooth' });

                if (data.redirect) {
                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 1800);
                }
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-paper-plane me-1"></i> Submit Application';
                alertBox.className = 'alert alert-danger rounded-3 p-3';
                alertBox.innerHTML = '<i class="fa fa-circle-exclamation me-1"></i> ' + (data.msg || 'Submission error.');
                alertBox.classList.remove('d-none');
                window.scrollTo({ top: alertBox.offsetTop - 80, behavior: 'smooth' });
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane me-1"></i> Submit Application';
            alertBox.className = 'alert alert-danger rounded-3 p-3';
            alertBox.innerHTML = '<i class="fa fa-circle-exclamation me-1"></i> Network error submitting your application. Please check your connection and try again.';
            alertBox.classList.remove('d-none');
        });
    });
});
</script>
