@extends('layouts.main')

<?php
global $siteConfig;

$application = $data['application'];
$appId = (int)$application->iD;
$track = $application->applicationtrack();
$trackCode = $track ? $track->code : 'apprentice';
$workHistories = $application->workHistories();
$referees = $application->referees();
$refereeTimings = $data['refereeTimings'] ?? [];
$currentStep = 4;
?>

<main class="portal-dashboard">
    <?php include _VIEWS_PATH . '/roster/apply_nav.php'; ?>

    <div class="container py-3">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h3 fw-bold mb-1">Step 4: Practical Experience & Referees</h2>
                        <p class="text-muted mb-0">
                            <?= $trackCode === 'apprentice' ? 'Share your practical coursework/internships and academic supervisor reference.' : 'Document key consulting deliverables and verifiable client/colleague references.'; ?>
                        </p>
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2">
                        Application #<?= $appId; ?>
                    </span>
                </div>

                <div id="experience_alert" style="display:none;" class="alert mb-4"></div>

                <form id="experience_form" action="<?= $siteConfig->siteUrl; ?>/roster/apply/experience" method="POST" class="card border-0 shadow-sm rounded-4 mb-4">
                    <input type="hidden" name="application_id" value="<?= $appId; ?>">

                    <div class="card-body p-4 p-md-5">
                        <!-- Section 1: Practical Deliverables / Work History -->
                        <div class="mb-5">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-secondary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                <h5 class="fw-bold mb-0"><?= $trackCode === 'apprentice' ? 'Key Practical Project or Internship' : 'Recent Professional Engagement / Role'; ?></h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><?= $trackCode === 'apprentice' ? 'Institution / Host Company' : 'Client / Employer Organization'; ?> <span class="text-danger">*</span></label>
                                    <input type="text" name="organization_name" class="form-control" required
                                           placeholder="e.g. <?= $trackCode === 'apprentice' ? 'University Project / FinTech Labs' : 'Econet Wireless / UNICEF Zimbabwe / Private Client'; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Role / Project Title <span class="text-danger">*</span></label>
                                    <input type="text" name="position_title" class="form-control" required
                                           placeholder="e.g. <?= $trackCode === 'apprentice' ? 'Lead Developer (Final Year Capstone) / Junior Intern' : 'Senior Systems Consultant / Advisory Lead'; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Start Period <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" class="form-control" required
                                           value="<?= date('Y-m-d', strtotime('-1 year')); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">End Period</label>
                                    <input type="date" name="end_date" class="form-control"
                                           value="<?= date('Y-m-d'); ?>">
                                    <div class="form-text">Leave blank if currently ongoing.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Key Deliverables & Responsibilities <span class="text-danger">*</span></label>
                                    <textarea name="key_deliverables" rows="3" class="form-control" required
                                              placeholder="Summarize what you built, analyzed, audited, or managed, including technologies or outcomes achieved."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Verifiable Referees -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                                <span class="badge bg-secondary rounded-circle p-2" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                <h5 class="fw-bold mb-0"><?= $trackCode === 'apprentice' ? 'Academic or Institutional Referee' : 'Professional Referees (1 Required)'; ?></h5>
                            </div>
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <h6 class="fw-bold text-dark mb-3">Primary Referee Contact</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="referee_name" class="form-control" required
                                               placeholder="e.g. Dr. K. Sibanda / Eng. M. Chitiyo">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Organization / University <span class="text-danger">*</span></label>
                                        <input type="text" name="referee_org" class="form-control" required
                                               placeholder="e.g. University of Zimbabwe / Deloitte">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Position / Designation <span class="text-danger">*</span></label>
                                        <input type="text" name="referee_pos" class="form-control" required
                                               placeholder="e.g. Senior Lecturer / Head of Department / Managing Partner">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Professional Relationship <span class="text-danger">*</span></label>
                                        <input type="text" name="referee_relationship" class="form-control" required
                                               placeholder="e.g. Academic Supervisor / Direct Client / Former Manager">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="referee_email" class="form-control" required
                                               placeholder="referee@institution.ac.zw">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Phone / WhatsApp Number <span class="text-danger">*</span></label>
                                        <input type="tel" name="referee_phone" class="form-control" required
                                               placeholder="+263 77 000 0000">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Contact Permission Timing <span class="text-danger">*</span></label>
                                        <select name="refereecontacttiming" class="form-select" required>
                                            <option value="1">Contact at any time during vetting</option>
                                            <option value="2">Only contact after initial screening shortlist</option>
                                            <option value="3">Notify me before reaching out</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions (Native Links) -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 border-top">
                            <a href="<?= $siteConfig->siteUrl; ?>/roster/apply/skills?id=<?= $appId; ?>" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fa fa-arrow-left me-1"></i> Back: Step 3
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                Save & Continue to Step 5: Review & Submit <i class="fa fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
