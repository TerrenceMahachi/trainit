@extends('layouts.main')

<?php
global $siteConfig;
?>
<main class="portal-dashboard">
    <section class="portal-dashboard-header">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" style="color:rgba(255,255,255,0.7); text-decoration:none;"><i class="fa fa-arrow-left me-1"></i> Back to Dashboard</a></p>
                <h1>Choose Your Application Track</h1>
                <p class="portal-dashboard-intro">Trainit operates two distinct paths for contributing to real client delivery. Select the route that matches your current stage and goals.</p>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <!-- Apprentice Track -->
                <div class="col-md-6 col-lg-5">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow:hidden;">
                        <div class="card-header bg-success text-white py-3">
                            <span class="badge bg-white text-success fw-bold text-uppercase px-2 py-1 mb-2">Early Career & Attachment</span>
                            <h3 class="h4 mb-0"><i class="fa fa-user-graduate me-2"></i> Apprentice Roster</h3>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted">Designed for tertiary students on Work-Related Learning (WRL) / industrial attachment, recent graduates (within 24 months), or early-career professionals.</p>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fa fa-check-circle text-success me-2"></i> Supervised placement under experienced Associates</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-success me-2"></i> Shared (up to 5 clients at 20%) or Dedicated placements</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-success me-2"></i> Structured logbook support & institution assessment</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-success me-2"></i> Monthly transport and meal stipend support</li>
                                </ul>
                            </div>
                            <div>
                                <a href="<?= $siteConfig->siteUrl; ?>/dashboard/apply/apprentice" class="btn btn-success w-100 py-2 fw-semibold">
                                    Start Apprentice Application <i class="fa fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Associate Track -->
                <div class="col-md-6 col-lg-5">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow:hidden;">
                        <div class="card-header bg-primary text-white py-3">
                            <span class="badge bg-white text-primary fw-bold text-uppercase px-2 py-1 mb-2">Expert & Specialist Layer</span>
                            <h3 class="h4 mb-0"><i class="fa fa-user-tie me-2"></i> Associate Roster</h3>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted">Designed for established practitioners, consultants, and senior specialists offering expert review, quality assurance, technical sign-off, and advisory oversight.</p>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Flexible on-demand assignment calls</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Competitive indicative day rates (USD)</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Senior QA and supervision of Apprentice delivery</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Inclusion in high-value tenders, proposals & PRAZ bids</li>
                                </ul>
                            </div>
                            <div>
                                <a href="<?= $siteConfig->siteUrl; ?>/dashboard/apply/associate" class="btn btn-primary w-100 py-2 fw-semibold">
                                    Start Associate Application <i class="fa fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mt-4 text-center border-0 shadow-sm" style="border-radius: 8px;">
                <i class="fa fa-info-circle me-1"></i> You may hold multiple profiles in different service functions or across both categories simultaneously.
            </div>
        </div>
    </section>
</main>
