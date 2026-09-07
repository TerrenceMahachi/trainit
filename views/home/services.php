@extends('layouts.main')

<?php global $siteConfig; ?>
<main class="trainit-page">
    <section class="trainit-page-head">
        <div class="trainit-wrap">
            <p class="trainit-eyebrow">Services</p>
            <h1>Dependable digital and professional services.</h1>
            <p class="trainit-lead">Trainit combines managed technology with a vetted delivery network. The initial portal service catalogue covers HR, finance, IT and marketing, with room to expand.</p>
        </div>
    </section>
    <section class="trainit-section trainit-white">
        <div class="trainit-wrap">
            <div class="trainit-service-list">
                <article class="trainit-service"><small>01</small><h2>Human Resources</h2><p>HR administration, policy, recruitment support, people operations and specialist advisory work.</p></article>
                <article class="trainit-service"><small>02</small><h2>Finance</h2><p>Bookkeeping support, reporting, reconciliations, financial operations and advisory services.</p></article>
                <article class="trainit-service"><small>03</small><h2>Information Technology</h2><p>Websites, cloud hosting, systems, domains, email, support, maintenance and technology advisory.</p></article>
                <article class="trainit-service"><small>04</small><h2>Marketing</h2><p>Campaign support, content, digital channels, brand execution and performance coordination.</p></article>
                <article class="trainit-service"><small>05</small><h2>Managed Requests</h2><p>A central channel for scoping, assignment, communication, time capture and approval.</p></article>
                <article class="trainit-service"><small>06</small><h2>Flexible Resourcing</h2><p>Associate-level expertise or supervised apprentice capacity, matched to the nature of the work.</p></article>
            </div>
            <div class="trainit-actions">
                <a class="trainit-button trainit-button-primary" href="<?= $siteConfig->siteUrl ?>/contact">Discuss a Service</a>
            </div>
        </div>
    </section>
</main>
