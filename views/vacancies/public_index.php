@extends('layouts.main')

<?php
global $siteConfig;
$vacancies   = $data['vacancies'] ?? [];
$departments = $data['departments'] ?? [];
$search      = $data['search'] ?? '';
$selectedDept= $data['selectedDept'] ?? 0;
?>

<main class="trainit-page">
    <section class="opportunity-hero py-5" style="background: linear-gradient(135deg, #1C0D30 0%, #2A114B 60%, #3B1B66 100%); color: #ffffff;">
        <div class="container py-4 text-center">
            <p class="text-warning text-uppercase fw-bold letter-spacing-1 mb-2" style="font-size: 0.85rem;">
                <i class="fa fa-bullhorn me-1"></i> Tsigiro Talent Network
            </p>
            <h1 class="display-5 fw-bold text-white mb-3">Published Job Vacancies &amp; Open Positions</h1>
            <p class="lead text-white-50 mx-auto mb-4" style="max-width: 720px;">
                Explore current career openings across Operations, IT &amp; Engineering, Finance &amp; Compliance, and Project Advisory.
            </p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-outline-light rounded-pill px-4">
                    <i class="fa fa-arrow-left me-1"></i> Back to Opportunities Overview
                </a>
            </div>
        </div>
    </section>

    <section class="py-5" style="background: #f8fafc;">
        <div class="container">
            <!-- Search & Filters -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white">
                <form method="GET" action="<?= $siteConfig->siteUrl; ?>/opportunities/vacancies" class="row g-2 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-search text-muted"></i></span>
                            <input type="search" name="search" value="<?= htmlspecialchars($search); ?>" class="form-control border-start-0" placeholder="Search job title, skills, keywords...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="dept" class="form-select" onchange="this.form.submit()">
                            <option value="0">All Departments</option>
                            <?php foreach ($departments as $d): ?>
                                <option value="<?= $d->iD; ?>" <?= $selectedDept === (int)$d->iD ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($d->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 fw-semibold rounded-3">Search</button>
                        <?php if ($search || $selectedDept): ?>
                            <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancies" class="btn btn-outline-secondary rounded-3" title="Clear Filters"><i class="fa fa-rotate-left"></i></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Vacancy Cards Grid -->
            <div class="row g-4">
                <?php if (empty($vacancies)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-briefcase fa-3x text-secondary opacity-50 mb-3"></i>
                        <h4 class="fw-bold text-dark">No Published Openings Found</h4>
                        <p class="text-muted">We do not have active openings matching your criteria right now. Check back soon or register your interest on our general roster.</p>
                        <a href="<?= $siteConfig->siteUrl; ?>/opportunities" class="btn btn-warning text-dark fw-bold rounded-pill px-4 mt-2">
                            Explore General Roster Tracks
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($vacancies as $v):
                        $dept = $v->department();
                        $engagement = $v->engagementbasis();
                        $location = $v->worklocationpreference();
                        $skills = $v->skills();
                        $days = $v->daysRemaining();
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 p-4 bg-white d-flex flex-column justify-content-between" style="border-top: 4px solid <?= $v->is_featured ? '#FFCC00' : '#2A114B'; ?> !important;">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <code class="small text-muted fw-bold"><?= htmlspecialchars($v->reference_number); ?></code>
                                        <?php if ($v->is_featured): ?>
                                            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.7rem;">Featured</span>
                                        <?php endif; ?>
                                    </div>
                                    <h5 class="fw-bold mb-2">
                                        <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancy/<?= urlencode($v->slug); ?>" class="text-dark text-decoration-none hover-primary">
                                            <?= htmlspecialchars($v->title); ?>
                                        </a>
                                    </h5>
                                    <div class="small text-secondary fw-semibold mb-2">
                                        <i class="fa fa-building me-1"></i> <?= htmlspecialchars($dept ? $dept->name : 'Operations'); ?>
                                    </div>
                                    <p class="small text-muted mb-3" style="line-height: 1.6;">
                                        <?= htmlspecialchars(mb_strimwidth($v->summary, 0, 140, '...')); ?>
                                    </p>

                                    <?php if (!empty($skills)): ?>
                                        <div class="d-flex flex-wrap gap-1 mb-3">
                                            <?php foreach (array_slice($skills, 0, 3) as $sk): ?>
                                                <span class="badge bg-light text-dark border small"><?= htmlspecialchars($sk['name']); ?></span>
                                            <?php endforeach; ?>
                                            <?php if (count($skills) > 3): ?>
                                                <span class="badge bg-light text-muted border small">+<?= count($skills) - 3; ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="pt-3 border-top mt-2">
                                    <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                                        <span><i class="fa fa-location-dot text-danger me-1"></i> <?= htmlspecialchars($location ? $location->name : 'Harare'); ?></span>
                                        <span class="text-success fw-semibold"><i class="fa fa-clock me-1"></i> <?= $days; ?> days left</span>
                                    </div>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/vacancy/<?= urlencode($v->slug); ?>" class="btn btn-warning text-dark fw-bold w-100 rounded-pill shadow-sm">
                                        View Details &amp; Apply <i class="fa fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>
