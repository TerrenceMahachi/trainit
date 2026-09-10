@extends('layouts.main')

<?php global $siteConfig; ?>
<main class="trainit-page">
    <section class="trainit-page-head">
        <div class="trainit-wrap">
            <p class="trainit-eyebrow">Our Service Lines</p>
            <h1>A full back office. Built around your mission.</h1>
            <p class="trainit-lead">Take one service on its own or combine several. Every service is available through both Shared (20%) and Dedicated (100%) subscription tiers with senior Associate quality oversight.</p>
        </div>
    </section>
    <section class="trainit-section trainit-white">
        <div class="trainit-wrap">
            <div class="trainit-service-list">
                <article class="trainit-service">
                    <small>01</small>
                    <h2>Information Technology</h2>
                    <p>Systems administration, cloud and email infrastructure, cybersecurity basics, helpdesk support and digital-tool rollout.</p>
                </article>
                <article class="trainit-service">
                    <small>02</small>
                    <h2>Finance</h2>
                    <p>Bookkeeping, management accounts, donor budget tracking, reconciliations and financial-systems setup.</p>
                </article>
                <article class="trainit-service">
                    <small>03</small>
                    <h2>Human Resources</h2>
                    <p>HR administration, recruitment support, policy development, statutory filings and payroll coordination.</p>
                </article>
                <article class="trainit-service">
                    <small>04</small>
                    <h2>Audit &amp; Compliance</h2>
                    <p>Internal controls support, compliance tracking, risk reviews and practical year-round audit readiness.</p>
                </article>
                <article class="trainit-service">
                    <small>05</small>
                    <h2>Monitoring &amp; Evaluation</h2>
                    <p>M&amp;E systems design, indicator tracking, data-collection support, field reporting and donor compliance.</p>
                </article>
                <article class="trainit-service">
                    <small>06</small>
                    <h2>Strategic &amp; Mission Support</h2>
                    <p>Communications, fundraising, donor proposal coordination and operational functions shaped around your goals.</p>
                </article>
            </div>
            <div class="trainit-actions">
                <a class="trainit-button trainit-button-primary" href="<?= $siteConfig->siteUrl ?>/contact">Discuss a Service</a>
                <a class="trainit-button trainit-button-outline" href="<?= defined('_WEBSITE_URL') ? _WEBSITE_URL . '#tiers' : 'https://tsigiro.co.zw#tiers' ?>" target="_blank" rel="noopener">Explore Subscription Tiers</a>
            </div>
        </div>
    </section>
</main>
