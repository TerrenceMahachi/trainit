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

            <!-- Shortlist & Magic Link Dispatch Card -->
            <?php if ($currentStatusId < 3): ?>
                <div class="card border-0 shadow-sm mb-4 bg-primary bg-opacity-10 border-start border-primary border-4" style="border-radius: 12px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold text-primary mb-1"><i class="fa fa-envelope-open-text me-2"></i> Initial Express Application (Pending Shortlisting)</h5>
                            <p class="text-muted small mb-0">Candidate has submitted their initial details and CV. Shortlisting triggers an automated email with their secure magic link to complete the Stage 2–5 verification dossier.</p>
                        </div>
                        <button type="button" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm" id="btn_shortlist_action">
                            <i class="fa fa-check-circle me-1"></i> Shortlist &amp; Send Dossier Invitation Link
                        </button>
                    </div>
                </div>
            <?php elseif ($currentStatusId === 3): ?>
                <div class="card border-0 shadow-sm mb-4 bg-success bg-opacity-10 border-start border-success border-4" style="border-radius: 12px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="fw-bold text-success mb-1"><i class="fa fa-clipboard-check me-2"></i> Candidate Shortlisted &ndash; Dossier Completion Pending</h5>
                            <p class="text-muted small mb-0">Shortlist invitation link was emailed to <strong><?= htmlspecialchars($app->email); ?></strong>. Candidate can now fill qualifications, skills matrix, and referee details.</p>
                        </div>
                        <button type="button" class="btn btn-outline-success fw-bold rounded-pill px-3 btn-sm" id="btn_shortlist_action">
                            <i class="fa fa-redo me-1"></i> Resend Dossier Invitation Link
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Left Column: Applicant Profile & Evidence -->
                <div class="col-lg-7">
                    
                    <!-- Candidate Details -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="fw-bold mb-0 text-dark">Candidate Dossier</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Contact Details</span>
                                    <strong><?= htmlspecialchars($app->email); ?><br><?= htmlspecialchars($app->mobile_number); ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Location & Right to Work</span>
                                    <strong><?= htmlspecialchars($app->city); ?>, <?= htmlspecialchars($app->zimprovince()->name ?? ''); ?><br><?= htmlspecialchars($app->workrightstatus()->name ?? 'Citizen'); ?></strong>
                                </div>
                            </div>

                            <hr class="my-3">

                            <?php if ($track->code === 'apprentice' && $app->apprenticeProfile()): ?>
                                <?php $ap = $app->apprenticeProfile(); ?>
                                <h6 class="fw-bold text-success mb-2"><i class="fa fa-university me-1"></i> Apprentice Academic Profile</h6>
                                <p class="mb-1"><strong>Institution:</strong> <?= htmlspecialchars($ap->institution_name ?? 'N/A'); ?> (<?= htmlspecialchars($ap->degree_programme ?? ''); ?>)</p>
                                <p class="mb-1"><strong>Status:</strong> <?= htmlspecialchars($ap->apprenticestatus()->name ?? ''); ?> | Level: <?= htmlspecialchars($ap->study_level ?? 'N/A'); ?></p>
                                <p class="mb-1"><strong>WRL Attachment:</strong> <?= $ap->is_wrl_attachment ? 'Yes (' . $ap->wrl_duration_months . ' months, Start: ' . $ap->wrl_start_date . ')' : 'No'; ?></p>
                                <p class="mb-0"><strong>Coordinator:</strong> <?= htmlspecialchars($ap->wrl_coordinator_name ?: 'None'); ?> (<?= htmlspecialchars($ap->wrl_coordinator_email ?: ''); ?>, <?= htmlspecialchars($ap->wrl_coordinator_phone ?: ''); ?>)</p>
                            <?php elseif ($track->code === 'associate' && $app->associateProfile()): ?>
                                <?php $asp = $app->associateProfile(); ?>
                                <h6 class="fw-bold text-primary mb-2"><i class="fa fa-award me-1"></i> Associate Seniority & Rate Card</h6>
                                <p class="mb-1"><strong>Experience:</strong> <?= htmlspecialchars($asp->years_experience ?? ''); ?> (<?= htmlspecialchars($asp->employmentstatus()->name ?? ''); ?>)</p>
                                <p class="mb-1"><strong>Day Rate:</strong> $<?= number_format((float)$asp->day_rate_expectation, 2); ?> USD | Capacity: <?= htmlspecialchars($asp->capacity_days_per_month ?? 'N/A'); ?></p>
                                <p class="mb-1"><strong>ZIMRA ITF263:</strong> <?= $asp->has_tax_clearance_itf263 ? '<span class="badge bg-success">Yes (BP: ' . htmlspecialchars($asp->zimra_bp_number) . ')</span>' : '<span class="badge bg-warning text-dark">No</span>'; ?></p>
                                <p class="mb-0"><strong>Conflict Disclosure:</strong> <?= htmlspecialchars($asp->conflict_of_interest ?: 'None declared'); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Skills Matrix -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="fw-bold mb-0 text-dark">Self-Assessed Competencies (1–5 Anchored Scale)</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if (empty($skills)): ?>
                                <p class="text-muted small mb-0">No skill ratings submitted.</p>
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
                    </div>

                    <!-- Scored Judgement Answers -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="fw-bold mb-0 text-dark">Scored Scenario Responses</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <span class="fw-bold text-dark d-block mb-1">Section 4.4 Deliverable Evidence Narrative:</span>
                                <div class="p-3 bg-light rounded border text-dark small">
                                    <?= nl2br(htmlspecialchars($judgement->primary_function_evidence ?? 'No answer provided.')); ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="fw-bold text-dark d-block mb-1">9.2 5-Client Workload Balance Plan:</span>
                                <div class="p-3 bg-light rounded border text-dark small">
                                    <?= nl2br(htmlspecialchars($judgement->shared_client_management_plan ?? 'No answer provided.')); ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="fw-bold text-dark d-block mb-1">9.4 Urgent Friday 16:45 Deadline Solution:</span>
                                <div class="p-3 bg-light rounded border text-dark small">
                                    <?= nl2br(htmlspecialchars($judgement->urgent_friday_deadline_dilemma ?? 'No answer provided.')); ?>
                                </div>
                            </div>
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
                                        <label class="form-label fw-bold small">1. Technical Fit (<?= $track->code === 'associate' ? 'Max 35' : 'Max 30'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_tech"><?= $assessment->technical_fit_score ?? 20; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="technical_fit_score" min="0" max="<?= $track->code === 'associate' ? '35' : '30'; ?>" step="0.5" value="<?= $assessment->technical_fit_score ?? 20; ?>" data-target="val_tech">
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small">2. Evidence Narrative & Track Record (<?= $track->code === 'associate' ? 'Max 30' : 'Max 20'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_evid"><?= $assessment->evidence_score ?? 15; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="evidence_score" min="0" max="<?= $track->code === 'associate' ? '30' : '20'; ?>" step="0.5" value="<?= $assessment->evidence_score ?? 15; ?>" data-target="val_evid">
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small">3. Judgement & Scenario Answers (<?= $track->code === 'associate' ? 'Max 20' : 'Max 25'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_judge"><?= $assessment->judgement_score ?? 15; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="judgement_score" min="0" max="<?= $track->code === 'associate' ? '20' : '25'; ?>" step="0.5" value="<?= $assessment->judgement_score ?? 15; ?>" data-target="val_judge">
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small">4. Availability & Logistics (<?= $track->code === 'associate' ? 'Max 10' : 'Max 15'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_avail"><?= $assessment->availability_score ?? 10; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="availability_score" min="0" max="<?= $track->code === 'associate' ? '10' : '15'; ?>" step="0.5" value="<?= $assessment->availability_score ?? 10; ?>" data-target="val_avail">
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label class="form-label fw-bold small">5. Motivation & Roster Fit (<?= $track->code === 'associate' ? 'Max 5' : 'Max 10'; ?> pts)</label>
                                        <span class="badge bg-secondary" id="val_motiv"><?= $assessment->motivation_score ?? 8; ?></span>
                                    </div>
                                    <input type="range" class="form-range score-slider" name="motivation_score" min="0" max="<?= $track->code === 'associate' ? '5' : '10'; ?>" step="0.5" value="<?= $assessment->motivation_score ?? 8; ?>" data-target="val_motiv">
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

    // Shortlist Candidate & Email Magic Link Handler
    $('#btn_shortlist_action').on('click', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const origHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Processing...');

        $.ajax({
            url: '<?= $siteConfig->siteUrl; ?>/admin/roster/shortlist',
            type: 'POST',
            data: { rosterapplication: <?= $app->iD; ?> },
            dataType: 'json',
            success: function(res) {
                if (res.status === 1) {
                    $btn.attr('class', 'btn btn-success fw-bold rounded-pill px-4 shadow-sm')
                        .html('<i class="fa fa-check me-1"></i> Shortlisted &amp; Link Sent!');

                    const copyBox = res.dossier_link ? `
                        <div class="mt-2 input-group input-group-sm">
                            <input type="text" class="form-control" value="${res.dossier_link}" id="shortlist_link_input" readonly>
                            <button class="btn btn-outline-dark" type="button" onclick="navigator.clipboard.writeText('${res.dossier_link}'); alert('Link copied to clipboard!');">Copy Link</button>
                        </div>
                    ` : '';

                    $('#review_alert').show().attr('class', 'alert alert-success d-flex align-items-start p-3 shadow-sm mb-4')
                        .html(`<i class="fa fa-check-circle fa-2x me-3 text-success"></i> <div><h6 class="mb-1 fw-bold">Candidate Shortlisted!</h6><div>${res.msg}</div>${copyBox}</div>`);

                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $btn.prop('disabled', false).html(origHtml);
                    alert(res.msg || 'Failed to shortlist candidate.');
                }
            },
            error: function() {
                $btn.prop('disabled', false).html(origHtml);
                alert('Server error while shortlisting candidate.');
            }
        });
    });
});
</script>
