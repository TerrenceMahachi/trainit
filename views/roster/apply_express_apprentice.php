@extends('layouts.main')

<?php
global $siteConfig;

$application = $data['application'] ?? null;
$appId = $application ? (int)$application->iD : 0;
$appProfile = $application ? $application->apprenticeProfile() : null;
$user = $data['user'] ?? null;
$provinces = $data['provinces'] ?? [];
$serviceFunctions = $data['serviceFunctions'] ?? [];
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
                    <span class="badge bg-success px-3 py-2 rounded-pill">
                        <i class="fa fa-graduation-cap me-1"></i> Apprentice Talent Track
                    </span>
                </div>
                <!-- Autosave / Restore Status Indicator -->
                <div id="storage_status_badge" style="display: none;">
                    <span class="badge bg-light text-success border px-2 py-1 small">
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

                <div id="express_alert" style="display:none;" class="alert mb-4"></div>

                <form id="express_apprentice_form" action="<?= $siteConfig->siteUrl; ?>/opportunities/apply/express" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-4">
                    <input type="hidden" name="track_code" value="apprentice">
                    <input type="hidden" name="application_id" value="<?= $appId; ?>">

                    <div class="card-body p-4 p-md-5">
                        <!-- Section 1: Candidate Contact -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-success rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                <h5 class="fw-bold mb-0">Contact & Identity</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                                    <input type="text" name="legal_name" id="field_legal_name" class="form-control" required
                                           value="<?= htmlspecialchars($application->legal_name ?? $user->name ?? ''); ?>"
                                           placeholder="e.g. Tinashe Brian Moyo">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Preferred Name / Call Name</label>
                                    <input type="text" name="preferred_name" id="field_preferred_name" class="form-control"
                                           value="<?= htmlspecialchars($application->preferred_name ?? ''); ?>"
                                           placeholder="e.g. Tinashe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="field_email" class="form-control" required
                                           value="<?= htmlspecialchars($application->email ?? $user->email ?? ''); ?>"
                                           placeholder="tinashe@example.ac.zw" <?= ($user && $user->email) ? 'readonly' : ''; ?>>
                                    <div class="form-text">Used for your shortlist invitation and candidate portal updates.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mobile / WhatsApp Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile_number" id="field_mobile_number" class="form-control" required
                                           value="<?= htmlspecialchars($application->mobile_number ?? ''); ?>"
                                           placeholder="e.g. +263 77 123 4567">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                                    <input type="text" name="city" id="field_city" class="form-control" required
                                           value="<?= htmlspecialchars($application->city ?? ''); ?>"
                                           placeholder="e.g. Harare, Bulawayo, Gweru">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Province <span class="text-danger">*</span></label>
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

                        <!-- Section 2: Academic Standing & Discipline -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-success rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                <h5 class="fw-bold mb-0">Discipline & Academic Standing</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Primary Practice Area / Discipline <span class="text-danger">*</span></label>
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
                                    <label class="form-label fw-semibold">Tertiary Institution Name <span class="text-danger">*</span></label>
                                    <input type="text" name="institution_name" id="field_institution_name" class="form-control" required
                                           value="<?= htmlspecialchars($appProfile->institution_name ?? ''); ?>"
                                           placeholder="e.g. University of Zimbabwe / NUST / HIT / Harare Poly">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Degree / Diploma Programme <span class="text-danger">*</span></label>
                                    <input type="text" name="degree_programme" id="field_degree_programme" class="form-control" required
                                           value="<?= htmlspecialchars($appProfile->degree_programme ?? ''); ?>"
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
                                           value="<?= htmlspecialchars($appProfile->wrl_start_date ?? date('Y-m-d')); ?>">
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
                                <input type="file" name="cv_doc" class="form-control" accept=".pdf,.docx,.doc" <?= $appId > 0 ? '' : 'required'; ?>>
                                <div class="form-text">
                                    Upload your current CV outlining your coursework, academic projects, and software/technical skills. Max 5MB.
                                </div>
                                <?php if ($application && count($application->documents()) > 0): ?>
                                    <div class="mt-2 text-success small">
                                        <i class="fa fa-check-circle me-1"></i> CV document already on file. Upload a new file to replace it.
                                    </div>
                                <?php endif; ?>
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
            </div>
        </div>
    </div>
</main>

<script>
// Resumable Form via LocalStorage
(function() {
    const STORAGE_KEY = 'tsigiro_express_apprentice';
    const form = document.getElementById('express_apprentice_form');
    const badge = document.getElementById('storage_status_badge');
    const clearBtn = document.getElementById('btn_clear_draft');

    const fieldsToTrack = [
        'legal_name', 'preferred_name', 'email', 'mobile_number',
        'city', 'zimprovince', 'primaryfunction', 'institution_name',
        'degree_programme', 'study_level', 'wrl_start_date', 'wrl_duration_months'
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
