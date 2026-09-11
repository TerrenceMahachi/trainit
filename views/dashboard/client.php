@extends('layouts.main')

<?php
global $siteConfig;
$userId = (int)($data['user']->iD ?? 0);
$userName = htmlspecialchars($data['user']->name ?? 'there', ENT_QUOTES);
$roleName = htmlspecialchars($data['user']->role()->name ?? 'General User', ENT_QUOTES);
$userEmail = $data['user']->email ?? '';

$profileData = (new \App\Controllers\RosterApplicationController())->getUserProfiles($userId);
$apprenticeApps = $profileData['apprentice'];
$associateApps = $profileData['associate'];

// Retrieve Vacancy Applications for this applicant
$vacancyApps = \App\Models\VacancyApplication::findByQuery(
    "SELECT * FROM vacancy_application WHERE (user = ? OR email = ?) AND status = 1 ORDER BY reg_date DESC",
    [$userId, $userEmail]
);
// Self-heal user ID if missing on email match
foreach ($vacancyApps as $va) {
    if (!$va->user && $userId) {
        $va->user = $userId;
        $va->update();
    }
}

$totalTalentItems = count($apprenticeApps) + count($associateApps) + count($vacancyApps);
$hasProfiles = $totalTalentItems > 0;

$clientMemberships = \App\Models\Clientmembership::where('user', $userId);
$isClientMember = !empty($clientMemberships) || ((int)($data['user']->role ?? 0) === 3);
?>


