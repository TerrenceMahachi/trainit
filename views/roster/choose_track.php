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

<style>
/* Tsigiro Roster Design System */
.track-page {
    background-color: #f8fafc;
    min-height: calc(100vh - 86px);
    color: #0f172a;
}

.track-hero {
    background: radial-gradient(circle at 85% 25%, rgba(50, 201, 154, 0.22), transparent 36%),
                linear-gradient(135deg, #090b0b 0%, #111a17 55%, #182823 100%);
    border-bottom: 2px solid rgba(50, 201, 154, 0.4);
    padding: 56px 0 46px;
    color: #ffffff;
}

.track-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: #32c99a;
    margin-bottom: 12px;
}

.track-hero h1 {
    font-size: clamp(2rem, 4vw, 2.75rem);
    font-weight: 750;
    letter-spacing: -0.025em;
    color: #ffffff;
    margin-bottom: 12px;
    line-height: 1.15;
}

.track-hero-intro {
    max-width: 680px;
    font-size: 1.05rem;
    line-height: 1.6;
    color: rgba(255, 255, 255, 0.82);
    margin: 0;
}

.track-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}
.track-back-link:hover {
    color: #32c99a;
}

/* Auth Banner */
.track-auth-notice {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #32c99a;
    border-radius: 16px;
    padding: 18px 24px;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
}

/* Layer Cards */
.track-card {
    background: #ffffff;
    border: 1px solid rgba(226, 232, 240, 0.9);
    border-radius: 24px;
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.04), 0 20px 40px -15px rgba(9, 11, 11, 0.03);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.track-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.1);
    border-color: rgba(50, 201, 154, 0.5);
}

.track-card-header {
    padding: 32px 32px 24px;
    border-bottom: 1px solid #f1f5f9;
}

.track-card-associate .track-card-header {
    background: linear-gradient(180deg, #daf8ee 0%, #ffffff 100%);
}

.track-card-apprentice .track-card-header {
    background: linear-gradient(180deg, #111a17 0%, #090b0b 100%);
    color: #ffffff;
}

.track-layer-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
}

.layer-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.layer-badge-associate {
    background: #090b0b;
    color: #32c99a;
}

.layer-badge-apprentice {
    background: #32c99a;
    color: #090b0b;
}

.track-layer-kicker {
    font-size: 0.72rem;
    font-weight: 750;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #159b75;
}
.track-card-apprentice .track-layer-kicker {
    color: #32c99a;
}

.track-card h3 {
    font-size: 1.65rem;
    font-weight: 750;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}

.track-card-associate h3 {
    color: #090b0b;
}

.track-card-apprentice h3 {
    color: #ffffff;
}

.track-card-desc {
    font-size: 0.92rem;
    line-height: 1.55;
    margin: 0;
}
.track-card-associate .track-card-desc {
    color: #475569;
}
.track-card-apprentice .track-card-desc {
    color: rgba(255, 255, 255, 0.75);
}

.track-card-body {
    padding: 32px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex: 1;
}

.track-features {
    list-style: none;
    padding: 0;
    margin: 0 0 32px;
}

.track-features li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 14px;
    font-size: 0.92rem;
    line-height: 1.5;
    color: #334155;
}

.track-check-icon {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
    background: #daf8ee;
    color: #159b75;
    font-size: 0.75rem;
}

/* Action Buttons */
.btn-track {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 14px 24px;
    border-radius: 999px;
    font-weight: 800;
    font-size: 0.95rem;
    letter-spacing: 0.01em;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    border: none;
}

.btn-track:hover {
    transform: translateY(-2px);
}

.btn-track-primary {
    background: #32c99a;
    color: #090b0b;
    box-shadow: 0 4px 16px rgba(50, 201, 154, 0.3);
}
.btn-track-primary:hover {
    background: #5bdbb3;
    color: #090b0b;
    box-shadow: 0 8px 24px rgba(50, 201, 154, 0.45);
}

.btn-track-dark {
    background: #090b0b;
    color: #ffffff;
    box-shadow: 0 4px 16px rgba(9, 11, 11, 0.2);
}
.btn-track-dark:hover {
    background: #172421;
    color: #ffffff;
    box-shadow: 0 8px 24px rgba(9, 11, 11, 0.32);
}

.btn-track-outline {
    background: #ffffff;
    color: #090b0b;
    border: 1.5px solid #cbd5e1;
}
.btn-track-outline:hover {
    border-color: #32c99a;
    color: #159b75;
    background: #fafdfc;
}

.track-bottom-note {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 20px;
    font-size: 0.88rem;
    color: #64748b;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
}
</style>

