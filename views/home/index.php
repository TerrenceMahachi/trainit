@extends('layouts.main')

<?php global $siteConfig; $homeUser = $data['user'] ?? null; ?>
<main class="trainit-page">
    <section class="trainit-hero">
        <div class="trainit-wrap trainit-hero-inner">
            <p class="trainit-eyebrow">Back-office shared services for mission-driven organisations</p>
            <h1>Your mission deserves a stronger operational backbone.</h1>
            <p>Reliable IT, Finance, HR, Audit &amp; Compliance, M&amp;E, Communications and Fundraising capacity—without the cost of carrying full in-house departments.</p>
            <div class="trainit-actions">
                <a class="trainit-button trainit-button-primary" href="<?= $siteConfig->siteUrl ?>/services">Explore Services</a>
                <?php if ($homeUser): ?>
                    <a class="trainit-button trainit-button-secondary" href="<?= $siteConfig->siteUrl ?>/dashboard">Open Dashboard</a>
                <?php else: ?>
                    <a class="trainit-button trainit-button-secondary" href="<?= $siteConfig->siteUrl ?>/opportunities">Recruitment Portal</a>
                    <a class="trainit-button trainit-button-outline" href="<?= $siteConfig->siteUrl ?>/login">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="trainit-section trainit-white">
        <div class="trainit-wrap trainit-split">
            <div>
                <p class="trainit-eyebrow trainit-eyebrow-dark">Built for mission-driven organisations</p>
                <h2>Support that keeps your attention where it belongs.</h2>
                <p>Tsigiro works exclusively with NGOs, civil society organisations, community-based organisations, trusts and networks. We understand the pressure of donor reporting cycles, audit readiness, compliance obligations and lean staffing between funding rounds.</p>
                <p>Our role is simple: carry the operational weight so your people, budgets and attention stay focused on the mission.</p>
            </div>
            <div class="trainit-visual">
                <h3>A trusted operational backbone for organisations across Zimbabwe and the region.</h3>
            </div>
        </div>
    </section>

    <section class="trainit-section">
        <div class="trainit-wrap">
            <p class="trainit-eyebrow trainit-eyebrow-dark">Our service lines</p>
            <h2>A full back office. Built around your mission.</h2>
            <div class="trainit-grid">
                <article class="trainit-card">
                    <h3>Information Technology</h3>
                    <p>Systems administration, cloud and email infrastructure, cybersecurity basics, helpdesk support and digital-tool rollout.</p>
                </article>
                <article class="trainit-card">
                    <h3>Finance</h3>
                    <p>Bookkeeping, management accounts, donor budget tracking, reconciliations and financial-systems setup.</p>
                </article>
                <article class="trainit-card">
                    <h3>Human Resources</h3>
                    <p>HR administration, recruitment support, policy development, statutory filings and payroll coordination.</p>
                </article>
                <article class="trainit-card">
                    <h3>Audit &amp; Compliance</h3>
                    <p>Internal controls support, compliance tracking, risk reviews and practical year-round audit readiness.</p>
                </article>
                <article class="trainit-card">
                    <h3>Monitoring &amp; Evaluation</h3>
                    <p>M&amp;E systems design, indicator frameworks, data-collection support and donor reporting.</p>
                </article>
                <article class="trainit-card">
                    <h3>Strategic Support</h3>
                    <p>Communications, fundraising, proposal coordination and operational functions shaped around your goals.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="trainit-section trainit-band">
        <div class="trainit-wrap trainit-split">
            <div>
                <h2>Expert depth meets consistent day-to-day capacity.</h2>
                <p>Every engagement is built on two complementary layers: senior Associate specialist oversight for quality assurance and escalation, combined with dedicated or shared Apprentice capacity handling everyday execution.</p>
            </div>
            <div class="trainit-metrics">
                <div class="trainit-metric"><strong>6</strong><span>Core back-office service lines</span></div>
                <div class="trainit-metric"><strong>2</strong><span>Delivery layers: Senior Associates &amp; Supervised Apprentices</span></div>
                <div class="trainit-metric"><strong>100%</strong><span>Audit-ready compliance and quality assurance</span></div>
            </div>
        </div>
    </section>
</main>
