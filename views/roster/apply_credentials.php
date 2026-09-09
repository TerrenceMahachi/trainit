@extends('layouts.main')

<?php
global $siteConfig;

$application = $data['application'];
$appId = (int)$application->iD;
$track = $application->applicationtrack();
$trackCode = $track ? $track->code : 'apprentice';
$appProfile = $application->apprenticeProfile();
$assocProfile = $application->associateProfile();
$qualifications = $application->qualifications();
$documents = $application->documents();
$qualificationTypes = $data['qualificationTypes'] ?? [];
$professionalBodies = $data['professionalBodies'] ?? [];
$currentStep = 2;
?>

<main class="portal-dashboard">
    <?php include _VIEWS_PATH . '/roster/apply_nav.php'; ?>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h3 fw-bold mb-1">Step 2: Qualifications & Supporting Credentials</h2>
                        <p class="text-muted mb-0">Provide verifiable academic records and official identification for vetting compliance.</p>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">
                        Application #<?= $appId; ?>
                    </span>
                </div>

                <div id="credentials_alert" style="display:none;" class="alert mb-4"></div>

                <form id="credentials_form" action="<?= $siteConfig->siteUrl; ?>/roster/apply/credentials" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-4 mb-4">
                    <input type="hidden" name="application_id" value="<?= $appId; ?>">

                    <div class="card-body p-4 p-md-5">
                        <!-- Section 1: Highest Educational Qualification -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-secondary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                <h5 class="fw-bold mb-0">Primary Qualification / Degree</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Qualification Level <span class="text-danger">*</span></label>
                                    <select name="qualificationtype" class="form-select" required>
                                        <option value="">-- Select Qualification Level --</option>
                                        <?php foreach ($qualificationTypes as $qt): ?>
                                            <option value="<?= $qt->iD; ?>">
                                                <?= htmlspecialchars($qt->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Degree / Certificate Title <span class="text-danger">*</span></label>
                                    <input type="text" name="qualification_title" class="form-control" required
                                           placeholder="e.g. Bachelor of Science in Information Technology">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Awarding Institution <span class="text-danger">*</span></label>
                                    <input type="text" name="qualification_institution" class="form-control" required
                                           placeholder="e.g. University of Zimbabwe / ACCA UK">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Major Field of Study <span class="text-danger">*</span></label>
                                    <input type="text" name="field_of_study" class="form-control" required
                                           placeholder="e.g. Software Engineering / Accounting">
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Track Specific Credentials & Docs -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-secondary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                <h5 class="fw-bold mb-0"><?= $trackCode === 'apprentice' ? 'Attachment & Institutional Verification' : 'Professional Accreditation & Compliance'; ?></h5>
                            </div>

                            <?php if ($trackCode === 'apprentice'): ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Student Registration / ID Number <span class="text-danger">*</span></label>
                                        <input type="text" name="student_reg_number" class="form-control" required
                                               value="<?= htmlspecialchars($appProfile->student_reg_number ?? ''); ?>"
                                               placeholder="e.g. R214582H">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Expected Graduation Year <span class="text-danger">*</span></label>
                                        <input type="number" min="2024" max="2030" name="expected_completion_year" class="form-control" required
                                               value="<?= htmlspecialchars($appProfile->expected_completion_date ? substr($appProfile->expected_completion_date, 0, 4) : date('Y', strtotime('+1 year'))); ?>"
                                               placeholder="e.g. 2027">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <label class="form-label fw-semibold">Official WRL / Attachment Letter</label>
                                            <input type="file" name="wrl_letter_doc" class="form-control" accept=".pdf,.jpg,.png">
                                            <div class="form-text">Official institutional introduction letter seeking attachment placement (PDF or Image).</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <label class="form-label fw-semibold">Academic Results Transcript</label>
                                            <input type="file" name="transcript_doc" class="form-control" accept=".pdf,.jpg,.png">
                                            <div class="form-text">Recent official or portal academic results transcript for grade verification.</div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Professional Body / Institute</label>
                                        <select name="professionalbody" class="form-select">
                                            <option value="">-- Select Body (if applicable) --</option>
                                            <?php foreach ($professionalBodies as $pb): ?>
                                                <option value="<?= $pb->iD; ?>">
                                                    <?= htmlspecialchars($pb->name); ?> (<?= htmlspecialchars($pb->abbreviation ?? ''); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Membership / Registration Number</label>
                                        <input type="text" name="professional_reg_number" class="form-control"
                                               placeholder="e.g. ICAZ-4921 / ACCA-10492">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <label class="form-label fw-semibold">Practising Certificate / Membership Doc</label>
                                            <input type="file" name="pro_cert_doc" class="form-control" accept=".pdf,.jpg,.png">
                                            <div class="form-text">Proof of active professional standing or practising licence.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3 border h-100">
                                            <label class="form-label fw-semibold">ZIMRA Tax Clearance (ITF263 / BP) (Optional)</label>
                                            <input type="file" name="tax_clearance_doc" class="form-control" accept=".pdf,.jpg,.png">
                                            <div class="form-text">Upload valid ITF263 tax clearance certificate if consulting as an entity.</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Section 3: National Identity Document -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-secondary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                <h5 class="fw-bold mb-0">National Identification (ID or Passport)</h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">National ID / Passport Number <span class="text-danger">*</span></label>
                                    <input type="text" name="national_id_number" class="form-control" required
                                           placeholder="e.g. 63-123456-X-42">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Upload ID / Passport Copy <span class="text-danger">*</span></label>
                                    <input type="file" name="national_id_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="form-text">Clear scanned copy or photo of your metal/plastic National ID or Passport bio-data page.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Uploaded Documents Preview -->
                        <?php if (!empty($documents)): ?>
                            <div class="mb-4">
                                <h6 class="fw-bold mb-2"><i class="fa fa-folder-open text-primary me-1"></i> Already Uploaded Documents</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white align-middle">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>Document Type</th>
                                                <th>File Name</th>
                                                <th>Size</th>
                                                <th>Uploaded</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($documents as $doc): 
                                                $docType = $doc->documenttype();
                                            ?>
                                                <tr>
                                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($docType ? $docType->name : 'Document'); ?></span></td>
                                                    <td class="text-truncate" style="max-width: 200px;"><?= htmlspecialchars($doc->original_name); ?></td>
                                                    <td><?= round($doc->file_size_kb, 1); ?> KB</td>
                                                    <td class="small text-muted"><?= htmlspecialchars($doc->reg_date); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Form Actions (Native Links) -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                            <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/<?= $trackCode; ?>?id=<?= $appId; ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa fa-arrow-left me-1"></i> Back: Step 1
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                Save & Continue to Step 3: Skills <i class="fa fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