<main class="track-page">
    <section class="track-hero">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="track-back-link">
                    <i class="fa fa-arrow-left"></i> Dashboard
                </a>
                <span class="track-kicker mb-0">
                    <i class="fa fa-shield-alt"></i> Tsigiro Talent Intake
                </span>
            </div>
            <h1>Choose Your Application Track</h1>
            <p class="track-hero-intro">Tsigiro operates two complementary delivery layers for high-impact back-office services. Select the track aligned with your career stage, expertise, and goals.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php if (!Auth::check()): ?>
            <div class="row justify-content-center mb-5">
                <div class="col-lg-10">
                    <div class="track-auth-notice d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="track-check-icon" style="width: 42px; height: 42px; font-size: 1rem;">
                                <i class="fa fa-user-check"></i>
                            </span>
                            <div>
                                <strong class="d-block text-dark" style="font-size: 0.95rem;">Already have a Tsigiro account?</strong>
                                <span class="small text-muted">Sign in before applying to auto-populate your verified details and link this submission to your talent profile.</span>
                            </div>
                        </div>
                        <a href="<?= $siteConfig->siteUrl; ?>/login" class="btn btn-sm btn-track-dark px-3 py-2" style="width: auto;">
                            <i class="fa fa-arrow-right-to-bracket me-1"></i> Sign In to Account
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row g-4 justify-content-center">
                <!-- Layer 01: Associate Track -->
                <div class="col-md-6 col-lg-5">
                    <div class="track-card track-card-associate">
                        <div class="track-card-header">
                            <div class="track-layer-meta">
                                <span class="layer-badge layer-badge-associate">Layer 01</span>
                                <span class="track-layer-kicker">Senior Expertise</span>
                            </div>
                            <h3>The Associate Layer</h3>
                            <p class="track-card-desc">For experienced practitioners, specialists, and consultants providing senior oversight, quality assurance, and high-level advisory.</p>
                        </div>
                        <div class="track-card-body">
                            <ul class="track-features">
                                <li>
                                    <span class="track-check-icon"><i class="fa fa-check"></i></span>
                                    <span>Senior quality assurance, escalation support &amp; technical sign-off</span>
                                </li>
                                <li>
                                    <span class="track-check-icon"><i class="fa fa-check"></i></span>
                                    <span>Competitive indicative day rates (USD) on flexible calls</span>
                                </li>
                                <li>
                                    <span class="track-check-icon"><i class="fa fa-check"></i></span>
                                    <span>Supervision and mentorship of early-career Apprentices</span>
                                </li>
                                <li>
                                    <span class="track-check-icon"><i class="fa fa-check"></i></span>
                                    <span>Inclusion in high-value institutional tenders, audits &amp; PRAZ bids</span>
                                </li>
                            </ul>
                            <div>
                                <?php if ($hasAssociate): ?>
                                    <div class="d-flex align-items-center justify-content-between mb-3 p-2 px-3 bg-light rounded-pill border">
                                        <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Under Review</span>
                                        <span class="text-muted small fw-semibold">Application Active</span>
                                    </div>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/associate" class="btn-track btn-track-outline">
                                        <span>View Status &amp; Manage</span> <i class="fa fa-arrow-right"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/associate" class="btn-track btn-track-dark">
                                        <span>Apply as an Associate</span> <i class="fa fa-arrow-right" style="color: #32c99a;"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Layer 02: Apprentice Track -->
                <div class="col-md-6 col-lg-5">
                    <div class="track-card track-card-apprentice">
                        <div class="track-card-header">
                            <div class="track-layer-meta">
                                <span class="layer-badge layer-badge-apprentice">Layer 02</span>
                                <span class="track-layer-kicker">Day-to-day Capacity</span>
                            </div>
                            <h3>The Apprentice Layer</h3>
                            <p class="track-card-desc">For tertiary students on industrial attachment (WRL), recent graduates (within 24 months), or early-career professionals.</p>
                        </div>
                        <div class="track-card-body">
                            <ul class="track-features">
                                <li>
                                    <span class="track-check-icon"><i class="fa fa-check"></i></span>
                                    <span>Supervised placement under senior Associate guidance</span>
                                </li>
                                <li>
                                    <span class="track-check-icon"><i class="fa fa-check"></i></span>
                                    <span>Shared (up to 5 clients at 20%) or Dedicated client allocation</span>
                                </li>
                                <li>
                                    <span class="track-check-icon"><i class="fa fa-check"></i></span>
                                    <span>Structured institutional logbook sign-off &amp; skills acceleration</span>
                                </li>
                                <li>
                                    <span class="track-check-icon"><i class="fa fa-check"></i></span>
                                    <span>Monthly transport and meal stipend support on active client placements</span>
                                </li>
                            </ul>
                            <div>
                                <?php if ($hasApprentice): ?>
                                    <div class="d-flex align-items-center justify-content-between mb-3 p-2 px-3 bg-light rounded-pill border">
                                        <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Under Review</span>
                                        <span class="text-muted small fw-semibold">Application Active</span>
                                    </div>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/apprentice" class="btn-track btn-track-outline">
                                        <span>View Status &amp; Manage</span> <i class="fa fa-arrow-right"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= $siteConfig->siteUrl; ?>/opportunities/apply/apprentice" class="btn-track btn-track-primary">
                                        <span>Apply as an Apprentice</span> <i class="fa fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-lg-10">
                    <div class="track-bottom-note">
                        <i class="fa fa-info-circle text-success me-1"></i>
                        <span>Candidates may submit applications across different service lines or hold multiple profiles simultaneously as career experience progresses.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
