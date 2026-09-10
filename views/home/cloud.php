@extends('layouts.main')

<?php global $siteConfig; ?>
<main class="trainit-page">
    <section class="trainit-page-head">
        <div class="trainit-wrap">
            <p class="trainit-eyebrow">Tsigiro Cloud &amp; Managed IT</p>
            <h1>Secure infrastructure and reliable IT capacity for your mission.</h1>
            <p class="trainit-lead">Tsigiro delivers turnkey cloud hosting, domain management, secure client portals, and dedicated IT administration designed specifically for mission-driven organisations.</p>
        </div>
    </section>
    <section class="trainit-section trainit-white">
        <div class="trainit-wrap trainit-split">
            <div class="trainit-visual">
                <h3>Cloud and IT foundations for websites, portals, email and operational systems.</h3>
            </div>
            <div>
                <h2>A practical, secure cloud layer for your organisation.</h2>
                <p>Mission-driven organisations need reliable digital foundations that stay available, backed up, and protected without requiring expensive in-house infrastructure engineers.</p>
                <ul class="trainit-check-list">
                    <li>Web hosting and domain-ready environments</li>
                    <li>SSL certificates, encryption and secure access controls</li>
                    <li>Enterprise email, file storage and collaboration suites</li>
                    <li>Proactive uptime checks, automated backups and helpdesk support</li>
                </ul>
                <div class="trainit-actions">
                    <a class="trainit-button trainit-button-primary" href="<?= $siteConfig->siteUrl ?>/contact">Request IT &amp; Cloud Support</a>
                </div>
            </div>
        </div>
    </section>
</main>
