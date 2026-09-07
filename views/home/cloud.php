@extends('layouts.main')

<?php global $siteConfig; ?>
<main class="trainit-page">
    <section class="trainit-page-head">
        <div class="trainit-wrap">
            <p class="trainit-eyebrow">Trainit Cloud</p>
            <h1>Hosting, access, and online services managed with care.</h1>
            <p class="trainit-lead">Trainit Cloud brings together hosting, domains, client access, security basics, and support for keeping services reachable.</p>
        </div>
    </section>
    <section class="trainit-section trainit-white">
        <div class="trainit-wrap trainit-split">
            <div class="trainit-visual"><h3>Cloud foundations for websites, portals, email and operational systems.</h3></div>
            <div>
                <h2>A practical cloud layer for your organisation.</h2>
                <p>The public company site and managed-service portal sit above the technical tools, giving clients a clear route into the right support or service channel.</p>
                <ul class="trainit-check-list">
                    <li>Web hosting and domain-ready environments</li>
                    <li>SSL and secure account access</li>
                    <li>Client-area and service-management foundations</li>
                    <li>Server checks, file access, backups and maintenance</li>
                </ul>
                <div class="trainit-actions"><a class="trainit-button trainit-button-primary" href="<?= $siteConfig->siteUrl ?>/contact">Request Cloud Support</a></div>
            </div>
        </div>
    </section>
</main>
