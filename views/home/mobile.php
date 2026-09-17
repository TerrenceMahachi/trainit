@extends('layouts.main')

<main class="trainit-page">
    <!-- Hero Header -->
    <section class="trainit-page-head">
        <div class="trainit-wrap">
            <p class="trainit-eyebrow">
                <i class="fa fa-mobile-alt me-1 text-primary"></i> Tsigiro Android Application
            </p>
            <h1>Operational Backing in the Palm of Your Hand</h1>
            <p class="trainit-lead">
                Manage service tickets, review deliverables, log specialist billable hours, and receive real-time notifications—with full offline caching for low-connectivity field areas across Zimbabwe.
            </p>
            <div class="mt-4 d-flex flex-wrap gap-3 align-items-center">
                <a href="<?= $GLOBALS['siteConfig']->siteUrl ?>/mobile/download" class="btn btn-primary btn-lg px-4 py-3 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px; font-weight: 600;">
                    <i class="fa fa-download fa-lg"></i>
                    <span>Download Android APK</span>
                    <span class="badge bg-white text-primary ms-1" style="font-size: 0.72rem;">v1.0 &bull; 1.3 MB</span>
                </a>
                <a href="#install-guide" class="btn btn-outline-secondary btn-lg px-4 py-3" style="border-radius: 12px; font-weight: 600;">
                    <i class="fa fa-info-circle me-1"></i> Installation Guide
                </a>
            </div>
            <p class="small text-muted mt-2 mb-0">
                <i class="fa fa-shield-alt text-success me-1"></i> Production Signed &bull; SHA-256 Verified &bull; Compatible with Android 7.0 (Nougat) to Android 15
            </p>
        </div>
    </section>

    <!-- App Highlights -->
    <section class="trainit-section trainit-white">
        <div class="trainit-wrap">
            <div class="text-center mb-5">
                <p class="trainit-eyebrow">Built for Modern Operations</p>
                <h2>Engineered for Clients, Specialists & Field Teams</h2>
                <p class="text-muted mx-auto" style="max-width: 680px;">
                    Whether you are an organization commissioning mission-critical deliverables or an associate specialist working on-site, Tsigiro Mobile keeps you synchronized.
                </p>
            </div>

            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; color: #2563eb;">
                            <i class="fa fa-wifi fa-lg"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Offline-First Caching</h4>
                        <p class="text-muted small mb-0">
                            Automatic local storage hydration stores active tickets and dashboard metrics. An intelligent banner notifies you when disconnected, with 1-tap network reconnection.
                        </p>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
                        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; color: #10b981;">
                            <i class="fa fa-bell fa-lg"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">In-App Notification Center</h4>
                        <p class="text-muted small mb-0">
                            Real-time header bell indicator with unread badge dot. Slide-out drawer delivers relative timestamps, unread filters, and 1-tap deep links straight to work orders.
                        </p>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; color: #d97706;">
                            <i class="fa fa-users-cog fa-lg"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Multi-Persona Portal</h4>
                        <p class="text-muted small mb-0">
                            Contextualized dashboards and ledger views for Corporate Clients, Associate Specialists, Apprentice Engineers, Job Candidates, and System Administrators.
                        </p>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
                        <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; color: #0284c7;">
                            <i class="fa fa-stopwatch fa-lg"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Rapid Time Logging</h4>
                        <p class="text-muted small mb-0">
                            Specialists and apprentices can log billable task hours on the go with quick-hour chips, category selectors, and daily summaries tied directly to retainer budgets.
                        </p>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
                        <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; color: #ef4444;">
                            <i class="fa fa-camera fa-lg"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Native Camera & File Picker</h4>
                        <p class="text-muted small mb-0">
                            Capture field receipts, inspection photos, and compliance PDF attachments directly through the modernized Android system file chooser.
                        </p>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="col-md-4">
                    <div class="card h-100 p-4 border-0 shadow-sm" style="border-radius: 16px; background: #f8fafc;">
                        <div class="rounded-circle bg-purple bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; color: #7c3aed; background-color: rgba(124, 58, 237, 0.1);">
                            <i class="fa fa-bolt fa-lg"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-2">Ultra-Lightweight & Fast</h4>
                        <p class="text-muted small mb-0">
                            Optimized with R8 minification and resource shrinking into a compact 1.3 MB package, conserving mobile data bundles while delivering instantaneous screen transitions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Installation Instructions -->
    <section id="install-guide" class="trainit-section">
        <div class="trainit-wrap">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <p class="trainit-eyebrow">Quick Setup</p>
                    <h2>How to Install on Your Android Device</h2>
                    <p class="text-muted mb-4">
                        Because Tsigiro Mobile is an enterprise companion application, you can install the standalone signed package in 3 quick steps:
                    </p>

                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <span class="badge bg-primary rounded-circle p-2 fs-6" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Download the APK File</h5>
                            <p class="text-muted small mb-0">Tap the <strong>Download Android APK</strong> button on your phone or tablet to save <code>tsigiro-mobile.apk</code>.</p>
                        </div>
                    </div>

                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <span class="badge bg-primary rounded-circle p-2 fs-6" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Allow Unknown Apps (First Time Only)</h5>
                            <p class="text-muted small mb-0">When prompted by Chrome or your browser, tap <em>Settings</em> and toggle <em>"Allow from this source"</em>.</p>
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="me-3">
                            <span class="badge bg-primary rounded-circle p-2 fs-6" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Tap Install & Launch</h5>
                            <p class="text-muted small mb-0">Tap <em>Install</em>, open the app, and log in with your existing Tsigiro credentials or browse opportunities as a guest.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 text-center">
                    <div class="card p-4 border-0 shadow-lg" style="border-radius: 20px; background: linear-gradient(135deg, #0f172a, #1e293b); color: #ffffff;">
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-primary rounded-3 text-white fw-bold shadow-sm" style="width: 56px; height: 56px; font-size: 1.6rem;">
                                T
                            </div>
                        </div>
                        <h3 class="h4 fw-bold mb-1">Tsigiro Mobile</h3>
                        <p class="text-secondary small mb-3">Enterprise Android Companion &bull; Release Build</p>

                        <div class="bg-dark bg-opacity-50 p-3 rounded-3 mb-4 text-start small font-monospace" style="border: 1px solid rgba(255,255,255,0.1);">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Package:</span>
                                <span>zw.co.tsigiro.mobile</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Version:</span>
                                <span>1.0 (Production Signed)</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">File Size:</span>
                                <span>1.3 MB (Optimized)</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Signature:</span>
                                <span>APK Scheme v2 (RSA 2048)</span>
                            </div>
                        </div>

                        <a href="<?= $GLOBALS['siteConfig']->siteUrl ?>/mobile/download" class="btn btn-primary btn-lg w-100 py-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow" style="border-radius: 12px;">
                            <i class="fa fa-download"></i>
                            <span>Download APK (1.3 MB)</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
