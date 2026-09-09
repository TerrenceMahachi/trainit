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
$professionalBodies = $data['professionalBodies'] ?? [];
$currentStep = 1;
$trackCode = 'associate';
?>

<main class="portal-dashboard">
    <?php include _VIEWS_PATH . '/roster/apply_nav.php'; ?>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Track Welcome Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="badge bg-primary text-white fw-bold px-3 py-2 rounded-pill">
                                Track 02 &middot; Specialist & Advisory Network
                            </span>
                            <span class="text-white-50 small"><i class="fa fa-clock me-1"></i> ~3 minutes to complete</span>
                        </div>
                        <h1 class="h2 fw-bold text-white mb-2">Associate Specialist Intake</h1>
                        <p class="text-white-50 mb-0 max-w-700">
                            Deploy your advisory oversight, technical leadership, and domain expertise on flexible client missions. Transparent milestone-based USD day rates without permanent employment restrictions.
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
                                    <input type="text" name="legal_name" class="form-control" required
                                           value="<?= htmlspecialchars($application->legal_name ?? $user->name ?? ''); ?>"
                                           placeholder="e.g. Dr. Nyasha Ndlovu">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Preferred Name / Title</label>
                                    <input type="text" name="preferred_name" class="form-control"
                                           value="<?= htmlspecialchars($application->preferred_name ?? ''); ?>"
                                           placeholder="e.g. Nyasha">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Professional Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required
                                           value="<?= htmlspecialchars($application->email ?? $user->email ?? ''); ?>"
                                           placeholder="nyasha.consulting@example.com" <?= ($user && $user->email) ? 'readonly' : ''; ?>>
                                    <div class="form-text">Used for your vetting correspondence and talent portal login.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Direct Phone / WhatsApp <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile_number" class="form-control" required
                                           value="<?= htmlspecialchars($application->mobile_number ?? ''); ?>"
                                           placeholder="e.g. +263 77 987 6543">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Primary City / Base <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control" required
                                           value="<?= htmlspecialchars($application->city ?? ''); ?>"
                                           placeholder="e.g. Harare, Bulawayo, Mutare, Diaspora">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Province / Location <span class="text-danger">*</span></label>
                                    <select name="zimprovince" class="form-select" required>
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

                        <!-- Section 2: Advisory Specialization & Experience -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-primary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                <h5 class="fw-bold mb-0">Advisory Domain & Experience Level</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Primary Practice Area <span class="text-danger">*</span></label>
                                    <select name="primaryfunction" class="form-select" required>
                                        <option value="">-- Select Practice Area --</option>
                                        <?php foreach ($serviceFunctions as $fn): ?>
                                            <option value="<?= $fn->iD; ?>" <?= ($application && (int)$application->primaryfunction === (int)$fn->iD) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($fn->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Total Years of Specialist Experience <span class="text-danger">*</span></label>
                                    <select name="years_experience" class="form-select" required>
                                        <option value="">-- Select Experience Band --</option>
                                        <option value="5-7 years" <?= ($assocProfile && $assocProfile->years_experience === '5-7 years') ? 'selected' : ''; ?>>5 - 7 Years (Mid-Senior Specialist)</option>
                                        <option value="8-12 years" <?= ($assocProfile && $assocProfile->years_experience === '8-12 years') ? 'selected' : ''; ?>>8 - 12 Years (Principal Consultant)</option>
                                        <option value="13-19 years" <?= ($assocProfile && $assocProfile->years_experience === '13-19 years') ? 'selected' : ''; ?>>13 - 19 Years (Lead Advisor / Director)</option>
                                        <option value="20+ years" <?= ($assocProfile && $assocProfile->years_experience === '20+ years') ? 'selected' : ''; ?>>20+ Years (Senior Executive Advisor)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Current Professional Status <span class="text-danger">*</span></label>
                                    <select name="employmentstatus" class="form-select" required>
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
                                        <input type="number" step="10" min="50" max="5000" name="day_rate_expectation" class="form-control" required
                                               value="<?= htmlspecialchars($assocProfile->day_rate_expectation ?? ''); ?>"
                                               placeholder="e.g. 250">
                                        <span class="input-group-text">/ day</span>
                                    </div>
                                    <div class="form-text">Gross daily consultation rate expectation for client scoping.</div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Available Capacity / Bandwidth <span class="text-danger">*</span></label>
                                    <select name="capacity_days_per_month" class="form-select" required>
                                        <option value="">-- Select Available Time --</option>
                                        <option value="Full-Time (15-22 days/month)" <?= ($assocProfile && $assocProfile->capacity_days_per_month === 'Full-Time (15-22 days/month)') ? 'selected' : ''; ?>>Full-Time (15 - 22 days per month)</option>
                                        <option value="Part-Time (8-14 days/month)" <?= ($assocProfile && $assocProfile->capacity_days_per_month === 'Part-Time (8-14 days/month)') ? 'selected' : ''; ?>>Part-Time (8 - 14 days per month)</option>
                                        <option value="Advisory / Ad-hoc (2-6 days/month)" <?= ($assocProfile && $assocProfile->capacity_days_per_month === 'Advisory / Ad-hoc (2-6 days/month)') ? 'selected' : ''; ?>>Advisory Oversight / Ad-hoc Calls (2 - 6 days per month)</option>
                                        <option value="Evenings & Weekends Only" <?= ($assocProfile && $assocProfile->capacity_days_per_month === 'Evenings & Weekends Only') ? 'selected' : ''; ?>>Flexible Deliverable-Based / Evenings & Weekends</option>
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
                                <label class="form-label fw-semibold">Upload Comprehensive CV or Capability Statement (PDF or DOCX) <span class="text-danger">*</span></label>
                                <input type="file" name="cv_doc" class="form-control" accept=".pdf,.docx,.doc" <?= $appId > 0 ? '' : 'required'; ?>>
                                <div class="form-text">
                                    Include project briefs led, donor/enterprise clients served, team sizes supervised, and technical deliverables produced. Max 5MB.
                                </div>
                                <?php if ($application && count($application->documents()) > 0): ?>
                                    <div class="mt-2 text-success small">
                                        <i class="fa fa-check-circle me-1"></i> Executive profile document on file. Upload a new file to replace it.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quick Declaration Checkbox -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="consent_declaration" id="consent_declaration" required value="1" checked>
                            <label class="form-check-label small text-muted" for="consent_declaration">
                                I confirm the accuracy of my professional background and agree to Tsigiro's independent associate engagement and vetting terms.
                            </label>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                            <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" id="btn_submit_express" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                Continue to Step 2: Credentials <i class="fa fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
