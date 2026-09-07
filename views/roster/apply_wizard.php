@extends('layouts.main')

<?php
global $siteConfig;

$track = $data['track'];
$app = $data['application'];
$trackCode = $track->code;
$appId = $app ? (int)$app->iD : 0;
$appProfile = $app ? $app->apprenticeProfile() : null;
$assocProfile = $app ? $app->associateProfile() : null;
$judgement = $app ? $app->judgementResponse() : null;
$skills = $app ? $app->skills() : [];
$qualifications = $app ? $app->qualifications() : [];
$workHistories = $app ? $app->workHistories() : [];
$referees = $app ? $app->referees() : [];

$existingSkillsMap = [];
foreach ($skills as $sk) {
    $existingSkillsMap[$sk->skillitem] = $sk->proficiencylevel;
}
?>

<main class="portal-dashboard">
    <section class="portal-dashboard-header py-3">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker mb-1"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" style="color:rgba(255,255,255,0.7); text-decoration:none;"><i class="fa fa-arrow-left me-1"></i> Dashboard</a> &rsaquo; Talent Roster</p>
                <h1 class="h3 mb-0">Apply as <?= htmlspecialchars($track->name); ?></h1>
            </div>
            <div class="portal-account-summary py-2 px-3">
                <span>Track</span>
                <strong class="h6 mb-0"><?= strtoupper($track->name); ?> ROSTER</strong>
            </div>
        </div>
    </section>

    <!-- Compact & Sticky Stepper + Progress Bar -->
    <div class="sticky-top bg-white border-bottom shadow-sm mb-4" style="top: 0; z-index: 1025;">
        <div class="container py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <!-- Compact Tab Pills -->
                <ul class="nav nav-pills nav-fill flex-nowrap overflow-auto gap-1" id="wizardSteps" role="tablist" style="max-width: 100%;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-1 px-2 px-md-3 fw-bold small text-nowrap" id="step1-tab" data-bs-toggle="tab" data-bs-target="#step1" type="button" role="tab">
                            1. Identity <span class="badge bg-secondary ms-1" id="step1_badge">0/6</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1 px-2 px-md-3 fw-bold small text-nowrap" id="step2-tab" data-bs-toggle="tab" data-bs-target="#step2" type="button" role="tab">
                            2. Education <span class="badge bg-secondary ms-1" id="step2_badge">0/4</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1 px-2 px-md-3 fw-bold small text-nowrap" id="step3-tab" data-bs-toggle="tab" data-bs-target="#step3" type="button" role="tab">
                            3. Skills <span class="badge bg-secondary ms-1" id="step3_badge">0/3</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1 px-2 px-md-3 fw-bold small text-nowrap" id="step4-tab" data-bs-toggle="tab" data-bs-target="#step4" type="button" role="tab">
                            4. Experience <span class="badge bg-secondary ms-1" id="step4_badge">0/4</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1 px-2 px-md-3 fw-bold small text-nowrap" id="step5-tab" data-bs-toggle="tab" data-bs-target="#step5" type="button" role="tab">
                            5. Consent <span class="badge bg-secondary ms-1" id="step5_badge">0/4</span>
                        </button>
                    </li>
                </ul>

                <!-- Progress Metric & Autosave Badge -->
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <span id="autosave_badge" class="badge bg-light text-muted border px-2 py-1 small fw-normal">
                        <i class="fa fa-cloud me-1"></i> Auto-save ready
                    </span>
                    <strong class="text-primary small" id="progress_percent_label">0%</strong>
                    <span class="text-muted small d-none d-md-inline" id="progress_counts_label">(0/0)</span>
                </div>
            </div>
            <!-- Slim 4px Progress Bar -->
            <div class="progress" style="height: 4px; border-radius: 2px; background-color: #e9ecef;">
                <div id="form_progress_bar" class="progress-bar bg-warning" role="progressbar" style="width: 0%;"></div>
            </div>
        </div>
    </div>

    <section class="portal-dashboard-body py-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    
                    <div id="form_alert" style="display:none;" class="alert mb-4"></div>

                    <form id="roster_wizard_form" enctype="multipart/form-data" method="POST" novalidate>
                        <input type="hidden" name="application_id" id="application_id" value="<?= $appId; ?>">
                        <input type="hidden" name="applicationtrack" value="<?= $track->iD; ?>">
                        <input type="hidden" name="is_submit" id="is_submit_flag" value="0">

                        <!-- Tab Content -->
                        <div class="tab-content" id="wizardContent">
                            
                            <!-- STEP 1: IDENTITY & CONTACT -->
                            <div class="tab-pane fade show active" id="step1" role="tabpanel">
                                <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                                    <div class="card-header bg-white py-3 border-bottom">
                                        <h4 class="mb-0 text-dark h5"><i class="fa fa-user-circle text-primary me-2"></i> Section 1 & 2: Identity, Location & Eligibility</h4>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Full Legal Name (as on National ID) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control track-progress req-field" name="legal_name" data-label="Full Legal Name" value="<?= htmlspecialchars($app->legal_name ?? $data['user']->name ?? ''); ?>" required placeholder="e.g. Tendai Samuel Moyo">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Preferred Name (What we should call you)</label>
                                                <input type="text" class="form-control track-progress" name="preferred_name" value="<?= htmlspecialchars($app->preferred_name ?? ''); ?>" placeholder="e.g. Tendai">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control track-progress req-field email-field" name="email" data-label="Email Address" value="<?= htmlspecialchars($app->email ?? $data['user']->email ?? ''); ?>" required placeholder="you@example.com">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">Mobile Number (+263) <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control track-progress req-field" name="mobile_number" data-label="Mobile Number" value="<?= htmlspecialchars($app->mobile_number ?? ''); ?>" required placeholder="+263 77 123 4567">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">WhatsApp Number (if different)</label>
                                                <input type="tel" class="form-control track-progress" name="whatsapp_number" value="<?= htmlspecialchars($app->whatsapp_number ?? ''); ?>" placeholder="+263 77 123 4567">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Date of Birth</label>
                                                <input type="date" class="form-control track-progress" name="date_of_birth" value="<?= htmlspecialchars($app->date_of_birth ?? ''); ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Gender</label>
                                                <select class="form-select track-progress" name="gender">
                                                    <option value="">Select Gender...</option>
                                                    <?php foreach ($data['genders'] as $g): ?>
                                                        <option value="<?= $g->iD; ?>" <?= ($app && (int)$app->gender === (int)$g->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($g->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Right to Work in Zimbabwe <span class="text-danger">*</span></label>
                                                <select class="form-select track-progress req-field" name="workrightstatus" data-label="Right to Work" required>
                                                    <?php foreach ($data['workRights'] as $wr): ?>
                                                        <option value="<?= $wr->iD; ?>" <?= ($app && (int)$app->workrightstatus === (int)$wr->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($wr->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Town / City of Residence <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control track-progress req-field" name="city" data-label="Town/City" value="<?= htmlspecialchars($app->city ?? 'Harare'); ?>" required placeholder="e.g. Harare, Bulawayo">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Suburb / Area</label>
                                                <input type="text" class="form-control track-progress" name="suburb" value="<?= htmlspecialchars($app->suburb ?? ''); ?>" placeholder="e.g. Avondale, Belmont">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Province <span class="text-danger">*</span></label>
                                                <select class="form-select track-progress req-field" name="zimprovince" data-label="Province" required>
                                                    <?php foreach ($data['provinces'] as $prv): ?>
                                                        <option value="<?= $prv->iD; ?>" <?= ($app && (int)$app->zimprovince === (int)$prv->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($prv->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">How did you hear about Trainit / Tsigiro?</label>
                                                <input type="text" class="form-control track-progress" name="how_heard" value="<?= htmlspecialchars($app->how_heard ?? ''); ?>" placeholder="e.g. University careers office, LinkedIn, WhatsApp, Referral">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">If referred, by whom?</label>
                                                <input type="text" class="form-control track-progress" name="referred_by" value="<?= htmlspecialchars($app->referred_by ?? ''); ?>" placeholder="Referee name or organisation">
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="has_disability_adjustment" id="disabilityCheck" value="1" <?= ($app && $app->has_disability_adjustment) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label fw-bold" for="disabilityCheck">
                                                        Do you have a health condition or disability for which you would like us to make workplace or recruitment adjustments?
                                                    </label>
                                                    <small class="form-text text-muted d-block">This does not affect your evaluation; it ensures we accommodate your working requirements.</small>
                                                </div>
                                                <div id="adjustmentDetailsDiv" class="mt-2 <?= ($app && $app->has_disability_adjustment) ? '' : 'd-none'; ?>">
                                                    <textarea class="form-control track-progress" name="adjustment_details" rows="2" placeholder="Describe any workplace or assessment adjustments that would assist you..."><?= htmlspecialchars($app->adjustment_details ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light text-end py-3">
                                        <button type="button" class="btn btn-outline-secondary me-2 btn-save-manual"><i class="fa fa-save me-1"></i> Save Now</button>
                                        <button type="button" class="btn btn-primary btn-next-tab" data-next="step2">Next: Education & Background <i class="fa fa-arrow-right ms-1"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 2: EDUCATION & ATTACHMENT / BACKGROUND -->
                            <div class="tab-pane fade" id="step2" role="tabpanel">
                                <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                                    <div class="card-header bg-white py-3 border-bottom">
                                        <h4 class="mb-0 text-dark h5"><i class="fa fa-graduation-cap text-primary me-2"></i> Section 3: Education <?= $trackCode === 'apprentice' ? '& Institutional Attachment (WRL)' : '& Qualifications'; ?></h4>
                                    </div>
                                    <div class="card-body p-4">
                                        
                                        <?php if ($trackCode === 'apprentice'): ?>
                                        <!-- Apprentice Specific Academic & Attachment Branch -->
                                        <div class="p-3 bg-light rounded mb-4 border">
                                            <h5 class="text-success fw-bold mb-3 h6"><i class="fa fa-university me-2"></i> Apprentice Academic & Attachment Details</h5>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Current Student / Graduate Status <span class="text-danger">*</span></label>
                                                    <select class="form-select track-progress req-field" name="apprenticestatus" data-label="Student/Graduate Status">
                                                        <?php foreach ($data['apprenticeStatuses'] as $ast): ?>
                                                            <option value="<?= $ast->iD; ?>" <?= ($appProfile && (int)$appProfile->apprenticestatus === (int)$ast->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($ast->name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">University / College / Polytechnic <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control track-progress req-field" name="institution_name" data-label="University/College" value="<?= htmlspecialchars($appProfile->institution_name ?? ''); ?>" placeholder="e.g. University of Zimbabwe, NUST, HIT, MSU, Harare Poly">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-bold">Programme / Degree Title <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control track-progress req-field" name="degree_programme" data-label="Programme Title" value="<?= htmlspecialchars($appProfile->degree_programme ?? ''); ?>" placeholder="e.g. BSc Computer Science, BCom Accounting">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold">Level / Year of Study</label>
                                                    <input type="text" class="form-control track-progress" name="study_level" value="<?= htmlspecialchars($appProfile->study_level ?? ''); ?>" placeholder="e.g. Part 3 / Year 3">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold">Student Registration Number</label>
                                                    <input type="text" class="form-control track-progress" name="student_reg_number" value="<?= htmlspecialchars($appProfile->student_reg_number ?? ''); ?>" placeholder="e.g. R214567X">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Expected Completion / Graduation Date</label>
                                                    <input type="date" class="form-control track-progress" name="expected_completion_date" value="<?= htmlspecialchars($appProfile->expected_completion_date ?? ''); ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Cumulative Average Grade / Class</label>
                                                    <input type="text" class="form-control track-progress" name="current_average_grade" value="<?= htmlspecialchars($appProfile->current_average_grade ?? ''); ?>" placeholder="e.g. 2.1 (Upper Second) / 74%">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check mt-4">
                                                        <input class="form-check-input" type="checkbox" name="is_wrl_attachment" id="wrlCheck" value="1" <?= ($appProfile && $appProfile->is_wrl_attachment) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label fw-bold" for="wrlCheck">
                                                            This application is for Industrial Attachment (WRL)
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="wrlSection" class="mt-3 p-3 bg-white rounded border <?= ($appProfile && $appProfile->is_wrl_attachment) ? '' : 'd-none'; ?>">
                                                <h6 class="fw-bold text-dark mb-2">Institutional WRL Requirements</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Earliest Attachment Start Date</label>
                                                        <input type="date" class="form-control form-control-sm track-progress" name="wrl_start_date" value="<?= htmlspecialchars($appProfile->wrl_start_date ?? ''); ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Attachment End Date</label>
                                                        <input type="date" class="form-control form-control-sm track-progress" name="wrl_end_date" value="<?= htmlspecialchars($appProfile->wrl_end_date ?? ''); ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Required Duration (Months)</label>
                                                        <select class="form-select form-select-sm track-progress" name="wrl_duration_months">
                                                            <option value="6" <?= ($appProfile && $appProfile->wrl_duration_months == 6) ? 'selected' : ''; ?>>6 Months</option>
                                                            <option value="8" <?= ($appProfile && $appProfile->wrl_duration_months == 8) ? 'selected' : ''; ?>>8 Months</option>
                                                            <option value="10" <?= ($appProfile && $appProfile->wrl_duration_months == 10) ? 'selected' : ''; ?>>10 Months</option>
                                                            <option value="12" <?= (!$appProfile || $appProfile->wrl_duration_months == 12) ? 'selected' : ''; ?>>12 Months</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Attachment Coordinator Name</label>
                                                        <input type="text" class="form-control form-control-sm track-progress" name="wrl_coordinator_name" value="<?= htmlspecialchars($appProfile->wrl_coordinator_name ?? ''); ?>" placeholder="Lecturer / Coordinator">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Coordinator Email</label>
                                                        <input type="email" class="form-control form-control-sm track-progress email-field" name="wrl_coordinator_email" value="<?= htmlspecialchars($appProfile->wrl_coordinator_email ?? ''); ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Coordinator Phone</label>
                                                        <input type="tel" class="form-control form-control-sm track-progress" name="wrl_coordinator_phone" value="<?= htmlspecialchars($appProfile->wrl_coordinator_phone ?? ''); ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Upload Proof of Registration / WRL Letter (PDF/JPG, Max 5MB)</label>
                                                        <input type="file" class="form-control form-control-sm" name="proof_of_registration_doc">
                                                        <?php if ($appProfile && $appProfile->proof_of_registration_doc): ?>
                                                            <small class="text-success"><i class="fa fa-check"></i> Current file: <?= htmlspecialchars($appProfile->proof_of_registration_doc); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Upload Academic Transcript / Results (PDF/JPG, Max 5MB)</label>
                                                        <input type="file" class="form-control form-control-sm" name="transcript_doc">
                                                        <?php if ($appProfile && $appProfile->transcript_doc): ?>
                                                            <small class="text-success"><i class="fa fa-check"></i> Current file: <?= htmlspecialchars($appProfile->transcript_doc); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Qualifications Repeatable Block (All / Associate) -->
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="fw-bold mb-0 h6">Qualifications & Certifications</h5>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="btn_add_qual"><i class="fa fa-plus me-1"></i> Add Qualification</button>
                                            </div>
                                            <div id="qualifications_container">
                                                <?php if (empty($qualifications)): ?>
                                                    <div class="row g-2 mb-2 qual-row p-2 border rounded bg-light">
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control form-control-sm track-progress" name="qual_title[]" placeholder="Qualification Title (e.g. BSc Computer Science, ACCA)">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <select class="form-select form-select-sm track-progress" name="qual_type[]">
                                                                <option value="">Level / Type...</option>
                                                                <?php foreach ($data['qualificationTypes'] as $qt): ?>
                                                                    <option value="<?= $qt->iD; ?>"><?= htmlspecialchars($qt->name); ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control form-control-sm track-progress" name="qual_inst[]" placeholder="Institution / Body">
                                                        </div>
                                                        <div class="col-md-2 d-flex">
                                                            <input type="date" class="form-control form-control-sm me-1 track-progress" name="qual_date[]" title="Date Obtained">
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <?php foreach ($qualifications as $q): ?>
                                                        <div class="row g-2 mb-2 qual-row p-2 border rounded bg-light">
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm track-progress" name="qual_title[]" value="<?= htmlspecialchars($q->title); ?>" placeholder="Qualification Title">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <select class="form-select form-select-sm track-progress" name="qual_type[]">
                                                                    <option value="">Level / Type...</option>
                                                                    <?php foreach ($data['qualificationTypes'] as $qt): ?>
                                                                        <option value="<?= $qt->iD; ?>" <?= ((int)$q->qualificationtype === (int)$qt->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($qt->name); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control form-control-sm track-progress" name="qual_inst[]" value="<?= htmlspecialchars($q->institution_name ?? ''); ?>" placeholder="Institution / Body">
                                                            </div>
                                                            <div class="col-md-2 d-flex">
                                                                <input type="date" class="form-control form-control-sm me-1 track-progress" name="qual_date[]" value="<?= htmlspecialchars($q->date_obtained ?? ''); ?>">
                                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fa fa-times"></i></button>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer bg-light d-flex justify-content-between py-3">
                                        <button type="button" class="btn btn-outline-secondary btn-prev-tab" data-prev="step1"><i class="fa fa-arrow-left me-1"></i> Back</button>
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary me-2 btn-save-manual"><i class="fa fa-save me-1"></i> Save Now</button>
                                            <button type="button" class="btn btn-primary btn-next-tab" data-next="step3">Next: Function & Skills <i class="fa fa-arrow-right ms-1"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: SERVICE FUNCTION & 1-5 SKILLS MATRIX -->
                            <div class="tab-pane fade" id="step3" role="tabpanel">
                                <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                                    <div class="card-header bg-white py-3 border-bottom">
                                        <h4 class="mb-0 text-dark h5"><i class="fa fa-cogs text-primary me-2"></i> Section 4: Service Functions & Anchored Skills Matrix</h4>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Primary Service Function <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-lg track-progress req-field" name="primaryfunction" id="primaryfunction_select" data-label="Primary Service Function" required>
                                                    <?php foreach ($data['serviceFunctions'] as $sf): ?>
                                                        <option value="<?= $sf->iD; ?>" <?= ($app && (int)$app->primaryfunction === (int)$sf->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($sf->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <small class="form-text text-muted">Select the principal area in which you deliver client work.</small>
                                            </div>
                                        </div>

                                        <!-- Anchored Scale Explainer Box -->
                                        <div class="alert alert-info border-0 shadow-sm p-3 mb-4" style="border-radius: 8px;">
                                            <h6 class="fw-bold mb-2 small"><i class="fa fa-info-circle me-1"></i> Standard 1–5 Anchored Competency Scale</h6>
                                            <div class="row text-center g-2 small">
                                                <div class="col"><span class="badge bg-secondary d-block p-2">1 - Aware<br><small>Concepts only</small></span></div>
                                                <div class="col"><span class="badge bg-info text-dark d-block p-2">2 - Assisted<br><small>With guidance</small></span></div>
                                                <div class="col"><span class="badge bg-primary d-block p-2">3 - Independent<br><small>End-to-end</small></span></div>
                                                <div class="col"><span class="badge bg-dark d-block p-2">4 - Expert<br><small>Complex cases</small></span></div>
                                                <div class="col"><span class="badge bg-success d-block p-2">5 - Can Train<br><small>Mentors others</small></span></div>
                                            </div>
                                        </div>

                                        <!-- Dynamic Skills Matrix Container -->
                                        <div class="mb-4">
                                            <h5 class="fw-bold text-dark mb-3 h6">Rate Your Competency on Specific Skill Items:</h5>
                                            <div id="skills_matrix_area" class="border rounded p-3 bg-light">
                                                <!-- Populated dynamically via JS API -->
                                                <div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin me-2"></i> Loading skills matrix...</div>
                                            </div>
                                        </div>

                                        <!-- Section 4.4 High-Signal Evidence Narrative -->
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Section 4.4 Deliverable Evidence (Highest Signal Item) <span class="text-danger">*</span></label>
                                            <p class="small text-muted mb-1">Describe ONE concrete piece of work in your primary function that you delivered end to end. What was the task, what exactly did you do, and what was the verifiable result? (Max 200 words)</p>
                                            <textarea class="form-control track-progress req-field" name="primary_function_evidence" data-label="Section 4.4 Deliverable Evidence" rows="4" maxlength="1500" required placeholder="Outline context, your specific contribution, tools used, and measurable outcome..."><?= htmlspecialchars($judgement->primary_function_evidence ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light d-flex justify-content-between py-3">
                                        <button type="button" class="btn btn-outline-secondary btn-prev-tab" data-prev="step2"><i class="fa fa-arrow-left me-1"></i> Back</button>
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary me-2 btn-save-manual"><i class="fa fa-save me-1"></i> Save Now</button>
                                            <button type="button" class="btn btn-primary btn-next-tab" data-next="step4">Next: Experience & Terms <i class="fa fa-arrow-right ms-1"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 4: EXPERIENCE & COMMERCIAL TERMS -->
                            <div class="tab-pane fade" id="step4" role="tabpanel">
                                <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                                    <div class="card-header bg-white py-3 border-bottom">
                                        <h4 class="mb-0 text-dark h5"><i class="fa fa-briefcase text-primary me-2"></i> Section 5 & 6: Experience, Availability & Terms</h4>
                                    </div>
                                    <div class="card-body p-4">
                                        
                                        <?php if ($trackCode === 'associate'): ?>
                                        <!-- Associate Specific Compliance & Seniority -->
                                        <div class="p-3 bg-light rounded mb-4 border">
                                            <h5 class="text-primary fw-bold mb-3 h6"><i class="fa fa-award me-2"></i> Associate Experience & Scale Handled</h5>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Current Employment Status <span class="text-danger">*</span></label>
                                                    <select class="form-select track-progress req-field" name="employmentstatus" data-label="Employment Status">
                                                        <?php foreach ($data['employmentStatuses'] as $es): ?>
                                                            <option value="<?= $es->iD; ?>" <?= ($assocProfile && (int)$assocProfile->employmentstatus === (int)$es->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($es->name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Years of Professional Experience</label>
                                                    <select class="form-select track-progress" name="years_experience">
                                                        <option value="1-2" <?= ($assocProfile && $assocProfile->years_experience === '1-2') ? 'selected' : ''; ?>>1–2 Years</option>
                                                        <option value="3-5" <?= ($assocProfile && $assocProfile->years_experience === '3-5') ? 'selected' : ''; ?>>3–5 Years</option>
                                                        <option value="6-10" <?= (!$assocProfile || $assocProfile->years_experience === '6-10') ? 'selected' : ''; ?>>6–10 Years</option>
                                                        <option value="11-15" <?= ($assocProfile && $assocProfile->years_experience === '11-15') ? 'selected' : ''; ?>>11–15 Years</option>
                                                        <option value="15+" <?= ($assocProfile && $assocProfile->years_experience === '15+') ? 'selected' : ''; ?>>15+ Years</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Donor-Funded / NGO Experience</label>
                                                    <select class="form-select track-progress" name="donor_experience_years">
                                                        <option value="none">None yet</option>
                                                        <option value="1-2">1–2 Years</option>
                                                        <option value="3-5" selected>3–5 Years</option>
                                                        <option value="6+">6+ Years</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Indicative Day Rate Expectation (USD) <span class="text-danger">*</span></label>
                                                    <input type="number" step="10" class="form-control track-progress req-field" name="day_rate_expectation" data-label="Day Rate Expectation" value="<?= htmlspecialchars($assocProfile->day_rate_expectation ?? '150'); ?>" required placeholder="e.g. 150">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Monthly Capacity Commitment</label>
                                                    <select class="form-select track-progress" name="capacity_days_per_month">
                                                        <option value="1-3">1–3 Days / Month</option>
                                                        <option value="4-7" selected>4–7 Days / Month</option>
                                                        <option value="8-12">8–12 Days / Month</option>
                                                        <option value="flexible">Varies / On-Demand</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold">Invoicing Entity Type</label>
                                                    <select class="form-select track-progress" name="invoiceentitytype">
                                                        <?php foreach ($data['invoiceEntityTypes'] as $iet): ?>
                                                            <option value="<?= $iet->iD; ?>" <?= ($assocProfile && (int)$assocProfile->invoiceentitytype === (int)$iet->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($iet->name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check mt-3">
                                                        <input class="form-check-input" type="checkbox" name="has_tax_clearance_itf263" id="itfCheck" value="1" <?= ($assocProfile && $assocProfile->has_tax_clearance_itf263) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label fw-bold" for="itfCheck">
                                                            I hold a current valid ZIMRA Tax Clearance Certificate (ITF263)
                                                        </label>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm mt-1 track-progress" name="zimra_bp_number" value="<?= htmlspecialchars($assocProfile->zimra_bp_number ?? ''); ?>" placeholder="ZIMRA Business Partner (BP) Number">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Upload ITF263 Tax Clearance (Optional, PDF/JPG)</label>
                                                    <input type="file" class="form-control form-control-sm" name="tax_clearance_doc">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Conflict of Interest Disclosure</label>
                                                    <textarea class="form-control track-progress" name="conflict_of_interest" rows="2" placeholder="List any organisations you are currently employed by, contracted to, or sitting on the board of that may create an engagement conflict..."><?= htmlspecialchars($assocProfile->conflict_of_interest ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Work History Block -->
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="fw-bold mb-0 h6">Employment & Project History</h5>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="btn_add_work"><i class="fa fa-plus me-1"></i> Add Work Entry</button>
                                            </div>
                                            <div id="work_history_container">
                                                <?php if (empty($workHistories)): ?>
                                                    <div class="p-3 mb-2 border rounded bg-light work-row">
                                                        <div class="row g-2">
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control form-control-sm track-progress" name="work_org[]" placeholder="Organisation Name (e.g. NGO, Enterprise)">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control form-control-sm track-progress" name="work_title[]" placeholder="Position / Role Title">
                                                            </div>
                                                            <div class="col-md-3 d-flex">
                                                                <select class="form-select form-select-sm me-1 track-progress" name="work_basis[]">
                                                                    <?php foreach ($data['engagementBases'] as $eb): ?>
                                                                        <option value="<?= $eb->iD; ?>"><?= htmlspecialchars($eb->name); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fa fa-times"></i></button>
                                                            </div>
                                                            <div class="col-12">
                                                                <textarea class="form-control form-control-sm track-progress" name="work_deliverables[]" rows="2" placeholder="Key deliverables & achievements in this role..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <?php foreach ($workHistories as $w): ?>
                                                        <div class="p-3 mb-2 border rounded bg-light work-row">
                                                            <div class="row g-2">
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control form-control-sm track-progress" name="work_org[]" value="<?= htmlspecialchars($w->organization_name); ?>" placeholder="Organisation Name">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control form-control-sm track-progress" name="work_title[]" value="<?= htmlspecialchars($w->position_title); ?>" placeholder="Position Title">
                                                                </div>
                                                                <div class="col-md-3 d-flex">
                                                                    <select class="form-select form-select-sm me-1 track-progress" name="work_basis[]">
                                                                        <?php foreach ($data['engagementBases'] as $eb): ?>
                                                                            <option value="<?= $eb->iD; ?>" <?= ((int)$w->engagementbasis === (int)$eb->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($eb->name); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fa fa-times"></i></button>
                                                                </div>
                                                                <div class="col-12">
                                                                    <textarea class="form-control form-control-sm track-progress" name="work_deliverables[]" rows="2" placeholder="Key deliverables..."><?= htmlspecialchars($w->key_deliverables ?? ''); ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Referees Block -->
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h5 class="fw-bold mb-0 h6">Referees (Academic & Professional)</h5>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="btn_add_ref"><i class="fa fa-plus me-1"></i> Add Referee</button>
                                            </div>
                                            <div id="referees_container">
                                                <?php if (empty($referees)): ?>
                                                    <div class="p-3 mb-2 border rounded bg-light ref-row">
                                                        <div class="row g-2">
                                                            <div class="col-md-3"><input type="text" class="form-control form-control-sm track-progress" name="referee_name[]" placeholder="Referee Full Name"></div>
                                                            <div class="col-md-3"><input type="text" class="form-control form-control-sm track-progress" name="ref_org[]" placeholder="Organisation / Institution"></div>
                                                            <div class="col-md-2"><input type="text" class="form-control form-control-sm track-progress" name="ref_pos[]" placeholder="Position Title"></div>
                                                            <div class="col-md-2"><input type="text" class="form-control form-control-sm track-progress email-field" name="ref_email[]" placeholder="Email Address"></div>
                                                            <div class="col-md-2 d-flex">
                                                                <input type="tel" class="form-control form-control-sm me-1 track-progress" name="ref_phone[]" placeholder="Phone Number">
                                                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fa fa-times"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <?php foreach ($referees as $rf): ?>
                                                        <div class="p-3 mb-2 border rounded bg-light ref-row">
                                                            <div class="row g-2">
                                                                <div class="col-md-3"><input type="text" class="form-control form-control-sm track-progress" name="referee_name[]" value="<?= htmlspecialchars($rf->referee_name); ?>" placeholder="Referee Name"></div>
                                                                <div class="col-md-3"><input type="text" class="form-control form-control-sm track-progress" name="ref_org[]" value="<?= htmlspecialchars($rf->organization); ?>" placeholder="Organisation"></div>
                                                                <div class="col-md-2"><input type="text" class="form-control form-control-sm track-progress" name="ref_pos[]" value="<?= htmlspecialchars($rf->position); ?>" placeholder="Position"></div>
                                                                <div class="col-md-2"><input type="text" class="form-control form-control-sm track-progress email-field" name="ref_email[]" value="<?= htmlspecialchars($rf->email); ?>" placeholder="Email"></div>
                                                                <div class="col-md-2 d-flex">
                                                                    <input type="tel" class="form-control form-control-sm me-1 track-progress" name="ref_phone[]" value="<?= htmlspecialchars($rf->phone); ?>" placeholder="Phone">
                                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><i class="fa fa-times"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer bg-light d-flex justify-content-between py-3">
                                        <button type="button" class="btn btn-outline-secondary btn-prev-tab" data-prev="step3"><i class="fa fa-arrow-left me-1"></i> Back</button>
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary me-2 btn-save-manual"><i class="fa fa-save me-1"></i> Save Now</button>
                                            <button type="button" class="btn btn-primary btn-next-tab" data-next="step5">Next: Scored Judgement & Consent <i class="fa fa-arrow-right ms-1"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 5: JUDGEMENT SCENARIOS, CONSENT & SUBMIT -->
                            <div class="tab-pane fade" id="step5" role="tabpanel">
                                <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                                    <div class="card-header bg-white py-3 border-bottom">
                                        <h4 class="mb-0 text-dark h5"><i class="fa fa-balance-scale text-primary me-2"></i> Section 9 & 10: Scored Scenarios & Legal Consent</h4>
                                    </div>
                                    <div class="card-body p-4">
                                        
                                        <!-- Motivation -->
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">9.1 Why Trainit, and why a roster model rather than a conventional single-employer job? <span class="text-danger">*</span></label>
                                            <textarea class="form-control track-progress req-field" name="motivation_narrative" data-label="Section 9.1 Motivation" rows="3" maxlength="1000" required placeholder="Explain what attracts you to the roster model and flexible client delivery..."><?= htmlspecialchars($judgement->motivation_narrative ?? ''); ?></textarea>
                                        </div>

                                        <!-- Scenario: 5 Client Balance -->
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">9.2 Workload Balance Scenario <span class="text-danger">*</span></label>
                                            <p class="small text-muted mb-1">In the Shared tier, talent may support up to five client organisations concurrently (at approx. 20% level of effort each). Describe how you organize a normal week so that all five clients remain confident their work is on track.</p>
                                            <textarea class="form-control track-progress req-field" name="shared_client_management_plan" data-label="Section 9.2 Workload Balance" rows="3" maxlength="1200" required placeholder="Describe your scheduling, communication updates, and priority management..."><?= htmlspecialchars($judgement->shared_client_management_plan ?? ''); ?></textarea>
                                        </div>

                                        <!-- Scenario: Friday 16:45 Deadline -->
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">9.4 Deadline & Missing Input Scenario <span class="text-danger">*</span></label>
                                            <p class="small text-muted mb-1">It is 16:45 on Friday. A client emails requesting an urgent report by 08:00 Monday. Completing it requires data only their finance officer can provide, and that officer is on leave until Tuesday. What do you do, in what order?</p>
                                            <textarea class="form-control track-progress req-field" name="urgent_friday_deadline_dilemma" data-label="Section 9.4 Urgent Deadline Scenario" rows="3" maxlength="1000" required placeholder="Outline your step-by-step communication and mitigation actions..."><?= htmlspecialchars($judgement->urgent_friday_deadline_dilemma ?? ''); ?></textarea>
                                        </div>

                                        <?php if ($trackCode === 'associate'): ?>
                                        <!-- Associate Specific Scenarios -->
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">9.5 Quality Assurance Scenario <span class="text-danger">*</span></label>
                                            <p class="small text-muted mb-1">You are reviewing work produced by an apprentice you have never met in person, for a client you do not see weekly. How do you assure quality without becoming a delivery bottleneck?</p>
                                            <textarea class="form-control track-progress" name="associate_apprentice_qa_methodology" rows="3" maxlength="1000" placeholder="Explain your review checkpoints, rubric, and feedback cadence..."><?= htmlspecialchars($judgement->associate_apprentice_qa_methodology ?? ''); ?></textarea>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Section 10: Legal Consent & Data Protection -->
                                        <div class="p-4 bg-light rounded border mt-4">
                                            <h5 class="fw-bold text-dark mb-3 h6"><i class="fa fa-shield-alt text-success me-2"></i> Declarations, Privacy & Electronic Signature</h5>
                                            
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="consent_accuracy" required checked>
                                                <label class="form-check-label small" for="consent_accuracy">
                                                    I confirm that all statements and credentials provided in this application are true and complete. I understand that false or misleading declarations will lead to disqualification or termination from the roster.
                                                </label>
                                            </div>

                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="consent_privacy" required checked>
                                                <label class="form-check-label small" for="consent_privacy">
                                                    <strong>Data Protection Consent:</strong> I consent to Trainit Technologies (Pvt) Ltd t/a Tsigiro processing my data for vetting, talent roster holding, and placement with prospective client organisations in accordance with Zimbabwe's <em>Cyber and Data Protection Act [Chapter 12:07]</em>.
                                                </label>
                                            </div>

                                            <div class="row g-3 mt-2 align-items-center">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-bold small">Type Full Legal Name (Electronic Signature) <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control track-progress req-field" name="e_signature" data-label="Electronic Signature" value="<?= htmlspecialchars($app->e_signature ?? $app->legal_name ?? $data['user']->name ?? ''); ?>" required placeholder="Type your full legal name here">
                                                </div>
                                                <div class="col-md-5">
                                                    <span class="small text-muted d-block mt-4"><i class="fa fa-clock me-1"></i> Timestamp recorded automatically upon submission.</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-footer bg-light d-flex justify-content-between py-3">
                                        <button type="button" class="btn btn-outline-secondary btn-prev-tab" data-prev="step4"><i class="fa fa-arrow-left me-1"></i> Back</button>
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary me-2 btn-save-manual"><i class="fa fa-save me-1"></i> Save Now</button>
                                            <button type="button" class="btn btn-success px-4 fw-bold" id="btn_final_submit"><i class="fa fa-paper-plane me-1"></i> Submit Application</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const siteUrl = '<?= $siteConfig->siteUrl; ?>';
    const form = document.getElementById('roster_wizard_form');
    const alertBox = document.getElementById('form_alert');
    const primaryFuncSelect = document.getElementById('primaryfunction_select');
    const skillsArea = document.getElementById('skills_matrix_area');
    const existingSkills = <?= json_encode($existingSkillsMap); ?>;

    const autosaveBadge = document.getElementById('autosave_badge');
    const progressBar = document.getElementById('form_progress_bar');
    const percentLabel = document.getElementById('progress_percent_label');
    const countsLabel = document.getElementById('progress_counts_label');

    let autoSaveTimer = null;
    let isSaving = false;

    // --- 1. Real-time Progress Tracker ---
    function updateFormProgress() {
        const stepTabs = [
            { id: 'step1', badge: document.getElementById('step1_badge') },
            { id: 'step2', badge: document.getElementById('step2_badge') },
            { id: 'step3', badge: document.getElementById('step3_badge') },
            { id: 'step4', badge: document.getElementById('step4_badge') },
            { id: 'step5', badge: document.getElementById('step5_badge') }
        ];

        let totalFilled = 0;
        let totalCount = 0;

        stepTabs.forEach(step => {
            const container = document.getElementById(step.id);
            if (!container) return;

            const inputs = container.querySelectorAll('input.track-progress, select.track-progress, textarea.track-progress');
            let stepFilled = 0;
            let stepTotal = 0;

            inputs.forEach(input => {
                if (input.closest('.d-none')) return;
                
                stepTotal++;
                totalCount++;

                const val = input.value ? input.value.trim() : '';
                if (val !== '') {
                    stepFilled++;
                    totalFilled++;
                }
            });

            // Skills matrix count
            if (step.id === 'step3') {
                const skillRadios = container.querySelectorAll('input[type="radio"]:checked');
                let validSkills = 0;
                skillRadios.forEach(r => {
                    if (parseInt(r.value, 10) > 0) validSkills++;
                });
                if (validSkills > 0) {
                    stepFilled++;
                    totalFilled++;
                }
                stepTotal++;
                totalCount++;
            }

            // Update Tab Badges
            if (step.badge) {
                if (stepTotal > 0 && stepFilled >= stepTotal) {
                    step.badge.className = 'badge bg-success ms-1';
                    step.badge.innerHTML = '<i class="fa fa-check"></i>';
                } else {
                    step.badge.className = 'badge bg-secondary ms-1';
                    step.badge.textContent = `${stepFilled}/${stepTotal}`;
                }
            }
        });

        const percent = totalCount > 0 ? Math.round((totalFilled / totalCount) * 100) : 0;
        progressBar.style.width = percent + '%';
        progressBar.setAttribute('aria-valuenow', percent);
        percentLabel.textContent = percent + '%';
        countsLabel.textContent = `(${totalFilled}/${totalCount})`;

        if (percent < 40) {
            progressBar.className = 'progress-bar bg-warning';
        } else if (percent < 80) {
            progressBar.className = 'progress-bar bg-primary';
        } else {
            progressBar.className = 'progress-bar bg-success';
        }
    }

    // --- 2. Auto-Save Engine ---
    function triggerAutoSave(isManual = false) {
        if (isSaving) return;

        autosaveBadge.className = 'badge bg-warning text-dark px-2 py-1 small';
        autosaveBadge.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';

        isSaving = true;
        document.getElementById('is_submit_flag').value = '0';
        const formData = new FormData(form);

        fetch(siteUrl + '/dashboard/apply/save', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            isSaving = false;
            if (data.status === 1) {
                if (data.application_id) {
                    document.getElementById('application_id').value = data.application_id;
                    const url = new URL(window.location);
                    url.searchParams.set('id', data.application_id);
                    window.history.replaceState({}, '', url);
                }

                const now = new Date();
                const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                
                autosaveBadge.className = 'badge bg-success text-white px-2 py-1 small';
                autosaveBadge.innerHTML = `<i class="fa fa-check-circle me-1"></i> Saved ${timeStr}`;

                if (isManual) {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML = '<i class="fa fa-check-circle me-2"></i> Application draft saved successfully.';
                    setTimeout(() => alertBox.style.display = 'none', 3000);
                }
            } else {
                autosaveBadge.className = 'badge bg-danger text-white px-2 py-1 small';
                autosaveBadge.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Save error';
            }
        })
        .catch(err => {
            isSaving = false;
            autosaveBadge.className = 'badge bg-secondary text-white px-2 py-1 small';
            autosaveBadge.innerHTML = '<i class="fa fa-wifi me-1"></i> Offline / Queued';
        });
    }

    function queueAutoSave() {
        updateFormProgress();
        autosaveBadge.className = 'badge bg-info text-dark px-2 py-1 small';
        autosaveBadge.innerHTML = '<i class="fa fa-pencil-alt me-1"></i> Unsaved...';
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => triggerAutoSave(false), 1200);
    }

    form.addEventListener('input', queueAutoSave);
    form.addEventListener('change', queueAutoSave);

    // --- 3. Dynamic Skills Loader ---
    function loadSkillsForFunction(funcId) {
        if (!funcId) return;
        skillsArea.innerHTML = '<div class="text-center text-muted py-3"><i class="fa fa-spinner fa-spin me-2"></i> Updating skills list...</div>';
        
        fetch(siteUrl + '/api/service-functions/skills?functions=' + funcId)
            .then(res => res.json())
            .then(items => {
                if (items.length === 0) {
                    skillsArea.innerHTML = '<p class="text-muted mb-0">No specific skills listed for this function yet.</p>';
                    updateFormProgress();
                    return;
                }
                let html = '<div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0">';
                html += '<thead class="table-light"><tr><th style="width:50%;">Skill / Competency Area</th><th style="width:50%;">Your Rating (1–5)</th></tr></thead><tbody>';
                
                items.forEach(it => {
                    const currentVal = existingSkills[it.id] || 0;
                    html += `<tr>
                        <td class="fw-semibold small">${it.name}</td>
                        <td>
                            <div class="btn-group btn-group-sm w-100" role="group">
                                <input type="radio" class="btn-check" name="skills[${it.id}]" id="sk_${it.id}_0" value="0" ${currentVal == 0 ? 'checked' : ''}>
                                <label class="btn btn-outline-secondary" for="sk_${it.id}_0">N/A</label>

                                <input type="radio" class="btn-check" name="skills[${it.id}]" id="sk_${it.id}_1" value="1" ${currentVal == 1 ? 'checked' : ''}>
                                <label class="btn btn-outline-secondary" for="sk_${it.id}_1" title="1 - Aware">1</label>

                                <input type="radio" class="btn-check" name="skills[${it.id}]" id="sk_${it.id}_2" value="2" ${currentVal == 2 ? 'checked' : ''}>
                                <label class="btn btn-outline-info" for="sk_${it.id}_2" title="2 - Assisted">2</label>

                                <input type="radio" class="btn-check" name="skills[${it.id}]" id="sk_${it.id}_3" value="3" ${currentVal == 3 ? 'checked' : ''}>
                                <label class="btn btn-outline-primary" for="sk_${it.id}_3" title="3 - Independent">3</label>

                                <input type="radio" class="btn-check" name="skills[${it.id}]" id="sk_${it.id}_4" value="4" ${currentVal == 4 ? 'checked' : ''}>
                                <label class="btn btn-outline-dark" for="sk_${it.id}_4" title="4 - Expert">4</label>

                                <input type="radio" class="btn-check" name="skills[${it.id}]" id="sk_${it.id}_5" value="5" ${currentVal == 5 ? 'checked' : ''}>
                                <label class="btn btn-outline-success" for="sk_${it.id}_5" title="5 - Master / Can train others">5</label>
                            </div>
                        </td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                skillsArea.innerHTML = html;
                updateFormProgress();
            })
            .catch(err => {
                skillsArea.innerHTML = '<p class="text-danger mb-0">Failed to load skills matrix.</p>';
            });
    }

    if (primaryFuncSelect) {
        primaryFuncSelect.addEventListener('change', function () {
            loadSkillsForFunction(this.value);
            triggerAutoSave(false);
        });
        loadSkillsForFunction(primaryFuncSelect.value);
    }

    // Toggle WRL section
    const wrlCheck = document.getElementById('wrlCheck');
    const wrlSection = document.getElementById('wrlSection');
    if (wrlCheck && wrlSection) {
        wrlCheck.addEventListener('change', function () {
            wrlSection.classList.toggle('d-none', !this.checked);
            updateFormProgress();
            triggerAutoSave(false);
        });
    }

    // Toggle Disability details
    const disabilityCheck = document.getElementById('disabilityCheck');
    const adjustmentDetailsDiv = document.getElementById('adjustmentDetailsDiv');
    if (disabilityCheck && adjustmentDetailsDiv) {
        disabilityCheck.addEventListener('change', function () {
            adjustmentDetailsDiv.classList.toggle('d-none', !this.checked);
            updateFormProgress();
            triggerAutoSave(false);
        });
    }

    // Tab navigation buttons
    document.querySelectorAll('.btn-next-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            triggerAutoSave(false);
            const targetTab = document.getElementById(this.dataset.next + '-tab');
            if (targetTab) {
                bootstrap.Tab.getOrCreateInstance(targetTab).show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });

    document.querySelectorAll('.btn-prev-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            triggerAutoSave(false);
            const targetTab = document.getElementById(this.dataset.prev + '-tab');
            if (targetTab) {
                bootstrap.Tab.getOrCreateInstance(targetTab).show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });

    // Repeatable rows handlers
    document.getElementById('btn_add_qual')?.addEventListener('click', function () {
        const container = document.getElementById('qualifications_container');
        const first = container.querySelector('.qual-row');
        if (first) {
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input').forEach(i => i.value = '');
            container.appendChild(clone);
            updateFormProgress();
        }
    });

    document.getElementById('btn_add_work')?.addEventListener('click', function () {
        const container = document.getElementById('work_history_container');
        const first = container.querySelector('.work-row');
        if (first) {
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input, textarea').forEach(i => i.value = '');
            container.appendChild(clone);
            updateFormProgress();
        }
    });

    document.getElementById('btn_add_ref')?.addEventListener('click', function () {
        const container = document.getElementById('referees_container');
        const first = container.querySelector('.ref-row');
        if (first) {
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input').forEach(i => i.value = '');
            container.appendChild(clone);
            updateFormProgress();
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-row')) {
            const row = e.target.closest('.qual-row, .work-row, .ref-row');
            if (row && row.parentNode.children.length > 1) {
                row.remove();
                updateFormProgress();
                triggerAutoSave(false);
            }
        }
    });

    // Manual Save
    document.querySelectorAll('.btn-save-manual').forEach(btn => {
        btn.addEventListener('click', () => triggerAutoSave(true));
    });

    // --- 4. Cross-Tab Multi-Step Form Validation ---
    function validateFormAcrossTabs() {
        // Clear previous validation styling
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        alertBox.style.display = 'none';

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const stepIds = ['step1', 'step2', 'step3', 'step4', 'step5'];

        for (let i = 0; i < stepIds.length; i++) {
            const stepId = stepIds[i];
            const pane = document.getElementById(stepId);
            if (!pane) continue;

            // 1. Check required fields in this pane
            const reqFields = pane.querySelectorAll('.req-field');
            for (let j = 0; j < reqFields.length; j++) {
                const f = reqFields[j];
                if (f.closest('.d-none')) continue;

                if (!f.value || f.value.trim() === '') {
                    // Activate this step tab
                    const tabBtn = document.getElementById(stepId + '-tab');
                    if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();

                    f.classList.add('is-invalid');
                    f.focus();

                    const fieldName = f.dataset.label || f.name || 'Required field';
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-danger shadow-sm';
                    alertBox.innerHTML = `<i class="fa fa-exclamation-triangle me-2"></i> Please complete: <strong>${fieldName}</strong> on Step ${i + 1}.`;
                    window.scrollTo({ top: f.offsetTop - 120, behavior: 'smooth' });
                    return false;
                }
            }

            // 2. Check typed email fields in this pane
            const emailFields = pane.querySelectorAll('.email-field');
            for (let k = 0; k < emailFields.length; k++) {
                const ef = emailFields[k];
                if (ef.closest('.d-none')) continue;

                const val = ef.value ? ef.value.trim() : '';
                if (val !== '' && !emailRegex.test(val)) {
                    const tabBtn = document.getElementById(stepId + '-tab');
                    if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();

                    ef.classList.add('is-invalid');
                    ef.focus();

                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-danger shadow-sm';
                    alertBox.innerHTML = `<i class="fa fa-exclamation-triangle me-2"></i> Please enter a valid email address for: <strong>${ef.placeholder || ef.name}</strong> on Step ${i + 1}.`;
                    window.scrollTo({ top: ef.offsetTop - 120, behavior: 'smooth' });
                    return false;
                }
            }
        }

        return true;
    }

    // Final Submit Button Click
    document.getElementById('btn_final_submit')?.addEventListener('click', function () {
        if (!validateFormAcrossTabs()) {
            return;
        }

        if (confirm('Are you sure you want to submit your application for formal vetting review?')) {
            document.getElementById('is_submit_flag').value = '1';
            const formData = new FormData(form);
            
            alertBox.style.display = 'block';
            alertBox.className = 'alert alert-info';
            alertBox.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Submitting application for vetting review...';

            fetch(siteUrl + '/dashboard/apply/save', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 1) {
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML = '<i class="fa fa-check-circle me-2"></i> ' + data.msg;
                    setTimeout(() => {
                        window.location.href = siteUrl + '/dashboard/application?id=' + data.application_id;
                    }, 1200);
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-2"></i> ' + data.msg;
                }
            })
            .catch(err => {
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-2"></i> Server error. Please try again.';
            });
        }
    });

    // Initial calculation on load
    updateFormProgress();
});
</script>
