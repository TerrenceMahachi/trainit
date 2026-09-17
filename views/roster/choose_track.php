@extends('layouts.main')

<?php
global $siteConfig;

use App\Helpers\Auth;
use App\Models\Rosterapplication;

$hasApprentice = false;
$hasAssociate = false;

if (Auth::check()) {
    $userId = Auth::id();
    $apps = Rosterapplication::findByQuery(
        "SELECT * FROM rosterapplication WHERE user = ? AND applicationstatus NOT IN (8, 9)",
        [$userId]
    );
    foreach ($apps as $a) {
        $trackCode = $a->applicationtrack()->code ?? '';
        if ($trackCode === 'apprentice' || (int)$a->applicationtrack === 1) {
            $hasApprentice = true;
        } elseif ($trackCode === 'associate' || (int)$a->applicationtrack === 2) {
            $hasAssociate = true;
        }
    }
}
?>
<main class="portal-dashboard">
    <section class="portal-dashboard-header">
        <div class="container portal-dashboard-header-inner">
            <div>
                <p class="portal-kicker"><a href="<?= $siteConfig->siteUrl; ?>/dashboard" style="color:rgba(255,255,255,0.7); text-decoration:none;"><i class="fa fa-arrow-left me-1"></i> Back to Dashboard</a></p>
                <h1>Choose Your Application Track</h1>
                <p class="portal-dashboard-intro">Tsigiro operates two distinct paths for contributing to real client delivery. Select the route that matches your current stage and goals.</p>
            </div>
        </div>
    </section>

    <section class="portal-dashboard-body">
        <div class="container">
            <?php if (!Auth::check()): ?>
            <div class="row justify-content-center mb-4">
                <div class="col-md-12 col-lg-10">
                    <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px; border-left: 4px solid #7c3aed !important;">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-light text-primary p-2 rounded-circle fs-5" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-user-check"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold text-dark">Already have a Tsigiro account?</div>
                                    <div class="small text-muted">Sign in to auto-fill your contact details and link new applications to your existing profile.</div>
                                </div>
                            </div>
                            <a href="<?= $siteConfig->siteUrl; ?>/login" class="btn btn-outline-primary btn-sm px-3 py-2 fw-semibold">
                                <i class="fa fa-arrow-right-to-bracket me-1"></i> Sign In to Account
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row g-4 justify-content-center">
                <!-- Apprentice Track -->
                <div class="col-md-6 col-lg-5">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow:hidden;">
                        <div class="card-header bg-success text-white py-3">
                            <span class="badge bg-white text-success fw-bold text-uppercase px-2 py-1 mb-2">Early Career &amp; Attachment</span>
                            <h3 class="h4 mb-0"><i class="fa fa-user-graduate me-2"></i> Apprentice Roster</h3>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted">Designed for tertiary students on Work-Related Learning (WRL) / industrial attachment, recent graduates (within 24 months), or early-career professionals.</p>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fa fa-check-circle text-success me-2"></i> Supervised placement under experienced Associates</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-success me-2"></i> Shared (up to 5 clients at 20%) or Dedicated placements</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-success me-2"></i> Structured logbook support &amp; institution assessment</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-success me-2"></i> Monthly transport and meal stipend support</li>
                                </ul>
                            </div>
                            <div>
                                <?php if ($hasApprentice): ?>
                                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded-3 border">
                                        <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Under Review</span>
                                        <span class="text-muted small">Application active</span>
                                    </div>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/apprentice" class="btn btn-outline-success w-100 py-2 fw-semibold">
                                        <i class="fa fa-eye me-1"></i> View Status &amp; Manage / Revoke <i class="fa fa-arrow-right ms-2"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/apprentice" class="btn btn-success w-100 py-2 fw-semibold">
                                        Start Apprentice Application <i class="fa fa-arrow-right ms-2"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Associate Track -->
                <div class="col-md-6 col-lg-5">
                    <div class="card h-100 shadow-sm border-0" style="border-radius: 12px; overflow:hidden;">
                        <div class="card-header bg-primary text-white py-3">
                            <span class="badge bg-white text-primary fw-bold text-uppercase px-2 py-1 mb-2">Expert &amp; Specialist Layer</span>
                            <h3 class="h4 mb-0"><i class="fa fa-user-tie me-2"></i> Associate Roster</h3>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <p class="text-muted">Designed for established practitioners, consultants, and senior specialists offering expert review, quality assurance, technical sign-off, and advisory oversight.</p>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Flexible on-demand assignment calls</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Competitive indicative day rates (USD)</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Senior QA and supervision of Apprentice delivery</li>
                                    <li class="mb-2"><i class="fa fa-check-circle text-primary me-2"></i> Inclusion in high-value tenders, proposals &amp; PRAZ bids</li>
                                </ul>
                            </div>
                            <div>
                                <?php if ($hasAssociate): ?>
                                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded-3 border">
                                        <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Under Review</span>
                                        <span class="text-muted small">Application active</span>
                                    </div>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/associate" class="btn btn-outline-primary w-100 py-2 fw-semibold">
                                        <i class="fa fa-eye me-1"></i> View Status &amp; Manage / Revoke <i class="fa fa-arrow-right ms-2"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/associate" class="btn btn-primary w-100 py-2 fw-semibold">
                                        Start Associate Application <i class="fa fa-arrow-right ms-2"></i>
                                    </a>
                                <?php endif; ?>
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
