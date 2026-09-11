@extends('layouts.main')

<?php
global $siteConfig;
$clientObj = $data['client'] ?? $client ?? null;
$clientName = htmlspecialchars($clientObj ? (is_object($clientObj) ? $clientObj->trading_name : $clientObj['trading_name']) : 'Client Workspace');
$clientId = $clientObj ? (is_object($clientObj) ? $clientObj->iD : $clientObj['iD']) : 0;
$activePlans = $data['activePlans'] ?? $activePlans ?? [];
$priorities = $data['priorities'] ?? $priorities ?? [];
$serviceFunctions = $data['serviceFunctions'] ?? $serviceFunctions ?? [];
$serviceCategories = $data['serviceCategories'] ?? $serviceCategories ?? [];
$activeTab = 'request_new';
?>

<main class="py-4" style="background-color: #fcfbfe; min-height: 85vh;">
    <div class="container-xl">

        <?php include _VIEWS_PATH . '/clients/nav.php'; ?>

        <!-- Resumable LocalStorage Draft Restored Alert -->
        <div id="draftRestoredBanner" class="alert alert-info alert-dismissible fade show rounded-4 mb-4 shadow-sm" style="display: none;" role="alert">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <i class="fa fa-history me-2 text-primary"></i>
                    <strong>Draft Restored:</strong> We automatically restored your uncommitted brief from your browser session.
                </div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1" onclick="clearDraft();">
                        <i class="fa fa-trash-alt me-1"></i> Discard Draft
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left 8-col: Main Structured Brief Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2 border-bottom pb-3">
                        <div>
                            <span class="badge bg-primary px-3 py-1 rounded-pill mb-2 fw-bold">
                                <i class="fa fa-file-contract me-1"></i> Work Brief Builder
                            </span>
                            <h3 class="fw-bold mb-1" style="color: #2A114B;">Submit New Service Request</h3>
                            <p class="text-muted small mb-0">Outline your project deliverables, technical scope, and desired milestones for your assigned team.</p>
                        </div>
                        <div>
                            <span class="badge bg-light text-muted border px-3 py-2 rounded-pill small" id="autoSaveIndicator">
                                <i class="fa fa-shield-alt text-success me-1"></i> Autosave active
                            </span>
                        </div>
                    </div>

                    <form id="clientWorkRequestForm" action="<?= $siteConfig->siteUrl ?>/client/requests/submit" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="client_id" id="field_client_id" value="<?= $clientId ?>">

                        <div class="row g-4">
                            <!-- Section 1: Retainer Plan & Practice Area -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fa fa-cubes text-primary me-1"></i> Active Retainer Plan <span class="text-danger">*</span>
                                </label>
                                <select name="service_plan_id" id="field_service_plan_id" class="form-select rounded-3 py-2" required>
                                    <?php if (empty($activePlans)): ?>
                                        <option value="1">Standard On-Demand Managed Plan</option>
                                    <?php else: ?>
                                        <?php foreach ($activePlans as $pItem): ?>
                                            <?php $pl = $pItem['plan']; ?>
                                            <option value="<?= is_object($pl) ? $pl->iD : $pl['iD'] ?>">
                                                <?= htmlspecialchars(is_object($pl) ? $pl->plan_name : $pl['plan_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text small">Task hours will be drawn from this plan's allowance.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fa fa-briefcase text-secondary me-1"></i> Practice Area / Function
                                </label>
                                <select name="service_function_id" id="field_service_function_id" class="form-select rounded-3 py-2">
                                    <option value="0">General / Cross-functional</option>
                                    <?php foreach ($serviceFunctions as $fn): ?>
                                        <option value="<?= is_object($fn) ? $fn->iD : $fn['iD'] ?>">
                                            <?= htmlspecialchars(is_object($fn) ? $fn->name : $fn['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text small">Assists our dispatch lead with targeted talent matching.</div>
                            </div>

                            <!-- Section 2: Title & Core Objective -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fa fa-heading text-primary me-1"></i> Engagement Title &amp; Objective <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title" id="field_title" class="form-control rounded-3 py-2" 
                                       placeholder="e.g. Kubernetes Cluster Ingress Failover &amp; Database Backup Automation" required>
                                <div class="form-text small">A concise headline summarizing the core business or technical outcome.</div>
                            </div>

                            <!-- Section 3: Scope of Work & Deliverables -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fa fa-align-left text-primary me-1"></i> Detailed Scope of Work &amp; Specifications <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" id="field_description" class="form-control rounded-3" rows="6" 
                                          placeholder="Please specify:&#10;1. Background context & current systems in place&#10;2. Specific deliverables or changes required&#10;3. Technical constraints or access protocols&#10;4. Key stakeholders or contacts" required></textarea>
                            </div>

                            <!-- Section 4: Expected Acceptance Criteria -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fa fa-check-double text-success me-1"></i> Acceptance Criteria (Success Definition)
                                </label>
                                <textarea name="acceptance_criteria" id="field_acceptance_criteria" class="form-control rounded-3" rows="3" 
                                          placeholder="e.g. Unit tests passing, live verification on staging, signed supervisor review sign-off, or audit acquittal report submitted."></textarea>
                            </div>

                            <!-- Section 5: Timeline & Urgency -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fa fa-flag text-danger me-1"></i> Priority &amp; SLA Urgency <span class="text-danger">*</span>
                                </label>
                                <select name="priority_id" id="field_priority_id" class="form-select rounded-3 py-2" required>
                                    <?php foreach ($priorities as $pri): ?>
                                        <option value="<?= is_object($pri) ? $pri->iD : $pri['iD'] ?>" <?= (is_object($pri) ? $pri->code : $pri['code']) === 'MEDIUM' ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(is_object($pri) ? $pri->name : $pri['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text small">Urgent/Critical tickets trigger immediate 15-min notification to service managers.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fa fa-calendar-alt text-primary me-1"></i> Target Delivery Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="desired_due_date" id="field_desired_due_date" class="form-control rounded-3 py-2" 
                                       value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                                <div class="form-text small">Requested date for deliverable completion and review.</div>
                            </div>

                            <!-- Section 6: Staffing & Talent Preference -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small mb-2">
                                    <i class="fa fa-users-gear text-info me-1"></i> Staffing Deployment Model
                                </label>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="card border rounded-3 p-3 h-100 bg-light">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="staffing_preference" id="staff_blended" value="blended" checked>
                                                <label class="form-check-label fw-semibold text-dark small" for="staff_blended">
                                                    Blended Delivery Team
                                                </label>
                                            </div>
                                            <p class="text-muted small mb-0 mt-1" style="font-size: 0.8rem;">Senior Associate oversight paired with dedicated Apprentice contributor (Cost-effective &amp; Quality assured).</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border rounded-3 p-3 h-100 bg-light">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="staffing_preference" id="staff_associate" value="associate">
                                                <label class="form-check-label fw-semibold text-dark small" for="staff_associate">
                                                    Associate Specialist Lead
                                                </label>
                                            </div>
                                            <p class="text-muted small mb-0 mt-1" style="font-size: 0.8rem;">Direct senior specialist deployment for architecture, audits, advisory, or sensitive production deliverables.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border rounded-3 p-3 h-100 bg-light">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="staffing_preference" id="staff_apprentice" value="apprentice">
                                                <label class="form-check-label fw-semibold text-dark small" for="staff_apprentice">
                                                    Mentored Apprentice
                                                </label>
                                            </div>
                                            <p class="text-muted small mb-0 mt-1" style="font-size: 0.8rem;">Hands-on practitioner support for execution, testing, data entry, and documentation under mentor review.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 7: File Attachment -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fa fa-paperclip text-secondary me-1"></i> Optional Scope Attachments (Brief, Specs, Mockups)
                                </label>
                                <input type="file" name="attachment" id="field_attachment" class="form-control rounded-3">
                                <div class="form-text small">Accepted formats: PDF, DOCX, ZIP, PNG, JPG (Max 15MB). Additional files can be attached in the request workspace.</div>
                            </div>

                            <!-- Form Actions -->
                            <div class="col-12 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="saveDraftManual();">
                                    <i class="fa fa-save me-1"></i> Save Draft
                                </button>
                                <div class="d-flex gap-2">
                                    <a href="<?= $siteConfig->siteUrl ?>/client/portal" class="btn btn-light rounded-pill px-4">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-5 shadow-sm" id="btnSubmitBrief">
                                        <i class="fa fa-paper-plane me-1"></i> Submit Work Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right 4-col: Instructions & Help Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-3" style="color: #2A114B;">
                        <i class="fa fa-lightbulb text-warning me-2"></i> How Work Requests Are Processed
                    </h5>
                    <ol class="small text-muted ps-3 mb-3" style="line-height: 1.7;">
                        <li class="mb-2"><strong>Instant Intake:</strong> Your ticket is immediately logged in the Tsigiro Dispatch Desk and an email receipt is dispatched.</li>
                        <li class="mb-2"><strong>Triage &amp; SLA Commitment:</strong> A dedicated Service Manager reviews scope feasibility and commits an SLA delivery target.</li>
                        <li class="mb-2"><strong>Practitioner Dispatch:</strong> Qualified Associates and Apprentices are paired with supervision mentors to execute tasks.</li>
                        <li class="mb-2"><strong>Live Collaboration:</strong> Exchange messages, review draft work, and upload revised assets directly in your Request Workspace.</li>
                        <li><strong>Sign-Off &amp; Closure:</strong> Once deliverables are approved, confirm completion and submit your satisfaction rating.</li>
                    </ol>

                    <div class="border-top pt-3">
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <i class="fa fa-shield-halved text-success"></i>
                            <span>Enterprise confidentiality and NDA protection active across all briefs.</span>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 100%);">
                    <h6 class="fw-bold mb-2 text-warning">Need Immediate Assistance?</h6>
                    <p class="small text-white-50 mb-3">If this is an active production emergency affecting client systems, please mark priority as <strong>Urgent</strong> or contact our operations lead directly.</p>
                    <a href="mailto:support@tsigiro.co.zw" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold">
                        <i class="fa fa-envelope me-1"></i> support@tsigiro.co.zw
                    </a>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
// Continuous LocalStorage Autosave Engine
const STORAGE_KEY = 'tsigiro_client_work_request';

function saveDraft() {
    const data = {
        service_plan_id: $('#field_service_plan_id').val(),
        service_function_id: $('#field_service_function_id').val(),
        title: $('#field_title').val(),
        description: $('#field_description').val(),
        acceptance_criteria: $('#field_acceptance_criteria').val(),
        priority_id: $('#field_priority_id').val(),
        desired_due_date: $('#field_desired_due_date').val(),
        staffing_preference: $('input[name="staffing_preference"]:checked').val(),
        saved_at: new Date().toISOString()
    };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    $('#autoSaveIndicator').html('<i class="fa fa-check text-success me-1"></i> Draft saved ' + new Date().toLocaleTimeString());
}

function restoreDraft() {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return;

    try {
        const data = JSON.parse(raw);
        if (data.title || data.description) {
            if (data.service_plan_id) $('#field_service_plan_id').val(data.service_plan_id);
            if (data.service_function_id) $('#field_service_function_id').val(data.service_function_id);
            if (data.title) $('#field_title').val(data.title);
            if (data.description) $('#field_description').val(data.description);
            if (data.acceptance_criteria) $('#field_acceptance_criteria').val(data.acceptance_criteria);
            if (data.priority_id) $('#field_priority_id').val(data.priority_id);
            if (data.desired_due_date) $('#field_desired_due_date').val(data.desired_due_date);
            if (data.staffing_preference) {
                $('input[name="staffing_preference"][value="' + data.staffing_preference + '"]').prop('checked', true);
            }
            $('#draftRestoredBanner').fadeIn();
        }
    } catch (e) {
        console.error('Error parsing restored draft', e);
    }
}

function clearDraft() {
    localStorage.removeItem(STORAGE_KEY);
    $('#field_title').val('');
    $('#field_description').val('');
    $('#field_acceptance_criteria').val('');
    $('#draftRestoredBanner').fadeOut();
    $('#autoSaveIndicator').html('<i class="fa fa-info-circle text-muted me-1"></i> Draft cleared');
}

function saveDraftManual() {
    saveDraft();
    alert('Your work brief draft has been saved in your browser storage. You can safely return anytime to finish and submit.');
}

$(document).ready(function () {
    restoreDraft();

    // Listen to changes for continuous autosave
    $('#field_service_plan_id, #field_service_function_id, #field_title, #field_description, #field_acceptance_criteria, #field_priority_id, #field_desired_due_date, input[name="staffing_preference"]').on('input change', function () {
        saveDraft();
    });

    // Clear draft on form submission
    $('#clientWorkRequestForm').on('submit', function () {
        localStorage.removeItem(STORAGE_KEY);
    });
});
</script>
