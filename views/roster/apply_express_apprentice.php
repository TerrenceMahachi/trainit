@extends('layouts.main')

<?php
global $siteConfig;

$application = $data['application'] ?? null;
$appId = $application ? (int)$application->iD : 0;
$appProfile = $application ? $application->apprenticeProfile() : null;
$user = $data['user'] ?? null;
$provinces = $data['provinces'] ?? [];
$serviceFunctions = $data['serviceFunctions'] ?? [];
$documents = $data['documents'] ?? ($application ? $application->documents() : []);

$isSubmitted = $application && !in_array((int)$application->applicationstatus, [8, 9]);
$statusObj = $application ? $application->applicationstatus() : null;
$statusCode = $statusObj ? $statusObj->code : 'submitted';
$statusName = $statusObj ? $statusObj->name : 'Under Review';
$primaryFunction = $application ? $application->primaryfunction() : null;
$provinceObj = $application ? $application->zimprovince() : null;
?>

<main class="portal-dashboard">
    <!-- Clean Breadcrumb -->
    <div class="bg-white border-bottom shadow-sm mb-4">
        <div class="container py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        <i class="fa fa-arrow-left me-1"></i> Back to Opportunities
                    </a>
                    <span class="badge bg-success px-3 py-2 rounded-pill">
                        <i class="fa fa-graduation-cap me-1"></i> Apprentice Talent Track
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <?php if (!$user && !$isSubmitted): ?>
                        <span class="small text-muted me-2 d-none d-sm-inline">
                            Already registered? <a href="<?= $siteConfig->siteUrl; ?>/login" class="text-success fw-semibold">Sign in here</a>
                        </span>
                    <?php endif; ?>
                    <?php if (!$isSubmitted): ?>
                    <!-- Autosave / Restore Status Indicator -->
                    <div id="storage_status_badge" style="display: none;">
                        <span class="badge bg-light text-success border px-2 py-1 small">
                            <i class="fa fa-cloud-arrow-down me-1"></i> Form draft restored
                        </span>
                        <button type="button" id="btn_clear_draft" class="btn btn-link btn-sm text-danger p-0 ms-2 text-decoration-none small">
                            <i class="fa fa-trash-can"></i> Clear
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div id="express_alert" style="display:none;" class="alert mb-4"></div>

                <?php if ($isSubmitted): ?>
                    <!-- APPLICATION STATUS & DOSSIER VIEW (Form is hidden because candidate has submitted) -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header border-0 p-4 p-md-5 text-white" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%);">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <span class="badge bg-white bg-opacity-25 text-white fw-bold px-3 py-2 rounded-pill font-monospace">
                                    TSG-<?= date('Y', strtotime($application->reg_date)); ?>-<?= str_pad($appId, 4, '0', STR_PAD_LEFT); ?>
                                </span>
                                <?php
                                $badgeClass = 'bg-warning text-dark';
                                if ($statusCode === 'screened') $badgeClass = 'bg-info text-dark';
                                elseif (in_array($statusCode, ['interviewed', 'on_roster', 'deployed'])) $badgeClass = 'bg-success text-white';
                                ?>
                                <span class="badge <?= $badgeClass; ?> px-3 py-2 rounded-pill fw-bold fs-6">
                                    <i class="fa fa-clock me-1"></i> <?= htmlspecialchars($statusName); ?>
                                </span>
                            </div>
                            <h1 class="h2 fw-bold text-white mb-2">Apprentice Application Submitted</h1>
                            <p class="text-white-50 mb-0">
                                Submitted on <?= date('M d, Y', strtotime($application->reg_date)); ?> • Vetting and host placement scoping in progress.
                            </p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <!-- 3-Stage Stepper Roadmap -->
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold mb-3"><i class="fa fa-route text-success me-2"></i> Application Progression</h5>
                                <div class="row g-3 text-center">
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-4 border bg-success bg-opacity-10 border-success h-100">
                                            <div class="badge bg-success rounded-circle p-2 mb-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-check text-white"></i>
                                            </div>
                                            <div class="fw-bold text-success small">1. Express Intake &amp; CV</div>
                                            <div class="text-muted small">Application received on file</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-4 border bg-warning bg-opacity-10 border-warning h-100">
                                            <div class="badge bg-warning text-dark rounded-circle p-2 mb-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-spinner fa-spin"></i>
                                            </div>
                                            <div class="fw-bold text-dark small">2. Vetting &amp; Discipline Matching</div>
                                            <div class="text-muted small">Scoping host assignments</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-4 border bg-light text-muted h-100">
                                            <div class="badge bg-secondary rounded-circle p-2 mb-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-graduation-cap text-white"></i>
                                            </div>
                                            <div class="fw-bold small">3. Placement &amp; Portal Access</div>
                                            <div class="text-muted small">Workspace activation upon approval</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submitted Details Summary -->
                            <div class="mb-4">
                                <h5 class="fw-bold mb-3"><i class="fa fa-clipboard-list text-success me-2"></i> Submitted Application Details</h5>
                                <div class="bg-light p-4 rounded-4 border">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Full Legal Name</label>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($application->legal_name); ?></div>
                                        </div>
                                        <?php if (!empty($application->preferred_name)): ?>
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Preferred / Call Name</label>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($application->preferred_name); ?></div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Primary Practice Area / Discipline</label>
                                            <div class="fw-bold text-success"><?= htmlspecialchars($primaryFunction ? $primaryFunction->name : 'N/A'); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Email Address</label>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($application->email); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Mobile / WhatsApp Number</label>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($application->mobile_number); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Location</label>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($application->city); ?><?= $provinceObj ? ', ' . htmlspecialchars($provinceObj->name) : ''; ?></div>
                                        </div>
                                        <?php if ($appProfile): ?>
                                            <div class="col-md-6">
                                                <label class="text-muted small d-block">Tertiary Institution</label>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($appProfile->institution_name ?? 'N/A'); ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small d-block">Degree / Diploma Programme</label>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($appProfile->degree_programme ?? 'N/A'); ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small d-block">Available Start Date</label>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($appProfile->wrl_start_date ?? 'Immediate'); ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small d-block">Attachment Duration</label>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($appProfile->wrl_duration_months ? $appProfile->wrl_duration_months . ' Months' : 'N/A'); ?></div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Curriculum Vitae</label>
                                            <div class="fw-bold text-success"><i class="fa fa-file-pdf text-danger me-1"></i> Attached &amp; On File</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 rounded-3 mb-4">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>What happens next?</strong> Our committee reviews applications within 24–48 hours. If shortlisted, you will receive an invitation email to submit supplementary credentials and referee details.
                            </div>

                            <!-- Actions Strip: Revoke & Navigation -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#revokeApprenticeModal">
                                    <i class="fa fa-trash-can me-2"></i> Revoke / Withdraw Application
                                </button>
                                <div class="d-flex gap-2">
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary rounded-pill px-4">
                                        <i class="fa fa-briefcase me-1"></i> Browse Opportunities
                                    </a>
                                    <a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="btn btn-success rounded-pill px-4 fw-bold">
                                        <i class="fa fa-gauge me-1"></i> Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revoke Confirmation Modal -->
                    <div class="modal fade" id="revokeApprenticeModal" tabindex="-1" aria-labelledby="revokeApprenticeModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger" id="revokeApprenticeModalLabel">
                                        <i class="fa fa-triangle-exclamation me-2"></i> Withdraw Application?
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-4">
                                    <p class="text-muted mb-0">
                                        Are you sure you want to withdraw your <strong>Apprentice Application</strong>? This will remove your application from our review queue. You will be able to submit a fresh application at any time.
                                    </p>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Keep Application</button>
                                    <button type="button" id="btn_confirm_revoke_appr" class="btn btn-danger rounded-pill px-4 fw-bold">
                                        <i class="fa fa-trash-can me-1"></i> Yes, Withdraw
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Track Welcome Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%); color: #ffffff;">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="badge bg-white text-success fw-bold px-3 py-2 rounded-pill">
                                    Single-Page Initial Application
                                </span>
                                <span class="text-white-50 small"><i class="fa fa-clock me-1"></i> ~2 minutes to apply</span>
                            </div>
                            <h1 class="h2 fw-bold text-white mb-2">Apprentice Talent Application</h1>
                            <p class="text-white-50 mb-0 max-w-700">
                                Apply for mentored client placement and industrial attachment. Fill in your basic details and upload your CV below. If shortlisted, you will receive an invitation email to complete your credentials and references.
                            </p>
                        </div>
                    </div>

                    <form id="express_apprentice_form" action="<?= $siteConfig->siteUrl; ?>/opportunities/apply/express" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-4">
                        <input type="hidden" name="track_code" value="apprentice">
                        <input type="hidden" name="application_id" value="0">

                        <div class="card-body p-4 p-md-5">
                            <!-- Section 1: Candidate Contact -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                    <span class="badge bg-success rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                    <h5 class="fw-bold mb-0">Contact &amp; Identity</h5>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                                        <input type="text" name="legal_name" id="field_legal_name" class="form-control" required
                                               value="<?= htmlspecialchars($user->name ?? ''); ?>"
                                               placeholder="e.g. Tinashe Brian Moyo">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Preferred Name / Call Name</label>
                                        <input type="text" name="preferred_name" id="field_preferred_name" class="form-control"
                                               placeholder="e.g. Tinashe">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="field_email" class="form-control" required
                                               value="<?= htmlspecialchars($user->email ?? ''); ?>"
                                               placeholder="tinashe@example.ac.zw" <?= ($user && $user->email) ? 'readonly' : ''; ?>>
                                        <div class="form-text">Used for your shortlist invitation and candidate portal updates.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Mobile / WhatsApp Number <span class="text-danger">*</span></label>
                                        <input type="tel" name="mobile_number" id="field_mobile_number" class="form-control" required
                                               placeholder="e.g. +263 77 123 4567">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                                        <input type="text" name="city" id="field_city" class="form-control" required
                                               placeholder="e.g. Harare, Bulawayo, Gweru">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Province <span class="text-danger">*</span></label>
                                        <select name="zimprovince" id="field_zimprovince" class="form-select" required>
                                            <option value="">-- Select Province --</option>
                                            <?php foreach ($provinces as $prov): ?>
                                                <option value="<?= $prov->iD; ?>">
                                                    <?= htmlspecialchars($prov->name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php if ($user): ?>
                                        <div class="col-12 mt-2">
                                            <div class="alert alert-info py-2 px-3 small mb-0 rounded-3 d-flex align-items-center gap-2">
                                                <i class="fa fa-user-check text-success fs-5"></i>
                                                <div>
                                                    Signed in as <strong><?= htmlspecialchars($user->name); ?></strong> (<?= htmlspecialchars($user->email); ?>). You can manage your applications anytime via your candidate dashboard.
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <!-- Sign-In Account Credentials -->
                                        <div class="col-12 mt-3">
                                            <div class="p-3 rounded-3 bg-light border border-success-subtle">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="fw-semibold text-dark"><i class="fa fa-lock text-success me-2"></i>Create Portal Account Sign-In Details</span>
                                                    <span class="badge bg-success text-white">Direct Access</span>
                                                </div>
                                                <p class="small text-muted mb-3">Set a password for your candidate account so you can log in anytime to monitor your application progress, complete your Verified Talent Dossier, and manage your profile.</p>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Create Password <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" name="password" id="field_password" class="form-control" required minlength="6" placeholder="Min 6 characters">
                                                            <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="field_password" title="Show / Hide Password">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                        </div>
                                                        <div class="form-text">Minimum 6 characters.</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" name="password_confirmation" id="field_password_confirmation" class="form-control" required minlength="6" placeholder="Re-enter password">
                                                            <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="field_password_confirmation" title="Show / Hide Password">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                        </div>
                                                        <div id="password_match_feedback" class="form-text text-muted">Must match password above.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Section 2: Academic Standing & Discipline -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                    <span class="badge bg-success rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                    <h5 class="fw-bold mb-0">Discipline &amp; Academic Standing</h5>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Primary Practice Area / Discipline <span class="text-danger">*</span></label>
                                        <select name="primaryfunction" id="field_primaryfunction" class="form-select" required>
                                            <option value="">-- Select Practice Area --</option>
                                            <?php foreach ($serviceFunctions as $fn): ?>
                                                <option value="<?= $fn->iD; ?>">
                                                    <?= htmlspecialchars($fn->name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tertiary Institution Name <span class="text-danger">*</span></label>
                                        <input type="text" name="institution_name" id="field_institution_name" class="form-control" required
                                               placeholder="e.g. University of Zimbabwe / NUST / HIT / Harare Poly">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Degree / Diploma Programme <span class="text-danger">*</span></label>
                                        <input type="text" name="degree_programme" id="field_degree_programme" class="form-control" required
                                               placeholder="e.g. BSc Honours Computer Science">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Current Study / Career Stage <span class="text-danger">*</span></label>
                                        <select name="study_level" id="field_study_level" class="form-select" required>
                                            <option value="">-- Select Study Level --</option>
                                            <option value="Seeking Attachment (Year 3/Part 3)">Seeking WRL Industrial Attachment (Year 3 / Part 3)</option>
                                            <option value="Currently on Attachment">Currently on Attachment (Seeking Extension / Host Change)</option>
                                            <option value="Final Year Student">Final Year Student (Graduating within 6 months)</option>
                                            <option value="Recent Graduate">Recent Graduate (Graduated within past 24 months)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Earliest Available Start Date <span class="text-danger">*</span></label>
                                        <input type="date" name="wrl_start_date" id="field_wrl_start_date" class="form-control" required
                                               value="<?= date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Available Duration <span class="text-danger">*</span></label>
                                        <select name="wrl_duration_months" id="field_wrl_duration_months" class="form-select" required>
                                            <option value="12">12 Months (Standard Attachment Year)</option>
                                            <option value="6">6 Months</option>
                                            <option value="3">3 Months (Short Internship / Vacation)</option>
                                            <option value="24">24 Months (Graduate Traineeship)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: CV / Resume Upload -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                    <span class="badge bg-success rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                    <h5 class="fw-bold mb-0">Curriculum Vitae / Resume</h5>
                                </div>
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label fw-semibold">Upload Your CV / Resume (PDF or DOCX) <span class="text-danger">*</span></label>
                                    <input type="file" name="cv_doc" class="form-control" accept=".pdf,.docx,.doc" required>
                                    <div class="form-text">
                                        Upload your current CV outlining your coursework, academic projects, and software/technical skills. Max 5MB.
                                    </div>
                                </div>
                            </div>

                            <!-- Notice: Further Verification On Shortlist -->
                            <div class="p-3 bg-light rounded-3 border mb-4 text-muted small">
                                <i class="fa fa-info-circle text-primary me-1"></i>
                                <strong>Note on Verification:</strong> Transcripts, institutional recommendation letters, skills proficiency assessments, and referee contacts are requested <em>only after shortlisting</em> via an email invitation link.
                            </div>

                            <!-- Declaration Checkbox -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="consent_declaration" id="consent_declaration" required value="1" checked>
                                <label class="form-check-label small text-muted" for="consent_declaration">
                                    I confirm that the academic and personal details provided are accurate and reflect my bona fide status as an emerging practitioner.
                                </label>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                                <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="fa fa-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" id="btn_submit_express" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                    <i class="fa fa-paper-plane me-2"></i> Submit Application &amp; Upload CV
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
(function() {
    // Revocation Action Handling
    const btnRevoke = document.getElementById('btn_confirm_revoke_appr');
    if (btnRevoke) {
        btnRevoke.addEventListener('click', async function() {
            btnRevoke.disabled = true;
            btnRevoke.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Withdrawing...';
            try {
                const formData = new FormData();
                formData.append('application_id', '<?= $appId; ?>');
                formData.append('track_code', 'apprentice');

                const res = await fetch('<?= $siteConfig->siteUrl; ?>/opportunities/apply/revoke', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.status === 1) {
                    try { localStorage.removeItem('tsigiro_express_apprentice'); } catch(e){}
                    window.location.reload();
                } else {
                    alert(data.message || 'Error withdrawing application.');
                    btnRevoke.disabled = false;
                    btnRevoke.innerHTML = '<i class="fa fa-trash-can me-1"></i> Yes, Withdraw';
                }
            } catch (err) {
                console.error(err);
                alert('Failed to withdraw application. Please try again.');
                btnRevoke.disabled = false;
                btnRevoke.innerHTML = '<i class="fa fa-trash-can me-1"></i> Yes, Withdraw';
            }
        });
    }

    // Resumable Form via LocalStorage
    const STORAGE_KEY = 'tsigiro_express_apprentice';
    const form = document.getElementById('express_apprentice_form');
    if (form) {
        const badge = document.getElementById('storage_status_badge');
        const clearBtn = document.getElementById('btn_clear_draft');

        const fieldsToTrack = [
            'legal_name', 'preferred_name', 'email', 'mobile_number',
            'city', 'zimprovince', 'primaryfunction', 'institution_name',
            'degree_programme', 'study_level', 'wrl_start_date', 'wrl_duration_months'
        ];

        // Restore from localStorage
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                const data = JSON.parse(saved);
                let restoredCount = 0;
                fieldsToTrack.forEach(name => {
                    const el = form.elements[name];
                    if (el && data[name] !== undefined && data[name] !== '') {
                        el.value = data[name];
                        restoredCount++;
                    }
                });
                if (restoredCount > 0 && badge) {
                    badge.style.display = 'inline-flex';
                }
            }
        } catch (e) {
            console.error('LocalStorage restore error', e);
        }

        // Save on input / change
        form.addEventListener('input', function(e) {
            if (fieldsToTrack.includes(e.target.name)) {
                saveDraft();
            }
        });
        form.addEventListener('change', function(e) {
            if (fieldsToTrack.includes(e.target.name)) {
                saveDraft();
            }
        });

        function saveDraft() {
            const data = {};
            fieldsToTrack.forEach(name => {
                const el = form.elements[name];
                if (el) data[name] = el.value;
            });
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            } catch (e) {}
        }

        // Clear Draft
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Clear saved form draft?')) {
                    localStorage.removeItem(STORAGE_KEY);
                    fieldsToTrack.forEach(name => {
                        const el = form.elements[name];
                        if (el && !el.readOnly) el.value = '';
                    });
                    badge.style.display = 'none';
                }
            });
        }

        // Clear storage and validate passwords on submission
        form.addEventListener('submit', function(e) {
            const passInput = document.getElementById('field_password');
            const confirmInput = document.getElementById('field_password_confirmation');
            if (passInput && confirmInput) {
                if (passInput.value.length < 6) {
                    alert('Please choose a password with at least 6 characters.');
                    passInput.focus();
                    e.preventDefault();
                    return false;
                }
                if (passInput.value !== confirmInput.value) {
                    alert('Passwords do not match. Please verify your password confirmation.');
                    confirmInput.focus();
                    e.preventDefault();
                    return false;
                }
            }
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (e) {}
        });

        // Password visibility toggles
        document.querySelectorAll('.toggle-password-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                const icon = this.querySelector('i');
                if (targetInput) {
                    if (targetInput.type === 'password') {
                        targetInput.type = 'text';
                        if (icon) {
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        }
                    } else {
                        targetInput.type = 'password';
                        if (icon) {
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    }
                }
            });
        });

        // Real-time password match feedback
        const passField = document.getElementById('field_password');
        const confirmField = document.getElementById('field_password_confirmation');
        const matchFeedback = document.getElementById('password_match_feedback');

        function checkPasswordMatch() {
            if (!passField || !confirmField || !matchFeedback) return;
            if (confirmField.value.length === 0) {
                matchFeedback.textContent = 'Must match password above.';
                matchFeedback.className = 'form-text text-muted';
            } else if (passField.value === confirmField.value) {
                matchFeedback.innerHTML = '<span class="text-success fw-semibold"><i class="fa fa-check me-1"></i> Passwords match</span>';
            } else {
                matchFeedback.innerHTML = '<span class="text-danger fw-semibold"><i class="fa fa-xmark me-1"></i> Passwords do not match</span>';
            }
        }
        if (passField) passField.addEventListener('input', checkPasswordMatch);
        if (confirmField) confirmField.addEventListener('input', checkPasswordMatch);
    }
})();
</script>
