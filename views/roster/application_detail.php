@extends('layouts.main')

<?php
global $siteConfig;
$app = $data['app'];
$track = $app->applicationtrack();
$status = $app->applicationstatus();
$function = $app->primaryfunction();
$skills = $app->skills();
$onboarding = $app->onboarding();
$statusCode = $status ? $status->code : 'submitted';
?>

<main class="portal-dashboard">
    <section class="portal-dashboard-header">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" style="color:rgba(255,255,255,0.7); text-decoration:none;"><i class="fa fa-arrow-left me-1"></i> Dashboard</a> &rsaquo; Applications</p>
                <h1>Application #<?= $app->iD; ?> — <?= htmlspecialchars($function->name ?? 'Talent Roster'); ?></h1>
                <p class="portal-dashboard-intro">Track: <strong><?= htmlspecialchars($track->name ?? ''); ?></strong> | Submitted by: <?= htmlspecialchars($app->legal_name); ?></p>
            </div>
            <div class="portal-account-summary">
                <span>Current Status</span>
                <strong class="text-uppercase"><?= htmlspecialchars($status->name ?? 'Under Review'); ?></strong>
                <small>Submitted on <?= date('d M Y', strtotime($app->reg_date)); ?></small>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">
            
            <!-- Multi-Stage Progress Bar -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Application Progress Pipeline</h5>
                    <div class="row text-center g-3">
                        <div class="col-md-3">
                            <div class="p-3 rounded <?= in_array($statusCode, ['draft', 'submitted', 'screened', 'interviewed', 'on_roster', 'deployed']) ? 'bg-success text-white' : 'bg-light text-muted border'; ?>">
                                <div class="h4 mb-1"><i class="fa fa-file-alt"></i></div>
                                <div class="fw-bold">Stage 1: Intake</div>
                                <small><?= $statusCode === 'draft' ? 'Draft in progress' : 'Submitted & Logged'; ?></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded <?= in_array($statusCode, ['screened', 'interviewed', 'on_roster', 'deployed']) ? 'bg-success text-white' : ($statusCode === 'submitted' ? 'bg-primary text-white' : 'bg-light text-muted border'); ?>">
                                <div class="h4 mb-1"><i class="fa fa-search"></i></div>
                                <div class="fw-bold">Stage 2: Vetting & Tests</div>
                                <small><?= in_array($statusCode, ['screened', 'interviewed', 'on_roster', 'deployed']) ? 'Passed / Shortlisted' : ($statusCode === 'submitted' ? 'In Vetting Queue' : 'Pending'); ?></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded <?= in_array($statusCode, ['on_roster', 'deployed']) ? 'bg-success text-white' : ($statusCode === 'interviewed' ? 'bg-warning text-dark' : 'bg-light text-muted border'); ?>">
                                <div class="h4 mb-1"><i class="fa fa-id-card"></i></div>
                                <div class="fw-bold">Stage 3: Onboarding</div>
                                <small><?= in_array($statusCode, ['on_roster', 'deployed']) ? 'Statutory Data Complete' : ($statusCode === 'interviewed' ? 'Awaiting Details' : 'Pending Admission'); ?></small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded <?= in_array($statusCode, ['on_roster', 'deployed']) ? 'bg-success text-white' : 'bg-light text-muted border'; ?>">
                                <div class="h4 mb-1"><i class="fa fa-user-check"></i></div>
                                <div class="fw-bold">Active on Roster</div>
                                <small><?= in_array($statusCode, ['on_roster', 'deployed']) ? 'Ready for Placements' : 'Pending Final Admission'; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stage 3 Action Callout (if eligible) -->
            <?php if (in_array($statusCode, ['screened', 'interviewed', 'on_roster', 'deployed'])): ?>
                <div class="alert alert-success border-0 shadow-sm p-4 mb-4 d-flex justify-content-between align-items-center" style="border-radius: 12px;">
                    <div>
                        <h4 class="alert-heading mb-1 fw-bold"><i class="fa fa-check-circle me-2"></i> Stage 3 Statutory Onboarding Unlocked!</h4>
                        <p class="mb-0">You have been approved for the talent roster. Please provide your statutory data (National ID, bank details, emergency contacts, NSSA, signed agreements) to complete admission.</p>
                    </div>
                    <div>
                        <a href="<?= $siteConfig->siteUrl; ?>/dashboard/application/onboarding?id=<?= $app->iD; ?>" class="btn btn-dark fw-bold px-4 py-2">
                            Complete Stage 3 Onboarding <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Application Details Grid -->
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">Application Summary</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Track & Service Function</span>
                                    <strong class="h6"><?= htmlspecialchars($track->name ?? ''); ?> — <?= htmlspecialchars($function->name ?? ''); ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Primary Contact</span>
                                    <strong><?= htmlspecialchars($app->email); ?> | <?= htmlspecialchars($app->mobile_number); ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Location & Province</span>
                                    <strong><?= htmlspecialchars($app->city); ?>, <?= htmlspecialchars($app->zimprovince()->name ?? ''); ?></strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted small d-block">Right to Work Status</span>
                                    <strong><?= htmlspecialchars($app->workrightstatus()->name ?? 'Citizen'); ?></strong>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="fw-bold mb-3">Assessed Skills Matrix:</h6>
                            <?php if (empty($skills)): ?>
                                <p class="text-muted small">No specific skills rated.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Skill Item</th>
                                                <th>Proficiency Level</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($skills as $sk): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($sk->skillitem()->name ?? 'Skill'); ?></td>
                                                    <td>
                                                        <span class="badge bg-primary"><?= htmlspecialchars($sk->proficiencylevel()->name ?? 'Level'); ?></span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>

                            <hr class="my-4">

                            <h6 class="fw-bold mb-2">Section 4.4 Deliverable Evidence:</h6>
                            <div class="p-3 bg-light rounded border text-muted small">
                                <?= nl2br(htmlspecialchars($app->judgementResponse()->primary_function_evidence ?? 'No narrative submitted.')); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">Track-Specific Overview</h5>
                        </div>
                        <div class="card-body p-4">
                            <?php if ($track->code === 'apprentice' && $app->apprenticeProfile()): ?>
                                <?php $ap = $app->apprenticeProfile(); ?>
                                <p class="mb-2"><strong>Institution:</strong><br><?= htmlspecialchars($ap->institution_name ?? 'N/A'); ?></p>
                                <p class="mb-2"><strong>Programme:</strong><br><?= htmlspecialchars($ap->degree_programme ?? 'N/A'); ?></p>
                                <p class="mb-2"><strong>WRL Attachment:</strong><br><?= $ap->is_wrl_attachment ? 'Yes (' . $ap->wrl_duration_months . ' Months)' : 'No'; ?></p>
                                <p class="mb-0"><strong>Coordinator:</strong><br><?= htmlspecialchars($ap->wrl_coordinator_name ?: 'None specified'); ?></p>
                            <?php elseif ($track->code === 'associate' && $app->associateProfile()): ?>
                                <?php $asp = $app->associateProfile(); ?>
                                <p class="mb-2"><strong>Experience:</strong><br><?= htmlspecialchars($asp->years_experience ?? 'N/A'); ?> (<?= htmlspecialchars($asp->employmentstatus()->name ?? ''); ?>)</p>
                                <p class="mb-2"><strong>Indicative Day Rate:</strong><br>$<?= number_format((float)$asp->day_rate_expectation, 2); ?> USD</p>
                                <p class="mb-2"><strong>ZIMRA ITF263 Clearance:</strong><br><?= $asp->has_tax_clearance_itf263 ? '<span class="text-success fw-bold">Yes</span>' : '<span class="text-warning fw-bold">No / Pending</span>'; ?></p>
                                <p class="mb-0"><strong>Invoicing Entity:</strong><br><?= htmlspecialchars($asp->invoiceentitytype()->name ?? 'Individual'); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-light py-3">
                            <a href="<?= $siteConfig->siteUrl; ?>/dashboard/apply/<?= $track->code; ?>?id=<?= $app->iD; ?>" class="btn btn-outline-secondary w-100 btn-sm">
                                <i class="fa fa-edit me-1"></i> Edit Application Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</main>
