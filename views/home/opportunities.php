@extends('layouts.main')

<?php
global $siteConfig;
$loggedInUser = $data['user'] ?? null;
$isAdmin = $data['isAdmin'] ?? false;
$apprenticeUrl = $siteConfig->siteUrl . '/opportunities/apply/apprentice';
$associateUrl = $siteConfig->siteUrl . '/opportunities/apply/associate';

$statuses = $data['statuses'] ?? [];
$tracks = $data['tracks'] ?? [];
$totalApplicants = $data['totalApplicants'] ?? 0;
$apprenticeCount = $data['apprenticeCount'] ?? 0;
$associateCount = $data['associateCount'] ?? 0;
$submittedCount = $data['submittedCount'] ?? 0;
$shortlistedCount = $data['shortlistedCount'] ?? 0;
$interviewCount = $data['interviewCount'] ?? 0;
$onRosterCount = $data['onRosterCount'] ?? 0;
$initialApplications = $data['initialApplications'] ?? [];
?>
<main class="trainit-page opportunity-page">
    <!-- Hero Section -->
    <section class="opportunity-hero">
        <div class="opportunity-orb opportunity-orb-one" aria-hidden="true"></div>
        <div class="opportunity-orb opportunity-orb-two" aria-hidden="true"></div>
        <div class="trainit-wrap opportunity-hero-grid">
            <div class="opportunity-hero-copy opportunity-reveal">
                <p class="opportunity-kicker"><span></span> Tsigiro Recruitment Portal</p>
                <h1>Launch Your Career. Contribute to Real Client Missions.</h1>
                <p class="opportunity-intro">The Tsigiro Talent Network connects emerging practitioners and seasoned specialists to verified client briefs across Africa and beyond. Build tangible work experience or provide high-impact advisory oversight.</p>
                
                <?php if ($isAdmin): ?>
                    <div class="p-3 my-3 rounded-4" style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.25);">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                            <span class="badge bg-warning text-dark px-3 py-1 fw-bold fs-6">
                                <i class="fa fa-shield-halved me-1"></i> Administrator Session Active
                            </span>
                            <span class="text-white small">
                                Signed in as <strong><?= htmlspecialchars($loggedInUser->name ?? 'Administrator') ?></strong>
                            </span>
                        </div>
                        <p class="text-white-50 small mb-3">
                            Candidate applications are displayed below in real-time. You can filter, review, score, and shortlist candidates directly from this console.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-warning fw-bold rounded-pill px-3 shadow-sm" href="#admin-applicants-section">
                                <i class="fa fa-users-viewfinder me-1"></i> View <?= $totalApplicants ?> Applicants
                            </a>
                            <a class="btn btn-sm btn-outline-light rounded-pill px-3" href="<?= $siteConfig->siteUrl ?>/admin/roster">
                                <i class="fa fa-list-check me-1"></i> Dedicated Vetting Console
                            </a>
                            <a class="btn btn-sm btn-outline-light rounded-pill px-3" href="<?= $siteConfig->siteUrl ?>/dashboard">
                                <i class="fa fa-gauge me-1"></i> Dashboard
                            </a>
                            <a class="btn btn-sm btn-outline-success rounded-pill px-3" href="<?= $apprenticeUrl ?>" target="_blank">
                                <i class="fa fa-graduation-cap me-1"></i> Apprentice Form
                            </a>
                            <a class="btn btn-sm btn-outline-info rounded-pill px-3" href="<?= $associateUrl ?>" target="_blank">
                                <i class="fa fa-user-tie me-1"></i> Associate Form
                            </a>
                        </div>
                    </div>
                <?php elseif ($loggedInUser): ?>
                    <div class="p-3 my-3 rounded-4" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <p class="mb-2 text-white small">
                            <i class="fa fa-user-circle text-warning me-1"></i> Signed in as <strong><?= htmlspecialchars($loggedInUser->name) ?></strong>
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-sm btn-success rounded-pill px-3" href="<?= $apprenticeUrl ?>">
                                <i class="fa fa-graduation-cap me-1"></i> Apply as Apprentice
                            </a>
                            <a class="btn btn-sm btn-light rounded-pill px-3" href="<?= $associateUrl ?>">
                                <i class="fa fa-briefcase me-1"></i> Apply as Associate
                            </a>
                            <a class="btn btn-sm btn-outline-light rounded-pill px-3" href="<?= $siteConfig->siteUrl ?>/dashboard">
                                <i class="fa fa-gauge me-1"></i> My Dashboard
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="trainit-actions">
                        <a class="trainit-button opportunity-button" href="<?= $apprenticeUrl ?>">
                            Apply as Apprentice <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </a>
                        <a class="trainit-button opportunity-button-ghost" href="<?= $associateUrl ?>">
                            Apply as Associate <i class="fa fa-user-tie" aria-hidden="true"></i>
                        </a>
                    </div>
                    <p class="opportunity-note mt-2">
                        <i class="fa fa-info-circle" aria-hidden="true"></i> Already registered? <a href="<?= $siteConfig->siteUrl ?>/login" class="text-white text-decoration-underline">Sign in to continue your application</a>
                    </p>
                <?php endif; ?>
            </div>
            <figure class="opportunity-hero-media opportunity-reveal" data-delay="120">
                <img src="<?= $siteConfig->assetsUrl ?>/images/opportunities/apprentices-work-experience.webp"
                    alt="Professionals collaborating on a real client project"
                    width="1600" height="852" fetchpriority="high">
                <figcaption>
                    <strong>Rigorous Talent Vetting</strong>
                    <span>Verified qualifications, structured mentorship, and enterprise-grade delivery standards.</span>
                </figcaption>
            </figure>
        </div>
    </section>

    <?php if ($isAdmin): ?>
        <!-- ========================================================= -->
        <!-- ADMIN APPLICANTS PIPELINE CONSOLE                         -->
        <!-- ========================================================= -->
        <section class="py-5 bg-white border-bottom shadow-sm" id="admin-applicants-section">
            <div class="container">
                <!-- Header Strip -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold">
                            <i class="fa fa-users-viewfinder me-1"></i> Talent Pipeline Management
                        </span>
                        <h2 class="h3 fw-bold text-dark mt-2 mb-1">
                            Talent Intake Applicants (<?= $totalApplicants; ?> Candidates)
                        </h2>
                        <p class="text-muted mb-0">
                            Manage incoming submissions across Apprentice and Associate opportunity tracks. Shortlist candidates to dispatch their magic link.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?= $siteConfig->siteUrl; ?>/admin/roster" class="btn btn-outline-primary rounded-pill px-3 btn-sm fw-semibold">
                            <i class="fa fa-expand me-1"></i> Dedicated Vetting Console
                        </a>
                        <button type="button" class="btn btn-primary rounded-pill px-3 btn-sm fw-semibold" onclick="loadData();">
                            <i class="fa fa-arrows-rotate me-1"></i> Refresh
                        </button>
                    </div>
                </div>

                <!-- KPI Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light h-100 text-center">
                            <span class="text-muted small d-block">Total Candidates</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1" id="kpi_total"><?= $totalApplicants; ?></h3>
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill mt-2">All Tracks</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light h-100 text-center">
                            <span class="text-muted small d-block">Apprentices</span>
                            <h3 class="fw-bold text-success mb-0 mt-1" id="kpi_apprentice"><?= $apprenticeCount; ?></h3>
                            <span class="badge bg-success-subtle text-success rounded-pill mt-2">Early Career</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light h-100 text-center">
                            <span class="text-muted small d-block">Associates</span>
                            <h3 class="fw-bold text-primary mb-0 mt-1" id="kpi_associate"><?= $associateCount; ?></h3>
                            <span class="badge bg-primary-subtle text-primary rounded-pill mt-2">Specialists</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-warning bg-opacity-10 border border-warning h-100 text-center">
                            <span class="text-warning-emphasis small d-block fw-semibold">Needs Shortlist</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1" id="kpi_submitted"><?= $submittedCount; ?></h3>
                            <span class="badge bg-warning text-dark rounded-pill mt-2">Status 2 (Intake)</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-info bg-opacity-10 border border-info h-100 text-center">
                            <span class="text-info-emphasis small d-block fw-semibold">Shortlisted</span>
                            <h3 class="fw-bold text-info-emphasis mb-0 mt-1" id="kpi_shortlisted"><?= $shortlistedCount; ?></h3>
                            <span class="badge bg-info text-dark rounded-pill mt-2">Status 3 (Dossier)</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="card border-0 shadow-sm rounded-4 p-3 bg-success bg-opacity-10 border border-success h-100 text-center">
                            <span class="text-success small d-block fw-semibold">Active Roster</span>
                            <h3 class="fw-bold text-success mb-0 mt-1" id="kpi_onroster"><?= $onRosterCount; ?></h3>
                            <span class="badge bg-success text-white rounded-pill mt-2">Status 5 (Ready)</span>
                        </div>
                    </div>
                </div>

                <!-- Feedback Alert for Inline Actions -->
                <div id="opportunities_admin_alert" style="display:none;" class="alert mb-4"></div>

                <!-- Main Interactive Console Card -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-light">
                    
                    <!-- Quick Filter Ribbon -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
                        <span class="text-muted small fw-semibold me-1"><i class="fa fa-filter me-1"></i> Quick Filter:</span>
                        <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 py-1 btn-quick-filter" data-track="0" data-status="0">
                            All Applicants (<?= $totalApplicants; ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 py-1 btn-quick-filter" data-track="0" data-status="2">
                            <i class="fa fa-clock me-1 text-warning"></i> Needs Shortlisting (<?= $submittedCount; ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info text-dark rounded-pill px-3 py-1 btn-quick-filter" data-track="0" data-status="3">
                            <i class="fa fa-star me-1 text-info"></i> Shortlisted (<?= $shortlistedCount; ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 btn-quick-filter" data-track="1" data-status="0">
                            <i class="fa fa-graduation-cap me-1 text-success"></i> Apprentices (<?= $apprenticeCount; ?>)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 btn-quick-filter" data-track="2" data-status="0">
                            <i class="fa fa-user-tie me-1 text-primary"></i> Associates (<?= $associateCount; ?>)
                        </button>
                    </div>

                    <!-- Search & Filters Toolbar -->
                    <div class="row g-2 justify-content-between align-items-center mb-3">
                        <div class="col-12 col-md-5">
                            <form id="opportunitiesSearchForm" onsubmit="current_page = 1; loadData(); return false;">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                                    <input type="search" name="search" id="roster_search_input" class="form-control border-start-0" placeholder="Search applicant name, email, discipline, city...">
                                    <button class="btn btn-primary" type="submit">Search</button>
                                </div>
                            </form>
                        </div>

                        <div class="col-12 col-md-auto d-flex flex-wrap align-items-center gap-2">
                            <!-- View Toggle -->
                            <div class="view-toggle" id="viewToggle" role="group" aria-label="Switch view">
                                <span class="tableView p-2" title="Table view"><i class="fas fa-table"></i></span>
                                <span class="listView p-2" title="Card / Grid view"><i class="fas fa-list"></i></span>
                            </div>

                            <!-- Track Select -->
                            <select class="form-select form-select-sm" id="track_filter" style="width: auto;">
                                <option value="0">All Tracks</option>
                                <?php foreach ($tracks as $tr): ?>
                                    <option value="<?= $tr->iD; ?>"><?= htmlspecialchars($tr->name); ?></option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Status Select -->
                            <select class="form-select form-select-sm" id="status_filter" style="width: auto;">
                                <option value="0">All Statuses</option>
                                <?php foreach ($statuses as $st): ?>
                                    <option value="<?= $st->iD; ?>"><?= htmlspecialchars($st->name); ?></option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Sort Select -->
                            <select class="form-select form-select-sm" id="order_filter" style="width: auto;">
                                <option value="reg_date DESC" selected>Newest First</option>
                                <option value="reg_date ASC">Oldest First</option>
                                <option value="legal_name ASC">Name A - Z</option>
                                <option value="legal_name DESC">Name Z - A</option>
                                <option value="iD DESC">App ID (High - Low)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Results Container -->
                    <div id="results" class="my-2">
                        <!-- SSR Initial Table Fallback -->
                        <div class="table-responsive bg-white rounded-3 shadow-sm border">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#ID</th>
                                        <th>Applicant</th>
                                        <th>Track</th>
                                        <th>Practice Area</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Score</th>
                                        <th>Applied</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($initialApplications as $app): 
                                        $tr = $app->applicationtrack();
                                        $st = $app->applicationstatus();
                                        $fn = $app->primaryfunction();
                                        $zp = $app->zimprovince();
                                        $assessment = $app->assessment();
                                        $docs = $app->documents();
                                        $hasCv = false;
                                        $cvPath = '';
                                        foreach ($docs as $d) {
                                            $dt = $d->documenttype();
                                            if ($dt && $dt->code === 'CV_RESUME') {
                                                $hasCv = true;
                                                $cvPath = $d->file_path;
                                                break;
                                            }
                                        }
                                        $stCode = $st ? $st->code : 'submitted';
                                        $badgeClass = 'bg-secondary';
                                        if ($stCode === 'submitted') $badgeClass = 'bg-primary';
                                        elseif ($stCode === 'screened') $badgeClass = 'bg-info text-dark';
                                        elseif ($stCode === 'interviewed') $badgeClass = 'bg-warning text-dark';
                                        elseif ($stCode === 'on_roster') $badgeClass = 'bg-success';
                                        elseif ($stCode === 'rejected') $badgeClass = 'bg-danger';

                                        $trCode = $tr ? $tr->code : 'apprentice';
                                        $trBadge = ($trCode === 'apprentice') ? 'bg-success' : 'bg-primary';
                                    ?>
                                        <tr>
                                            <td class="fw-bold text-muted">#APP-<?= str_pad((string)$app->iD, 5, '0', STR_PAD_LEFT); ?></td>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    <?= htmlspecialchars($app->legal_name); ?>
                                                    <?php if ($hasCv): ?>
                                                        <span class="badge bg-light text-danger border ms-1"><i class="fa fa-file-pdf me-1"></i>CV</span>
                                                    <?php endif; ?>
                                                </div>
                                                <small class="text-muted"><?= htmlspecialchars($app->email); ?> &bull; <?= htmlspecialchars($app->mobile_number); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?= $trBadge; ?> rounded-pill px-2 py-1">
                                                    <?= htmlspecialchars($tr ? $tr->name : 'General'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="small fw-semibold"><?= htmlspecialchars($fn ? $fn->name : 'General'); ?></span>
                                            </td>
                                            <td>
                                                <span class="small text-muted"><?= htmlspecialchars($app->city ?: 'Harare'); ?><?= $zp ? ', ' . htmlspecialchars($zp->name) : ''; ?></span>
                                            </td>
                                            <td>
                                                <span class="badge <?= $badgeClass; ?> rounded-pill px-2 py-1" id="status_badge_<?= $app->iD; ?>">
                                                    <?= htmlspecialchars($st ? $st->name : 'Submitted'); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($assessment && $assessment->total_score > 0): ?>
                                                    <strong class="text-dark small"><?= number_format((float)$assessment->total_score, 1); ?>/100</strong>
                                                <?php else: ?>
                                                    <span class="text-muted small">Not Scored</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= date('d M Y', strtotime($app->reg_date)); ?></small>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                    <?php if ((int)$app->applicationstatus === 2): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill btn-inline-shortlist" data-app-id="<?= $app->iD; ?>">
                                                            <i class="fa fa-check-circle me-1"></i> Shortlist
                                                        </button>
                                                    <?php elseif ((int)$app->applicationstatus === 3): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill btn-inline-shortlist" data-app-id="<?= $app->iD; ?>">
                                                            <i class="fa fa-redo me-1"></i> Resend Link
                                                        </button>
                                                    <?php endif; ?>
                                                    <a href="<?= $siteConfig->siteUrl; ?>/admin/roster/review?id=<?= $app->iD; ?>" class="btn btn-sm btn-primary rounded-pill">
                                                        <i class="fa fa-gavel me-1"></i> Review
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Boilerplate Standard AJAX Pagination Include -->
                    <div id="pagination-controls" class="d-flex justify-content-center mt-3"></div>
                    <script>
                        var table = "opportunities-admin";
                        var site = "<?= $siteConfig->siteUrl; ?>";
                    </script>
                    <?php include($siteConfig->assetsLoc . '/nav/ajax-pagination.php'); ?>

                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Path Chooser -->
    <section class="opportunity-section" id="choose-your-path">
        <div class="trainit-wrap">
            <div class="opportunity-section-heading opportunity-reveal">
                <p class="opportunity-kicker opportunity-kicker-dark"><span></span> Choose Your Track</p>
                <h2>Two Clear Tracks. One High Standard of Delivery.</h2>
                <p>Select the pathway that matches your career stage and availability. Both tracks are backed by clear onboarding, transparent compensation, and verified evaluation.</p>
            </div>

            <!-- Apprentice Path -->
            <article class="opportunity-feature opportunity-feature-apprentice opportunity-reveal mb-5">
                <div class="opportunity-feature-image">
                    <img src="<?= $siteConfig->assetsUrl ?>/images/opportunities/apprentices-work-experience.webp"
                        alt="Young professionals in mentored work experience"
                        width="1600" height="852" loading="lazy">
                    <span class="opportunity-image-label">Track 01 &middot; Early Career & Attachment</span>
                </div>
                <div class="opportunity-feature-copy">
                    <span class="opportunity-number">01</span>
                    <p class="opportunity-kicker opportunity-kicker-dark"><span></span> Apprentice Talent Roster</p>
                    <h2>Bridge the gap between academic theory and real-world execution.</h2>
                    <p>Designed for tertiary students on Work-Related Learning (WRL) / industrial attachment, recent graduates within 24 months, and transitioning junior professionals. Receive close mentorship under senior associates on live client briefs.</p>
                    <ul class="opportunity-benefits">
                        <li>
                            <i class="fa fa-briefcase text-success" aria-hidden="true"></i>
                            <span><strong>Practical Client Exposure</strong>Contribute directly to production deliverables across ICT, finance, software, and administration.</span>
                        </li>
                        <li>
                            <i class="fa fa-compass text-success" aria-hidden="true"></i>
                            <span><strong>Direct Mentor Supervision</strong>Pair with experienced Associates who review your work and provide structured guidance.</span>
                        </li>
                        <li>
                            <i class="fa fa-file-invoice text-success" aria-hidden="true"></i>
                            <span><strong>Logbook & Institutional Endorsement</strong>Complete official university/college logbooks with signed supervisor evaluations.</span>
                        </li>
                        <li>
                            <i class="fa fa-wallet text-success" aria-hidden="true"></i>
                            <span><strong>Transport & Meal Stipend</strong>Receive monthly financial support during active placements and attachments.</span>
                        </li>
                    </ul>
                    <div class="mt-4">
                        <a class="btn btn-success rounded-pill px-4 py-2 fw-semibold" href="<?= $apprenticeUrl ?>">
                            Start Apprentice Application <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </article>

            <!-- Associate Path -->
            <article class="opportunity-feature opportunity-feature-associate opportunity-reveal">
                <div class="opportunity-feature-copy">
                    <span class="opportunity-number">02</span>
                    <p class="opportunity-kicker opportunity-kicker-dark"><span></span> Associate Specialist Network</p>
                    <h2>Deploy your specialized expertise on your own schedule.</h2>
                    <p>Associates are independent practitioners, certified specialists, and experienced consultants (5+ years experience) who contribute technical leadership, advisory oversight, and quality sign-off without permanent employment commitments.</p>
                    <ul class="opportunity-benefits">
                        <li>
                            <i class="fa fa-user-check text-primary" aria-hidden="true"></i>
                            <span><strong>Vetted Specialist Standing</strong>Maintain a verified profile detailing your qualifications, past projects, and core capabilities.</span>
                        </li>
                        <li>
                            <i class="fa fa-calendar-check text-primary" aria-hidden="true"></i>
                            <span><strong>Flexible On-Demand Calls</strong>Review and accept client engagements based on your current bandwidth and interest.</span>
                        </li>
                        <li>
                            <i class="fa fa-coins text-primary" aria-hidden="true"></i>
                            <span><strong>Competitive Day Rates</strong>Earn established professional rates (USD) with timely disbursements upon milestone completion.</span>
                        </li>
                        <li>
                            <i class="fa fa-award text-primary" aria-hidden="true"></i>
                            <span><strong>Advisory & Tender Inclusion</strong>Participate in enterprise consulting proposals, advisory panels, and PRAZ tenders.</span>
                        </li>
                    </ul>
                    <div class="mt-4">
                        <a class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" href="<?= $associateUrl ?>">
                            Start Associate Application <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="opportunity-feature-image">
                    <img src="<?= $siteConfig->assetsUrl ?>/images/opportunities/associates-on-demand.webp"
                        alt="Senior consultant leading an advisory strategy session"
                        width="1600" height="854" loading="lazy">
                    <span class="opportunity-image-label">Track 02 &middot; Specialist & Advisory Layer</span>
                </div>
            </article>
        </div>
    </section>

    <!-- Practice Areas / Disciplines Grid -->
    <section class="py-5" style="background-color: #f1f5f9;">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5 opportunity-reveal">
                <p class="opportunity-kicker opportunity-kicker-dark"><span></span> Active Roster Disciplines</p>
                <h2 class="fw-bold">Key Practice Areas & Functional Functions</h2>
                <p class="text-muted">We actively recruit and place talent across diverse core disciplines for corporate, NGO, and institutional clients.</p>
            </div>

            <div class="row g-4">
                <div class="col-sm-6 col-md-4 col-lg-3 opportunity-reveal">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                        <div class="fs-1 text-primary mb-3"><i class="fa fa-code"></i></div>
                        <h5 class="fw-bold mb-2">Software Development</h5>
                        <p class="text-muted small mb-0">Web applications, APIs, mobile solutions, and database integrations.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 opportunity-reveal" data-delay="50">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                        <div class="fs-1 text-success mb-3"><i class="fa fa-server"></i></div>
                        <h5 class="fw-bold mb-2">ICT & Systems Admin</h5>
                        <p class="text-muted small mb-0">Cloud environments, network infrastructure, security, and tech support.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 opportunity-reveal" data-delay="100">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                        <div class="fs-1 text-info mb-3"><i class="fa fa-calculator"></i></div>
                        <h5 class="fw-bold mb-2">Finance & Accounting</h5>
                        <p class="text-muted small mb-0">Management accounting, statutory returns (ZIMRA/NSSA), and audits.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 opportunity-reveal" data-delay="150">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                        <div class="fs-1 text-warning mb-3"><i class="fa fa-users-gear"></i></div>
                        <h5 class="fw-bold mb-2">Human Resources</h5>
                        <p class="text-muted small mb-0">Payroll processing, talent acquisition, labor compliance, and policy.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 opportunity-reveal" data-delay="200">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                        <div class="fs-1 text-danger mb-3"><i class="fa fa-hand-holding-dollar"></i></div>
                        <h5 class="fw-bold mb-2">Grants & Donor Compliance</h5>
                        <p class="text-muted small mb-0">NGO financial reporting, grant acquittals, and donor compliance.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 opportunity-reveal" data-delay="250">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                        <div class="fs-1 text-primary mb-3"><i class="fa fa-chart-pie"></i></div>
                        <h5 class="fw-bold mb-2">MEAL & Data Analytics</h5>
                        <p class="text-muted small mb-0">Baseline surveys, impact evaluation, PowerBI dashboards, and reporting.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 opportunity-reveal" data-delay="300">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                        <div class="fs-1 text-secondary mb-3"><i class="fa fa-boxes-packing"></i></div>
                        <h5 class="fw-bold mb-2">Procurement & Logistics</h5>
                        <p class="text-muted small mb-0">Vendor sourcing, fleet logistics, inventory control, and contracts.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-3 opportunity-reveal" data-delay="350">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-4 text-center">
                        <div class="fs-1 text-dark mb-3"><i class="fa fa-shield-halved"></i></div>
                        <h5 class="fw-bold mb-2">Internal Audit & Risk</h5>
                        <p class="text-muted small mb-0">Risk assessment, internal controls review, and governance audits.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4-Stage Vetting & Onboarding Process -->
    <section class="opportunity-process">
        <div class="trainit-wrap">
            <div class="opportunity-section-heading opportunity-reveal text-center">
                <p class="opportunity-kicker"><span></span> The Vetting Workflow</p>
                <h2>4 Steps from Application to Placement</h2>
                <p class="text-white-50">Our objective, transparent vetting framework ensures high standards while giving applicants clear visibility at every stage.</p>
            </div>
            <div class="opportunity-steps">
                <article class="opportunity-step opportunity-reveal">
                    <span>01</span>
                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                    <h3>Online Application</h3>
                    <p>Select your track (Apprentice or Associate), enter personal information, and outline your functional competencies.</p>
                </article>
                <article class="opportunity-step opportunity-reveal" data-delay="80">
                    <span>02</span>
                    <i class="fa fa-file-arrow-up" aria-hidden="true"></i>
                    <h3>Credentials Filing</h3>
                    <p>Submit educational transcripts, CV, national identification, professional certificates, and referee details.</p>
                </article>
                <article class="opportunity-step opportunity-reveal" data-delay="160">
                    <span>03</span>
                    <i class="fa fa-award" aria-hidden="true"></i>
                    <h3>100-Point Assessment</h3>
                    <p>Applications undergo structured evaluation scoring qualifications, situational judgement, technical skills, and references.</p>
                </article>
                <article class="opportunity-step opportunity-reveal" data-delay="240">
                    <span>04</span>
                    <i class="fa fa-handshake" aria-hidden="true"></i>
                    <h3>Roster Induction</h3>
                    <p>Approved candidates complete statutory onboarding, execute codes of conduct, and are active for client placement calls.</p>
                </article>
            </div>
            <p class="opportunity-disclaimer opportunity-reveal mt-4">
                <i class="fa fa-shield-alt me-1"></i> Admission to the talent roster places candidates in our verified talent pool. Specific client engagements depend on project requirements, availability, and active client demand.
            </p>
        </div>
    </section>

    <!-- Recruitment FAQ -->
    <section class="py-5" style="background-color: #ffffff;">
        <div class="container py-4 max-w-900 mx-auto">
            <div class="text-center mb-5 opportunity-reveal">
                <p class="opportunity-kicker opportunity-kicker-dark"><span></span> Common Inquiries</p>
                <h2 class="fw-bold">Frequently Asked Questions</h2>
            </div>
            <div class="accordion opportunity-reveal" id="recruitmentFaq">
                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h2 class="accordion-header" id="faqOneHeader">
                        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne">
                            Can tertiary students currently on Work-Related Learning (WRL) apply?
                        </button>
                    </h2>
                    <div id="faqOne" class="accordion-collapse collapse" data-bs-parent="#recruitmentFaq">
                        <div class="accordion-body text-muted">
                            Yes! The Apprentice Track is specifically tailored for university and polytechnic students on industrial attachment. We support official institutional logbooks and assign senior mentors to oversee practical outputs and sign evaluation reports.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h2 class="accordion-header" id="faqTwoHeader">
                        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo">
                            How are Associates engaged and compensated?
                        </button>
                    </h2>
                    <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#recruitmentFaq">
                        <div class="accordion-body text-muted">
                            Associates operate as independent contractors. When a matching client request arises, we confirm your availability and agree on scope and deliverables. Remuneration is established on agreed daily or milestone rates (USD) and disbursed upon client sign-off.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h2 class="accordion-header" id="faqThreeHeader">
                        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree">
                            How long does the 100-point vetting process take?
                        </button>
                    </h2>
                    <div id="faqThree" class="accordion-collapse collapse" data-bs-parent="#recruitmentFaq">
                        <div class="accordion-body text-muted">
                            Initial review typically takes 3 to 5 business days after all supporting documents and referee contacts are submitted. You can track your real-time vetting progress in the candidate dashboard.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 mb-3 shadow-sm rounded-4 overflow-hidden">
                    <h2 class="accordion-header" id="faqFourHeader">
                        <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqFour">
                            Can I hold profiles across multiple disciplines?
                        </button>
                    </h2>
                    <div id="faqFour" class="accordion-collapse collapse" data-bs-parent="#recruitmentFaq">
                        <div class="accordion-body text-muted">
                            Yes. Multidisciplinary candidates can register primary and secondary service functions (e.g. ICT Administration and Software Engineering, or Accounting and Grants Compliance) to increase matching opportunities.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final Call to Action -->
    <section class="opportunity-cta">
        <div class="trainit-wrap opportunity-cta-inner opportunity-reveal">
            <div>
                <p class="opportunity-kicker"><span></span> Ready to Get Started?</p>
                <h2>Submit Your Application Today</h2>
                <p>Join an active roster of qualified professionals and motivated apprentices building real solutions for real clients.</p>
            </div>
            <div class="d-flex flex-wrap gap-3">
                <a class="trainit-button opportunity-button" href="<?= $apprenticeUrl ?>">
                    <i class="fa fa-graduation-cap me-1"></i> Apply as Apprentice
                </a>
                <a class="trainit-button opportunity-button-ghost" href="<?= $associateUrl ?>">
                    <i class="fa fa-briefcase me-1"></i> Apply as Associate
                </a>
            </div>
        </div>
    </section>
</main>
<script src="<?= $siteConfig->assetsUrl ?>/scripts/opportunities.js?v=<?= _ASSET_VERSION ?>"></script>
<?php if ($isAdmin): ?>
<script>
$(document).ready(function () {
    // Quick filter click handler
    $('.btn-quick-filter').on('click', function () {
        $('.btn-quick-filter').removeClass('btn-dark text-white').addClass('text-dark');
        $(this).removeClass('text-dark').addClass('btn-dark text-white');

        const trackVal = $(this).data('track');
        const statusVal = $(this).data('status');

        $('#track_filter').val(trackVal);
        $('#status_filter').val(statusVal);

        current_page = 1;
        if (typeof pageState !== 'undefined') {
            pageState.set('current_page', current_page);
        }
        loadData();
    });

    // Dropdown change handlers
    $('#track_filter, #status_filter').on('change', function () {
        current_page = 1;
        if (typeof pageState !== 'undefined') {
            pageState.set('current_page', current_page);
        }
        loadData();
    });
});

function loadData() {
    $('#results').html('<div class="text-center text-muted py-5"><span class="spinner-border text-primary me-2"></span>Loading candidates...</div>');

    const search = $('#roster_search_input').val();
    const ps = (typeof pageState !== 'undefined') ? pageState.get('page_size', '10') : 10;
    const ob = $('#order_filter').val() || 'reg_date DESC';
    const track = $('#track_filter').val() || 0;
    const status = $('#status_filter').val() || 0;
    const uri = site + "/get-admin-roster-records";

    $.ajax({
        url: uri,
        type: "POST",
        dataType: "json",
        data: {
            search: search,
            page: current_page,
            order_by: ob,
            page_size: ps,
            track: track,
            status: status
        },
        success: function (response) {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            if (data.status === 1) {
                displayResults(data.records, data.pagination);
            } else {
                $('#results').html('<div class="alert alert-warning text-center py-4">' + (data.msg || 'No candidates found.') + '</div>');
            }
        },
        error: function () {
            $('#results').html('<div class="alert alert-danger text-center py-4"><i class="fa fa-exclamation-triangle me-2"></i>Could not load candidate records. Please try again.</div>');
        }
    });
}

function displayResults(records, pagination) {
    if (typeof generate_pagination_list === 'function' && pagination) {
        generate_pagination_list(pagination.total_pages, pagination.total_records);
    }

    if (!records || records.length === 0) {
        $('#results').html('<div class="text-center py-5 text-muted bg-white rounded-3 shadow-sm border p-4"><i class="fa fa-inbox fa-3x mb-3 d-block text-secondary"></i><h5>No candidates found</h5><p class="small text-muted mb-0">Try adjusting your filters or search terms.</p></div>');
        return;
    }

    if (currentView === 'table') {
        let rows = '';
        records.forEach(function (app) {
            let badgeClass = 'bg-secondary';
            if (app.status_code === 'submitted') badgeClass = 'bg-primary';
            else if (app.status_code === 'screened') badgeClass = 'bg-info text-dark';
            else if (app.status_code === 'interviewed') badgeClass = 'bg-warning text-dark';
            else if (app.status_code === 'on_roster') badgeClass = 'bg-success';
            else if (app.status_code === 'rejected') badgeClass = 'bg-danger';

            const scoreDisplay = (app.total_score !== null && app.total_score > 0)
                ? '<strong class="text-dark small">' + parseFloat(app.total_score).toFixed(1) + '/100</strong>'
                : '<span class="text-muted small">Not Scored</span>';

            const trackBadgeClass = (app.track_code === 'apprentice') ? 'bg-success' : 'bg-primary';
            const cvBadge = app.has_cv
                ? '<span class="badge bg-light text-danger border ms-1"><i class="fa fa-file-pdf me-1"></i>CV</span>'
                : '';

            let shortlistAction = '';
            if (app.status_id === 2) {
                shortlistAction = '<button type="button" class="btn btn-sm btn-outline-success rounded-pill btn-inline-shortlist" data-app-id="' + app.iD + '"><i class="fa fa-check-circle me-1"></i> Shortlist</button>';
            } else if (app.status_id === 3) {
                shortlistAction = '<button type="button" class="btn btn-sm btn-outline-info rounded-pill btn-inline-shortlist" data-app-id="' + app.iD + '"><i class="fa fa-redo me-1"></i> Resend Link</button>';
            }

            rows += `<tr>
                <td class="fw-bold text-muted">#APP-${String(app.iD).padStart(5, '0')}</td>
                <td>
                    <div class="fw-bold text-dark">${escapeHtml(app.legal_name)}${cvBadge}</div>
                    <small class="text-muted">${escapeHtml(app.email)} &bull; ${escapeHtml(app.mobile_number || '')}</small>
                </td>
                <td><span class="badge ${trackBadgeClass} rounded-pill px-2 py-1">${escapeHtml(app.track_name)}</span></td>
                <td><span class="small fw-semibold">${escapeHtml(app.primary_function)}</span></td>
                <td><span class="small text-muted">${escapeHtml(app.city || 'Harare')}${app.province ? ', ' + escapeHtml(app.province) : ''}</span></td>
                <td><span class="badge ${badgeClass} rounded-pill px-2 py-1" id="status_badge_${app.iD}">${escapeHtml(app.status_name)}</span></td>
                <td>${scoreDisplay}</td>
                <td><small class="text-muted">${app.reg_date}</small></td>
                <td class="text-end">
                    <div class="d-flex justify-content-end gap-1 flex-wrap">
                        ${shortlistAction}
                        <a href="${site}/admin/roster/review?id=${app.iD}" class="btn btn-sm btn-primary rounded-pill">
                            <i class="fa fa-gavel me-1"></i> Review
                        </a>
                    </div>
                </td>
            </tr>`;
        });

        const tableHtml = `
            <div class="table-responsive bg-white rounded-3 shadow-sm border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Applicant</th>
                            <th>Track</th>
                            <th>Practice Area</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Applied</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>`;
        $('#results').html(tableHtml);
    } else {
        let cards = '<div class="row g-3">';
        records.forEach(function (app) {
            let badgeClass = 'bg-secondary';
            if (app.status_code === 'submitted') badgeClass = 'bg-primary';
            else if (app.status_code === 'screened') badgeClass = 'bg-info text-dark';
            else if (app.status_code === 'interviewed') badgeClass = 'bg-warning text-dark';
            else if (app.status_code === 'on_roster') badgeClass = 'bg-success';
            else if (app.status_code === 'rejected') badgeClass = 'bg-danger';

            const trackBadgeClass = (app.track_code === 'apprentice') ? 'bg-success' : 'bg-primary';
            const scoreDisplay = (app.total_score !== null && app.total_score > 0)
                ? '<span class="badge bg-dark fs-6">' + parseFloat(app.total_score).toFixed(1) + ' / 100 PTS</span>'
                : '<span class="badge bg-light text-muted border">Not Scored</span>';

            let shortlistAction = '';
            if (app.status_id === 2) {
                shortlistAction = '<button type="button" class="btn btn-sm btn-outline-success rounded-pill btn-inline-shortlist" data-app-id="' + app.iD + '"><i class="fa fa-check-circle me-1"></i> Shortlist</button>';
            } else if (app.status_id === 3) {
                shortlistAction = '<button type="button" class="btn btn-sm btn-outline-info rounded-pill btn-inline-shortlist" data-app-id="' + app.iD + '"><i class="fa fa-redo me-1"></i> Resend Link</button>';
            }

            cards += `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 bg-white p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge ${trackBadgeClass} rounded-pill px-2 py-1">${escapeHtml(app.track_name)}</span>
                                <span class="badge ${badgeClass} rounded-pill px-2 py-1" id="status_badge_${app.iD}">${escapeHtml(app.status_name)}</span>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">
                                #${String(app.iD).padStart(5, '0')} ${escapeHtml(app.legal_name)}
                                ${app.has_cv ? '<span class="badge bg-light text-danger border ms-1"><i class="fa fa-file-pdf"></i></span>' : ''}
                            </h6>
                            <p class="text-muted small mb-1"><i class="fa fa-envelope me-1"></i> ${escapeHtml(app.email)}</p>
                            <p class="text-muted small mb-1"><i class="fa fa-phone me-1"></i> ${escapeHtml(app.mobile_number || 'N/A')}</p>
                            <p class="text-muted small mb-1"><i class="fa fa-briefcase me-1"></i> ${escapeHtml(app.primary_function)}</p>
                            <p class="text-muted small mb-0"><i class="fa fa-map-marker-alt me-1"></i> ${escapeHtml(app.city || 'Harare')}${app.province ? ', ' + escapeHtml(app.province) : ''}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3">
                            <div>${scoreDisplay}</div>
                            <div class="d-flex gap-1">
                                ${shortlistAction}
                                <a href="${site}/admin/roster/review?id=${app.iD}" class="btn btn-sm btn-primary rounded-pill">
                                    <i class="fa fa-gavel me-1"></i> Review
                                </a>
                            </div>
                        </div>
                    </div>
                </div>`;
        });
        cards += '</div>';
        $('#results').html(cards);
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return $('<div>').text(str).html();
}

$(document).on('click', '.btn-inline-shortlist', function () {
    const $btn = $(this);
    const appId = $btn.data('app-id');
    if (!appId) return;

    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Processing...');

    $.ajax({
        url: site + "/admin/roster/shortlist",
        type: "POST",
        dataType: "json",
        data: { rosterapplication: appId, id: appId },
        success: function (res) {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            if (data.status === 1) {
                $('#opportunities_admin_alert')
                    .removeClass('alert-danger')
                    .addClass('alert-success')
                    .html('<i class="fa fa-check-circle me-2"></i><strong>Candidate #' + appId + ' Shortlisted!</strong> Magic link dispatched via email. <a href="' + (data.dossier_link || '#') + '" target="_blank" class="alert-link ms-2"><i class="fa fa-external-link-alt me-1"></i>Open Dossier Form</a>')
                    .fadeIn();

                // Update status badge
                $('#status_badge_' + appId)
                    .removeClass('bg-secondary bg-primary bg-danger')
                    .addClass('bg-info text-dark')
                    .text('Screened / Shortlisted');

                // Update button
                $btn.removeClass('btn-outline-success')
                    .addClass('btn-outline-info')
                    .prop('disabled', false)
                    .html('<i class="fa fa-redo me-1"></i> Resend Link');

                // Update KPIs
                const $subCount = $('#kpi_submitted');
                const $shortCount = $('#kpi_shortlisted');
                if ($subCount.length) {
                    let subVal = Math.max(0, parseInt($subCount.text()) - 1);
                    $subCount.text(subVal);
                }
                if ($shortCount.length) {
                    let shortVal = parseInt($shortCount.text()) + 1;
                    $shortCount.text(shortVal);
                }
            } else {
                $('#opportunities_admin_alert')
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .html('<i class="fa fa-exclamation-circle me-2"></i>' + (data.msg || 'Could not shortlist candidate.'))
                    .fadeIn();
                $btn.prop('disabled', false).html(originalHtml);
            }
        },
        error: function () {
            $('#opportunities_admin_alert')
                .removeClass('alert-success')
                .addClass('alert-danger')
                .html('<i class="fa fa-exclamation-circle me-2"></i>Network error occurred while shortlisting candidate.')
                .fadeIn();
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
});
</script>
<?php endif; ?>
