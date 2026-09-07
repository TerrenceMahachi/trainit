@extends('layouts.main')

<?php global $siteConfig; $homeUser = $data['user'] ?? null; ?>
<main class="trainit-page">
    <section class="trainit-hero">
        <div class="trainit-wrap trainit-hero-inner">
            <p class="trainit-eyebrow">Cloud, systems, websites and support</p>
            <h1>Technology that keeps your organisation moving.</h1>
            <p>Trainit helps teams put reliable digital foundations in place: hosting, domains, email, business systems, websites, and hands-on technical support.</p>
            <div class="trainit-actions">
                <a class="trainit-button trainit-button-primary" href="<?= $siteConfig->siteUrl ?>/services">Explore Services</a>
                <?php if ($homeUser): ?>
                    <a class="trainit-button trainit-button-secondary" href="<?= $siteConfig->siteUrl ?>/dashboard">Open Dashboard</a>
                <?php else: ?>
                    <a class="trainit-button trainit-button-secondary" href="<?= $siteConfig->siteUrl ?>/register">Create an Account</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="trainit-section trainit-white">
        <div class="trainit-wrap trainit-split">
            <div>
                <p class="trainit-eyebrow trainit-eyebrow-dark">Built for practical operations</p>
                <h2>One partner for the everyday systems behind your work.</h2>
                <p>From online presence to internal tools, Trainit focuses on technology that can be deployed, managed, and supported with clarity. Our managed-service portal will give clients one place to submit work, communicate, and follow delivery.</p>
            </div>
            <div class="trainit-visual">
                <h3>Connected services for teams that need uptime, access and support.</h3>
            </div>
        </div>
    </section>

    <section class="trainit-section">
        <div class="trainit-wrap">
            <p class="trainit-eyebrow trainit-eyebrow-dark">What we cover</p>
            <h2>Digital services under one roof.</h2>
            <div class="trainit-grid">
                <article class="trainit-card"><h3>Cloud Hosting</h3><p>Server space, client portals, web hosting foundations, and practical support for keeping services available.</p></article>
                <article class="trainit-card"><h3>Business Systems</h3><p>ERP, workflow, document, and operational platforms configured around how your organisation works.</p></article>
                <article class="trainit-card"><h3>Web Presence</h3><p>Company websites, landing pages, domain setup, SSL, and email-ready infrastructure.</p></article>
            </div>
        </div>
    </section>

    <section class="trainit-section trainit-band">
        <div class="trainit-wrap trainit-split">
            <div>
                <h2>Start with the systems you already have.</h2>
                <p>Trainit can assess current hosting, files, domains, email, and business applications before recommending the next step. That keeps work focused and avoids unnecessary rebuilds.</p>
            </div>
            <div class="trainit-metrics">
                <div class="trainit-metric"><strong>4</strong><span>Initial professional service areas</span></div>
                <div class="trainit-metric"><strong>1</strong><span>Shared request and communication portal</span></div>
                <div class="trainit-metric"><strong>100%</strong><span>Human-reviewed assignments and billing</span></div>
            </div>
        </div>
    </section>
</main>