<main class="portal-dashboard">
    <section class="portal-dashboard-header">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker">Tsigiro Portal</p>
                <h1>Welcome, <?= $userName; ?></h1>
                <p class="portal-dashboard-intro">Track your active applications, check vetting stages, and manage your talent profiles across the Tsigiro network.</p>
            </div>
            <div class="portal-account-summary" aria-label="Account summary">
                <span>Account Status</span>
                <strong><?= $hasProfiles ? $totalTalentItems . ' Active Profile / Application(s)' : $roleName; ?></strong>
                <small><?= $hasProfiles ? 'Real-time tracking active' : 'Ready to request talent profiles'; ?></small>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">
            
            <?php if (\App\Helpers\Auth::isStaff() || in_array((int)($data['user']->role ?? 0), [1, 6, 7, 8], true)): ?>
                <!-- Staff / Admin Quick-Switch Banner -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #2A114B 0%, #3D1A6D 100%); color: #ffffff; border-left: 6px solid #FFCC00 !important;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border shadow-sm" style="width: 50px; height: 50px; background: #FFCC00; color: #2A114B; flex-shrink: 0;">
                                <i class="fa fa-shield-alt fa-lg"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="fw-bold mb-0 text-white">Staff / Administrator View</h5>
                                    <span class="badge bg-warning text-dark fw-bold">Internal Operations</span>
                                </div>
                                <p class="text-white-50 mb-0 small">You are logged in with staff privileges. Access the Staff Registration Hub or switch to the main Command Center.</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?= $siteConfig->siteUrl; ?>/admin/staff" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm">
                                <i class="fa fa-id-badge me-1"></i> Staff Directory
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/admin/staff/create" class="btn btn-outline-light fw-bold px-3 py-2 shadow-sm">
                                <i class="fa fa-user-plus me-1"></i> Register Staff
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="btn btn-light text-dark fw-bold px-3 py-2 shadow-sm">
                                <i class="fa fa-tachometer-alt me-1"></i> Command Center
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($isClientMember): ?>
                <!-- Client Organization Workspace Access Banner -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #1C0D30 0%, #2A114B 100%); color: #ffffff; border-left: 6px solid #FFCC00 !important;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border shadow-sm" style="width: 50px; height: 50px; background: #FFCC00; color: #2A114B; flex-shrink: 0;">
                                <i class="fa fa-handshake fa-lg"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h5 class="fw-bold mb-0 text-white">Client Partner Workspace</h5>
                                    <span class="badge bg-warning text-dark fw-bold">Active Organization</span>
                                </div>
                                <p class="text-white-50 mb-0 small">Access your service retainers, submit structured work briefs, track active milestones, and sign off on completed deliverables.</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="<?= $siteConfig->siteUrl; ?>/client/requests/new" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm">
                                <i class="fa fa-paper-plane me-1"></i> Request Work
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/client/portal" class="btn btn-outline-light fw-bold px-3 py-2 shadow-sm">
                                <i class="fa fa-tachometer-alt me-1"></i> Client Portal
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$hasProfiles): ?>
                <!-- ZERO-PROFILE STATE: Guide user to apply for Vacancies or request Apprentice / Associate profile -->
                <div class="portal-dashboard-grid mb-4">
                    <section class="portal-panel portal-next-steps">
                        <div class="portal-section-heading">
                            <p class="portal-kicker">Get Started</p>
                            <h2>Launch Your Journey With Tsigiro</h2>
                        </div>

                        <div class="portal-action-list">
                            <article class="portal-action-row">
                                <i class="fa fa-bullhorn text-warning" aria-hidden="true"></i>
                                <div>
                                    <h3 class="text-warning">Explore Job Vacancies &amp; Staff Roles</h3>
                                    <p>Discover live staff positions, immediate openings, and specialized roles advertised across Tsigiro divisions.</p>
                                </div>
                                <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-warning text-dark fw-bold">View Openings</a>
                            </article>

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
                            <li class="is-complete"><i class="fa fa-check" aria-hidden="true"></i><span>Registered candidate account active</span></li>
                            <li><i class="fa fa-clock" aria-hidden="true"></i><span>No job applications or talent profiles submitted yet</span></li>
                            <li><i class="fa fa-clock" aria-hidden="true"></i><span>Ready to apply for published vacancies</span></li>
                        </ul>
                    </aside>
                </div>

            <?php else: ?>
                <!-- MULTI-PROFILE ACTIVE STATE: Display all user's held accounts & applications -->
                
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h2 class="h4 mb-0 fw-bold">Your Applications &amp; Talent Profiles</h2>
                        <p class="text-muted small mb-0">You hold <?= $totalTalentItems; ?> active application(s) and profile(s) across the Tsigiro network.</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-warning text-dark fw-bold shadow-sm">
                            <i class="fa fa-bullhorn me-1"></i> Browse Vacancies
                        </a>
                        <a href="<?= $siteConfig->siteUrl; ?>/dashboard/apply" class="btn btn-success fw-bold shadow-sm">
                            <i class="fa fa-plus-circle me-1"></i> Request Talent Profile
                        </a>
                    </div>
                </div>

                <div class="row g-4 mb-4">

                    <!-- Vacancy Applications Section -->
                    <?php if (!empty($vacancyApps)): ?>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="fa fa-bullhorn me-2 text-warning"></i> Job Vacancy Applications (<?= count($vacancyApps); ?>)
                                </h5>
                                <span class="badge bg-light text-dark border">Direct Recruitment</span>
                            </div>
                            <div class="row g-3">
                                <?php foreach ($vacancyApps as $va): ?>
                                    <?php
                                    $vac = $va->vacancy();
                                    $stRec = $va->statusRecord();
                                    $dept = $vac ? $vac->department() : null;
                                    $loc = $vac ? $vac->worklocationpreference() : null;
                                    $eng = $vac ? $vac->engagementbasis() : null;
                                    $badgeCls = $stRec ? $stRec->badge_class : 'bg-secondary';
                                    $stName = $stRec ? $stRec->name : 'Submitted';
                                    $invite = $va->staffInvite();
                                    $isOnboarded = $invite && !empty($invite['used']);
                                    ?>
                                    <div class="col-md-6 col-lg-6">
                                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #FFCC00 !important;">
                                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                                <div>
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <span class="badge bg-warning text-dark text-uppercase fw-bold">Job Vacancy</span>
                                                        <span class="badge <?= $badgeCls; ?>"><?= htmlspecialchars($stName); ?></span>
                                                    </div>
                                                    <h4 class="h5 fw-bold mb-1" style="color: #1C0D30;">
                                                        <?= htmlspecialchars($vac ? $vac->title : 'Staff Vacancy'); ?>
                                                    </h4>
                                                    <p class="text-muted small mb-2">
                                                        Ref: <code><?= htmlspecialchars($va->application_number); ?></code> | <?= htmlspecialchars($dept ? $dept->name : 'Operations'); ?>
                                                    </p>
                                                    <div class="small text-dark mb-3">
                                                        <span class="me-2"><i class="fa fa-briefcase text-muted me-1"></i> <?= htmlspecialchars($eng ? $eng->name : 'Full-Time'); ?></span>
                                                        <span><i class="fa fa-location-dot text-muted me-1"></i> <?= htmlspecialchars($loc ? $loc->name : 'Harare'); ?></span>
                                                    </div>

                                                    <?php if (!empty($va->interview_at)): ?>
                                                        <div class="p-2 rounded-3 bg-warning bg-opacity-10 border border-warning mb-3 small">
                                                            <div class="fw-bold text-dark"><i class="fa fa-calendar-check text-warning me-1"></i> Interview Scheduled:</div>
                                                            <div class="text-muted"><?= date('l, d F Y \a\t H:i', strtotime($va->interview_at)); ?></div>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ((int)$va->application_status === 5 || $invite): ?>
                                                        <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success mb-3 small">
                                                            <div class="fw-bold text-success mb-1">
                                                                <i class="fa fa-award me-1"></i> Appointed to Staff Position!
                                                            </div>
                                                            <?php if ($isOnboarded): ?>
                                                                <span class="badge bg-success text-white"><i class="fa fa-check me-1"></i> Statutory Onboarding Complete</span>
                                                            <?php elseif ($invite && !empty($invite['token'])): ?>
                                                                <p class="text-muted mb-2">Congratulations on your appointment. Please complete statutory onboarding to finalize your contract.</p>
                                                                <a href="<?= $siteConfig->siteUrl; ?>/staff/onboard?token=<?= urlencode($invite['token']); ?>" class="btn btn-sm btn-success fw-bold text-white shadow-sm">
                                                                    <i class="fa fa-id-card me-1"></i> Complete Staff Onboarding
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="pt-3 border-top d-flex justify-content-between align-items-center gap-2">
                                                    <small class="text-muted">
                                                        <i class="fa fa-clock me-1"></i> Applied <?= date('d M Y', strtotime($va->reg_date)); ?>
                                                    </small>
                                                    <?php if ($vac): ?>
                                                        <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancy/<?= urlencode($vac->slug); ?>" class="btn btn-sm btn-outline-dark fw-semibold">
                                                            <i class="fa fa-external-link-alt me-1"></i> View Vacancy
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
                    <span>Profiles &amp; Applications</span>
                    <strong><?= $totalTalentItems; ?></strong>
                    <small>Active talent profiles &amp; vacancy submissions.</small>
                </article>
                <article class="portal-metric">
                    <i class="fa fa-bullhorn text-warning" aria-hidden="true"></i>
                    <span>Job Vacancies</span>
                    <strong><?= count($vacancyApps); ?></strong>
                    <small>Direct staff applications in review.</small>
                </article>
                <article class="portal-metric">
                    <i class="fa fa-graduation-cap text-info" aria-hidden="true"></i>
                    <span>Talent Roster</span>
                    <strong><?= count($apprenticeApps) + count($associateApps); ?></strong>
                    <small>Apprentice &amp; Associate profiles.</small>
                </article>
                <article class="portal-metric">
                    <i class="fa fa-bell text-danger" aria-hidden="true"></i>
                    <span>Interviews &amp; Alerts</span>
                    <strong><?= !empty($vacancyApps) ? count(array_filter($vacancyApps, fn($v) => !empty($v->interview_at))) : 0; ?></strong>
                    <small>Upcoming assessments &amp; interview schedules.</small>
                </article>
            </section>

        </div>
    </section>
</main>
