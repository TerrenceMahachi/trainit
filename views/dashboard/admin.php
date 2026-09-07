@extends('layouts.main')

<?php
global $siteConfig;
$user = $data['user'];

// Gather Key Metrics
$totalApplications = App\Models\Rosterapplication::countAll();
$apprenticeCount = count(App\Models\Rosterapplication::findByQuery("SELECT iD FROM rosterapplication WHERE applicationtrack = 1"));
$associateCount = count(App\Models\Rosterapplication::findByQuery("SELECT iD FROM rosterapplication WHERE applicationtrack = 2"));
$pendingReviewCount = count(App\Models\Rosterapplication::findByQuery("SELECT iD FROM rosterapplication WHERE applicationstatus = 2"));
$onRosterCount = count(App\Models\Rosterapplication::findByQuery("SELECT iD FROM rosterapplication WHERE applicationstatus = 5"));
$onboardingCount = App\Models\Rosteronboarding::countAll();
$totalUsers = App\Models\User::countAll();

// Recent Applications
$recentApplications = App\Models\Rosterapplication::findByQuery(
    "SELECT * FROM rosterapplication ORDER BY iD DESC LIMIT 6"
);
?>

<main class="portal-dashboard">
    <!-- Hero Header -->
    <section class="portal-dashboard-header">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker"><i class="fa fa-shield-alt me-1"></i> Trainit Command Center</p>
                <h1>Administrator Dashboard</h1>
                <p class="portal-dashboard-intro">Overview of talent pipeline intake, vetting scoring, candidate onboarding, and system dictionary governance.</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="<?= $siteConfig->siteUrl; ?>/admin/roster" class="btn btn-success text-white fw-bold shadow-sm px-3 py-2">
                    <i class="fa fa-gavel me-1"></i> Talent Pipeline Console
                </a>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body py-4">
        <div class="container">
            
            <!-- Top KPI Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #007bff !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold text-uppercase">Total Applications</span>
                                <i class="fa fa-folder-open text-primary fa-lg"></i>
                            </div>
                            <h2 class="h3 fw-bold mb-1 text-dark"><?= $totalApplications; ?></h2>
                            <small class="text-muted">
                                <span class="text-success fw-bold"><?= $apprenticeCount; ?></span> Apprentice &bull; <span class="text-primary fw-bold"><?= $associateCount; ?></span> Associate
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #ffc107 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold text-uppercase">Pending Vetting</span>
                                <i class="fa fa-clock text-warning fa-lg"></i>
                            </div>
                            <h2 class="h3 fw-bold mb-1 text-dark"><?= $pendingReviewCount; ?></h2>
                            <small class="text-muted">Awaiting 100-pt scoring & red-flag check</small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #28a745 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold text-uppercase">Admitted On Roster</span>
                                <i class="fa fa-user-check text-success fa-lg"></i>
                            </div>
                            <h2 class="h3 fw-bold mb-1 text-dark"><?= $onRosterCount; ?></h2>
                            <small class="text-muted">Cleared & placement ready</small>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #6f42c1 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small fw-bold text-uppercase">Stage 3 Onboarded</span>
                                <i class="fa fa-id-card text-purple fa-lg"></i>
                            </div>
                            <h2 class="h3 fw-bold mb-1 text-dark"><?= $onboardingCount; ?></h2>
                            <small class="text-muted">Bank & statutory details filed</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Applications Table Section -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow:hidden;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa fa-list-alt text-primary me-2"></i> Recent Pipeline Submissions
                    </h5>
                    <a href="<?= $siteConfig->siteUrl; ?>/admin/roster" class="btn btn-sm btn-outline-primary fw-semibold">
                        View Full Pipeline <i class="fa fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>Candidate Name</th>
                                <th>Track</th>
                                <th>Primary Function</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Assessment Score</th>
                                <th>Date Submitted</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentApplications)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                        No applications recorded in the pipeline yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentApplications as $app): ?>
                                    <?php
                                    $tr = $app->applicationtrack();
                                    $st = $app->applicationstatus();
                                    $fn = $app->primaryfunction();
                                    $assessment = $app->assessment();
                                    $badgeClass = 'bg-secondary';
                                    if ($st) {
                                        if ($st->code === 'submitted') $badgeClass = 'bg-primary';
                                        elseif ($st->code === 'screened') $badgeClass = 'bg-info text-dark';
                                        elseif ($st->code === 'interviewed') $badgeClass = 'bg-warning text-dark';
                                        elseif ($st->code === 'on_roster') $badgeClass = 'bg-success';
                                        elseif ($st->code === 'rejected') $badgeClass = 'bg-danger';
                                    }
                                    ?>
                                    <tr>
                                        <td class="fw-bold">#<?= $app->iD; ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($app->legal_name); ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($app->email); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?= ($tr && $tr->code === 'apprentice') ? 'bg-success' : 'bg-primary'; ?>">
                                                <?= htmlspecialchars($tr->name ?? 'Track'); ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($fn->name ?? 'General'); ?></td>
                                        <td><?= htmlspecialchars($app->city ?? 'Harare'); ?></td>
                                        <td><span class="badge <?= $badgeClass; ?>"><?= htmlspecialchars($st->name ?? 'Draft'); ?></span></td>
                                        <td>
                                            <?php if ($assessment && $assessment->total_score > 0): ?>
                                                <strong class="text-dark"><?= number_format((float)$assessment->total_score, 1); ?>/100</strong>
                                            <?php else: ?>
                                                <span class="text-muted small">Pending Score</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><small class="text-muted"><?= date('d M Y', strtotime($app->reg_date)); ?></small></td>
                                        <td class="text-end">
                                            <a href="<?= $siteConfig->siteUrl; ?>/admin/roster/review?id=<?= $app->iD; ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                                                <i class="fa fa-gavel me-1"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- System Governance & Reference Dictionaries Hub -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa fa-sliders-h text-secondary me-2"></i> System Administration & Normalized Dictionaries</h5>
                    <p class="text-muted small mb-0">Manage underlying category models, standard scales, qualification types, and platform user accounts.</p>
                </div>
            </div>

            <div class="row g-3 mb-4">
                
                <!-- 1. Taxonomy & Skills -->
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-cogs text-primary me-2"></i> Taxonomy & Skills</h6>
                        </div>
                        <div class="list-group list-group-flush small">
                            <a href="<?= $siteConfig->siteUrl; ?>/servicefunctions" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Service Functions</span>
                                <span class="badge bg-secondary"><?= App\Models\Servicefunction::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/skillitems" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Skill Items Matrix</span>
                                <span class="badge bg-secondary"><?= App\Models\Skillitem::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/proficiencylevels" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Proficiency Levels (1–5)</span>
                                <span class="badge bg-secondary"><?= App\Models\Proficiencylevel::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/vettingrecommendations" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Vetting Recommendations</span>
                                <span class="badge bg-secondary"><?= App\Models\Vettingrecommendation::countAll(); ?></span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 2. Candidate Classifications -->
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-user-tag text-success me-2"></i> Classifications</h6>
                        </div>
                        <div class="list-group list-group-flush small">
                            <a href="<?= $siteConfig->siteUrl; ?>/applicationtracks" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Application Tracks</span>
                                <span class="badge bg-secondary"><?= App\Models\Applicationtrack::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/applicationstatuss" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Application Statuses</span>
                                <span class="badge bg-secondary"><?= App\Models\Applicationstatus::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/apprenticestatuss" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Apprentice Statuses</span>
                                <span class="badge bg-secondary"><?= App\Models\Apprenticestatus::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/employmentstatuss" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Employment Statuses</span>
                                <span class="badge bg-secondary"><?= App\Models\Employmentstatus::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/zimprovinces" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Zim Provinces</span>
                                <span class="badge bg-secondary"><?= App\Models\Zimprovince::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/workrightstatuss" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Work Right Statuses</span>
                                <span class="badge bg-secondary"><?= App\Models\Workrightstatus::countAll(); ?></span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 3. Qualifications & Bodies -->
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-graduation-cap text-warning me-2"></i> Qualifications & Bodies</h6>
                        </div>
                        <div class="list-group list-group-flush small">
                            <a href="<?= $siteConfig->siteUrl; ?>/qualificationtypes" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Qualification Types</span>
                                <span class="badge bg-secondary"><?= App\Models\Qualificationtype::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/qualificationstatuss" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Qualification Statuses</span>
                                <span class="badge bg-secondary"><?= App\Models\Qualificationstatus::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/professionalbodys" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Professional Bodies</span>
                                <span class="badge bg-secondary"><?= App\Models\Professionalbody::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/sectortypes" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Sector Types</span>
                                <span class="badge bg-secondary"><?= App\Models\Sectortype::countAll(); ?></span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 4. Engagement & Security -->
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa fa-users-cog text-info me-2"></i> Commercials & Users</h6>
                        </div>
                        <div class="list-group list-group-flush small">
                            <a href="<?= $siteConfig->siteUrl; ?>/users" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center fw-bold text-primary">
                                <span>User Accounts</span>
                                <span class="badge bg-primary"><?= $totalUsers; ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/engagementbasiss" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Engagement Bases</span>
                                <span class="badge bg-secondary"><?= App\Models\Engagementbasis::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/engagementmodels" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Engagement Models</span>
                                <span class="badge bg-secondary"><?= App\Models\Engagementmodel::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/worklocationpreferences" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Location Preferences</span>
                                <span class="badge bg-secondary"><?= App\Models\Worklocationpreference::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/invoiceentitytypes" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Invoice Entity Types</span>
                                <span class="badge bg-secondary"><?= App\Models\Invoiceentitytype::countAll(); ?></span>
                            </a>
                            <a href="<?= $siteConfig->siteUrl; ?>/refereecontacttimings" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>Referee Contact Timings</span>
                                <span class="badge bg-secondary"><?= App\Models\Refereecontacttiming::countAll(); ?></span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
</main>
