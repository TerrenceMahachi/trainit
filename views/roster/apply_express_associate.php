@extends('layouts.main')

<?php
global $siteConfig;

$application = $data['application'] ?? null;
$appId = $application ? (int)$application->iD : 0;
$assocProfile = $application ? $application->associateProfile() : null;
$user = $data['user'] ?? null;
$provinces = $data['provinces'] ?? [];
$serviceFunctions = $data['serviceFunctions'] ?? [];
$employmentStatuses = $data['employmentStatuses'] ?? [];
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
                    <span class="badge bg-primary px-3 py-2 rounded-pill">
                        <i class="fa fa-user-tie me-1"></i> Associate Specialist Network
                    </span>
                </div>
                <?php if (!$isSubmitted): ?>
                <!-- Autosave / Restore Status Indicator -->
                <div id="storage_status_badge" style="display: none;">
                    <span class="badge bg-light text-primary border px-2 py-1 small">
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

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div id="express_alert" style="display:none;" class="alert mb-4"></div>

                <?php if ($isSubmitted): ?>
                    <!-- APPLICATION STATUS & DOSSIER VIEW (Form is hidden because candidate has submitted) -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <div class="card-header border-0 p-4 p-md-5 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
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
                            <h1 class="h2 fw-bold text-white mb-2">Associate Specialist Application Submitted</h1>
                            <p class="text-white-50 mb-0">
                                Submitted on <?= date('M d, Y', strtotime($application->reg_date)); ?> • Vetting and advisory domain matching in progress.
                            </p>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            <!-- 3-Stage Stepper Roadmap -->
                            <div class="mb-4 pb-3 border-bottom">
                                <h5 class="fw-bold mb-3"><i class="fa fa-route text-primary me-2"></i> Application Progression</h5>
                                <div class="row g-3 text-center">
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-4 border bg-primary bg-opacity-10 border-primary h-100">
                                            <div class="badge bg-primary rounded-circle p-2 mb-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-check text-white"></i>
                                            </div>
                                            <div class="fw-bold text-primary small">1. Express Intake &amp; CV</div>
                                            <div class="text-muted small">Executive profile received</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-4 border bg-warning bg-opacity-10 border-warning h-100">
                                            <div class="badge bg-warning text-dark rounded-circle p-2 mb-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-spinner fa-spin"></i>
                                            </div>
                                            <div class="fw-bold text-dark small">2. Advisory Domain Vetting</div>
                                            <div class="text-muted small">Assessing practice track &amp; briefs</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-4 border bg-light text-muted h-100">
                                            <div class="badge bg-secondary rounded-circle p-2 mb-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-user-tie text-white"></i>
                                            </div>
                                            <div class="fw-bold small">3. Specialist Network &amp; Tenders</div>
                                            <div class="text-muted small">Inclusion in PRAZ &amp; client bids</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submitted Details Summary -->
                            <div class="mb-4">
                                <h5 class="fw-bold mb-3"><i class="fa fa-clipboard-list text-primary me-2"></i> Submitted Consultant Details</h5>
                                <div class="bg-light p-4 rounded-4 border">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Consultant Legal Name</label>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($application->legal_name); ?></div>
                                        </div>
                                        <?php if (!empty($application->preferred_name)): ?>
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Preferred Name / Title</label>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($application->preferred_name); ?></div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Primary Practice Area</label>
                                            <div class="fw-bold text-primary"><?= htmlspecialchars($primaryFunction ? $primaryFunction->name : 'N/A'); ?></div>
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
                                            <label class="text-muted small d-block">Primary Location</label>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($application->city); ?><?= $provinceObj ? ', ' . htmlspecialchars($provinceObj->name) : ''; ?></div>
                                        </div>
                                        <?php if ($assocProfile): ?>
                                            <div class="col-md-6">
                                                <label class="text-muted small d-block">Specialist Experience Level</label>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($assocProfile->years_experience ?? 'N/A'); ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small d-block">Indicative Day Rate Expectation</label>
                                                <div class="fw-bold text-primary">$<?= htmlspecialchars($assocProfile->day_rate_expectation ?? '0'); ?> / day</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small d-block">Available Capacity / Bandwidth</label>
                                                <div class="fw-bold text-dark"><?= htmlspecialchars($assocProfile->capacity_days_per_month ?? 'N/A'); ?></div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="col-md-6">
                                            <label class="text-muted small d-block">Curriculum Vitae / Capability Statement</label>
                                            <div class="fw-bold text-success"><i class="fa fa-file-pdf text-danger me-1"></i> Attached &amp; On File</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 rounded-3 mb-4">
                                <i class="fa fa-info-circle me-2"></i>
                                <strong>What happens next?</strong> Our talent committee evaluates associate profiles against current and upcoming advisory requirements. If shortlisted, you will receive an invitation to file practicing credentials, tax compliance, and client references.
                            </div>

                            <!-- Actions Strip: Revoke & Navigation -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#revokeAssociateModal">
                                    <i class="fa fa-trash-can me-2"></i> Revoke / Withdraw Application
                                </button>
                                <div class="d-flex gap-2">
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary rounded-pill px-4">
                                        <i class="fa fa-briefcase me-1"></i> Browse Opportunities
                                    </a>
                                    <a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="btn btn-primary rounded-pill px-4 fw-bold">
                                        <i class="fa fa-gauge me-1"></i> Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revoke Confirmation Modal -->
                    <div class="modal fade" id="revokeAssociateModal" tabindex="-1" aria-labelledby="revokeAssociateModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-danger" id="revokeAssociateModalLabel">
                                        <i class="fa fa-triangle-exclamation me-2"></i> Withdraw Application?
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body py-4">
                                    <p class="text-muted mb-0">
                                        Are you sure you want to withdraw your <strong>Associate Specialist Application</strong>? This will remove your application from our review queue. You will be able to submit a fresh application at any time.
                                    </p>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Keep Application</button>
                                    <button type="button" id="btn_confirm_revoke_assoc" class="btn btn-danger rounded-pill px-4 fw-bold">
                                        <i class="fa fa-trash-can me-1"></i> Yes, Withdraw
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Track Welcome Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="badge bg-primary text-white fw-bold px-3 py-2 rounded-pill">
                                    Single-Page Initial Application
                                </span>
                                <span class="text-white-50 small"><i class="fa fa-clock me-1"></i> ~2 minutes to apply</span>
                            </div>
                            <h1 class="h2 fw-bold text-white mb-2">Associate Specialist Application</h1>
                            <p class="text-white-50 mb-0 max-w-700">
                                Join our vetted specialist roster for advisory missions, PRAZ tenders, and project leadership. Submit your core background and executive CV below. Shortlisted consultants receive an email link to complete full credentials and references.
                            </p>
                        </div>
                    </div>

                    <form id="express_associate_form" action="<?= $siteConfig->siteUrl; ?>/opportunities/apply/express" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-4">
                        <input type="hidden" name="track_code" value="associate">
                        <input type="hidden" name="application_id" value="0">

                        <div class="card-body p-4 p-md-5">
                            <!-- Section 1: Consultant Contact -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                    <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                    <h5 class="fw-bold mb-0">Consultant Identity &amp; Contact</h5>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                                        <input type="text" name="legal_name" id="field_legal_name" class="form-control" required
                                               value="<?= htmlspecialchars($user->name ?? ''); ?>"
                                               placeholder="e.g. Dr. Nyasha Ndlovu">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Preferred Name / Title</label>
                                        <input type="text" name="preferred_name" id="field_preferred_name" class="form-control"
                                               placeholder="e.g. Nyasha">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Professional Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="field_email" class="form-control" required
                                               value="<?= htmlspecialchars($user->email ?? ''); ?>"
                                               placeholder="nyasha.consulting@example.com" <?= ($user && $user->email) ? 'readonly' : ''; ?>>
                                        <div class="form-text">Used for your shortlist invitation and consultant portal access.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Direct Phone / WhatsApp <span class="text-danger">*</span></label>
                                        <input type="tel" name="mobile_number" id="field_mobile_number" class="form-control" required
                                               placeholder="e.g. +263 77 987 6543">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Primary City / Base <span class="text-danger">*</span></label>
                                        <input type="text" name="city" id="field_city" class="form-control" required
                                               placeholder="e.g. Harare, Bulawayo, Diaspora">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Province / Location <span class="text-danger">*</span></label>
                                        <select name="zimprovince" id="field_zimprovince" class="form-select" required>
                                            <option value="">-- Select Province --</option>
                                            <?php foreach ($provinces as $prov): ?>
                                                <option value="<?= $prov->iD; ?>">
                                                    <?= htmlspecialchars($prov->name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Advisory Domain & Experience Level -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                    <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                    <h5 class="fw-bold mb-0">Advisory Domain &amp; Experience Level</h5>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Primary Practice Area <span class="text-danger">*</span></label>
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
                                        <label class="form-label fw-semibold">Years of Specialist Experience <span class="text-danger">*</span></label>
                                        <select name="years_experience" id="field_years_experience" class="form-select" required>
                                            <option value="">-- Select Experience Band --</option>
                                            <option value="5-7 years">5 - 7 Years (Mid-Senior Specialist)</option>
                                            <option value="8-12 years">8 - 12 Years (Principal Consultant)</option>
                                            <option value="13-19 years">13 - 19 Years (Lead Advisor / Director)</option>
                                            <option value="20+ years">20+ Years (Senior Executive Advisor)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Current Professional Status <span class="text-danger">*</span></label>
                                        <select name="employmentstatus" id="field_employmentstatus" class="form-select" required>
                                            <option value="">-- Select Status --</option>
                                            <?php foreach ($employmentStatuses as $es): ?>
                                                <option value="<?= $es->iD; ?>">
                                                    <?= htmlspecialchars($es->name); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Indicative Day Rate Expectation (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="10" min="50" max="5000" name="day_rate_expectation" id="field_day_rate_expectation" class="form-control" required
                                                   placeholder="e.g. 250">
                                            <span class="input-group-text">/ day</span>
                                        </div>
                                        <div class="form-text">Gross daily consultation rate expectation for client scoping.</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Available Capacity / Bandwidth <span class="text-danger">*</span></label>
                                        <select name="capacity_days_per_month" id="field_capacity_days_per_month" class="form-select" required>
                                            <option value="">-- Select Available Time --</option>
                                            <option value="Full-Time (15-22 days/month)">Full-Time (15 - 22 days per month)</option>
                                            <option value="Part-Time (8-14 days/month)">Part-Time (8 - 14 days per month)</option>
                                            <option value="Advisory / Ad-hoc (2-6 days/month)">Advisory Oversight / Ad-hoc Calls (2 - 6 days per month)</option>
                                            <option value="Evenings & Weekends Only">Flexible Deliverable-Based / Evenings &amp; Weekends</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Executive CV Upload -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                    <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                    <h5 class="fw-bold mb-0">Executive CV / Professional Profile</h5>
                                </div>
                                <div class="p-3 bg-light rounded-3 border">
                                    <label class="form-label fw-semibold">Upload CV or Capability Statement (PDF or DOCX) <span class="text-danger">*</span></label>
                                    <input type="file" name="cv_doc" class="form-control" accept=".pdf,.docx,.doc" required>
                                    <div class="form-text">
                                        Include project briefs led, enterprise clients served, team sizes supervised, and technical deliverables produced. Max 5MB.
                                    </div>
                                </div>
                            </div>

                            <!-- Notice: Further Verification On Shortlist -->
                            <div class="p-3 bg-light rounded-3 border mb-4 text-muted small">
                                <i class="fa fa-info-circle text-primary me-1"></i>
                                <strong>Note on Verification:</strong> Professional practising certificates, tax clearances (ITF263), detailed competency matrices, and client references are requested <em>only after shortlisting</em> via an email invitation link.
                            </div>

                            <!-- Declaration Checkbox -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="consent_declaration" id="consent_declaration" required value="1" checked>
                                <label class="form-check-label small text-muted" for="consent_declaration">
                                    I confirm the accuracy of my professional background and agree to Tsigiro's independent associate engagement terms.
                                </label>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                                <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary rounded-pill px-4">
                                    <i class="fa fa-arrow-left me-1"></i> Cancel
                                </a>
                                <button type="submit" id="btn_submit_express" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
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
    const btnRevoke = document.getElementById('btn_confirm_revoke_assoc');
    if (btnRevoke) {
        btnRevoke.addEventListener('click', async function() {
            btnRevoke.disabled = true;
            btnRevoke.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Withdrawing...';
            try {
                const formData = new FormData();
                formData.append('application_id', '<?= $appId; ?>');
                formData.append('track_code', 'associate');

                const res = await fetch('<?= $siteConfig->siteUrl; ?>/opportunities/apply/revoke', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.status === 1) {
                    try { localStorage.removeItem('tsigiro_express_associate'); } catch(e){}
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
    const STORAGE_KEY = 'tsigiro_express_associate';
    const form = document.getElementById('express_associate_form');
    if (form) {
        const badge = document.getElementById('storage_status_badge');
        const clearBtn = document.getElementById('btn_clear_draft');

        const fieldsToTrack = [
            'legal_name', 'preferred_name', 'email', 'mobile_number',
            'city', 'zimprovince', 'primaryfunction', 'years_experience',
            'employmentstatus', 'day_rate_expectation', 'capacity_days_per_month'
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

        // Clear storage on successful submission
        form.addEventListener('submit', function() {
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (e) {}
        });
    }
})();
</script>
