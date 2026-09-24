@extends('layouts.main')

<?php
global $siteConfig;
$app = $data['app'];
$assessment = $data['assessment'];
$recommendations = $data['recommendations'];
$statuses = $data['statuses'];
$redFlags = $data['redFlags'];
$track = $app->applicationtrack();
$function = $app->primaryfunction();
$skills = $app->skills();
$qualifications = $app->qualifications();
$workHistories = $app->workHistories();
$referees = $app->referees();
$judgement = $app->judgementResponse();
$documents = $app->documents();

$currentStatusCode = $app->applicationstatus() ? $app->applicationstatus()->code : 'submitted';
$currentStatusId = (int)$app->applicationstatus;
$currentRecommendationId = $assessment ? (int)$assessment->vettingrecommendation : 1;

// Define Valid Status Transitions based on Current Status
$statusMap = [];
foreach ($statuses as $st) {
    $statusMap[$st->code] = $st;
}

$validTransitions = [];
switch ($currentStatusCode) {
    case 'draft':
    case 'submitted':
        if (isset($statusMap['screened'])) $validTransitions[] = ['status' => $statusMap['screened'], 'btn' => 'outline-info', 'icon' => 'fa-clipboard-check'];
        if (isset($statusMap['interviewed'])) $validTransitions[] = ['status' => $statusMap['interviewed'], 'btn' => 'outline-primary', 'icon' => 'fa-calendar-alt'];
        if (isset($statusMap['on_roster'])) $validTransitions[] = ['status' => $statusMap['on_roster'], 'btn' => 'outline-success', 'icon' => 'fa-user-check'];
        if (isset($statusMap['talent_pool'])) $validTransitions[] = ['status' => $statusMap['talent_pool'], 'btn' => 'outline-warning', 'icon' => 'fa-archive'];
        if (isset($statusMap['rejected'])) $validTransitions[] = ['status' => $statusMap['rejected'], 'btn' => 'outline-danger', 'icon' => 'fa-ban'];
        break;

    case 'screened':
        if (isset($statusMap['interviewed'])) $validTransitions[] = ['status' => $statusMap['interviewed'], 'btn' => 'outline-primary', 'icon' => 'fa-calendar-alt'];
        if (isset($statusMap['on_roster'])) $validTransitions[] = ['status' => $statusMap['on_roster'], 'btn' => 'outline-success', 'icon' => 'fa-user-check'];
        if (isset($statusMap['talent_pool'])) $validTransitions[] = ['status' => $statusMap['talent_pool'], 'btn' => 'outline-warning', 'icon' => 'fa-archive'];
        if (isset($statusMap['rejected'])) $validTransitions[] = ['status' => $statusMap['rejected'], 'btn' => 'outline-danger', 'icon' => 'fa-ban'];
        break;

    case 'interviewed':
        if (isset($statusMap['on_roster'])) $validTransitions[] = ['status' => $statusMap['on_roster'], 'btn' => 'outline-success', 'icon' => 'fa-user-check'];
        if (isset($statusMap['talent_pool'])) $validTransitions[] = ['status' => $statusMap['talent_pool'], 'btn' => 'outline-warning', 'icon' => 'fa-archive'];
        if (isset($statusMap['rejected'])) $validTransitions[] = ['status' => $statusMap['rejected'], 'btn' => 'outline-danger', 'icon' => 'fa-ban'];
        break;

    case 'on_roster':
        if (isset($statusMap['deployed'])) $validTransitions[] = ['status' => $statusMap['deployed'], 'btn' => 'outline-primary', 'icon' => 'fa-rocket'];
        if (isset($statusMap['talent_pool'])) $validTransitions[] = ['status' => $statusMap['talent_pool'], 'btn' => 'outline-warning', 'icon' => 'fa-archive'];
        if (isset($statusMap['archived'])) $validTransitions[] = ['status' => $statusMap['archived'], 'btn' => 'outline-secondary', 'icon' => 'fa-power-off'];
        break;

    case 'deployed':
        if (isset($statusMap['on_roster'])) $validTransitions[] = ['status' => $statusMap['on_roster'], 'btn' => 'outline-success', 'icon' => 'fa-undo'];
        if (isset($statusMap['archived'])) $validTransitions[] = ['status' => $statusMap['archived'], 'btn' => 'outline-secondary', 'icon' => 'fa-power-off'];
        break;

    default: // rejected / talent_pool / archived
        if (isset($statusMap['screened'])) $validTransitions[] = ['status' => $statusMap['screened'], 'btn' => 'outline-info', 'icon' => 'fa-redo'];
        if (isset($statusMap['on_roster'])) $validTransitions[] = ['status' => $statusMap['on_roster'], 'btn' => 'outline-success', 'icon' => 'fa-user-check'];
        break;
}
?>

