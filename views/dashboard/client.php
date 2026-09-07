@extends('layouts.main')

<?php
global $siteConfig;
$userId = (int)($data['user']->iD ?? 0);
$userName = htmlspecialchars($data['user']->name ?? 'there', ENT_QUOTES);
$roleName = htmlspecialchars($data['user']->role()->name ?? 'General User', ENT_QUOTES);

$profileData = (new \App\Controllers\RosterApplicationController())->getUserProfiles($userId);
$hasProfiles = $profileData['has_profiles'];
$apprenticeApps = $profileData['apprentice'];
$associateApps = $profileData['associate'];
?>

<main class="portal-dashboard">
    <section class="portal-dashboard-header">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker">Trainit Portal</p>
                <h1>Welcome, <?= $userName; ?></h1>
                <p class="portal-dashboard-intro">Manage your active talent profiles, check application vetting stages, or request new professional profiles across the Trainit network.</p>
            </div>
            <div class="portal-account-summary" aria-label="Account summary">
                <span>Account Status</span>
                <strong><?= $hasProfiles ? count($apprenticeApps) + count($associateApps) . ' Active Profile(s)' : $roleName; ?></strong>
                <small><?= $hasProfiles ? 'Multi-profile enabled' : 'Ready to request talent profiles'; ?></small>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">
            
            <?php if (!$hasProfiles): ?>
                <!-- ZERO-PROFILE STATE: Guide user to request Apprentice or Associate profile -->
                <div class="portal-dashboard-grid mb-4">
                    <section class="portal-panel portal-next-steps">
                        <div class="portal-section-heading">
                            <p class="portal-kicker">Get Started</p>
                            <h2>Request Your Talent Profile</h2>
                        </div>

                        <div class="portal-action-list">
                            <article class="portal-action-row">
                                <i class="fa fa-user-graduate text-success" aria-hidden="true"></i>
                                <div>
                                    <h3 class="text-success">Apply as an Apprentice</h3>
                                    <p>For students seeking industrial attachment (WRL), recent graduates, and early-career talent seeking guided client work.</p>
                                </div>
                                <a href="<?= $siteConfig->siteUrl; ?>/dashboard/apply/apprentice" class="btn btn-success text-white fw-bold">Apply Now</a>
                            </article>

                            <article class="portal-action-row">
                                <i class="fa fa-user-tie text-primary" aria-hidden="true"></i>
                                <div>
                                    <h3 class="text-primary">Join as an Associate</h3>
                                    <p>For experienced professionals and consultants providing specialist oversight, review, technical sign-off, and advisory.</p>
                                </div>
                                <a href="<?= $siteConfig->siteUrl; ?>/dashboard/apply/associate" class="btn btn-primary text-white fw-bold">Apply Now</a>
                            </article>

                            <article class="portal-action-row">
                                <i class="fa fa-building" aria-hidden="true"></i>
                                <div>
                                    <h3>Request a Client Organisation Account</h3>
                                    <p>Need managed technology, accounting, HR or IT services delivered to your organisation? Request a client service plan.</p>
                                </div>
                                <a href="<?= $siteConfig->siteUrl; ?>/contact">Contact Team</a>
                            </article>
                        </div>
                    </section>

                    <aside class="portal-panel portal-status-panel">
                        <div class="portal-section-heading">
                            <p class="portal-kicker">Status</p>
                            <h2>Account Setup</h2>
                        </div>
                        <ul class="portal-status-list">
                            <li class="is-complete"><i class="fa fa-check" aria-hidden="true"></i><span>Registered account created</span></li>
                            <li><i class="fa fa-clock" aria-hidden="true"></i><span>No Apprentice or Associate profile requested yet</span></li>
                            <li><i class="fa fa-clock" aria-hidden="true"></i><span>Stage 1 vetting pending request</span></li>
                        </ul>
                    </aside>
                </div>

            <?php else: ?>
                <!-- MULTI-PROFILE ACTIVE STATE: Display all user's held accounts & applications -->
                
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h2 class="h4 mb-0 fw-bold">Your Talent Profiles & Applications</h2>
                        <p class="text-muted small mb-0">You hold <?= count($apprenticeApps) + count($associateApps); ?> profile(s) across the Trainit talent network.</p>
                    </div>
                    <div>
                        <a href="<?= $siteConfig->siteUrl; ?>/dashboard/apply" class="btn btn-success fw-bold shadow-sm">
                            <i class="fa fa-plus-circle me-1"></i> Request Another Profile / Function
                        </a>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    
                    <!-- Apprentice Profiles Section -->
                    <?php if (!empty($apprenticeApps)): ?>
                        <div class="col-12">
                            <h5 class="fw-bold text-success mb-3"><i class="fa fa-user-graduate me-2"></i> Apprentice Profiles (<?= count($apprenticeApps); ?>)</h5>
                            <div class="row g-3">
                                <?php foreach ($apprenticeApps as $ap): ?>
                                    <?php
                                    $st = $ap->applicationstatus();
                                    $fn = $ap->primaryfunction();
                                    $apProfile = $ap->apprenticeProfile();
                                    $statusCode = $st ? $st->code : 'submitted';
                                    $badgeClass = 'bg-secondary';
                                    if ($statusCode === 'submitted') $badgeClass = 'bg-primary';
                                    elseif ($statusCode === 'screened') $badgeClass = 'bg-info text-dark';
                                    elseif ($statusCode === 'interviewed') $badgeClass = 'bg-warning text-dark';
                                    elseif ($statusCode === 'on_roster') $badgeClass = 'bg-success';
                                    ?>
                                    <div class="col-md-6 col-lg-6">
                                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #28a745 !important;">
                                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <span class="badge bg-success text-uppercase">Apprentice</span>
                                                        <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($st->name ?? 'Draft'); ?></span>
                                                    </div>
                                                    <h4 class="h5 fw-bold mb-1"><?= htmlspecialchars($fn->name ?? 'Service Function'); ?></h4>
                                                    <p class="text-muted small mb-2">App #<?= $ap->iD; ?> | <?= htmlspecialchars($apProfile->institution_name ?? $ap->city ?? 'Zimbabwe'); ?></p>
                                                    <p class="small text-dark mb-3">
                                                        <?= $apProfile && $apProfile->is_wrl_attachment ? '<i class="fa fa-university me-1 text-success"></i> WRL Attachment (' . $apProfile->wrl_duration_months . ' mos)' : '<i class="fa fa-graduation-cap me-1"></i> General Placement'; ?>
                                                    </p>
                                                </div>
                                                <div class="pt-3 border-top d-flex gap-2">
                                                    <a href="<?= $siteConfig->siteUrl; ?>/dashboard/application?id=<?= $ap->iD; ?>" class="btn btn-sm btn-outline-success flex-grow-1 fw-semibold">
                                                        <i class="fa fa-eye me-1"></i> View Progress
                                                    </a>
                                                    <?php if ($statusCode === 'draft'): ?>
                                                        <a href="<?= $siteConfig->siteUrl; ?>/dashboard/apply/apprentice?id=<?= $ap->iD; ?>" class="btn btn-sm btn-success fw-semibold">
                                                            <i class="fa fa-edit me-1"></i> Resume
                                                        </a>
                                                    <?php elseif (in_array($statusCode, ['screened', 'interviewed', 'on_roster'])): ?>
                                                        <a href="<?= $siteConfig->siteUrl; ?>/dashboard/application/onboarding?id=<?= $ap->iD; ?>" class="btn btn-sm btn-dark fw-semibold">
                                                            <i class="fa fa-id-card me-1"></i> Onboarding
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Associate Profiles Section -->
                    <?php if (!empty($associateApps)): ?>
                        <div class="col-12 mt-4">
                            <h5 class="fw-bold text-primary mb-3"><i class="fa fa-user-tie me-2"></i> Associate Profiles (<?= count($associateApps); ?>)</h5>
                            <div class="row g-3">
                                <?php foreach ($associateApps as $asp): ?>
                                    <?php
                                    $st = $asp->applicationstatus();
                                    $fn = $asp->primaryfunction();
                                    $assocProfile = $asp->associateProfile();
                                    $statusCode = $st ? $st->code : 'submitted';
                                    $badgeClass = 'bg-secondary';
                                    if ($statusCode === 'submitted') $badgeClass = 'bg-primary';
                                    elseif ($statusCode === 'screened') $badgeClass = 'bg-info text-dark';
                                    elseif ($statusCode === 'interviewed') $badgeClass = 'bg-warning text-dark';
                                    elseif ($statusCode === 'on_roster') $badgeClass = 'bg-success';
                                    ?>
                                    <div class="col-md-6 col-lg-6">
                                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #007bff !important;">
                                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <span class="badge bg-primary text-uppercase">Associate</span>
                                                        <span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($st->name ?? 'Draft'); ?></span>
                                                    </div>
                                                    <h4 class="h5 fw-bold mb-1"><?= htmlspecialchars($fn->name ?? 'Service Function'); ?></h4>
                                                    <p class="text-muted small mb-2">App #<?= $asp->iD; ?> | Rate: $<?= number_format((float)($assocProfile->day_rate_expectation ?? 0), 2); ?> USD/day</p>
                                                    <p class="small text-dark mb-3">
                                                        <i class="fa fa-briefcase me-1 text-primary"></i> <?= htmlspecialchars($assocProfile->years_experience ?? 'Experienced'); ?> years experience | <?= $assocProfile && $assocProfile->has_tax_clearance_itf263 ? 'ITF263 Cleared' : 'Standard Rate'; ?>
                                                    </p>
                                                </div>
                                                <div class="pt-3 border-top d-flex gap-2">
                                                    <a href="<?= $siteConfig->siteUrl; ?>/dashboard/application?id=<?= $asp->iD; ?>" class="btn btn-sm btn-outline-primary flex-grow-1 fw-semibold">
                                                        <i class="fa fa-eye me-1"></i> View Progress
                                                    </a>
                                                    <?php if ($statusCode === 'draft'): ?>
                                                        <a href="<?= $siteConfig->siteUrl; ?>/dashboard/apply/associate?id=<?= $asp->iD; ?>" class="btn btn-sm btn-primary fw-semibold">
                                                            <i class="fa fa-edit me-1"></i> Resume
                                                        </a>
                                                    <?php elseif (in_array($statusCode, ['screened', 'interviewed', 'on_roster'])): ?>
                                                        <a href="<?= $siteConfig->siteUrl; ?>/dashboard/application/onboarding?id=<?= $asp->iD; ?>" class="btn btn-sm btn-dark fw-semibold">
                                                            <i class="fa fa-id-card me-1"></i> Onboarding
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

            <?php endif; ?>

            <!-- Portal Overview Metrics -->
            <section class="portal-quick-grid" aria-label="Portal overview">
                <article class="portal-metric">
                    <i class="fa fa-user-check text-success" aria-hidden="true"></i>
                    <span>Profiles</span>
                    <strong><?= count($apprenticeApps) + count($associateApps); ?></strong>
                    <small>Total active Apprentice & Associate profiles.</small>
                </article>
                <article class="portal-metric">
                    <i class="fa fa-tasks text-primary" aria-hidden="true"></i>
                    <span>Assignments</span>
                    <strong>0</strong>
                    <small>Client tasks and supervised hours.</small>
                </article>
                <article class="portal-metric">
                    <i class="fa fa-file-invoice-dollar text-warning" aria-hidden="true"></i>
                    <span>Stipends & Fees</span>
                    <strong>$0.00</strong>
                    <small>Disbursements & approved billing.</small>
                </article>
                <article class="portal-metric">
                    <i class="fa fa-bell text-danger" aria-hidden="true"></i>
                    <span>Notifications</span>
                    <strong>0</strong>
                    <small>Vetting updates & placement alerts.</small>
                </article>
            </section>

        </div>
    </section>
</main>
