@extends('layouts.main')

<?php
global $siteConfig;

$application = $data['application'];
$appId = (int)$application->iD;
$track = $application->applicationtrack();
$trackCode = $track ? $track->code : 'apprentice';
$statusObj = $application->applicationstatus();
$statusCode = $statusObj ? $statusObj->code : 'submitted';
$primaryFunction = $application->primaryfunction();
$documents = $application->documents();
$statusEvents = $application->statusEvents();

// Calculate milestone progression
$step1Done = true;
$step2Done = in_array($statusCode, ['interviewed', 'on_roster', 'deployed']);
$step3Done = in_array($statusCode, ['on_roster', 'deployed']);
$step4Done = in_array($statusCode, ['on_roster', 'deployed']);
?>

<main class="portal-dashboard">
    <section class="portal-dashboard-header py-4 bg-dark text-white">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker mb-1">
                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="text-white-50 text-decoration-none">
                        <i class="fa fa-arrow-left me-1"></i> Opportunities
                    </a> &rsaquo; Application Status
                </p>
                <h1 class="h2 mb-1">Candidate Onboarding Status</h1>
                <p class="text-white-50 mb-0">Application Reference: <strong class="text-white">TSG-<?= date('Y', strtotime($application->reg_date)); ?>-<?= str_pad($appId, 4, '0', STR_PAD_LEFT); ?></strong></p>
            </div>
            <div class="portal-account-summary py-2 px-3 text-end">
                <span class="text-white-50 small d-block">Current Status</span>
                <?php
                $badgeClass = 'bg-warning text-dark';
                if ($statusCode === 'screened') $badgeClass = 'bg-info text-dark';
                elseif (in_array($statusCode, ['interviewed', 'on_roster', 'deployed'])) $badgeClass = 'bg-success text-white';
                ?>
                <span class="badge <?= $badgeClass; ?> fs-6 px-3 py-2 rounded-pill">
                    <i class="fa fa-clock me-1"></i> <?= htmlspecialchars($statusObj ? $statusObj->name : 'Submitted'); ?>
                </span>
            </div>
        </div>
    </section>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Flash Notifications -->
                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                        <i class="fa fa-check-circle me-2"></i> <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($_SESSION['flash_warning'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                        <i class="fa fa-info-circle me-2"></i> <?= htmlspecialchars($_SESSION['flash_warning']); unset($_SESSION['flash_warning']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                        <i class="fa fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Contextual Status Banners -->
                <?php if ($statusCode === 'screened'): ?>
                    <!-- Shortlisted: Call to Action to Complete Dossier -->
                    <div class="card border-0 bg-success bg-opacity-10 border-start border-success border-4 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                <div>
                                    <h4 class="fw-bold text-success mb-1"><i class="fa fa-trophy text-warning me-2"></i> Congratulations! You Have Been Shortlisted</h4>
                                    <p class="text-muted mb-0">
                                        Your CV and profile have been reviewed and selected by our talent committee. Please complete your verification dossier (academic credentials, skills matrix, and referees) to proceed to final onboarding.
                                    </p>
                                </div>
                                <div>
                                    <a href="<?= $siteConfig->siteUrl; ?>/roster/apply/credentials?id=<?= $appId; ?>" class="btn btn-success btn-lg rounded-pill px-4 fw-bold shadow text-nowrap">
                                        Complete Dossier <i class="fa fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($statusCode === 'submitted'): ?>
                    <!-- Initial Submission Received - Under Screening -->
                    <div class="card border-0 bg-light border-start border-primary border-4 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-primary mb-1"><i class="fa fa-check-circle text-primary me-2"></i> Application &amp; CV Received</h5>
                            <p class="text-muted mb-0">
                                Your application has been logged and is under screening by our vetting panel. To keep our intake lean, detailed verification (certificates, skills ratings, and referee checks) is only required from shortlisted candidates. If selected, you will receive an invitation email containing a secure link to complete the candidate dossier.
                            </p>
                        </div>
                    </div>
                <?php elseif ($statusCode === 'interviewed'): ?>
                    <!-- Verification Dossier Filed -->
                    <div class="card border-0 bg-primary bg-opacity-10 border-start border-primary border-4 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-primary mb-1"><i class="fa fa-clipboard-check text-primary me-2"></i> Verification Dossier Received</h5>
                            <p class="text-muted mb-0">
                                Your qualifications, competency matrix, and referee contacts have been received. Our review committee is finalizing background verification and will contact you regarding your induction interview.
                            </p>
                        </div>
                    </div>
                <?php elseif (in_array($statusCode, ['on_roster', 'deployed'])): ?>
                    <!-- Active on Roster -->
                    <div class="card border-0 bg-success bg-opacity-10 border-start border-success border-4 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold text-success mb-1"><i class="fa fa-star text-warning me-2"></i> Active Talent Network Member</h5>
                            <p class="text-muted mb-0">
                                You are verified and active on the Tsigiro Roster. You will be notified when matched to client briefs and project opportunities.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Milestone Roadmap Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0"><i class="fa fa-route text-primary me-2"></i> 4-Stage Vetting &amp; Onboarding Roadmap</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 text-center">
                            <!-- Milestone 1 -->
                            <div class="col-sm-6 col-md-3">
                                <div class="p-3 rounded-4 border bg-success bg-opacity-10 border-success">
                                    <div class="badge bg-success rounded-circle p-2 mb-2" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-check text-white"></i>
                                    </div>
                                    <h6 class="fw-bold text-success mb-1">1. Express Intake</h6>
                                    <p class="text-muted small mb-0">Profile &amp; CV submitted.</p>
                                </div>
                            </div>
                            <!-- Milestone 2 -->
                            <div class="col-sm-6 col-md-3">
                                <?php
                                $m2Class = 'bg-light text-muted';
                                $m2Badge = 'bg-secondary';
                                $m2Icon = 'fa-certificate';
                                $m2TextClass = 'text-secondary';
                                $m2Desc = 'Unlocked upon shortlisting.';
                                if ($step2Done) {
                                    $m2Class = 'bg-success bg-opacity-10 border-success';
                                    $m2Badge = 'bg-success';
                                    $m2Icon = 'fa-check';
                                    $m2TextClass = 'text-success';
                                    $m2Desc = 'Dossier completed &amp; signed.';
                                } elseif ($statusCode === 'screened') {
                                    $m2Class = 'bg-warning bg-opacity-10 border-warning';
                                    $m2Badge = 'bg-warning text-dark';
                                    $m2Icon = 'fa-exclamation';
                                    $m2TextClass = 'text-dark fw-bold';
                                    $m2Desc = 'Action Needed: Fill dossier.';
                                }
                                ?>
                                <div class="p-3 rounded-4 border <?= $m2Class; ?>">
                                    <div class="badge <?= $m2Badge; ?> rounded-circle p-2 mb-2" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa <?= $m2Icon; ?> text-white"></i>
                                    </div>
                                    <h6 class="fw-bold <?= $m2TextClass; ?> mb-1">2. Verification Dossier</h6>
                                    <p class="text-muted small mb-0"><?= $m2Desc; ?></p>
                                </div>
                            </div>
                            <!-- Milestone 3 -->
                            <div class="col-sm-6 col-md-3">
                                <?php
                                $m3Class = 'bg-light text-muted';
                                $m3Badge = 'bg-secondary';
                                $m3Icon = 'fa-award';
                                $m3TextClass = 'text-secondary';
                                if ($step3Done) {
                                    $m3Class = 'bg-success bg-opacity-10 border-success';
                                    $m3Badge = 'bg-success';
                                    $m3Icon = 'fa-check';
                                    $m3TextClass = 'text-success';
                                } elseif ($statusCode === 'interviewed') {
                                    $m3Class = 'bg-primary bg-opacity-10 border-primary';
                                    $m3Badge = 'bg-primary';
                                    $m3Icon = 'fa-spinner fa-spin';
                                    $m3TextClass = 'text-primary';
                                }
                                ?>
                                <div class="p-3 rounded-4 border <?= $m3Class; ?>">
                                    <div class="badge <?= $m3Badge; ?> rounded-circle p-2 mb-2" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa <?= $m3Icon; ?> text-white"></i>
                                    </div>
                                    <h6 class="fw-bold <?= $m3TextClass; ?> mb-1">3. 100-Pt Vetting</h6>
                                    <p class="text-muted small mb-0">Structured scoring &amp; interview.</p>
                                </div>
                            </div>
                            <!-- Milestone 4 -->
                            <div class="col-sm-6 col-md-3">
                                <div class="p-3 rounded-4 border <?= $step4Done ? 'bg-success bg-opacity-10 border-success' : 'bg-light text-muted'; ?>">
                                    <div class="badge <?= $step4Done ? 'bg-success' : 'bg-secondary'; ?> rounded-circle p-2 mb-2" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa <?= $step4Done ? 'fa-check' : 'fa-handshake'; ?> text-white"></i>
                                    </div>
                                    <h6 class="fw-bold <?= $step4Done ? 'text-success' : 'text-secondary'; ?> mb-1">4. Active Roster</h6>
                                    <p class="text-muted small mb-0">Induction &amp; client deployment calls.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Left: Profile Summary -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="fw-bold mb-0"><i class="fa fa-user-circle text-primary me-2"></i> Candidate Overview</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Applicant Name:</span>
                                        <strong><?= htmlspecialchars($application->legal_name); ?></strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Target Track:</span>
                                        <span class="badge <?= $trackCode === 'associate' ? 'bg-primary' : 'bg-success'; ?>">
                                            <?= htmlspecialchars($track ? $track->name : ''); ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Primary Practice Area:</span>
                                        <strong><?= htmlspecialchars($primaryFunction ? $primaryFunction->name : 'N/A'); ?></strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Email:</span>
                                        <span><?= htmlspecialchars($application->email); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Mobile / WhatsApp:</span>
                                        <span><?= htmlspecialchars($application->mobile_number); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-muted">Submitted On:</span>
                                        <span><?= date('M d, Y H:i', strtotime($application->reg_date)); ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Filed Credentials -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white py-3 border-bottom">
                                <h6 class="fw-bold mb-0"><i class="fa fa-folder-open text-primary me-2"></i> Documents Filed</h6>
                            </div>
                            <div class="card-body">
                                <?php if (empty($documents)): ?>
                                    <p class="text-muted small mb-0">No documents on file.</p>
                                <?php else: ?>
                                    <ul class="list-group list-group-flush small">
                                        <?php foreach ($documents as $doc): 
                                            $docType = $doc->documenttype();
                                        ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                <div>
                                                    <i class="fa fa-file-pdf text-danger me-2"></i>
                                                    <strong><?= htmlspecialchars($docType ? $docType->name : 'Document'); ?></strong>
                                                    <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($doc->original_name); ?></div>
                                                </div>
                                                <span class="badge bg-light text-muted border"><?= round($doc->file_size_kb, 1); ?> KB</span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline / Status Events -->
                <?php if (!empty($statusEvents)): ?>
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0"><i class="fa fa-history text-primary me-2"></i> Activity & Milestone Audit Log</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="timeline">
                                <?php foreach ($statusEvents as $ev): 
                                    $evStatus = $ev->applicationstatus();
                                ?>
                                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom last-border-0">
                                        <div class="badge bg-primary-subtle text-primary rounded-circle p-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fa fa-check small"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small"><?= htmlspecialchars($evStatus ? $evStatus->name : 'Status Update'); ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($ev->remarks); ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= date('M d, Y H:i', strtotime($ev->reg_date)); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Actions Strip -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fa fa-gauge me-1"></i> Go to Dashboard
                    </a>
                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fa fa-briefcase me-1"></i> View More Opportunities
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>
