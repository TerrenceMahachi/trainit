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
?>

<main class="portal-dashboard">
    <!-- Clean Breadcrumb (No multi-step wizard tabs) -->
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
                <!-- Autosave / Restore Status Indicator -->
                <div id="storage_status_badge" style="display: none;">
                    <span class="badge bg-light text-primary border px-2 py-1 small">
                        <i class="fa fa-cloud-arrow-down me-1"></i> Form draft restored
                    </span>
                    <button type="button" id="btn_clear_draft" class="btn btn-link btn-sm text-danger p-0 ms-2 text-decoration-none small">
                        <i class="fa fa-trash-can"></i> Clear
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
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

                <div id="express_alert" style="display:none;" class="alert mb-4"></div>

                <form id="express_associate_form" action="<?= $siteConfig->siteUrl; ?>/opportunities/apply/express" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-4">
                    <input type="hidden" name="track_code" value="associate">
                    <input type="hidden" name="application_id" value="<?= $appId; ?>">

                    <div class="card-body p-4 p-md-5">
                        <!-- Section 1: Consultant Contact -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                <h5 class="fw-bold mb-0">Consultant Identity & Contact</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                                    <input type="text" name="legal_name" id="field_legal_name" class="form-control" required
                                           value="<?= htmlspecialchars($application->legal_name ?? $user->name ?? ''); ?>"
                                           placeholder="e.g. Dr. Nyasha Ndlovu">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Preferred Name / Title</label>
                                    <input type="text" name="preferred_name" id="field_preferred_name" class="form-control"
                                           value="<?= htmlspecialchars($application->preferred_name ?? ''); ?>"
                                           placeholder="e.g. Nyasha">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Professional Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="field_email" class="form-control" required
                                           value="<?= htmlspecialchars($application->email ?? $user->email ?? ''); ?>"
                                           placeholder="nyasha.consulting@example.com" <?= ($user && $user->email) ? 'readonly' : ''; ?>>
                                    <div class="form-text">Used for your shortlist invitation and consultant portal access.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Direct Phone / WhatsApp <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile_number" id="field_mobile_number" class="form-control" required
                                           value="<?= htmlspecialchars($application->mobile_number ?? ''); ?>"
                                           placeholder="e.g. +263 77 987 6543">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Primary City / Base <span class="text-danger">*</span></label>
                                    <input type="text" name="city" id="field_city" class="form-control" required
                                           value="<?= htmlspecialchars($application->city ?? ''); ?>"
                                           placeholder="e.g. Harare, Bulawayo, Diaspora">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Province / Location <span class="text-danger">*</span></label>
                                    <select name="zimprovince" id="field_zimprovince" class="form-select" required>
                                        <option value="">-- Select Province --</option>
                                        <?php foreach ($provinces as $prov): ?>
                                            <option value="<?= $prov->iD; ?>" <?= ($application && (int)$application->zimprovince === (int)$prov->iD) ? 'selected' : ''; ?>>
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
                                <h5 class="fw-bold mb-0">Advisory Domain & Experience Level</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Primary Practice Area <span class="text-danger">*</span></label>
                                    <select name="primaryfunction" id="field_primaryfunction" class="form-select" required>
                                        <option value="">-- Select Practice Area --</option>
                                        <?php foreach ($serviceFunctions as $fn): ?>
                                            <option value="<?= $fn->iD; ?>" <?= ($application && (int)$application->primaryfunction === (int)$fn->iD) ? 'selected' : ''; ?>>
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
                                            <option value="<?= $es->iD; ?>" <?= ($assocProfile && (int)$assocProfile->employmentstatus === (int)$es->iD) ? 'selected' : ''; ?>>
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
                                               value="<?= htmlspecialchars($assocProfile->day_rate_expectation ?? ''); ?>"
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
                                        <option value="Evenings & Weekends Only">Flexible Deliverable-Based / Evenings & Weekends</option>
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
                                <input type="file" name="cv_doc" class="form-control" accept=".pdf,.docx,.doc" <?= $appId > 0 ? '' : 'required'; ?>>
                                <div class="form-text">
                                    Include project briefs led, enterprise clients served, team sizes supervised, and technical deliverables produced. Max 5MB.
                                </div>
                                <?php if ($application && count($application->documents()) > 0): ?>
                                    <div class="mt-2 text-success small">
                                        <i class="fa fa-check-circle me-1"></i> Executive profile already on file. Upload a new file to replace it.
                                    </div>
                                <?php endif; ?>
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
            </div>
        </div>
    </div>
</main>

<script>
// Resumable Form via LocalStorage
(function() {
    const STORAGE_KEY = 'tsigiro_express_associate';
    const form = document.getElementById('express_associate_form');
    const badge = document.getElementById('storage_status_badge');
    const clearBtn = document.getElementById('btn_clear_draft');

    const fieldsToTrack = [
        'legal_name', 'preferred_name', 'email', 'mobile_number',
        'city', 'zimprovince', 'primaryfunction', 'years_experience',
        'employmentstatus', 'day_rate_expectation', 'capacity_days_per_month'
    ];

    // Restore from localStorage on page load if no server application_id
    const appId = parseInt("<?= $appId; ?>") || 0;
    if (appId === 0) {
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
})();
</script>
