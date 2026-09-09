@extends('layouts.main')

<?php
global $siteConfig;
$loggedInUser = $data['user'] ?? null;
$apprenticeUrl = $siteConfig->siteUrl . '/opportunities/apply/apprentice';
$associateUrl = $siteConfig->siteUrl . '/opportunities/apply/associate';
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
                
                <?php if ($loggedInUser): ?>
                    <div class="p-3 my-3 rounded-4" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <p class="mb-2 text-white small">
                            <i class="fa fa-user-circle text-warning me-1"></i> Signed in as <strong><?= htmlspecialchars($loggedInUser->name) ?></strong>
                            <?php if ((int)$loggedInUser->role === 1): ?>
                                <span class="badge bg-warning text-dark ms-2">Administrator</span>
                            <?php endif; ?>
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
                            <?php if ((int)$loggedInUser->role === 1): ?>
                                <a class="btn btn-sm btn-outline-warning rounded-pill px-3" href="<?= $siteConfig->siteUrl ?>/admin/roster">
                                    <i class="fa fa-list-check me-1"></i> Vetting Pipeline
                                </a>
                            <?php endif; ?>
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