<main class="portal-dashboard">
    <section class="portal-dashboard-header">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker"><a href="<?= $siteConfig->siteUrl; ?>/admin/roster" style="color:rgba(255,255,255,0.7); text-decoration:none;"><i class="fa fa-arrow-left me-1"></i> Talent Pipeline</a> &rsaquo; Review Console</p>
                <h1>Vetting & Scoring: <?= htmlspecialchars($app->legal_name); ?></h1>
                <p class="portal-dashboard-intro">Track: <strong><?= htmlspecialchars($track->name ?? ''); ?></strong> | Function: <strong><?= htmlspecialchars($function->name ?? ''); ?></strong> | App #<?= $app->iD; ?></p>
            </div>
            <div class="portal-account-summary">
                <span>Vetting Score</span>
                <strong id="header_score_display"><?= number_format((float)($assessment->total_score ?? 0), 1); ?> / 100 PTS</strong>
                <small>Gate Status: <span id="header_gate_status"><?= ($assessment && $assessment->eligibility_gate_passed) ? '<span class="text-success fw-bold">PASSED</span>' : '<span class="text-warning fw-bold">PENDING / FAIL</span>'; ?></span></small>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">
            <div id="review_alert" style="display:none;" class="alert mb-4"></div>

            <!-- Automated Red Flags Alert Box -->
            <?php if (!empty($redFlags)): ?>
                <div class="alert alert-warning border-0 shadow-sm p-4 mb-4" style="border-radius: 12px;">
                    <h5 class="alert-heading fw-bold text-dark mb-2"><i class="fa fa-exclamation-triangle text-danger me-2"></i> Automated Reviewer Red Flags (<?= count($redFlags); ?>)</h5>
                    <ul class="mb-0 ps-3 text-dark">
                        <?php foreach ($redFlags as $flag): ?>
                            <li class="mb-1"><?= htmlspecialchars($flag); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Active On Roster Status Banner -->
            <?php if ($currentStatusId === 5): ?>
                <div class="card border-0 shadow-sm mb-4 bg-success bg-opacity-10 border-start border-success border-4" style="border-radius: 12px;">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold text-success mb-1"><i class="fa fa-check-double me-2"></i> Admitted to Active Talent Roster</h5>
                            <p class="text-muted small mb-0">Candidate has been verified, assessed, and admitted to the active roster. Stage 3 Statutory Onboarding is active on their candidate dashboard.</p>
                        </div>
                        <span class="badge bg-success px-3 py-2 rounded-pill fw-semibold"><i class="fa fa-shield me-1"></i> Active on Roster</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Left Column: Applicant Profile & Evidence -->
                <div class="col-lg-7">
                    
                    <!-- Submitted Application Form Details Card -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-id-card text-primary me-2"></i> Stage 1: Submitted Application Details</h5>
                            <span class="badge <?= ($track->code === 'apprentice') ? 'bg-success' : 'bg-primary'; ?> px-3 py-2 fs-6">
                                <?= htmlspecialchars($track->name ?? 'Talent Intake'); ?>
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <!-- Primary Identification & Contact -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Full Legal Name</span>
                                    <strong class="fs-6 text-dark"><?= htmlspecialchars($app->legal_name); ?></strong>
                                    <?php if (!empty($app->preferred_name)): ?>
                                        <span class="text-muted small">(Prefers: <?= htmlspecialchars($app->preferred_name); ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Practice Area / Primary Function</span>
                                    <span class="badge bg-secondary fs-6"><i class="fa fa-briefcase me-1"></i> <?= htmlspecialchars($function->name ?? 'General Specialist'); ?></span>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Email Address</span>
                                    <a href="mailto:<?= htmlspecialchars($app->email); ?>" class="fw-bold text-decoration-none text-primary">
                                        <i class="fa fa-envelope me-1"></i> <?= htmlspecialchars($app->email); ?>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Mobile / WhatsApp</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <strong><i class="fa fa-phone me-1"></i> <?= htmlspecialchars($app->mobile_number); ?></strong>
                                        <?php 
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $app->whatsapp_number ?: $app->mobile_number);
                                            if ($cleanPhone): 
                                        ?>
                                            <a href="https://wa.me/<?= $cleanPhone; ?>" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill" title="Chat on WhatsApp">
                                                <i class="fab fa-whatsapp"></i> Chat
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">City / Base</span>
                                    <strong class="text-dark"><i class="fa fa-map-marker-alt text-danger me-1"></i> <?= htmlspecialchars($app->city ?: 'Harare'); ?><?= ($app->zimprovince() && $app->zimprovince()->name) ? ', ' . htmlspecialchars($app->zimprovince()->name) : ''; ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Application Received</span>
                                    <strong class="text-dark"><i class="fa fa-clock text-primary me-1"></i> <?= date('d M Y, H:i', strtotime($app->reg_date)); ?></strong>
                                </div>
                            </div>

                            <hr class="my-3">

                            <!-- Track Specific Intake Fields -->
                            <?php if ($track->code === 'apprentice'): ?>
                                <?php $ap = $app->apprenticeProfile(); ?>
                                <h6 class="fw-bold text-success mb-3"><i class="fa fa-university me-1"></i> Academic Discipline &amp; Attachment Scope</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <span class="text-muted small d-block">Tertiary Institution</span>
                                        <strong class="text-dark"><?= htmlspecialchars($ap && $ap->institution_name ? $ap->institution_name : 'Not specified'); ?></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted small d-block">Degree / Diploma Programme</span>
                                        <strong class="text-dark"><?= htmlspecialchars($ap && $ap->degree_programme ? $ap->degree_programme : 'Not specified'); ?></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted small d-block">Current Study / Career Stage</span>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($ap && $ap->study_level ? $ap->study_level : 'Seeking Attachment'); ?></span>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted small d-block">Earliest Available Start Date</span>
                                        <strong><i class="fa fa-calendar-alt text-primary me-1"></i> <?= htmlspecialchars($ap && $ap->wrl_start_date ? date('d M Y', strtotime($ap->wrl_start_date)) : 'Immediately / Flexible'); ?></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted small d-block">Available Attachment Duration</span>
                                        <strong><?= htmlspecialchars($ap && $ap->wrl_duration_months ? $ap->wrl_duration_months . ' Months' : '12 Months'); ?></strong>
                                    </div>
                                    <?php if ($ap && !empty($ap->student_reg_number)): ?>
                                        <div class="col-md-6">
                                            <span class="text-muted small d-block">Student Reg Number</span>
                                            <strong class="font-monospace text-dark"><?= htmlspecialchars($ap->student_reg_number); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            <?php elseif ($track->code === 'associate'): ?>
                                <?php $asp = $app->associateProfile(); ?>
                                <h6 class="fw-bold text-primary mb-3"><i class="fa fa-award me-1"></i> Advisory Domain, Seniority &amp; Commercial Terms</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <span class="text-muted small d-block">Years of Specialist Experience</span>
                                        <strong class="fs-6 text-dark"><i class="fa fa-clock text-warning me-1"></i> <?= htmlspecialchars($asp && $asp->years_experience ? $asp->years_experience : 'Not specified'); ?></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted small d-block">Current Professional Status</span>
                                        <strong><?= htmlspecialchars($asp && $asp->employmentstatus() ? $asp->employmentstatus()->name : 'Not specified'); ?></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted small d-block">Indicative Day Rate Expectation</span>
                                        <strong class="text-success fs-6">
                                            <?= ($asp && $asp->day_rate_expectation) ? '$' . number_format((float)$asp->day_rate_expectation, 2) . ' USD / Day' : '<span class="text-muted fw-normal">Negotiable / Open</span>'; ?>
                                        </strong>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="text-muted small d-block">Available Consulting Capacity</span>
                                        <strong><i class="fa fa-business-time text-primary me-1"></i> <?= htmlspecialchars($asp && $asp->capacity_days_per_month ? $asp->capacity_days_per_month : 'Flexible / Project-based'); ?></strong>
                                    </div>
                                    <?php if ($asp && $asp->has_tax_clearance_itf263): ?>
                                        <div class="col-md-6">
                                            <span class="text-muted small d-block">ZIMRA ITF263 Tax Clearance</span>
                                            <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i> Valid (BP: <?= htmlspecialchars($asp->zimra_bp_number); ?>)</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($asp && !empty($asp->conflict_of_interest)): ?>
                                        <div class="col-md-6">
                                            <span class="text-muted small d-block">Conflict of Interest Disclosures</span>
                                            <span class="small text-dark"><?= htmlspecialchars($asp->conflict_of_interest); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($app->how_heard) || !empty($app->referred_by)): ?>
                                <hr class="my-3">
                                <div class="row g-2 small text-muted">
                                    <?php if (!empty($app->how_heard)): ?>
                                        <div class="col-md-6">Source: <strong><?= htmlspecialchars($app->how_heard); ?></strong></div>
                                    <?php endif; ?>
                                    <?php if (!empty($app->referred_by)): ?>
                                        <div class="col-md-6">Referred by: <strong><?= htmlspecialchars($app->referred_by); ?></strong></div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Submitted Documents & CV / Resume Card -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-file-pdf text-danger me-2"></i> Submitted CV &amp; Intake Documents</h5>
                            <span class="badge bg-dark rounded-pill"><?= count($documents); ?> File<?= count($documents) === 1 ? '' : 's'; ?></span>
                        </div>
                        <div class="card-body p-4">
                            <?php if (empty($documents)): ?>
                                <div class="p-3 bg-light rounded text-center text-muted">
                                    <i class="fa fa-file-excel fa-2x mb-2 d-block text-secondary"></i>
                                    <p class="mb-0 small">No CV or documents uploaded with this application (Legacy or draft submission).</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($documents as $doc): ?>
                                        <?php 
                                            $dt = $doc->documenttype();
                                            $docName = $dt ? $dt->name : 'Document';
                                            $isCv = $dt && $dt->code === 'CV_RESUME';
                                        ?>
                                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div class="d-flex align-items-start gap-3">
                                                <div class="rounded-circle p-2 <?= $isCv ? 'bg-danger bg-opacity-10 text-danger' : 'bg-primary bg-opacity-10 text-primary'; ?>">
                                                    <i class="fa <?= $isCv ? 'fa-file-pdf' : 'fa-file-alt'; ?> fa-lg"></i>
                                                </div>
                                                <div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <strong class="text-dark"><?= htmlspecialchars($doc->original_name ?: basename($doc->file_path)); ?></strong>
                                                        <span class="badge <?= $isCv ? 'bg-danger' : 'bg-secondary'; ?> small"><?= htmlspecialchars($docName); ?></span>
                                                    </div>
                                                    <small class="text-muted">
                                                        <?= $doc->file_size_kb ? number_format($doc->file_size_kb) . ' KB' : 'Document'; ?> &bull; 
                                                        Uploaded on <?= date('d M Y, H:i', strtotime($doc->reg_date)); ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="<?= $siteConfig->siteUrl; ?>/roster/document/view?id=<?= $doc->iD; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3">
                                                    <i class="fa fa-eye me-1"></i> Preview
                                                </a>
                                                <a href="<?= $siteConfig->siteUrl; ?>/roster/document/download?id=<?= $doc->iD; ?>" class="btn btn-sm btn-light border fw-semibold rounded-pill px-3">
                                                    <i class="fa fa-download me-1"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Stages 2–5: Talent Dossier -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-folder-open text-primary me-2"></i> Stages 2–5: Candidate Talent Dossier</h5>
                            <span class="badge <?= $currentStatusId >= 5 ? 'bg-success' : ($currentStatusId >= 3 ? 'bg-primary' : 'bg-info text-dark'); ?> px-3 py-1">
                                <?= $currentStatusId >= 5 ? 'Active on Roster' : ($currentStatusId >= 3 ? 'Under Evaluation' : 'Application Submitted'); ?>
                            </span>
                        </div>
                        <div class="card-body p-4">

                            <!-- Stage 2: Qualifications & Transcripts -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                    <i class="fa fa-graduation-cap text-success me-2"></i> Stage 2: Qualifications &amp; Certifications (<?= count($qualifications); ?>)
                                </h6>
                                <?php if (empty($qualifications)): ?>
                                    <p class="text-muted small fst-italic mb-0">No qualification records uploaded yet.</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($qualifications as $q): ?>
                                            <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <div>
                                                    <strong class="text-dark d-block"><?= htmlspecialchars($q->title); ?></strong>
                                                    <small class="text-muted">
                                                        <?= htmlspecialchars($q->institution_name ?: 'Institution'); ?>
                                                        <?php if (!empty($q->field_of_study)): ?> &bull; <?= htmlspecialchars($q->field_of_study); ?><?php endif; ?>
                                                        <?php if (!empty($q->date_obtained)): ?> &bull; Obtained <?= date('M Y', strtotime($q->date_obtained)); ?><?php endif; ?>
                                                    </small>
                                                </div>
                                                <?php if (!empty($q->certificate_doc)): ?>
                                                    <span class="badge bg-light text-primary border"><i class="fa fa-file-check me-1"></i> Doc Attached</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Stage 3: Skills & Competencies Matrix -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                    <i class="fa fa-sliders text-primary me-2"></i> Stage 3: Self-Assessed Competencies (1–5 Anchored Scale)
                                </h6>
                                <?php if (empty($skills)): ?>
                                    <p class="text-muted small fst-italic mb-0">No skill ratings submitted yet.</p>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Competency Item</th>
                                                    <th style="width:140px;">Rating</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($skills as $sk): ?>
                                                    <?php $pl = $sk->proficiencylevel(); ?>
                                                    <tr>
                                                        <td class="small fw-semibold"><?= htmlspecialchars($sk->skillitem()->name ?? 'Skill'); ?></td>
                                                        <td>
                                                            <span class="badge <?= ($pl && $pl->level_number >= 4) ? 'bg-success' : (($pl && $pl->level_number == 3) ? 'bg-primary' : 'bg-secondary'); ?>">
                                                                <?= htmlspecialchars($pl->name ?? 'Level'); ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Stage 4: Practical Deliverables & Work History -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                    <i class="fa fa-briefcase text-warning me-2"></i> Stage 4: Work Experience &amp; Deliverables (<?= count($workHistories); ?>)
                                </h6>
                                <?php if (empty($workHistories)): ?>
                                    <p class="text-muted small fst-italic mb-0">No work experience records submitted yet.</p>
                                <?php else: ?>
                                    <div class="list-group list-group-flush">
                                        <?php foreach ($workHistories as $wh): ?>
                                            <div class="list-group-item px-0 py-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong class="text-dark"><?= htmlspecialchars($wh->position_title); ?> &bull; <?= htmlspecialchars($wh->organization_name); ?></strong>
                                                    <small class="text-muted">
                                                        <?= $wh->start_date ? date('M Y', strtotime($wh->start_date)) : ''; ?> &ndash; 
                                                        <?= $wh->is_current ? 'Present' : ($wh->end_date ? date('M Y', strtotime($wh->end_date)) : 'Completed'); ?>
                                                    </small>
                                                </div>
                                                <?php if (!empty($wh->key_deliverables)): ?>
                                                    <p class="small text-muted mb-0 bg-light p-2 rounded border">
                                                        <?= nl2br(htmlspecialchars($wh->key_deliverables)); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Stage 4: Verified Referees -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                    <i class="fa fa-user-check text-info me-2"></i> Stage 4: Referees &amp; Academic Supervisors (<?= count($referees); ?>)
                                </h6>
                                <?php if (empty($referees)): ?>
                                    <p class="text-muted small fst-italic mb-0">No referees submitted yet.</p>
                                <?php else: ?>
                                    <div class="row g-3">
                                        <?php foreach ($referees as $rf): ?>
                                            <div class="col-md-6">
                                                <div class="p-3 bg-light rounded border h-100">
                                                    <strong class="text-dark d-block"><?= htmlspecialchars($rf->referee_name); ?></strong>
                                                    <small class="text-muted d-block"><?= htmlspecialchars($rf->position); ?> &bull; <?= htmlspecialchars($rf->organization); ?></small>
                                                    <span class="badge bg-secondary small mb-2"><?= htmlspecialchars($rf->relationship ?: 'Professional Reference'); ?></span>
                                                    <div class="small mt-1">
                                                        <div><i class="fa fa-envelope text-primary me-1"></i> <a href="mailto:<?= htmlspecialchars($rf->email); ?>"><?= htmlspecialchars($rf->email); ?></a></div>
                                                        <div><i class="fa fa-phone text-success me-1"></i> <a href="tel:<?= htmlspecialchars($rf->phone); ?>"><?= htmlspecialchars($rf->phone); ?></a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Stage 5: Situational Judgement & Evidence Narratives -->
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                    <i class="fa fa-scale-balanced text-primary me-2"></i> Stage 5: Situational Judgement &amp; Evidence Narratives
                                </h6>
                                <?php if (!$judgement || (empty($judgement->primary_function_evidence) && empty($judgement->shared_client_management_plan) && empty($judgement->urgent_friday_deadline_dilemma))): ?>
                                    <p class="text-muted small fst-italic mb-0">No situational judgement responses submitted.</p>
                                <?php else: ?>
                                    <div class="vstack gap-3">
                                        <?php if (!empty($judgement->primary_function_evidence)): ?>
                                            <div class="p-3 bg-light rounded border">
                                                <strong class="text-dark d-block mb-1"><i class="fa fa-lightbulb text-warning me-1"></i> Core Domain Competence &amp; Evidence Narrative:</strong>
                                                <div class="text-secondary small"><?= nl2br(htmlspecialchars($judgement->primary_function_evidence)); ?></div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($judgement->shared_client_management_plan)): ?>
                                            <div class="p-3 bg-light rounded border">
                                                <strong class="text-dark d-block mb-1"><i class="fa fa-tasks text-info me-1"></i> Multi-Client Workload Prioritization Plan:</strong>
                                                <div class="text-secondary small"><?= nl2br(htmlspecialchars($judgement->shared_client_management_plan)); ?></div>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($judgement->urgent_friday_deadline_dilemma)): ?>
                                            <div class="p-3 bg-light rounded border">
                                                <strong class="text-dark d-block mb-1"><i class="fa fa-fire text-danger me-1"></i> Critical Production / Urgent Deadline Dilemma:</strong>
                                                <div class="text-secondary small"><?= nl2br(htmlspecialchars($judgement->urgent_friday_deadline_dilemma)); ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Stage 5: Digital E-Signature & Consent -->
                            <?php if (!empty($app->e_signature)): ?>
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold text-dark mb-1"><i class="fa fa-signature text-primary me-2"></i> Digital Declaration &amp; E-Signature</h6>
                                    <p class="small text-muted mb-0">
                                        Digitally signed by <strong><?= htmlspecialchars($app->e_signature); ?></strong> 
                                        on <?= date('d M Y, H:i:s', strtotime($app->consent_timestamp)); ?> 
                                        (IP: <code><?= htmlspecialchars($app->consent_ip_address ?: '127.0.0.1'); ?></code>).
                                    </p>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>

                <!-- Right Column: Reviewer Scoring Console -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm sticky-top" style="border-radius: 12px; top: 20px;">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="fw-bold mb-0"><i class="fa fa-gavel text-warning me-2"></i> 100-Point Scoring Console</h5>
                        </div>
                        <div class="card-body p-4">
                            
                            <!-- In-card feedback alert container -->
                            <div id="console_feedback_alert" style="display:none;" class="mb-3"></div>

                            <form id="assessment_form" method="POST">
                                <input type="hidden" name="rosterapplication" value="<?= $app->iD; ?>">

                                <!-- Eligibility Gate -->
                                <div class="p-3 bg-light rounded border mb-3">
                                    <div class="form-check d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" name="eligibility_gate_passed" id="gateCheck" value="1" <?= ($assessment && $assessment->eligibility_gate_passed) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold text-dark mb-0 ms-2" for="gateCheck">
                                            Eligibility Gate Passed (Pass/Fail)
                                        </label>
                                    </div>
                                    <small class="form-text text-muted d-block mt-1">Right to work, education threshold, safeguarding & compliance checks.</small>
                                </div>

                                <!-- 5 Scoring Dimensions -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small mb-1">1. Technical Fit &amp; Academic Baseline (<?= $track->code === 'associate' ? 'Max 35' : 'Max 30'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_tech"><?= $assessment->technical_fit_score ?? 20; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="technical_fit_score" min="0" max="<?= $track->code === 'associate' ? '35' : '30'; ?>" step="0.5" value="<?= $assessment->technical_fit_score ?? 20; ?>" data-target="val_tech">
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">Discipline match, tertiary institution standing, and core domain knowledge.</div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small mb-1">2. CV &amp; Practical Track Record (<?= $track->code === 'associate' ? 'Max 30' : 'Max 20'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_evid"><?= $assessment->evidence_score ?? 15; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="evidence_score" min="0" max="<?= $track->code === 'associate' ? '30' : '20'; ?>" step="0.5" value="<?= $assessment->evidence_score ?? 15; ?>" data-target="val_evid">
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">Quality of submitted CV, past deliverables, client projects, and work history.</div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small mb-1">3. Competencies &amp; Vetting Assessment (<?= $track->code === 'associate' ? 'Max 20' : 'Max 25'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_judge"><?= $assessment->judgement_score ?? 15; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="judgement_score" min="0" max="<?= $track->code === 'associate' ? '20' : '25'; ?>" step="0.5" value="<?= $assessment->judgement_score ?? 15; ?>" data-target="val_judge">
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">Skills matrix competency depth, technical problem-solving, and interview performance.</div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small mb-1">4. Availability &amp; Delivery Logistics (<?= $track->code === 'associate' ? 'Max 10' : 'Max 15'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_avail"><?= $assessment->availability_score ?? 10; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="availability_score" min="0" max="<?= $track->code === 'associate' ? '10' : '15'; ?>" step="0.5" value="<?= $assessment->availability_score ?? 10; ?>" data-target="val_avail">
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">Earliest start date, attachment duration or consulting days/month, and rate expectations.</div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small mb-1">5. Motivation &amp; Professional Alignment (<?= $track->code === 'associate' ? 'Max 5' : 'Max 10'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_motiv"><?= $assessment->motivation_score ?? 8; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="motivation_score" min="0" max="<?= $track->code === 'associate' ? '5' : '10'; ?>" step="0.5" value="<?= $assessment->motivation_score ?? 8; ?>" data-target="val_motiv">
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">Communication clarity, professional dedication, and roster cultural fit.</div>
                                </div>

                                <!-- Total Computed Score Display -->
                                <div class="p-3 bg-dark text-white rounded mb-3 text-center">
                                    <span class="small text-uppercase text-muted">Total Evaluated Score</span>
                                    <h2 class="mb-0 text-warning" id="total_score_display"><?= number_format((float)($assessment->total_score ?? 68), 1); ?> / 100</h2>
                                </div>

                                <!-- ACTION BUTTONS: Vetting Recommendation -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark d-block mb-2">Vetting Recommendation Action</label>
                                    <input type="hidden" name="vettingrecommendation" id="input_vettingrecommendation" value="<?= $currentRecommendationId; ?>">
                                    <div class="d-grid gap-2" id="recommendation_buttons">
                                        <?php foreach ($recommendations as $rec): ?>
                                            <?php
                                            $recId = (int)$rec->iD;
                                            $isSelected = ($currentRecommendationId === $recId);
                                            $btnClass = 'btn-outline-secondary';
                                            $icon = 'fa-circle';
                                            if ($recId === 1) { $btnClass = $isSelected ? 'btn-success' : 'btn-outline-success'; $icon = 'fa-check-circle'; }
                                            elseif ($recId === 2) { $btnClass = $isSelected ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'; $icon = 'fa-archive'; }
                                            elseif ($recId === 3) { $btnClass = $isSelected ? 'btn-primary' : 'btn-outline-primary'; $icon = 'fa-comments'; }
                                            elseif ($recId === 4) { $btnClass = $isSelected ? 'btn-danger' : 'btn-outline-danger'; $icon = 'fa-times-circle'; }
                                            ?>
                                            <button type="button" class="btn btn-sm <?= $btnClass; ?> text-start d-flex justify-content-between align-items-center py-2 px-3 btn-rec-action" data-val="<?= $recId; ?>">
                                                <span><i class="fa <?= $icon; ?> me-2"></i> <?= htmlspecialchars($rec->name); ?></span>
                                                <i class="fa fa-check rec-check-icon <?= $isSelected ? '' : 'd-none'; ?>"></i>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- ACTION BUTTONS: Contextual Application Status Transitions -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark d-block mb-1">
                                        Update Status <span class="text-muted fw-normal">(Current: <span class="badge bg-secondary"><?= htmlspecialchars($app->applicationstatus()->name ?? 'Draft'); ?></span>)</span>
                                    </label>
                                    <input type="hidden" name="new_applicationstatus" id="input_new_applicationstatus" value="<?= $currentStatusId; ?>">
                                    <div class="d-grid gap-2" id="status_action_buttons">
                                        <?php foreach ($validTransitions as $vt): ?>
                                            <?php
                                            $stObj = $vt['status'];
                                            $stId = (int)$stObj->iD;
                                            $isSelected = ($currentStatusId === $stId);
                                            $btnStyle = $isSelected ? str_replace('outline-', '', $vt['btn']) : 'btn-' . $vt['btn'];
                                            ?>
                                            <button type="button" class="btn btn-sm <?= $btnStyle; ?> text-start d-flex justify-content-between align-items-center py-2 px-3 btn-status-action" data-val="<?= $stId; ?>" data-base-btn="<?= $vt['btn']; ?>">
                                                <span><i class="fa <?= $vt['icon']; ?> me-2"></i> <?= htmlspecialchars($stObj->name); ?></span>
                                                <i class="fa fa-check st-check-icon <?= $isSelected ? '' : 'd-none'; ?>"></i>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Interview & Technical Notes</label>
                                    <textarea class="form-control form-control-sm" name="interview_notes" rows="2" placeholder="Record verification interview feedback or flawed deliverable test outcome..."><?= htmlspecialchars($assessment->interview_notes ?? ''); ?></textarea>
                                </div>

                                <button type="submit" id="btn_submit_assessment" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                                    <i class="fa fa-save me-1"></i> Save Assessment & Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>

<script>
$(document).ready(function () {
    const $sliders = $('.score-slider');
    const $totalDisplay = $('#total_score_display');
    const $headerScore = $('#header_score_display');
    const $headerGate = $('#header_gate_status');
    const $form = $('#assessment_form');
    const $submitBtn = $('#btn_submit_assessment');
    const $consoleAlert = $('#console_feedback_alert');
    const $topAlert = $('#review_alert');

    // 1. Scoring sliders logic
    function calculateTotal() {
        let sum = 0;
        $sliders.each(function () {
            const val = parseFloat($(this).val()) || 0;
            const targetId = $(this).data('target');
            $('#' + targetId).text(val.toFixed(1));
            sum += val;
        });
        const formatted = sum.toFixed(1) + ' / 100';
        $totalDisplay.text(formatted);
        $headerScore.text(sum.toFixed(1) + ' / 100 PTS');
    }

    $sliders.on('input change', calculateTotal);
    calculateTotal();

    $('#gateCheck').on('change', function () {
        if ($(this).is(':checked')) {
            $headerGate.html('<span class="text-success fw-bold">PASSED</span>');
        } else {
            $headerGate.html('<span class="text-warning fw-bold">PENDING / FAIL</span>');
        }
    });

    // 2. Vetting Recommendation Action Buttons Toggle
    $(document).on('click', '.btn-rec-action', function () {
        const val = parseInt($(this).data('val'), 10);
        $('#input_vettingrecommendation').val(val);

        $('.btn-rec-action').each(function () {
            const bVal = parseInt($(this).data('val'), 10);
            const $check = $(this).find('.rec-check-icon');
            if (bVal === 1) {
                $(this).attr('class', (bVal === val) ? 'btn btn-sm btn-success text-start d-flex justify-content-between align-items-center py-2 px-3 btn-rec-action' : 'btn btn-sm btn-outline-success text-start d-flex justify-content-between align-items-center py-2 px-3 btn-rec-action');
            } else if (bVal === 2) {
                $(this).attr('class', (bVal === val) ? 'btn btn-sm btn-warning text-dark text-start d-flex justify-content-between align-items-center py-2 px-3 btn-rec-action' : 'btn btn-sm btn-outline-warning text-dark text-start d-flex justify-content-between align-items-center py-2 px-3 btn-rec-action');
            } else if (bVal === 3) {
                $(this).attr('class', (bVal === val) ? 'btn btn-sm btn-primary text-start d-flex justify-content-between align-items-center py-2 px-3 btn-rec-action' : 'btn btn-sm btn-outline-primary text-start d-flex justify-content-between align-items-center py-2 px-3 btn-rec-action');
            } else if (bVal === 4) {
                $(this).attr('class', (bVal === val) ? 'btn btn-sm btn-danger text-start d-flex justify-content-between align-items-center py-2 px-3 btn-rec-action' : 'btn btn-sm btn-outline-danger text-start d-flex justify-content-between align-items-center py-2 px-3 btn-rec-action');
            }
            $check.toggleClass('d-none', bVal !== val);
        });
    });

    // 3. Status Action Buttons Toggle
    $(document).on('click', '.btn-status-action', function () {
        const val = parseInt($(this).data('val'), 10);
        $('#input_new_applicationstatus').val(val);

        $('.btn-status-action').each(function () {
            const bVal = parseInt($(this).data('val'), 10);
            const baseBtn = $(this).data('baseBtn');
            const $check = $(this).find('.st-check-icon');
            if (bVal === val) {
                $(this).attr('class', 'btn btn-sm ' + baseBtn.replace('outline-', '') + ' text-start d-flex justify-content-between align-items-center py-2 px-3 btn-status-action');
            } else {
                $(this).attr('class', 'btn btn-sm btn-' + baseBtn + ' text-start d-flex justify-content-between align-items-center py-2 px-3 btn-status-action');
            }
            $check.toggleClass('d-none', bVal !== val);
        });
    });

    // 4. Submit Assessment AJAX Handler with Unmissable Visual Feedback
    $form.on('submit', function (e) {
        e.preventDefault();

        $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving Assessment...');

        $consoleAlert.stop(true, true).slideDown().html(`
            <div class="alert alert-info py-2 d-flex align-items-center mb-0 small">
                <span class="spinner-border spinner-border-sm me-2"></span>
                <span>Saving assessment and updating status...</span>
            </div>
        `);

        const formData = $form.serialize();

        $.ajax({
            url: "<?= $siteConfig->siteUrl; ?>/admin/roster/assessment",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (data) {
                if (data.status === 1) {
                    $submitBtn.attr('class', 'btn btn-success w-100 fw-bold py-2 shadow-sm')
                              .html('<i class="fa fa-check-circle me-1"></i> Assessment Saved Successfully!');

                    $consoleAlert.html(`
                        <div class="alert alert-success d-flex align-items-center py-2 px-3 shadow-sm mb-0">
                            <i class="fa fa-check-circle fa-lg me-2 text-success"></i>
                            <div><strong>Success!</strong> ${data.msg} (Total Score: ${data.total_score}/100)</div>
                        </div>
                    `);

                    $topAlert.show().attr('class', 'alert alert-success d-flex align-items-center p-3 shadow-sm mb-4')
                             .html(`<i class="fa fa-check-circle fa-2x me-3 text-success"></i> <div><h6 class="mb-0 fw-bold">Assessment Recorded</h6><div>${data.msg}</div></div>`);

                    $('html, body').animate({
                        scrollTop: $("#console_feedback_alert").offset().top - 100
                    }, 300);

                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    $submitBtn.prop('disabled', false).attr('class', 'btn btn-primary w-100 fw-bold py-2 shadow-sm')
                              .html('<i class="fa fa-save me-1"></i> Save Assessment & Update Status');

                    $consoleAlert.html(`
                        <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-0 small">
                            <i class="fa fa-exclamation-triangle me-2 text-danger"></i>
                            <div><strong>Error:</strong> ${data.msg || 'Failed to save assessment.'}</div>
                        </div>
                    `);
                }
            },
            error: function (xhr, status, err) {
                $submitBtn.prop('disabled', false).attr('class', 'btn btn-primary w-100 fw-bold py-2 shadow-sm')
                          .html('<i class="fa fa-save me-1"></i> Save Assessment & Update Status');

                $consoleAlert.html(`
                    <div class="alert alert-danger d-flex align-items-center py-2 px-3 mb-0 small">
                        <i class="fa fa-exclamation-triangle me-2 text-danger"></i>
                        <div><strong>Error:</strong> Server communication failed. Please try again.</div>
                    </div>
                `);
            }
        });
    });
});
</script>
