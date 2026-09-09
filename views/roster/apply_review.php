@extends('layouts.main')

<?php
global $siteConfig;

$application = $data['application'];
$appId = (int)$application->iD;
$track = $application->applicationtrack();
$trackCode = $track ? $track->code : 'apprentice';
$primaryFunction = $application->primaryfunction();
$appProfile = $application->apprenticeProfile();
$assocProfile = $application->associateProfile();
$qualifications = $application->qualifications();
$skills = $application->skills();
$workHistories = $application->workHistories();
$referees = $application->referees();
$documents = $application->documents();
$currentStep = 5;
?>

<main class="portal-dashboard">
    <?php include _VIEWS_PATH . '/roster/apply_nav.php'; ?>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h3 fw-bold mb-1">Step 5: Review & Digital Declaration</h2>
                        <p class="text-muted mb-0">Confirm your application details and sign the professional vetting consent.</p>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">
                        Application #<?= $appId; ?>
                    </span>
                </div>

                <div id="review_alert" style="display:none;" class="alert mb-4"></div>

                <!-- Review Section 1: Candidate Identity -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle p-2" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;"><i class="fa fa-check small text-white"></i></span>
                            <h6 class="fw-bold mb-0">1. Candidate Identity & Contact</h6>
                        </div>
                        <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/<?= $trackCode; ?>?id=<?= $appId; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="fa fa-pencil me-1"></i> Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 small">
                            <div class="col-sm-6"><strong>Full Legal Name:</strong> <?= htmlspecialchars($application->legal_name); ?></div>
                            <div class="col-sm-6"><strong>Email:</strong> <?= htmlspecialchars($application->email); ?></div>
                            <div class="col-sm-6"><strong>Mobile / WhatsApp:</strong> <?= htmlspecialchars($application->mobile_number); ?></div>
                            <div class="col-sm-6"><strong>Location:</strong> <?= htmlspecialchars($application->city ?? ''); ?>, <?= htmlspecialchars($application->zimprovince()->name ?? ''); ?></div>
                            <div class="col-sm-6"><strong>Discipline:</strong> <span class="badge bg-light text-dark border"><?= htmlspecialchars($primaryFunction ? $primaryFunction->name : 'N/A'); ?></span></div>
                            <div class="col-sm-6"><strong>Track:</strong> <span class="badge <?= $trackCode === 'associate' ? 'bg-primary' : 'bg-success'; ?>"><?= htmlspecialchars($track ? $track->name : ''); ?></span></div>
                        </div>
                    </div>
                </div>

                <!-- Review Section 2: Credentials & Docs -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle p-2" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;"><i class="fa fa-check small text-white"></i></span>
                            <h6 class="fw-bold mb-0">2. Credentials & Uploaded Documents</h6>
                        </div>
                        <a href="<?= $siteConfig->siteUrl; ?>/roster/apply/credentials?id=<?= $appId; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="fa fa-pencil me-1"></i> Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($trackCode === 'apprentice' && $appProfile): ?>
                            <p class="small mb-2"><strong>Institution:</strong> <?= htmlspecialchars($appProfile->institution_name ?? 'N/A'); ?> &middot; <strong>Programme:</strong> <?= htmlspecialchars($appProfile->degree_programme ?? 'N/A'); ?> (Reg: <?= htmlspecialchars($appProfile->student_reg_number ?? 'N/A'); ?>)</p>
                        <?php elseif ($trackCode === 'associate' && $assocProfile): ?>
                            <p class="small mb-2"><strong>Experience:</strong> <?= htmlspecialchars($assocProfile->years_experience ?? 'N/A'); ?> &middot; <strong>Rate Expectation:</strong> $<?= htmlspecialchars($assocProfile->day_rate_expectation ?? 'N/A'); ?>/day &middot; <strong>Bandwidth:</strong> <?= htmlspecialchars($assocProfile->capacity_days_per_month ?? 'N/A'); ?></p>
                        <?php endif; ?>

                        <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                            <?php if (empty($documents)): ?>
                                <span class="text-muted small">No documents recorded yet.</span>
                            <?php else: ?>
                                <?php foreach ($documents as $doc): ?>
                                    <span class="badge bg-light text-secondary border p-2">
                                        <i class="fa fa-file-pdf text-danger me-1"></i>
                                        <?= htmlspecialchars($doc->original_name); ?> (<?= round($doc->file_size_kb, 1); ?> KB)
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Review Section 3: Skills & Competencies -->
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle p-2" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;"><i class="fa fa-check small text-white"></i></span>
                            <h6 class="fw-bold mb-0">3. Competencies & Self-Assessment</h6>
                        </div>
                        <a href="<?= $siteConfig->siteUrl; ?>/roster/apply/skills?id=<?= $appId; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="fa fa-pencil me-1"></i> Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($skills)): ?>
                            <p class="text-muted small mb-0">No specific skills selected.</p>
                        <?php else: ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($skills as $sk): 
                                    $item = $sk->skillitem();
                                    $lvl = $sk->proficiencylevel();
                                ?>
                                    <span class="badge bg-light text-dark border p-2">
                                        <?= htmlspecialchars($item ? $item->name : 'Skill'); ?>:
                                        <strong class="text-primary"><?= htmlspecialchars($lvl ? $lvl->name : 'Rated'); ?></strong>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Review Section 4: Experience & Referees -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success rounded-circle p-2" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;"><i class="fa fa-check small text-white"></i></span>
                            <h6 class="fw-bold mb-0">4. Experience & Referees</h6>
                        </div>
                        <a href="<?= $siteConfig->siteUrl; ?>/roster/apply/experience?id=<?= $appId; ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="fa fa-pencil me-1"></i> Edit
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($workHistories)): ?>
                            <div class="mb-2">
                                <strong>Experience:</strong>
                                <?php foreach ($workHistories as $wh): ?>
                                    <span class="badge bg-light text-dark border ms-1">
                                        <?= htmlspecialchars($wh->position_title); ?> at <?= htmlspecialchars($wh->organization_name); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($referees)): ?>
                            <div>
                                <strong>Referee:</strong>
                                <?php foreach ($referees as $rf): ?>
                                    <span class="badge bg-light text-dark border ms-1">
                                        <?= htmlspecialchars($rf->referee_name); ?> (<?= htmlspecialchars($rf->organization); ?> &middot; <?= htmlspecialchars($rf->email); ?>)
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Digital Declarations & Submission Form -->
                <form id="final_submit_form" action="<?= $siteConfig->siteUrl; ?>/roster/apply/submit" method="POST" class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <input type="hidden" name="application_id" value="<?= $appId; ?>">

                    <div class="card-header bg-dark text-white p-4">
                        <h5 class="fw-bold mb-1"><i class="fa fa-file-contract text-warning me-2"></i> Professional Conduct & Vetting Declaration</h5>
                        <p class="text-white-50 small mb-0">By submitting this application, you enter the Tsigiro talent evaluation pipeline.</p>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="p-3 bg-light rounded-3 border mb-4 small text-muted lh-base">
                            <p class="mb-2">
                                <strong>1. Accuracy of Information:</strong> I declare that all statements, qualifications, transcripts, certificates, and work deliverables referenced in this application are genuine and free of misrepresentation.
                            </p>
                            <p class="mb-2">
                                <strong>2. Authorization for Verification:</strong> I hereby authorize Tsigiro, its vetting officers, and its institutional partners to contact educational institutions, previous employers, and designated referees to verify my standing.
                            </p>
                            <p class="mb-0">
                                <strong>3. Client Confidentiality:</strong> I agree to maintain strict confidentiality regarding all Tsigiro methodologies, client briefs, and technical engagements to which I may be assigned.
                            </p>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Digital Signature (Type Full Name) <span class="text-danger">*</span></label>
                                <input type="text" name="e_signature" class="form-control form-control-lg font-monospace" required
                                       value="<?= htmlspecialchars($application->e_signature ?? $application->legal_name); ?>"
                                       placeholder="e.g. Tinashe Brian Moyo">
                                <div class="form-text">Your typed legal name constitutes an electronic signature under the Cyber & Data Protection Act.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date & IP Timestamp</label>
                                <input type="text" class="form-control form-control-lg bg-light" readonly
                                       value="<?= date('Y-m-d H:i'); ?> &middot; <?= $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'; ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                            <a href="<?= $siteConfig->siteUrl; ?>/roster/apply/experience?id=<?= $appId; ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa fa-arrow-left me-1"></i> Back: Step 4
                            </a>
                            <button type="submit" id="btn_final_submit" class="btn btn-success btn-lg rounded-pill px-5 fw-bold shadow">
                                <i class="fa fa-paper-plane me-2"></i> Submit Application for Vetting
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
