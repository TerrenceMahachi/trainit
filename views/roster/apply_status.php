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
$step2Done = in_array($statusCode, ['screened', 'interviewed', 'on_roster', 'deployed']);
$step3Done = in_array($statusCode, ['interviewed', 'on_roster', 'deployed']);
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
                <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill">
                    <i class="fa fa-clock me-1"></i> <?= htmlspecialchars($statusObj ? $statusObj->name : 'Submitted'); ?>
                </span>
            </div>
        </div>
    </section>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Milestone Roadmap Card -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="fw-bold mb-0"><i class="fa fa-route text-primary me-2"></i> 4-Stage Vetting & Onboarding Roadmap</h5>
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
                                    <p class="text-muted small mb-0">Profile & credentials logged successfully.</p>
                                </div>
                            </div>
                            <!-- Milestone 2 -->
                            <div class="col-sm-6 col-md-3">
                                <div class="p-3 rounded-4 border <?= $step2Done ? 'bg-success bg-opacity-10 border-success' : 'bg-primary bg-opacity-10 border-primary'; ?>">
                                    <div class="badge <?= $step2Done ? 'bg-success' : 'bg-primary'; ?> rounded-circle p-2 mb-2" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa <?= $step2Done ? 'fa-check' : 'fa-spinner fa-spin'; ?> text-white"></i>
                                    </div>
                                    <h6 class="fw-bold <?= $step2Done ? 'text-success' : 'text-primary'; ?> mb-1">2. Verification</h6>
                                    <p class="text-muted small mb-0">Academic & referee check in progress.</p>
                                </div>
                            </div>
                            <!-- Milestone 3 -->
                            <div class="col-sm-6 col-md-3">
                                <div class="p-3 rounded-4 border <?= $step3Done ? 'bg-success bg-opacity-10 border-success' : 'bg-light text-muted'; ?>">
                                    <div class="badge <?= $step3Done ? 'bg-success' : 'bg-secondary'; ?> rounded-circle p-2 mb-2" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa <?= $step3Done ? 'fa-check' : 'fa-award'; ?> text-white"></i>
                                    </div>
                                    <h6 class="fw-bold <?= $step3Done ? 'text-success' : 'text-secondary'; ?> mb-1">3. 100-Pt Vetting</h6>
                                    <p class="text-muted small mb-0">Structured competency scoring.</p>
                                </div>
                            </div>
                            <!-- Milestone 4 -->
                            <div class="col-sm-6 col-md-3">
                                <div class="p-3 rounded-4 border <?= $step4Done ? 'bg-success bg-opacity-10 border-success' : 'bg-light text-muted'; ?>">
                                    <div class="badge <?= $step4Done ? 'bg-success' : 'bg-secondary'; ?> rounded-circle p-2 mb-2" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa <?= $step4Done ? 'fa-check' : 'fa-handshake'; ?> text-white"></i>
                                    </div>
                                    <h6 class="fw-bold <?= $step4Done ? 'text-success' : 'text-secondary'; ?> mb-1">4. Active Roster</h6>
                                    <p class="text-muted small mb-0">Induction & client deployment calls.</p>
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
