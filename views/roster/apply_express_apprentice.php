@extends('layouts.main')

<?php
global $siteConfig;

$application = $data['application'] ?? null;
$appId = $application ? (int)$application->iD : 0;
$appProfile = $application ? $application->apprenticeProfile() : null;
$user = $data['user'] ?? null;
$provinces = $data['provinces'] ?? [];
$serviceFunctions = $data['serviceFunctions'] ?? [];
$currentStep = 1;
$trackCode = 'apprentice';
?>

<main class="portal-dashboard">
    <?php include _VIEWS_PATH . '/roster/apply_nav.php'; ?>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Track Welcome Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #065f46 0%, #047857 100%); color: #ffffff;">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="badge bg-white text-success fw-bold px-3 py-2 rounded-pill">
                                Track 01 &middot; Early Career & Attachment
                            </span>
                            <span class="text-white-50 small"><i class="fa fa-clock me-1"></i> ~3 minutes to complete</span>
                        </div>
                        <h1 class="h2 fw-bold text-white mb-2">Apprentice Talent Intake</h1>
                        <p class="text-white-50 mb-0 max-w-700">
                            Connect your academic learning with hands-on corporate and NGO client projects. Structured mentorship under senior associates with signed institutional logbooks.
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
                                <h5 class="fw-bold mb-0">Candidate Contact & Identity</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                                    <input type="text" name="legal_name" class="form-control" required
                                           value="<?= htmlspecialchars($application->legal_name ?? $user->name ?? ''); ?>"
                                           placeholder="e.g. Tinashe Brian Moyo">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Preferred Name / Call Name</label>
                                    <input type="text" name="preferred_name" class="form-control"
                                           value="<?= htmlspecialchars($application->preferred_name ?? ''); ?>"
                                           placeholder="e.g. Tinashe">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required
                                           value="<?= htmlspecialchars($application->email ?? $user->email ?? ''); ?>"
                                           placeholder="tinashe@example.ac.zw" <?= ($user && $user->email) ? 'readonly' : ''; ?>>
                                    <div class="form-text">Used for your application updates and candidate account access.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mobile / WhatsApp Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="mobile_number" class="form-control" required
                                           value="<?= htmlspecialchars($application->mobile_number ?? ''); ?>"
                                           placeholder="e.g. +263 77 123 4567">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">City / Town <span class="text-danger">*</span></label>
                                    <input type="text" name="city" class="form-control" required
                                           value="<?= htmlspecialchars($application->city ?? ''); ?>"
                                           placeholder="e.g. Harare, Bulawayo, Gweru">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Province <span class="text-danger">*</span></label>
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

                        <!-- Section 2: Academic & Institutional Standing -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-success rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                <h5 class="fw-bold mb-0">Academic Standing & Discipline</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Primary Functional Discipline <span class="text-danger">*</span></label>
                                    <select name="primaryfunction" class="form-select" required>
                                        <option value="">-- Select Practice Area --</option>
                                        <?php foreach ($serviceFunctions as $fn): ?>
                                            <option value="<?= $fn->iD; ?>" <?= ($application && (int)$application->primaryfunction === (int)$fn->iD) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($fn->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Choose the functional area matching your studies or aspirations.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tertiary Institution Name <span class="text-danger">*</span></label>
                                    <input type="text" name="institution_name" class="form-control" required
                                           value="<?= htmlspecialchars($appProfile->institution_name ?? ''); ?>"
                                           placeholder="e.g. University of Zimbabwe / NUST / HIT">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Degree / Diploma Programme <span class="text-danger">*</span></label>
                                    <input type="text" name="degree_programme" class="form-control" required
                                           value="<?= htmlspecialchars($appProfile->degree_programme ?? ''); ?>"
                                           placeholder="e.g. BSc Honours Computer Science / BCom Accounting">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Current Career / Study Stage <span class="text-danger">*</span></label>
                                    <select name="study_level" class="form-select" required>
                                        <option value="">-- Select Study Level --</option>
                                        <option value="Seeking Attachment (Year 3/Part 3)" <?= ($appProfile && $appProfile->study_level === 'Seeking Attachment (Year 3/Part 3)') ? 'selected' : ''; ?>>Seeking WRL Industrial Attachment (Year 3 / Part 3)</option>
                                        <option value="Currently on Attachment" <?= ($appProfile && $appProfile->study_level === 'Currently on Attachment') ? 'selected' : ''; ?>>Currently on Attachment (Seeking Host Change / Extension)</option>
                                        <option value="Final Year Student" <?= ($appProfile && $appProfile->study_level === 'Final Year Student') ? 'selected' : ''; ?>>Final Year Student (Graduating within 6 months)</option>
                                        <option value="Recent Graduate" <?= ($appProfile && $appProfile->study_level === 'Recent Graduate') ? 'selected' : ''; ?>>Recent Graduate (Graduated within past 24 months)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Earliest Available Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="wrl_start_date" class="form-control" required
                                           value="<?= htmlspecialchars($appProfile->wrl_start_date ?? date('Y-m-d')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Available Duration <span class="text-danger">*</span></label>
                                    <select name="wrl_duration_months" class="form-select" required>
                                        <option value="12" <?= ($appProfile && (int)$appProfile->wrl_duration_months === 12) ? 'selected' : ''; ?>>12 Months (Standard Attachment Year)</option>
                                        <option value="6" <?= ($appProfile && (int)$appProfile->wrl_duration_months === 6) ? 'selected' : ''; ?>>6 Months</option>
                                        <option value="3" <?= ($appProfile && (int)$appProfile->wrl_duration_months === 3) ? 'selected' : ''; ?>>3 Months (Short Internship / Vacation Placement)</option>
                                        <option value="24" <?= ($appProfile && (int)$appProfile->wrl_duration_months === 24) ? 'selected' : ''; ?>>24 Months (Graduate Traineeship)</option>
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
                                    Upload your current CV outlining your coursework, academic projects, software/tools familiar with, and any prior part-time or volunteer experience. Max 5MB.
                                </div>
                                <?php if ($application && count($application->documents()) > 0): ?>
                                    <div class="mt-2 text-success small">
                                        <i class="fa fa-check-circle me-1"></i> Currently attached document on file. Upload a new file to replace it.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quick Declaration Checkbox -->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="consent_declaration" id="consent_declaration" required value="1" checked>
                            <label class="form-check-label small text-muted" for="consent_declaration">
                                I confirm that the academic and personal information provided is accurate and represents my bona fide standing as an emerging practitioner.
                            </label>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                            <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" id="btn_submit_express" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                Continue to Step 2: Credentials <i class="fa fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
