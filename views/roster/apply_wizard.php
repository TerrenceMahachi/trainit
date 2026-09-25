@extends('layouts.main')

<?php
global $siteConfig;

$track = $data['track'];
$app = $data['application'];
$trackCode = $track->code;
$appId = $app ? (int)$app->iD : 0;
$appProfile = $app ? $app->apprenticeProfile() : null;
$assocProfile = $app ? $app->associateProfile() : null;
$judgement = $app ? $app->judgementResponse() : null;
$skills = $app ? $app->skills() : [];
$qualifications = $app ? $app->qualifications() : [];
$workHistories = $app ? $app->workHistories() : [];
$referees = $app ? $app->referees() : [];

$existingSkillsMap = [];
foreach ($skills as $sk) {
    $existingSkillsMap[$sk->skillitem] = $sk->proficiencylevel;
}

$isLoggedIn = \App\Helpers\Auth::check() && !empty($data['user']);
$currentUser = $isLoggedIn ? $data['user'] : null;
?>

<style>
/* ==========================================================================
   Tsigiro Roster Wizard - Modern Design System
   ========================================================================== */
:root {
    --ts-ink: #090b0b;
    --ts-ink-soft: #172421;
    --ts-green: #32c99a;
    --ts-green-dark: #159b75;
    --ts-green-soft: #daf8ee;
    --ts-green-glow: rgba(50, 201, 154, 0.22);
    --ts-bg: #f8fafc;
    --ts-card-bg: #ffffff;
    --ts-border: #e2e8f0;
    --ts-text: #0f172a;
    --ts-text-muted: #64748b;
    --ts-radius: 20px;
    --ts-radius-sm: 12px;
    --ts-radius-pill: 9999px;
    --ts-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.04), 0 20px 40px -15px rgba(9, 11, 11, 0.03);
}

.wizard-page {
    background-color: var(--ts-bg);
    min-height: calc(100vh - 86px);
    color: var(--ts-text);
}

/* Header */
.wizard-header {
    background: radial-gradient(circle at 85% 25%, rgba(50, 201, 154, 0.22), transparent 36%),
                linear-gradient(135deg, #090b0b 0%, #111a17 55%, #182823 100%);
    border-bottom: 2px solid rgba(50, 201, 154, 0.4);
    padding: 36px 0 30px;
    color: #ffffff;
}

.wizard-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}

.wizard-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--ts-green);
    margin-bottom: 6px;
}

.wizard-back-link {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.2s ease;
    font-weight: 600;
}
.wizard-back-link:hover {
    color: #ffffff;
}

.wizard-header h1 {
    font-size: clamp(1.75rem, 3.2vw, 2.25rem);
    font-weight: 750;
    letter-spacing: -0.025em;
    color: #ffffff;
    margin: 0;
    line-height: 1.2;
}

.wizard-track-badge {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 8px 18px;
    border-radius: var(--ts-radius-pill);
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
}

.wizard-track-badge.track-associate {
    border-color: rgba(50, 201, 154, 0.4);
    background: rgba(50, 201, 154, 0.1);
}

.wizard-track-badge.track-apprentice {
    border-color: rgba(50, 201, 154, 0.5);
    background: rgba(50, 201, 154, 0.15);
}

.wizard-track-pill {
    padding: 4px 10px;
    border-radius: var(--ts-radius-pill);
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.track-associate .wizard-track-pill {
    background: #090b0b;
    color: var(--ts-green);
}

.track-apprentice .wizard-track-pill {
    background: var(--ts-green);
    color: #090b0b;
}

/* Floating Stepper Navigation */
.wizard-stepper-sticky {
    position: sticky;
    top: 0;
    z-index: 1025;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--ts-border);
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
}

.stepper-nav {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding: 6px 0;
    margin: 0;
    list-style: none;
}
.stepper-nav::-webkit-scrollbar {
    display: none;
}

.stepper-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: var(--ts-radius-pill);
    border: 1px solid transparent;
    background: transparent;
    color: var(--ts-text-muted);
    font-size: 0.85rem;
    font-weight: 650;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none;
}

.stepper-tab-btn:hover {
    color: var(--ts-text);
    background: #f1f5f9;
}

.stepper-tab-btn.active {
    background: var(--ts-ink) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(9, 11, 11, 0.18);
}

.stepper-tab-btn.active .step-num-circle {
    background: var(--ts-green);
    color: var(--ts-ink);
}

.step-num-circle {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #475569;
    font-size: 0.72rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.step-badge {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: var(--ts-radius-pill);
    transition: all 0.2s ease;
}

.step-badge.bg-secondary {
    background: #e2e8f0 !important;
    color: #64748b !important;
}

.step-badge.bg-success {
    background: var(--ts-green) !important;
    color: var(--ts-ink) !important;
}

/* Autosave & Progress Widget */
.autosave-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: #f8fafc;
    border: 1px solid var(--ts-border);
    border-radius: var(--ts-radius-pill);
    padding: 5px 12px;
    font-size: 0.78rem;
    color: var(--ts-text-muted);
    transition: all 0.2s ease;
}

.autosave-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #94a3b8;
    display: inline-block;
    transition: all 0.2s ease;
}

.autosave-pill.status-saved .autosave-dot {
    background: var(--ts-green);
    box-shadow: 0 0 8px var(--ts-green);
}

.autosave-pill.status-saving .autosave-dot {
    background: #eab308;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { transform: scale(0.9); opacity: 0.7; }
    50% { transform: scale(1.3); opacity: 1; }
    100% { transform: scale(0.9); opacity: 0.7; }
}

.wizard-progress-bar-container {
    height: 4px;
    background: #e2e8f0;
    overflow: hidden;
}

.wizard-progress-bar {
    height: 100%;
    transition: width 0.35s ease;
    background: linear-gradient(90deg, #159b75 0%, #32c99a 100%);
    box-shadow: 0 0 10px rgba(50, 201, 154, 0.4);
}

/* Wizard Form Cards */
.wizard-card {
    background: var(--ts-card-bg);
    border: 1px solid rgba(226, 232, 240, 0.85);
    border-radius: var(--ts-radius);
    box-shadow: var(--ts-shadow);
    overflow: hidden;
    margin-bottom: 24px;
}

.wizard-card-header {
    padding: 26px 32px 20px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdfc 100%);
    border-bottom: 1px solid #f1f5f9;
}

.wizard-card-header .stage-kicker {
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--ts-green-dark);
    margin-bottom: 4px;
    display: block;
}

.wizard-card-header h2 {
    font-size: 1.35rem;
    font-weight: 750;
    letter-spacing: -0.015em;
    color: var(--ts-ink);
    margin: 0 0 4px;
}

.wizard-card-header p {
    font-size: 0.875rem;
    color: var(--ts-text-muted);
    margin: 0;
}

.wizard-card-body {
    padding: 32px;
}

.wizard-section-block {
    background: #fbfcfd;
    border: 1px solid #eef2f6;
    border-radius: var(--ts-radius-sm);
    padding: 24px;
    margin-bottom: 26px;
}

.wizard-section-block-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.98rem;
    font-weight: 750;
    color: var(--ts-ink);
    margin-bottom: 18px;
}

.wizard-section-block-title i {
    color: var(--ts-green-dark);
}

/* Email Identification Gate */
#email_gate_section {
    background: linear-gradient(135deg, #ffffff 0%, #f6fbf9 100%);
    border: 1.5px solid rgba(50, 201, 154, 0.45);
    border-radius: var(--ts-radius-sm);
    box-shadow: 0 4px 20px rgba(50, 201, 154, 0.06);
    padding: 24px;
    margin-bottom: 26px;
    transition: all 0.3s ease;
}

#email_gate_section .input-group {
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    border-radius: var(--ts-radius-pill);
    overflow: hidden;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

#email_gate_section .input-group:focus-within {
    border-color: var(--ts-green);
    box-shadow: 0 0 0 4px var(--ts-green-glow);
}

#email_gate_section .input-group input {
    height: 48px;
    font-size: 0.98rem;
    font-weight: 500;
    border: none !important;
}

#email_gate_section .input-group input:focus {
    box-shadow: none !important;
}

#email_gate_section .input-group .input-group-text {
    border: none;
    background: transparent;
    padding-left: 18px;
    color: #64748b;
    font-size: 1.1rem;
}

#btn_check_email {
    height: 48px;
    border-radius: var(--ts-radius-pill);
    font-size: 0.95rem;
    font-weight: 700;
    letter-spacing: 0.02em;
}

/* Modern Form Fields */
.form-label {
    font-size: 0.82rem;
    font-weight: 650;
    color: #1e293b;
    margin-bottom: 6px;
}

.form-label .text-danger {
    color: #ef4444 !important;
}

.form-control, .form-select {
    border: 1.5px solid var(--ts-border);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 0.92rem;
    color: var(--ts-text);
    background-color: #ffffff;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--ts-green) !important;
    box-shadow: 0 0 0 3.5px var(--ts-green-glow) !important;
    outline: none;
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #ef4444 !important;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
}

textarea.form-control {
    border-radius: 12px;
    line-height: 1.55;
    padding: 12px 14px;
}

/* Guest Password Box */
.guest-account-box {
    background: rgba(50, 201, 154, 0.05);
    border: 1.5px dashed rgba(50, 201, 154, 0.4);
    border-radius: var(--ts-radius-sm);
    padding: 20px;
    margin-bottom: 16px;
}

/* Competency Scale Explainer */
.scale-explainer-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    margin-bottom: 22px;
}

.scale-card {
    background: #ffffff;
    border: 1px solid var(--ts-border);
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
}

.scale-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 0.75rem;
    font-weight: 800;
    margin-bottom: 4px;
}

.scale-card-1 .scale-num { background: #f1f5f9; color: #475569; }
.scale-card-2 .scale-num { background: #e0f2fe; color: #0284c7; }
.scale-card-3 .scale-num { background: #dbeafe; color: #2563eb; }
.scale-card-4 .scale-num { background: #1e293b; color: #ffffff; }
.scale-card-5 .scale-num { background: #daf8ee; color: #159b75; }

.scale-title {
    font-size: 0.82rem;
    font-weight: 750;
    color: var(--ts-ink);
    display: block;
}

.scale-desc {
    font-size: 0.72rem;
    color: var(--ts-text-muted);
    display: block;
    line-height: 1.2;
    margin-top: 2px;
}

/* Skills Rating Selector */
.skills-table {
    border: 1px solid var(--ts-border);
    border-radius: var(--ts-radius-sm);
    overflow: hidden;
    background: #ffffff;
}

.skills-table th {
    background: #f8fafc;
    font-size: 0.78rem;
    font-weight: 750;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--ts-text-muted);
    padding: 12px 18px;
    border-bottom: 1px solid var(--ts-border);
}

.skills-table td {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.rating-btn-group {
    display: flex;
    align-items: center;
    gap: 4px;
}

.rating-btn-group input[type="radio"] {
    display: none;
}

.rating-btn-group label {
    flex: 1;
    text-align: center;
    padding: 6px 4px;
    border-radius: 8px;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s ease;
}

.rating-btn-group label:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}

.rating-btn-group input[type="radio"]:checked + label {
    border-color: var(--ts-green);
    background: var(--ts-green);
    color: var(--ts-ink);
    box-shadow: 0 2px 8px rgba(50, 201, 154, 0.35);
}

.rating-btn-group input[value="0"]:checked + label {
    background: #e2e8f0;
    color: #475569;
    border-color: #cbd5e1;
    box-shadow: none;
}

/* Repeatable Item Cards */
.repeatable-item-card {
    background: #ffffff;
    border: 1px solid var(--ts-border);
    border-radius: var(--ts-radius-sm);
    padding: 16px;
    margin-bottom: 12px;
    position: relative;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
}

.btn-remove-item {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: 1px solid #fee2e2;
    background: #ffffff;
    color: #ef4444;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-remove-item:hover {
    background: #ef4444;
    color: #ffffff;
    border-color: #ef4444;
}

.btn-add-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: var(--ts-radius-pill);
    border: 1.5px dashed var(--ts-green-dark);
    background: rgba(50, 201, 154, 0.06);
    color: var(--ts-green-dark);
    font-size: 0.85rem;
    font-weight: 750;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-add-item:hover {
    background: rgba(50, 201, 154, 0.15);
    border-color: var(--ts-green);
    color: #090b0b;
}

/* Scenario Cards */
.scenario-card {
    background: #ffffff;
    border: 1px solid var(--ts-border);
    border-radius: var(--ts-radius-sm);
    padding: 22px;
    margin-bottom: 22px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.scenario-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 12px;
}

.scenario-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: var(--ts-green-soft);
    color: var(--ts-green-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.95rem;
}

.scenario-title {
    font-size: 0.98rem;
    font-weight: 750;
    color: var(--ts-ink);
    margin-bottom: 4px;
}

.scenario-brief {
    font-size: 0.88rem;
    color: var(--ts-text-muted);
    line-height: 1.5;
    margin: 0;
}

/* File Upload Zone */
.upload-dropzone {
    border: 2px dashed #cbd5e1;
    border-radius: var(--ts-radius-sm);
    padding: 24px;
    background: #fbfcfe;
    text-align: center;
    transition: all 0.2s ease;
}
.upload-dropzone:hover {
    border-color: var(--ts-green);
    background: rgba(50, 201, 154, 0.03);
}

/* Card Footer Navigation */
.wizard-card-footer {
    padding: 22px 32px;
    background: #f8fafc;
    border-top: 1px solid #eef2f6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.btn-wiz-prev {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    border-radius: var(--ts-radius-pill);
    border: 1.5px solid var(--ts-border);
    background: #ffffff;
    color: #475569;
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-wiz-prev:hover {
    border-color: #cbd5e1;
    background: #f1f5f9;
    color: var(--ts-ink);
}

.btn-wiz-save {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: var(--ts-radius-pill);
    border: 1.5px solid var(--ts-border);
    background: #ffffff;
    color: var(--ts-ink);
    font-size: 0.9rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-wiz-save:hover {
    border-color: var(--ts-green);
    color: var(--ts-green-dark);
}

.btn-wiz-next {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 11px 28px;
    border-radius: var(--ts-radius-pill);
    border: none;
    background: var(--ts-green);
    color: var(--ts-ink);
    font-size: 0.92rem;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(50, 201, 154, 0.35);
    transition: all 0.2s ease;
}
.btn-wiz-next:hover {
    background: #5bdbb3;
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(50, 201, 154, 0.45);
}

.btn-wiz-submit {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 13px 34px;
    border-radius: var(--ts-radius-pill);
    border: none;
    background: var(--ts-green);
    color: var(--ts-ink);
    font-size: 0.96rem;
    font-weight: 800;
    letter-spacing: 0.01em;
    cursor: pointer;
    box-shadow: 0 6px 20px rgba(50, 201, 154, 0.4);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.btn-wiz-submit:hover {
    background: #5bdbb3;
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(50, 201, 154, 0.5);
}

@media (max-width: 768px) {
    .wizard-card-header, .wizard-card-body, .wizard-card-footer {
        padding: 20px 18px;
    }
    .scale-explainer-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="wizard-page">
    <!-- Hero Header -->
    <header class="wizard-header">
        <div class="container">
            <div class="wizard-header-inner">
                <div>
                    <div class="wizard-kicker">
                        <a href="<?= $siteConfig->siteUrl; ?>/dashboard" class="wizard-back-link">
                            <i class="fa fa-arrow-left me-1"></i> Dashboard
                        </a>
                        <span>&rsaquo;</span>
                        <span>Talent Roster Intake</span>
                    </div>
                    <h1>Apply as <?= htmlspecialchars($track->name); ?></h1>
                </div>
                <div class="wizard-track-badge track-<?= htmlspecialchars($trackCode); ?>">
                    <span class="wizard-track-pill">
                        <?= $trackCode === 'associate' ? 'Layer 01' : 'Layer 02'; ?>
                    </span>
                    <strong class="small text-uppercase">
                        <?= htmlspecialchars($track->name); ?> Roster
                    </strong>
                </div>
            </div>
        </div>
    </header>

    <!-- Sleek Sticky Stepper & Live Progress Bar -->
    <div class="wizard-stepper-sticky">
        <div class="container py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <!-- Stepper Tabs -->
                <ul class="stepper-nav" id="wizardSteps" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="stepper-tab-btn active" id="step1-tab" data-bs-toggle="tab" data-bs-target="#step1" type="button" role="tab">
                            <span class="step-num-circle">1</span>
                            <span>1. Identity</span>
                            <span class="step-badge bg-secondary" id="step1_badge">0/6</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="stepper-tab-btn" id="step2-tab" data-bs-toggle="tab" data-bs-target="#step2" type="button" role="tab">
                            <span class="step-num-circle">2</span>
                            <span>2. Education</span>
                            <span class="step-badge bg-secondary" id="step2_badge">0/4</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="stepper-tab-btn" id="step3-tab" data-bs-toggle="tab" data-bs-target="#step3" type="button" role="tab">
                            <span class="step-num-circle">3</span>
                            <span>3. Skills</span>
                            <span class="step-badge bg-secondary" id="step3_badge">0/3</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="stepper-tab-btn" id="step4-tab" data-bs-toggle="tab" data-bs-target="#step4" type="button" role="tab">
                            <span class="step-num-circle">4</span>
                            <span>4. Experience</span>
                            <span class="step-badge bg-secondary" id="step4_badge">0/4</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="stepper-tab-btn" id="step5-tab" data-bs-toggle="tab" data-bs-target="#step5" type="button" role="tab">
                            <span class="step-num-circle">5</span>
                            <span>5. Consent</span>
                            <span class="step-badge bg-secondary" id="step5_badge">0/4</span>
                        </button>
                    </li>
                </ul>

                <!-- Live Autosave & Progress Counts -->
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div class="autosave-pill" id="autosave_badge">
                        <span class="autosave-dot"></span>
                        <span class="autosave-text">Auto-save ready</span>
                    </div>
                    <strong class="small fw-bold" style="color: var(--ts-green-dark);" id="progress_percent_label">0%</strong>
                    <span class="text-muted small d-none d-md-inline" id="progress_counts_label">(0/0)</span>
                </div>
            </div>
            <!-- Progress Track -->
            <div class="wizard-progress-bar-container rounded-pill">
                <div id="form_progress_bar" class="wizard-progress-bar" role="progressbar" style="width: 0%;"></div>
            </div>
        </div>
    </div>

    <!-- Main Wizard Body -->
    <section class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    
                    <div id="form_alert" style="display:none;" class="alert mb-4"></div>

                    <form id="roster_wizard_form" enctype="multipart/form-data" method="POST" novalidate>
                        <input type="hidden" name="application_id" id="application_id" value="<?= $appId; ?>">
                        <input type="hidden" name="applicationtrack" value="<?= $track->iD; ?>">
                        <input type="hidden" name="is_submit" id="is_submit_flag" value="0">

                        <div class="tab-content" id="wizardContent">
                            
                            <!-- =======================================================
                                 STAGE 1: IDENTITY & CONTACT
                                 ======================================================= -->
                            <div class="tab-pane fade show active" id="step1" role="tabpanel">
                                <div class="wizard-card">
                                    <div class="wizard-card-header">
                                        <span class="stage-kicker">Stage 01</span>
                                        <h2>Candidate Identification &amp; Contact Details</h2>
                                        <p>Verify your email to resume an existing application or begin a new talent profile.</p>
                                    </div>
                                    <div class="wizard-card-body">
                                        
                                        <?php if ($isLoggedIn && $currentUser): ?>
                                        <!-- Authenticated User Confirmation Banner -->
                                        <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="step-num-circle" style="background:#daf8ee; color:#159b75; width:36px; height:36px; font-size:1rem;">
                                                    <i class="fa fa-user-check"></i>
                                                </span>
                                                <div>
                                                    <strong class="d-block text-dark" style="font-size: 0.95rem;"><?= htmlspecialchars($currentUser->name); ?></strong>
                                                    <span class="small text-muted">Signed in as <strong><?= htmlspecialchars($currentUser->email); ?></strong></span>
                                                </div>
                                            </div>
                                            <span class="badge bg-success text-white px-3 py-2 rounded-pill"><i class="fa fa-shield-alt me-1"></i> Verified Account</span>
                                        </div>
                                        <input type="hidden" name="email" id="field_email" value="<?= htmlspecialchars($currentUser->email); ?>">
                                        <?php else: ?>
                                        <!-- SECTION 1: EMAIL LOOKUP GATE (New, Resuming, or Already Applied) -->
                                        <div class="wizard-section-block mb-4" id="email_gate_section">
                                            <div class="wizard-section-block-title">
                                                <i class="fa fa-envelope-open-text"></i>
                                                <span>Email Identification &amp; Account Status Check</span>
                                            </div>
                                            <p class="small text-muted mb-3">
                                                Enter your primary email address below. We'll verify if you are a new applicant, resuming a saved draft, or checking existing application status.
                                            </p>

                                            <div class="row g-2 align-items-center">
                                                <div class="col-md-8 col-lg-7">
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fa fa-envelope"></i></span>
                                                        <input type="email" class="form-control border-start-0 track-progress req-field email-field" name="email" id="field_email" data-label="Email Address" value="<?= htmlspecialchars($app->email ?? ''); ?>" required placeholder="name@example.com" autocomplete="email">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-lg-3">
                                                    <button type="button" class="btn btn-wiz-next w-100 py-2" id="btn_check_email">
                                                        <span id="btn_check_email_text">Continue</span> <i class="fa fa-arrow-right ms-1"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="email_check_feedback" class="mt-2" style="display:none;"></div>

                                            <!-- State A: Already Applied - Password Verification Challenge -->
                                            <div id="box_already_applied" class="p-3 bg-white rounded-3 border mt-3" style="display:none;">
                                                <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold">
                                                    <i class="fa fa-info-circle fs-5"></i>
                                                    <span id="label_already_applied">Application Already Submitted</span>
                                                </div>
                                                <p class="small text-muted mb-3" id="msg_already_applied">
                                                    You have already submitted an application for this roster. Enter your password to view your application status and track review progress.
                                                </p>
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-md-7">
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="pwd_already_applied" placeholder="Enter your account password">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="const p = document.getElementById('pwd_already_applied'); p.type = p.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('fa-eye'); this.querySelector('i').classList.toggle('fa-eye-slash');"><i class="fa fa-eye"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <button type="button" class="btn btn-wiz-next w-100" id="btn_submit_already_applied">
                                                            <i class="fa fa-arrow-right-to-bracket me-1"></i> Sign In &amp; View Status
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="error_already_applied" class="small text-danger mt-2" style="display:none;"></div>
                                            </div>

                                            <!-- State B: Resuming Draft - Password Verification Challenge -->
                                            <div id="box_resuming" class="p-3 bg-white rounded-3 border mt-3" style="display:none;">
                                                <div class="d-flex align-items-center gap-2 mb-2 text-success fw-bold">
                                                    <i class="fa fa-clock fs-5"></i>
                                                    <span id="label_resuming">In-Progress Application Draft Found</span>
                                                </div>
                                                <p class="small text-muted mb-3" id="msg_resuming">
                                                    Welcome back! You have an unfinished application draft. Enter your password to resume where you left off:
                                                </p>
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-md-7">
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="pwd_resuming" placeholder="Enter your account password">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="const p = document.getElementById('pwd_resuming'); p.type = p.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('fa-eye'); this.querySelector('i').classList.toggle('fa-eye-slash');"><i class="fa fa-eye"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <button type="button" class="btn btn-wiz-next w-100" id="btn_submit_resuming">
                                                            <i class="fa fa-play me-1"></i> Sign In &amp; Resume Draft
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="error_resuming" class="small text-danger mt-2" style="display:none;"></div>
                                            </div>

                                            <!-- State C: Existing User, New Track -->
                                            <div id="box_existing_new_track" class="p-3 bg-white rounded-3 border mt-3" style="display:none;">
                                                <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold">
                                                    <i class="fa fa-user-lock fs-5 text-primary"></i>
                                                    <span>Existing Tsigiro Profile Identified</span>
                                                </div>
                                                <p class="small text-muted mb-3" id="msg_existing_new_track">
                                                    Welcome back! Enter your password to sign in and apply for this track:
                                                </p>
                                                <div class="row g-2 align-items-center">
                                                    <div class="col-md-7">
                                                        <div class="input-group">
                                                            <input type="password" class="form-control" id="pwd_existing_new_track" placeholder="Enter your account password">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="const p = document.getElementById('pwd_existing_new_track'); p.type = p.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('fa-eye'); this.querySelector('i').classList.toggle('fa-eye-slash');"><i class="fa fa-eye"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <button type="button" class="btn btn-wiz-next w-100" id="btn_submit_existing_new_track">
                                                            <i class="fa fa-arrow-right-to-bracket me-1"></i> Sign In &amp; Continue
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="error_existing_new_track" class="small text-danger mt-2" style="display:none;"></div>
                                            </div>

                                            <!-- State D: New Candidate - Phase 2 Registration Card -->
                                            <div id="box_new_candidate" class="p-4 bg-white rounded-3 border mt-3" style="display:none; border-color: rgba(50, 201, 154, 0.45) !important; box-shadow: 0 4px 18px rgba(50, 201, 154, 0.08);">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-3 mb-3 border-bottom">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="step-num-circle" style="background:#daf8ee; color:#159b75; width:34px; height:34px; font-size:0.95rem;">
                                                            <i class="fa fa-user-plus"></i>
                                                        </span>
                                                        <div>
                                                            <strong class="d-block text-dark" style="font-size: 0.98rem;">New Candidate Account Registration</strong>
                                                            <span class="small text-muted">Registering as <strong id="new_candidate_email_display" class="text-dark"></strong></span>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btn_change_email">
                                                        <i class="fa fa-pencil-alt me-1"></i> Change Email
                                                    </button>
                                                </div>

                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label class="form-label">Email Address</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-light text-muted"><i class="fa fa-envelope"></i></span>
                                                            <input type="email" class="form-control bg-light" id="reg_email_display" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Full Legal Name (as on National ID) <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-white text-muted"><i class="fa fa-user"></i></span>
                                                            <input type="text" class="form-control" name="reg_legal_name" id="reg_legal_name" placeholder="e.g. Tendai Samuel Moyo" required autocomplete="name">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Create Account Password <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text bg-white text-muted"><i class="fa fa-lock"></i></span>
                                                            <input type="password" class="form-control" name="reg_password" id="reg_password" placeholder="Password (min 6 characters)" required minlength="6" autocomplete="new-password">
                                                            <button class="btn btn-outline-secondary" type="button" onclick="const p = document.getElementById('reg_password'); p.type = p.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('fa-eye'); this.querySelector('i').classList.toggle('fa-eye-slash');"><i class="fa fa-eye"></i></button>
                                                        </div>
                                                        <small class="text-muted" style="font-size:0.75rem;">Minimum 6 characters. Used to sign in and view application status.</small>
                                                    </div>

                                                    <div class="col-12 mt-3">
                                                        <button type="button" class="btn btn-wiz-next w-100 py-3 rounded-pill fw-bold" id="btn_submit_registration">
                                                            <span id="btn_submit_registration_text">Register &amp; Continue to Application</span> <i class="fa fa-arrow-right ms-2"></i>
                                                        </button>
                                                        <div id="reg_error_feedback" class="small text-danger mt-2 text-center" style="display:none;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Profile Inputs Grid (Unlocked after registration / authentication) -->
                                        <div id="stage1_profile_fields" class="row g-3" style="<?= ($isLoggedIn || $appId > 0) ? '' : 'display:none;'; ?>">
                                            <div class="col-12">
                                                <label class="form-label">Full Legal Name (as on National ID) <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control track-progress req-field" name="legal_name" id="field_legal_name" data-label="Full Legal Name" value="<?= htmlspecialchars($app->legal_name ?? $data['user']->name ?? ''); ?>" required placeholder="e.g. Tendai Samuel Moyo">
                                                <input type="hidden" name="preferred_name" id="field_preferred_name" value="<?= htmlspecialchars($app->preferred_name ?? ''); ?>">
                                            </div>

                                            <?php if (!$isLoggedIn): ?>
                                            <!-- Fallback hidden password inputs for test compatibility -->
                                            <input type="hidden" name="password" id="field_password" value="">
                                            <input type="hidden" name="password_confirmation" id="field_password_confirmation" value="">
                                            <?php endif; ?>

                                            <div class="col-md-3">
                                                <label class="form-label">Mobile Number (+263) <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control track-progress req-field" name="mobile_number" data-label="Mobile Number" value="<?= htmlspecialchars($app->mobile_number ?? ''); ?>" required placeholder="+263 77 123 4567">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">WhatsApp Number (if different)</label>
                                                <input type="tel" class="form-control track-progress" name="whatsapp_number" value="<?= htmlspecialchars($app->whatsapp_number ?? ''); ?>" placeholder="+263 77 123 4567">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Date of Birth</label>
                                                <input type="date" class="form-control track-progress" name="date_of_birth" value="<?= htmlspecialchars($app->date_of_birth ?? ''); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Gender</label>
                                                <select class="form-select track-progress" name="gender">
                                                    <option value="">Select Gender...</option>
                                                    <?php foreach ($data['genders'] as $g): ?>
                                                        <option value="<?= $g->iD; ?>" <?= ($app && (int)$app->gender === (int)$g->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($g->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Right to Work in Zimbabwe <span class="text-danger">*</span></label>
                                                <select class="form-select track-progress req-field" name="workrightstatus" data-label="Right to Work" required>
                                                    <?php foreach ($data['workRights'] as $wr): ?>
                                                        <option value="<?= $wr->iD; ?>" <?= ($app && (int)$app->workrightstatus === (int)$wr->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($wr->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Town / City of Residence <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control track-progress req-field" name="city" data-label="Town/City" value="<?= htmlspecialchars($app->city ?? 'Harare'); ?>" required placeholder="e.g. Harare, Bulawayo">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Suburb / Area</label>
                                                <input type="text" class="form-control track-progress" name="suburb" value="<?= htmlspecialchars($app->suburb ?? ''); ?>" placeholder="e.g. Avondale, Belmont">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Province <span class="text-danger">*</span></label>
                                                <select class="form-select track-progress req-field" name="zimprovince" data-label="Province" required>
                                                    <?php foreach ($data['provinces'] as $prv): ?>
                                                        <option value="<?= $prv->iD; ?>" <?= ($app && (int)$app->zimprovince === (int)$prv->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($prv->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">How did you hear about Tsigiro?</label>
                                                <input type="text" class="form-control track-progress" name="how_heard" value="<?= htmlspecialchars($app->how_heard ?? ''); ?>" placeholder="e.g. LinkedIn, University, Referral">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">If referred, by whom?</label>
                                                <input type="text" class="form-control track-progress" name="referred_by" value="<?= htmlspecialchars($app->referred_by ?? ''); ?>" placeholder="Referee name or organisation">
                                            </div>

                                            <div class="col-12 mt-3">
                                                <div class="p-3 bg-light rounded-3 border">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="has_disability_adjustment" id="disabilityCheck" value="1" <?= ($app && $app->has_disability_adjustment) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label fw-bold" for="disabilityCheck">
                                                            Do you have a health condition or disability for which you would like us to make workplace or recruitment adjustments?
                                                        </label>
                                                        <small class="form-text text-muted d-block">This does not affect your evaluation; it ensures we accommodate your working requirements.</small>
                                                    </div>
                                                    <div id="adjustmentDetailsDiv" class="mt-2 <?= ($app && $app->has_disability_adjustment) ? '' : 'd-none'; ?>">
                                                        <textarea class="form-control track-progress" name="adjustment_details" rows="2" placeholder="Describe any workplace or assessment adjustments that would assist you..."><?= htmlspecialchars($app->adjustment_details ?? ''); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="wizard-card-footer" id="stage1_card_footer" style="<?= ($isLoggedIn || $appId > 0) ? '' : 'display:none;'; ?>">
                                        <button type="button" class="btn-wiz-save btn-save-manual"><i class="fa fa-save"></i> Save Draft</button>
                                        <button type="button" class="btn-wiz-next btn-next-tab" data-next="step2">Continue to Stage 2 <i class="fa fa-arrow-right"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- =======================================================
                                 STAGE 2: EDUCATION & QUALIFICATIONS
                                 ======================================================= -->
                            <div class="tab-pane fade" id="step2" role="tabpanel">
                                <div class="wizard-card">
                                    <div class="wizard-card-header">
                                        <span class="stage-kicker">Stage 02</span>
                                        <h2>Education <?= $trackCode === 'apprentice' ? '&amp; Institutional Attachment (WRL)' : '&amp; Qualifications'; ?></h2>
                                        <p>Record your academic degrees, tertiary institutions, certifications, and attachment details.</p>
                                    </div>
                                    <div class="wizard-card-body">
                                        
                                        <?php if ($trackCode === 'apprentice'): ?>
                                        <!-- Apprentice Specific Academic & Attachment Branch -->
                                        <div class="wizard-section-block">
                                            <div class="wizard-section-block-title">
                                                <i class="fa fa-university"></i>
                                                <span>Apprentice Academic &amp; Attachment Profile</span>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Current Student / Graduate Status <span class="text-danger">*</span></label>
                                                    <select class="form-select track-progress req-field" name="apprenticestatus" data-label="Student/Graduate Status">
                                                        <?php foreach ($data['apprenticeStatuses'] as $ast): ?>
                                                            <option value="<?= $ast->iD; ?>" <?= ($appProfile && (int)$appProfile->apprenticestatus === (int)$ast->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($ast->name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">University / Polytechnic / College <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control track-progress req-field" name="institution_name" data-label="University/College" value="<?= htmlspecialchars($appProfile->institution_name ?? ''); ?>" placeholder="e.g. University of Zimbabwe, NUST, HIT, MSU, Harare Poly">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Programme / Degree Title <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control track-progress req-field" name="degree_programme" data-label="Programme Title" value="<?= htmlspecialchars($appProfile->degree_programme ?? ''); ?>" placeholder="e.g. BSc Computer Science, BCom Accounting">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Level / Year of Study</label>
                                                    <input type="text" class="form-control track-progress" name="study_level" value="<?= htmlspecialchars($appProfile->study_level ?? ''); ?>" placeholder="e.g. Part 3 / Year 3">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Student Reg Number</label>
                                                    <input type="text" class="form-control track-progress" name="student_reg_number" value="<?= htmlspecialchars($appProfile->student_reg_number ?? ''); ?>" placeholder="e.g. R214567X">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Expected Completion Date</label>
                                                    <input type="date" class="form-control track-progress" name="expected_completion_date" value="<?= htmlspecialchars($appProfile->expected_completion_date ?? ''); ?>">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Cumulative Average Grade / Class</label>
                                                    <input type="text" class="form-control track-progress" name="current_average_grade" value="<?= htmlspecialchars($appProfile->current_average_grade ?? ''); ?>" placeholder="e.g. 2.1 (Upper Second) / 74%">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check mt-4 pt-1">
                                                        <input class="form-check-input" type="checkbox" name="is_wrl_attachment" id="wrlCheck" value="1" <?= ($appProfile && $appProfile->is_wrl_attachment) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label fw-bold" for="wrlCheck">
                                                            This application is for WRL Industrial Attachment
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="wrlSection" class="mt-4 p-3 bg-white rounded-3 border <?= ($appProfile && $appProfile->is_wrl_attachment) ? '' : 'd-none'; ?>">
                                                <h6 class="fw-bold text-dark mb-3"><i class="fa fa-file-contract text-success me-1"></i> Institutional WRL Attachment Requirements</h6>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Earliest Attachment Start Date</label>
                                                        <input type="date" class="form-control form-control-sm track-progress" name="wrl_start_date" value="<?= htmlspecialchars($appProfile->wrl_start_date ?? ''); ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Attachment End Date</label>
                                                        <input type="date" class="form-control form-control-sm track-progress" name="wrl_end_date" value="<?= htmlspecialchars($appProfile->wrl_end_date ?? ''); ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Required Duration (Months)</label>
                                                        <select class="form-select form-select-sm track-progress" name="wrl_duration_months">
                                                            <option value="6" <?= ($appProfile && $appProfile->wrl_duration_months == 6) ? 'selected' : ''; ?>>6 Months</option>
                                                            <option value="8" <?= ($appProfile && $appProfile->wrl_duration_months == 8) ? 'selected' : ''; ?>>8 Months</option>
                                                            <option value="10" <?= ($appProfile && $appProfile->wrl_duration_months == 10) ? 'selected' : ''; ?>>10 Months</option>
                                                            <option value="12" <?= (!$appProfile || $appProfile->wrl_duration_months == 12) ? 'selected' : ''; ?>>12 Months</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Attachment Coordinator Name</label>
                                                        <input type="text" class="form-control form-control-sm track-progress" name="wrl_coordinator_name" value="<?= htmlspecialchars($appProfile->wrl_coordinator_name ?? ''); ?>" placeholder="Lecturer / Coordinator">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Coordinator Email</label>
                                                        <input type="email" class="form-control form-control-sm track-progress email-field" name="wrl_coordinator_email" value="<?= htmlspecialchars($appProfile->wrl_coordinator_email ?? ''); ?>">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Coordinator Phone</label>
                                                        <input type="tel" class="form-control form-control-sm track-progress" name="wrl_coordinator_phone" value="<?= htmlspecialchars($appProfile->wrl_coordinator_phone ?? ''); ?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Upload Proof of Registration / WRL Letter (PDF/JPG, Max 5MB)</label>
                                                        <input type="file" class="form-control form-control-sm" name="proof_of_registration_doc">
                                                        <?php if ($appProfile && $appProfile->proof_of_registration_doc): ?>
                                                            <small class="text-success d-block mt-1"><i class="fa fa-check-circle"></i> On file: <?= htmlspecialchars($appProfile->proof_of_registration_doc); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Upload Academic Transcript / Results (PDF/JPG, Max 5MB)</label>
                                                        <input type="file" class="form-control form-control-sm" name="transcript_doc">
                                                        <?php if ($appProfile && $appProfile->transcript_doc): ?>
                                                            <small class="text-success d-block mt-1"><i class="fa fa-check-circle"></i> On file: <?= htmlspecialchars($appProfile->transcript_doc); ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Qualifications Repeatable Section -->
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">Qualifications &amp; Certifications</h5>
                                                    <small class="text-muted">Degrees, diplomas, and professional credentials (e.g. ACCA, CIS, PMP, CCNA).</small>
                                                </div>
                                                <button type="button" class="btn-add-item" id="btn_add_qual">
                                                    <i class="fa fa-plus"></i> Add Qualification
                                                </button>
                                            </div>

                                            <div id="qualifications_container">
                                                <?php if (empty($qualifications)): ?>
                                                    <div class="repeatable-item-card qual-row">
                                                        <div class="row g-2 align-items-center">
                                                            <div class="col-md-4">
                                                                <label class="form-label small">Qualification Title</label>
                                                                <input type="text" class="form-control form-control-sm track-progress" name="qual_title[]" placeholder="e.g. BSc Computer Science, ACCA">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small">Type / Level</label>
                                                                <select class="form-select form-select-sm track-progress" name="qual_type[]">
                                                                    <option value="">Level / Type...</option>
                                                                    <?php foreach ($data['qualificationTypes'] as $qt): ?>
                                                                        <option value="<?= $qt->iD; ?>"><?= htmlspecialchars($qt->name); ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small">Institution / Body</label>
                                                                <input type="text" class="form-control form-control-sm track-progress" name="qual_inst[]" placeholder="Institution / Exam Board">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <label class="form-label small">Date</label>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <input type="date" class="form-control form-control-sm track-progress" name="qual_date[]">
                                                                    <button type="button" class="btn-remove-item btn-remove-row" title="Remove"><i class="fa fa-trash-alt"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <?php foreach ($qualifications as $q): ?>
                                                        <div class="repeatable-item-card qual-row">
                                                            <div class="row g-2 align-items-center">
                                                                <div class="col-md-4">
                                                                    <label class="form-label small">Qualification Title</label>
                                                                    <input type="text" class="form-control form-control-sm track-progress" name="qual_title[]" value="<?= htmlspecialchars($q->title); ?>" placeholder="Qualification Title">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label small">Type / Level</label>
                                                                    <select class="form-select form-select-sm track-progress" name="qual_type[]">
                                                                        <option value="">Level / Type...</option>
                                                                        <?php foreach ($data['qualificationTypes'] as $qt): ?>
                                                                            <option value="<?= $qt->iD; ?>" <?= ((int)$q->qualificationtype === (int)$qt->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($qt->name); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label small">Institution / Body</label>
                                                                    <input type="text" class="form-control form-control-sm track-progress" name="qual_inst[]" value="<?= htmlspecialchars($q->institution_name ?? ''); ?>" placeholder="Institution / Exam Board">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <label class="form-label small">Date</label>
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <input type="date" class="form-control form-control-sm track-progress" name="qual_date[]" value="<?= htmlspecialchars($q->date_obtained ?? ''); ?>">
                                                                        <button type="button" class="btn-remove-item btn-remove-row" title="Remove"><i class="fa fa-trash-alt"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="wizard-card-footer">
                                        <button type="button" class="btn-wiz-prev btn-prev-tab" data-prev="step1"><i class="fa fa-arrow-left"></i> Previous</button>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn-wiz-save btn-save-manual"><i class="fa fa-save"></i> Save Draft</button>
                                            <button type="button" class="btn-wiz-next btn-next-tab" data-next="step3">Continue to Stage 3 <i class="fa fa-arrow-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- =======================================================
                                 STAGE 3: FUNCTION & SKILLS MATRIX
                                 ======================================================= -->
                            <div class="tab-pane fade" id="step3" role="tabpanel">
                                <div class="wizard-card">
                                    <div class="wizard-card-header">
                                        <span class="stage-kicker">Stage 03</span>
                                        <h2>Primary Service Line &amp; Anchored Skills Matrix</h2>
                                        <p>Select your core functional practice and self-rate your competency across essential client deliverables.</p>
                                    </div>
                                    <div class="wizard-card-body">
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-7">
                                                <label class="form-label fw-bold">Primary Service Function <span class="text-danger">*</span></label>
                                                <select class="form-select form-select-lg track-progress req-field" name="primaryfunction" id="primaryfunction_select" data-label="Primary Service Function" required>
                                                    <?php foreach ($data['serviceFunctions'] as $sf): ?>
                                                        <option value="<?= $sf->iD; ?>" <?= ($app && (int)$app->primaryfunction === (int)$sf->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($sf->name); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <small class="form-text text-muted">Changing this function automatically updates the competency skill matrix below.</small>
                                            </div>
                                        </div>

                                        <!-- Anchored Scale Explainer -->
                                        <div class="mb-4">
                                            <label class="form-label fw-bold mb-2">Standard 1–5 Anchored Competency Rubric</label>
                                            <div class="scale-explainer-grid">
                                                <div class="scale-card scale-card-1">
                                                    <span class="scale-num">1</span>
                                                    <span class="scale-title">Aware</span>
                                                    <span class="scale-desc">Knows concepts; requires active guidance</span>
                                                </div>
                                                <div class="scale-card scale-card-2">
                                                    <span class="scale-num">2</span>
                                                    <span class="scale-title">Assisted</span>
                                                    <span class="scale-desc">Delivers routine tasks with supervisor QA</span>
                                                </div>
                                                <div class="scale-card scale-card-3">
                                                    <span class="scale-num">3</span>
                                                    <span class="scale-title">Independent</span>
                                                    <span class="scale-desc">Delivers end-to-end reliably on own</span>
                                                </div>
                                                <div class="scale-card scale-card-4">
                                                    <span class="scale-num">4</span>
                                                    <span class="scale-title">Expert</span>
                                                    <span class="scale-desc">Solves complex edge cases &amp; audits</span>
                                                </div>
                                                <div class="scale-card scale-card-5">
                                                    <span class="scale-num">5</span>
                                                    <span class="scale-title">Master / QA</span>
                                                    <span class="scale-desc">Can train others &amp; sign off deliverables</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Dynamic Skills Matrix Container -->
                                        <div class="mb-4">
                                            <label class="form-label fw-bold mb-2">Rate Your Competency on Specific Deliverables:</label>
                                            <div id="skills_matrix_area" class="skills-table">
                                                <div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin me-2"></i> Loading function skills...</div>
                                            </div>
                                        </div>

                                        <!-- Deliverable Evidence Narrative -->
                                        <div class="scenario-card">
                                            <div class="scenario-header">
                                                <div class="scenario-icon"><i class="fa fa-award"></i></div>
                                                <div>
                                                    <div class="scenario-title">Section 4.4 Deliverable Evidence (Highest Signal Item) <span class="text-danger">*</span></div>
                                                    <p class="scenario-brief">Describe ONE concrete piece of work in your primary function that you delivered end to end. What was the task, what exactly did you do, and what was the verifiable result? (Max 200 words)</p>
                                                </div>
                                            </div>
                                            <textarea class="form-control track-progress req-field" name="primary_function_evidence" data-label="Section 4.4 Deliverable Evidence" rows="4" maxlength="1500" required placeholder="Outline context, your specific contribution, tools used, and measurable outcome..."><?= htmlspecialchars($judgement->primary_function_evidence ?? ''); ?></textarea>
                                        </div>

                                    </div>
                                    <div class="wizard-card-footer">
                                        <button type="button" class="btn-wiz-prev btn-prev-tab" data-prev="step2"><i class="fa fa-arrow-left"></i> Previous</button>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn-wiz-save btn-save-manual"><i class="fa fa-save"></i> Save Draft</button>
                                            <button type="button" class="btn-wiz-next btn-next-tab" data-next="step4">Continue to Stage 4 <i class="fa fa-arrow-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- =======================================================
                                 STAGE 4: EXPERIENCE & COMMERCIAL TERMS
                                 ======================================================= -->
                            <div class="tab-pane fade" id="step4" role="tabpanel">
                                <div class="wizard-card">
                                    <div class="wizard-card-header">
                                        <span class="stage-kicker">Stage 04</span>
                                        <h2>Experience, Engagements &amp; Commercial Terms</h2>
                                        <p>Document your career timeline, key client deliverables, references, and availability parameters.</p>
                                    </div>
                                    <div class="wizard-card-body">
                                        
                                        <?php if ($trackCode === 'associate'): ?>
                                        <!-- Associate Specific Commercial & Seniority Profile -->
                                        <div class="wizard-section-block">
                                            <div class="wizard-section-block-title">
                                                <i class="fa fa-briefcase"></i>
                                                <span>Associate Practice &amp; Commercial Availability</span>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label">Current Employment Status <span class="text-danger">*</span></label>
                                                    <select class="form-select track-progress req-field" name="employmentstatus" data-label="Employment Status">
                                                        <?php foreach ($data['employmentStatuses'] as $es): ?>
                                                            <option value="<?= $es->iD; ?>" <?= ($assocProfile && (int)$assocProfile->employmentstatus === (int)$es->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($es->name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Years of Professional Experience</label>
                                                    <select class="form-select track-progress" name="years_experience">
                                                        <option value="1-2" <?= ($assocProfile && $assocProfile->years_experience === '1-2') ? 'selected' : ''; ?>>1–2 Years</option>
                                                        <option value="3-5" <?= ($assocProfile && $assocProfile->years_experience === '3-5') ? 'selected' : ''; ?>>3–5 Years</option>
                                                        <option value="6-10" <?= (!$assocProfile || $assocProfile->years_experience === '6-10') ? 'selected' : ''; ?>>6–10 Years</option>
                                                        <option value="11-15" <?= ($assocProfile && $assocProfile->years_experience === '11-15') ? 'selected' : ''; ?>>11–15 Years</option>
                                                        <option value="15+" <?= ($assocProfile && $assocProfile->years_experience === '15+') ? 'selected' : ''; ?>>15+ Years</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Donor-Funded / NGO Experience</label>
                                                    <select class="form-select track-progress" name="donor_experience_years">
                                                        <option value="none">None yet</option>
                                                        <option value="1-2">1–2 Years</option>
                                                        <option value="3-5" selected>3–5 Years</option>
                                                        <option value="6+">6+ Years</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Indicative Day Rate Expectation (USD) <span class="text-danger">*</span></label>
                                                    <input type="number" step="10" class="form-control track-progress req-field" name="day_rate_expectation" data-label="Day Rate Expectation" value="<?= htmlspecialchars($assocProfile->day_rate_expectation ?? '150'); ?>" required placeholder="e.g. 150">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Monthly Capacity Commitment</label>
                                                    <select class="form-select track-progress" name="capacity_days_per_month">
                                                        <option value="1-3">1–3 Days / Month</option>
                                                        <option value="4-7" selected>4–7 Days / Month</option>
                                                        <option value="8-12">8–12 Days / Month</option>
                                                        <option value="flexible">Varies / On-Demand</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Invoicing Entity Type</label>
                                                    <select class="form-select track-progress" name="invoiceentitytype">
                                                        <?php foreach ($data['invoiceEntityTypes'] as $iet): ?>
                                                            <option value="<?= $iet->iD; ?>" <?= ($assocProfile && (int)$assocProfile->invoiceentitytype === (int)$iet->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($iet->name); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check mt-2">
                                                        <input class="form-check-input" type="checkbox" name="has_tax_clearance_itf263" id="itfCheck" value="1" <?= ($assocProfile && $assocProfile->has_tax_clearance_itf263) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label fw-bold" for="itfCheck">
                                                            I hold a valid ZIMRA Tax Clearance Certificate (ITF263)
                                                        </label>
                                                    </div>
                                                    <input type="text" class="form-control form-control-sm mt-2 track-progress" name="zimra_bp_number" value="<?= htmlspecialchars($assocProfile->zimra_bp_number ?? ''); ?>" placeholder="ZIMRA Business Partner (BP) Number">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small">Upload ITF263 Tax Clearance (Optional, PDF/JPG)</label>
                                                    <input type="file" class="form-control form-control-sm" name="tax_clearance_doc">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Conflict of Interest Disclosure</label>
                                                    <textarea class="form-control track-progress" name="conflict_of_interest" rows="2" placeholder="List any organisations you are currently employed by, contracted to, or sitting on the board of that may create an engagement conflict..."><?= htmlspecialchars($assocProfile->conflict_of_interest ?? ''); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Work History Section -->
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">Employment &amp; Project History</h5>
                                                    <small class="text-muted">Past roles, consultancies, or internships and verifiable achievements.</small>
                                                </div>
                                                <button type="button" class="btn-add-item" id="btn_add_work">
                                                    <i class="fa fa-plus"></i> Add Work Entry
                                                </button>
                                            </div>
                                            <div id="work_history_container">
                                                <?php if (empty($workHistories)): ?>
                                                    <div class="repeatable-item-card work-row">
                                                        <div class="row g-2">
                                                            <div class="col-md-5">
                                                                <label class="form-label small">Organisation Name</label>
                                                                <input type="text" class="form-control form-control-sm track-progress" name="work_org[]" placeholder="Organisation / Enterprise Name">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label small">Position / Title</label>
                                                                <input type="text" class="form-control form-control-sm track-progress" name="work_title[]" placeholder="Position Title">
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label small">Engagement Basis</label>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <select class="form-select form-select-sm track-progress" name="work_basis[]">
                                                                        <?php foreach ($data['engagementBases'] as $eb): ?>
                                                                            <option value="<?= $eb->iD; ?>"><?= htmlspecialchars($eb->name); ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                    <button type="button" class="btn-remove-item btn-remove-row" title="Remove"><i class="fa fa-trash-alt"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 mt-2">
                                                                <label class="form-label small">Key Deliverables &amp; Achievements</label>
                                                                <textarea class="form-control form-control-sm track-progress" name="work_deliverables[]" rows="2" placeholder="Key deliverables, responsibilities, and measurable outcomes..."></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <?php foreach ($workHistories as $w): ?>
                                                        <div class="repeatable-item-card work-row">
                                                            <div class="row g-2">
                                                                <div class="col-md-5">
                                                                    <label class="form-label small">Organisation Name</label>
                                                                    <input type="text" class="form-control form-control-sm track-progress" name="work_org[]" value="<?= htmlspecialchars($w->organization_name); ?>" placeholder="Organisation Name">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="form-label small">Position / Title</label>
                                                                    <input type="text" class="form-control form-control-sm track-progress" name="work_title[]" value="<?= htmlspecialchars($w->position_title); ?>" placeholder="Position Title">
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label class="form-label small">Engagement Basis</label>
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <select class="form-select form-select-sm track-progress" name="work_basis[]">
                                                                            <?php foreach ($data['engagementBases'] as $eb): ?>
                                                                                <option value="<?= $eb->iD; ?>" <?= ((int)$w->engagementbasis === (int)$eb->iD) ? 'selected' : ''; ?>><?= htmlspecialchars($eb->name); ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                        <button type="button" class="btn-remove-item btn-remove-row" title="Remove"><i class="fa fa-trash-alt"></i></button>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 mt-2">
                                                                    <label class="form-label small">Key Deliverables</label>
                                                                    <textarea class="form-control form-control-sm track-progress" name="work_deliverables[]" rows="2" placeholder="Key deliverables..."><?= htmlspecialchars($w->key_deliverables ?? ''); ?></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Referees Section -->
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h5 class="fw-bold mb-0" style="font-size: 1.05rem;">Referees (Academic &amp; Professional)</h5>
                                                    <small class="text-muted">Individuals who can attest to your technical ability, delivery ethics, and reliability.</small>
                                                </div>
                                                <button type="button" class="btn-add-item" id="btn_add_ref">
                                                    <i class="fa fa-plus"></i> Add Referee
                                                </button>
                                            </div>
                                            <div id="referees_container">
                                                <?php if (empty($referees)): ?>
                                                    <div class="repeatable-item-card ref-row">
                                                        <div class="row g-2 align-items-center">
                                                            <div class="col-md-3"><label class="form-label small">Full Name</label><input type="text" class="form-control form-control-sm track-progress" name="referee_name[]" placeholder="Referee Full Name"></div>
                                                            <div class="col-md-3"><label class="form-label small">Organisation</label><input type="text" class="form-control form-control-sm track-progress" name="ref_org[]" placeholder="Organisation / Institution"></div>
                                                            <div class="col-md-2"><label class="form-label small">Position</label><input type="text" class="form-control form-control-sm track-progress" name="ref_pos[]" placeholder="Position Title"></div>
                                                            <div class="col-md-2"><label class="form-label small">Email</label><input type="text" class="form-control form-control-sm track-progress email-field" name="ref_email[]" placeholder="Email Address"></div>
                                                            <div class="col-md-2"><label class="form-label small">Phone</label>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <input type="tel" class="form-control form-control-sm track-progress" name="ref_phone[]" placeholder="Phone Number">
                                                                    <button type="button" class="btn-remove-item btn-remove-row" title="Remove"><i class="fa fa-trash-alt"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <?php foreach ($referees as $rf): ?>
                                                        <div class="repeatable-item-card ref-row">
                                                            <div class="row g-2 align-items-center">
                                                                <div class="col-md-3"><label class="form-label small">Full Name</label><input type="text" class="form-control form-control-sm track-progress" name="referee_name[]" value="<?= htmlspecialchars($rf->referee_name); ?>" placeholder="Referee Name"></div>
                                                                <div class="col-md-3"><label class="form-label small">Organisation</label><input type="text" class="form-control form-control-sm track-progress" name="ref_org[]" value="<?= htmlspecialchars($rf->organization); ?>" placeholder="Organisation"></div>
                                                                <div class="col-md-2"><label class="form-label small">Position</label><input type="text" class="form-control form-control-sm track-progress" name="ref_pos[]" value="<?= htmlspecialchars($rf->position); ?>" placeholder="Position"></div>
                                                                <div class="col-md-2"><label class="form-label small">Email</label><input type="text" class="form-control form-control-sm track-progress email-field" name="ref_email[]" value="<?= htmlspecialchars($rf->email); ?>" placeholder="Email"></div>
                                                                <div class="col-md-2"><label class="form-label small">Phone</label>
                                                                    <div class="d-flex align-items-center gap-1">
                                                                        <input type="tel" class="form-control form-control-sm track-progress" name="ref_phone[]" value="<?= htmlspecialchars($rf->phone); ?>" placeholder="Phone">
                                                                        <button type="button" class="btn-remove-item btn-remove-row" title="Remove"><i class="fa fa-trash-alt"></i></button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="wizard-card-footer">
                                        <button type="button" class="btn-wiz-prev btn-prev-tab" data-prev="step3"><i class="fa fa-arrow-left"></i> Previous</button>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn-wiz-save btn-save-manual"><i class="fa fa-save"></i> Save Draft</button>
                                            <button type="button" class="btn-wiz-next btn-next-tab" data-next="step5">Continue to Stage 5 <i class="fa fa-arrow-right"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- =======================================================
                                 STAGE 5: JUDGEMENT SCENARIOS & CONSENT
                                 ======================================================= -->
                            <div class="tab-pane fade" id="step5" role="tabpanel">
                                <div class="wizard-card">
                                    <div class="wizard-card-header">
                                        <span class="stage-kicker">Stage 05</span>
                                        <h2>Practical Case Scenarios, CV &amp; Declarations</h2>
                                        <p>Situational problem-solving scenarios, curriculum vitae upload, and regulatory compliance consent.</p>
                                    </div>
                                    <div class="wizard-card-body">
                                        
                                        <!-- Scenario 9.1 -->
                                        <div class="scenario-card">
                                            <div class="scenario-header">
                                                <div class="scenario-icon"><i class="fa fa-compass"></i></div>
                                                <div>
                                                    <div class="scenario-title">9.1 Motivation &amp; Roster Model Fit <span class="text-danger">*</span></div>
                                                    <p class="scenario-brief">Why Trainit / Tsigiro, and why a roster shared-services model rather than a conventional single-employer job? (Max 150 words)</p>
                                                </div>
                                            </div>
                                            <textarea class="form-control track-progress req-field" name="motivation_narrative" data-label="Section 9.1 Motivation" rows="3" maxlength="1000" required placeholder="Explain what attracts you to the roster model and flexible client delivery..."><?= htmlspecialchars($judgement->motivation_narrative ?? ''); ?></textarea>
                                        </div>

                                        <!-- Scenario 9.2 -->
                                        <div class="scenario-card">
                                            <div class="scenario-header">
                                                <div class="scenario-icon"><i class="fa fa-tasks"></i></div>
                                                <div>
                                                    <div class="scenario-title">9.2 Workload Balance Scenario <span class="text-danger">*</span></div>
                                                    <p class="scenario-brief">In the Shared tier, talent may support up to five client organisations concurrently (at approx. 20% level of effort each). Describe how you organize a normal week so that all five clients remain confident their work is on track.</p>
                                                </div>
                                            </div>
                                            <textarea class="form-control track-progress req-field" name="shared_client_management_plan" data-label="Section 9.2 Workload Balance" rows="3" maxlength="1200" required placeholder="Describe your scheduling, communication updates, and priority management..."><?= htmlspecialchars($judgement->shared_client_management_plan ?? ''); ?></textarea>
                                        </div>

                                        <!-- Scenario 9.4 -->
                                        <div class="scenario-card">
                                            <div class="scenario-header">
                                                <div class="scenario-icon"><i class="fa fa-clock"></i></div>
                                                <div>
                                                    <div class="scenario-title">9.4 Deadline &amp; Missing Input Scenario <span class="text-danger">*</span></div>
                                                    <p class="scenario-brief">It is 16:45 on Friday. A client emails requesting an urgent report by 08:00 Monday. Completing it requires data only their finance officer can provide, and that officer is on leave until Tuesday. What do you do, in what order?</p>
                                                </div>
                                            </div>
                                            <textarea class="form-control track-progress req-field" name="urgent_friday_deadline_dilemma" data-label="Section 9.4 Urgent Deadline Scenario" rows="3" maxlength="1000" required placeholder="Outline your step-by-step communication and mitigation actions..."><?= htmlspecialchars($judgement->urgent_friday_deadline_dilemma ?? ''); ?></textarea>
                                        </div>

                                        <?php if ($trackCode === 'associate'): ?>
                                        <!-- Scenario 9.5 (Associate) -->
                                        <div class="scenario-card">
                                            <div class="scenario-header">
                                                <div class="scenario-icon"><i class="fa fa-check-double"></i></div>
                                                <div>
                                                    <div class="scenario-title">9.5 Quality Assurance Scenario <span class="text-danger">*</span></div>
                                                    <p class="scenario-brief">You are reviewing work produced by an apprentice you have never met in person, for a client you do not see weekly. How do you assure quality without becoming a delivery bottleneck?</p>
                                                </div>
                                            </div>
                                            <textarea class="form-control track-progress" name="associate_apprentice_qa_methodology" rows="3" maxlength="1000" placeholder="Explain your review checkpoints, rubric, and feedback cadence..."><?= htmlspecialchars($judgement->associate_apprentice_qa_methodology ?? ''); ?></textarea>
                                        </div>
                                        <?php endif; ?>

                                        <!-- CV / Resume Upload Box -->
                                        <div class="scenario-card">
                                            <div class="scenario-header">
                                                <div class="scenario-icon"><i class="fa fa-file-pdf"></i></div>
                                                <div>
                                                    <div class="scenario-title">Curriculum Vitae (CV) / Comprehensive Resume <span class="text-danger">*</span></div>
                                                    <p class="scenario-brief">Upload your up-to-date CV detailing education, project experience, and technical competencies.</p>
                                                </div>
                                            </div>
                                            <div class="upload-dropzone">
                                                <i class="fa fa-cloud-upload-alt fs-2 text-success mb-2"></i>
                                                <div class="fw-bold mb-1">Choose your CV document</div>
                                                <div class="small text-muted mb-3">Accepted formats: PDF or Word (.pdf, .doc, .docx) &middot; Max 5MB</div>
                                                <div class="d-flex justify-content-center">
                                                    <input type="file" class="form-control form-control-sm track-progress req-field" style="max-width: 380px;" name="cv_doc" id="cv_doc_input" data-label="Curriculum Vitae (CV)" accept=".pdf,.doc,.docx" required>
                                                </div>
                                                <?php
                                                    $existingDocs = $app ? $app->documents() : [];
                                                    $existingCv = null;
                                                    foreach ($existingDocs as $d) {
                                                        $dt = $d->documenttype();
                                                        if ($dt && $dt->code === 'CV_RESUME') {
                                                            $existingCv = $d;
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <?php if ($existingCv): ?>
                                                    <div class="mt-3 p-2 bg-white rounded-pill border d-inline-flex align-items-center gap-2 small text-success px-3">
                                                        <i class="fa fa-check-circle"></i> Current CV on file: <strong><?= htmlspecialchars($existingCv->original_name ?: basename($existingCv->file_path)); ?></strong>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Declarations & Consent -->
                                        <div class="wizard-section-block mt-4">
                                            <div class="wizard-section-block-title">
                                                <i class="fa fa-shield-alt"></i>
                                                <span>Declarations, Privacy &amp; Electronic Signature</span>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="consent_accuracy" required checked>
                                                <label class="form-check-label small" for="consent_accuracy">
                                                    I confirm that all statements, credentials, and achievements submitted in this application are true, accurate, and complete. I acknowledge that misrepresentation will result in disqualification or termination from the roster.
                                                </label>
                                            </div>

                                            <div class="form-check mb-4">
                                                <input class="form-check-input" type="checkbox" id="consent_privacy" required checked>
                                                <label class="form-check-label small" for="consent_privacy">
                                                    <strong>Data Protection Consent:</strong> I authorize Trainit Technologies (Pvt) Ltd t/a Tsigiro to process my submitted data for background verification, scoring, roster holding, and client placement in full compliance with Zimbabwe's <em>Cyber and Data Protection Act [Chapter 12:07]</em>.
                                                </label>
                                            </div>

                                            <div class="row g-3 align-items-center pt-3 border-top">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-bold">Full Legal Name (Electronic Signature) <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control track-progress req-field" name="e_signature" data-label="Electronic Signature" value="<?= htmlspecialchars($app->e_signature ?? $app->legal_name ?? $data['user']->name ?? ''); ?>" required placeholder="Type your full legal name as digital signature">
                                                </div>
                                                <div class="col-md-5">
                                                    <span class="small text-muted d-block mt-md-4"><i class="fa fa-clock me-1 text-success"></i> Submission timestamp will be recorded automatically upon filing.</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="wizard-card-footer">
                                        <button type="button" class="btn-wiz-prev btn-prev-tab" data-prev="step4"><i class="fa fa-arrow-left"></i> Previous</button>
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn-wiz-save btn-save-manual"><i class="fa fa-save"></i> Save Draft</button>
                                            <button type="button" class="btn-wiz-submit" id="btn_final_submit"><i class="fa fa-paper-plane"></i> Submit Application</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const siteUrl = '<?= $siteConfig->siteUrl; ?>';
    const trackCode = '<?= htmlspecialchars($trackCode); ?>';
    const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false'; ?>;
    const initialStep = <?= (int)($data['furthestStep'] ?? 1); ?>;

    const form = document.getElementById('roster_wizard_form');
    const alertBox = document.getElementById('form_alert');
    const primaryFuncSelect = document.getElementById('primaryfunction_select');
    const skillsArea = document.getElementById('skills_matrix_area');
    const existingSkills = <?= json_encode($existingSkillsMap); ?>;

    const autosaveBadge = document.getElementById('autosave_badge');
    const progressBar = document.getElementById('form_progress_bar');
    const percentLabel = document.getElementById('progress_percent_label');
    const countsLabel = document.getElementById('progress_counts_label');

    let autoSaveTimer = null;
    let isSaving = false;

    // --- 1. Email Intake Gate Handler ---
    const initialAppId = <?= (int)$appId; ?>;
    const stage1ProfileFields = document.getElementById('stage1_profile_fields');
    const stage1CardFooter = document.getElementById('stage1_card_footer');
    const fieldEmail = document.getElementById('field_email');
    const btnCheckEmail = document.getElementById('btn_check_email');
    const btnCheckText = document.getElementById('btn_check_email_text');
    const emailFeedback = document.getElementById('email_check_feedback');
    const boxAlreadyApplied = document.getElementById('box_already_applied');
    const boxResuming = document.getElementById('box_resuming');
    const boxExistingNewTrack = document.getElementById('box_existing_new_track');
    const boxNewCandidate = document.getElementById('box_new_candidate');

    function hideAllChallengeBoxes() {
        if (boxAlreadyApplied) boxAlreadyApplied.style.display = 'none';
        if (boxResuming) boxResuming.style.display = 'none';
        if (boxExistingNewTrack) boxExistingNewTrack.style.display = 'none';
        if (boxNewCandidate) boxNewCandidate.style.display = 'none';
        if (emailFeedback) emailFeedback.style.display = 'none';
    }

    function checkEmail() {
        if (!fieldEmail) return;
        const email = fieldEmail.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email || !emailRegex.test(email)) {
            emailFeedback.style.display = 'block';
            emailFeedback.className = 'small text-danger';
            emailFeedback.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Please enter a valid email address.';
            fieldEmail.focus();
            return;
        }

        hideAllChallengeBoxes();
        btnCheckEmail.disabled = true;
        btnCheckText.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Checking...';

        const formData = new FormData();
        formData.append('email', email);
        formData.append('track', trackCode);

        fetch(siteUrl + '/opportunities/apply/check-email', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btnCheckEmail.disabled = false;
            btnCheckText.textContent = 'Continue';

            if (data.status !== 1) {
                emailFeedback.style.display = 'block';
                emailFeedback.className = 'small text-danger';
                emailFeedback.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> ' + (data.msg || 'Error checking email.');
                return;
            }

            if (data.account_state === 'already_applied') {
                boxAlreadyApplied.style.display = 'block';
                document.getElementById('msg_already_applied').textContent = data.message;
                const pwdInput = document.getElementById('pwd_already_applied');
                if (pwdInput) pwdInput.focus();
            } else if (data.account_state === 'resuming') {
                boxResuming.style.display = 'block';
                document.getElementById('msg_resuming').textContent = data.message;
                const pwdInput = document.getElementById('pwd_resuming');
                if (pwdInput) pwdInput.focus();
            } else if (data.account_state === 'existing_user_new_track') {
                boxExistingNewTrack.style.display = 'block';
                document.getElementById('msg_existing_new_track').textContent = data.message;
                const pwdInput = document.getElementById('pwd_existing_new_track');
                if (pwdInput) pwdInput.focus();
            } else if (data.account_state === 'new') {
                boxNewCandidate.style.display = 'block';
                document.getElementById('new_candidate_email_display').textContent = email;
                const regEmailDisplay = document.getElementById('reg_email_display');
                if (regEmailDisplay) regEmailDisplay.value = email;
                btnCheckEmail.innerHTML = '<i class="fa fa-check text-success me-1"></i> Verified';
                btnCheckEmail.classList.remove('btn-wiz-next');
                btnCheckEmail.classList.add('btn-outline-success');
                fieldEmail.readOnly = true;

                if (stage1ProfileFields) stage1ProfileFields.style.display = 'none';
                if (stage1CardFooter) stage1CardFooter.style.display = 'none';

                const regLegalName = document.getElementById('reg_legal_name');
                if (regLegalName) {
                    regLegalName.focus();
                    regLegalName.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        })
        .catch(err => {
            btnCheckEmail.disabled = false;
            btnCheckText.textContent = 'Continue';
            emailFeedback.style.display = 'block';
            emailFeedback.className = 'small text-danger';
            emailFeedback.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Network error checking email. Please try again.';
        });
    }

    if (btnCheckEmail) {
        btnCheckEmail.addEventListener('click', checkEmail);
        fieldEmail.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkEmail();
            }
        });
    }

    document.getElementById('btn_change_email')?.addEventListener('click', function () {
        hideAllChallengeBoxes();
        btnCheckEmail.innerHTML = '<span id="btn_check_email_text">Continue</span> <i class="fa fa-arrow-right ms-1"></i>';
        btnCheckEmail.classList.remove('btn-outline-success');
        btnCheckEmail.classList.add('btn-wiz-next');
        btnCheckEmail.disabled = false;
        fieldEmail.readOnly = false;
        fieldEmail.focus();

        if (!isLoggedIn && !initialAppId) {
            if (stage1ProfileFields) stage1ProfileFields.style.display = 'none';
            if (stage1CardFooter) stage1CardFooter.style.display = 'none';
        }
        updateFormProgress();
    });

    // --- 1.1 Phase 2 Registration Submission ---
    function submitRegistration() {
        const regLegalName = document.getElementById('reg_legal_name');
        const regPassword = document.getElementById('reg_password');
        const regError = document.getElementById('reg_error_feedback');
        const btnReg = document.getElementById('btn_submit_registration');
        const btnRegText = document.getElementById('btn_submit_registration_text');

        const email = fieldEmail.value.trim();
        const legalName = regLegalName ? regLegalName.value.trim() : '';
        const password = regPassword ? regPassword.value : '';

        if (!legalName || legalName.length < 2) {
            regError.style.display = 'block';
            regError.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Please enter your full legal name as it appears on your National ID.';
            if (regLegalName) regLegalName.focus();
            return;
        }

        if (!password || password.length < 6) {
            regError.style.display = 'block';
            regError.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Password must be at least 6 characters long.';
            if (regPassword) regPassword.focus();
            return;
        }

        regError.style.display = 'none';
        btnReg.disabled = true;
        const originalText = btnRegText.textContent;
        btnRegText.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Registering Account...';

        const formData = new FormData();
        formData.append('email', email);
        formData.append('legal_name', legalName);
        formData.append('password', password);
        formData.append('track', trackCode);

        fetch(siteUrl + '/opportunities/apply/register-candidate', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 1) {
                btnRegText.innerHTML = '<i class="fa fa-check me-2"></i> Account Registered! Loading application...';
                btnReg.classList.remove('btn-wiz-next');
                btnReg.classList.add('btn-success');
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 350);
            } else {
                btnReg.disabled = false;
                btnRegText.textContent = originalText;
                regError.style.display = 'block';
                regError.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> ' + (data.msg || 'Registration failed.');
            }
        })
        .catch(err => {
            btnReg.disabled = false;
            btnRegText.textContent = originalText;
            regError.style.display = 'block';
            regError.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Network error registering account. Please try again.';
        });
    }

    document.getElementById('btn_submit_registration')?.addEventListener('click', submitRegistration);
    document.getElementById('reg_legal_name')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('reg_password')?.focus();
        }
    });
    document.getElementById('reg_password')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitRegistration();
        }
    });

    // Auto-sync preferred name from legal name in Stage 1
    const fieldLegalName = document.getElementById('field_legal_name');
    const fieldPreferredName = document.getElementById('field_preferred_name');
    if (fieldLegalName && fieldPreferredName) {
        fieldLegalName.addEventListener('input', function() {
            const first = this.value.trim().split(/\s+/)[0] || '';
            fieldPreferredName.value = first;
        });
    }

    // Guard stepper navigation tabs if candidate has not verified email or registered
    document.querySelectorAll('.stepper-tab-btn').forEach(tabBtn => {
        tabBtn.addEventListener('click', function (e) {
            const isEmailVerified = isLoggedIn || initialAppId > 0;
            if (!isEmailVerified) {
                e.preventDefault();
                e.stopPropagation();
                if (emailFeedback) {
                    emailFeedback.style.display = 'block';
                    emailFeedback.className = 'small text-danger';
                    emailFeedback.innerHTML = '<i class="fa fa-info-circle me-1"></i> Please complete your email check and account registration first.';
                }
                if (fieldEmail) {
                    fieldEmail.focus();
                    fieldEmail.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });

    // Handle Password Verification for Returning / Resuming Users
    function submitLoginChallenge(pwdInputId, errorDivId, btnId) {
        const pwdInput = document.getElementById(pwdInputId);
        const errDiv = document.getElementById(errorDivId);
        const btn = document.getElementById(btnId);
        const email = fieldEmail.value.trim();
        const pwd = pwdInput ? pwdInput.value : '';

        if (!pwd) {
            errDiv.style.display = 'block';
            errDiv.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Please enter your account password.';
            if (pwdInput) pwdInput.focus();
            return;
        }

        errDiv.style.display = 'none';
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Authenticating...';

        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', pwd);
        formData.append('track', trackCode);

        fetch(siteUrl + '/opportunities/apply/verify-login', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;

            if (data.status === 1) {
                btn.innerHTML = '<i class="fa fa-check me-1"></i> Success!';
                btn.classList.remove('btn-wiz-next');
                btn.classList.add('btn-success');
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 400);
            } else {
                errDiv.style.display = 'block';
                errDiv.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> ' + (data.msg || 'Invalid password.');
                if (pwdInput) pwdInput.focus();
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            errDiv.style.display = 'block';
            errDiv.innerHTML = '<i class="fa fa-exclamation-triangle me-1"></i> Server error. Please try again.';
        });
    }

    document.getElementById('btn_submit_already_applied')?.addEventListener('click', function () {
        submitLoginChallenge('pwd_already_applied', 'error_already_applied', 'btn_submit_already_applied');
    });
    document.getElementById('pwd_already_applied')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitLoginChallenge('pwd_already_applied', 'error_already_applied', 'btn_submit_already_applied');
        }
    });

    document.getElementById('btn_submit_resuming')?.addEventListener('click', function () {
        submitLoginChallenge('pwd_resuming', 'error_resuming', 'btn_submit_resuming');
    });
    document.getElementById('pwd_resuming')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitLoginChallenge('pwd_resuming', 'error_resuming', 'btn_submit_resuming');
        }
    });

    document.getElementById('btn_submit_existing_new_track')?.addEventListener('click', function () {
        submitLoginChallenge('pwd_existing_new_track', 'error_existing_new_track', 'btn_submit_existing_new_track');
    });
    document.getElementById('pwd_existing_new_track')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitLoginChallenge('pwd_existing_new_track', 'error_existing_new_track', 'btn_submit_existing_new_track');
        }
    });

    // Resume to specific step if requested via query or draft
    const urlParams = new URLSearchParams(window.location.search);
    const stepParam = urlParams.get('step') || (initialStep > 1 ? String(initialStep) : null);
    if (stepParam && stepParam !== '1') {
        const targetTab = document.getElementById('step' + stepParam + '-tab');
        if (targetTab) {
            bootstrap.Tab.getOrCreateInstance(targetTab).show();
        }
    }

    // --- 2. Real-time Progress Tracker ---
    function updateFormProgress() {
        const stepTabs = [
            { id: 'step1', badge: document.getElementById('step1_badge') },
            { id: 'step2', badge: document.getElementById('step2_badge') },
            { id: 'step3', badge: document.getElementById('step3_badge') },
            { id: 'step4', badge: document.getElementById('step4_badge') },
            { id: 'step5', badge: document.getElementById('step5_badge') }
        ];

        let totalFilled = 0;
        let totalCount = 0;

        stepTabs.forEach(step => {
            const container = document.getElementById(step.id);
            if (!container) return;

            const inputs = container.querySelectorAll('input.track-progress, select.track-progress, textarea.track-progress');
            let stepFilled = 0;
            let stepTotal = 0;

            inputs.forEach(input => {
                if (input.closest('.d-none')) return;
                
                stepTotal++;
                totalCount++;

                const val = input.value ? input.value.trim() : '';
                if (val !== '') {
                    stepFilled++;
                    totalFilled++;
                }
            });

            // Skills matrix count
            if (step.id === 'step3') {
                const skillRadios = container.querySelectorAll('input[type="radio"]:checked');
                let validSkills = 0;
                skillRadios.forEach(r => {
                    if (parseInt(r.value, 10) > 0) validSkills++;
                });
                if (validSkills > 0) {
                    stepFilled++;
                    totalFilled++;
                }
                stepTotal++;
                totalCount++;
            }

            // Update Tab Badges
            if (step.badge) {
                if (stepTotal > 0 && stepFilled >= stepTotal) {
                    step.badge.className = 'step-badge bg-success';
                    step.badge.innerHTML = '<i class="fa fa-check"></i>';
                } else {
                    step.badge.className = 'step-badge bg-secondary';
                    step.badge.textContent = `${stepFilled}/${stepTotal}`;
                }
            }
        });

        const percent = totalCount > 0 ? Math.round((totalFilled / totalCount) * 100) : 0;
        progressBar.style.width = percent + '%';
        progressBar.setAttribute('aria-valuenow', percent);
        percentLabel.textContent = percent + '%';
        countsLabel.textContent = `(${totalFilled}/${totalCount})`;
    }

    // --- 3. Auto-Save Engine ---
    function triggerAutoSave(isManual = false) {
        if (isSaving) return;

        autosaveBadge.className = 'autosave-pill status-saving';
        autosaveBadge.innerHTML = '<span class="autosave-dot"></span> <span class="autosave-text">Saving draft...</span>';

        isSaving = true;
        document.getElementById('is_submit_flag').value = '0';
        const formData = new FormData(form);

        fetch(siteUrl + '/dashboard/apply/save', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            isSaving = false;
            if (data.status === 1) {
                if (data.application_id) {
                    document.getElementById('application_id').value = data.application_id;
                    const url = new URL(window.location);
                    url.searchParams.set('id', data.application_id);
                    window.history.replaceState({}, '', url);
                }

                const now = new Date();
                const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                
                autosaveBadge.className = 'autosave-pill status-saved';
                autosaveBadge.innerHTML = `<span class="autosave-dot"></span> <span class="autosave-text">Saved ${timeStr}</span>`;

                if (isManual) {
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-success border-0 shadow-sm';
                    alertBox.innerHTML = '<i class="fa fa-check-circle me-2"></i> Application draft saved successfully.';
                    setTimeout(() => alertBox.style.display = 'none', 3000);
                }
            } else {
                autosaveBadge.className = 'autosave-pill text-danger';
                autosaveBadge.innerHTML = '<span class="autosave-dot bg-danger"></span> <span class="autosave-text">Save error</span>';
            }
        })
        .catch(err => {
            isSaving = false;
            autosaveBadge.className = 'autosave-pill text-muted';
            autosaveBadge.innerHTML = '<span class="autosave-dot"></span> <span class="autosave-text">Offline / Queued</span>';
        });
    }

    function queueAutoSave() {
        updateFormProgress();
        autosaveBadge.className = 'autosave-pill status-saving';
        autosaveBadge.innerHTML = '<span class="autosave-dot"></span> <span class="autosave-text">Editing...</span>';
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => triggerAutoSave(false), 1200);
    }

    form.addEventListener('input', queueAutoSave);
    form.addEventListener('change', queueAutoSave);

    // --- 4. Dynamic Skills Loader with Rating Selector ---
    function loadSkillsForFunction(funcId) {
        if (!funcId) return;
        skillsArea.innerHTML = '<div class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin me-2"></i> Updating skills list...</div>';
        
        fetch(siteUrl + '/api/service-functions/skills?functions=' + funcId)
            .then(res => res.json())
            .then(items => {
                if (items.length === 0) {
                    skillsArea.innerHTML = '<p class="text-muted p-4 mb-0 text-center">No specific skills listed for this function yet.</p>';
                    updateFormProgress();
                    return;
                }
                let html = '<div class="table-responsive"><table class="table align-middle mb-0">';
                html += '<thead><tr><th style="width:48%;">Skill / Competency Area</th><th style="width:52%;">Your Rating (1–5 Scale)</th></tr></thead><tbody>';
                
                items.forEach(it => {
                    const currentVal = existingSkills[it.id] || 0;
                    html += `<tr>
                        <td class="fw-semibold" style="font-size:0.92rem; color:#0f172a;">${it.name}</td>
                        <td>
                            <div class="rating-btn-group" role="group">
                                <input type="radio" name="skills[${it.id}]" id="sk_${it.id}_0" value="0" ${currentVal == 0 ? 'checked' : ''}>
                                <label for="sk_${it.id}_0" title="Not Applicable">N/A</label>

                                <input type="radio" name="skills[${it.id}]" id="sk_${it.id}_1" value="1" ${currentVal == 1 ? 'checked' : ''}>
                                <label for="sk_${it.id}_1" title="1 - Aware (Basic concepts)">1</label>

                                <input type="radio" name="skills[${it.id}]" id="sk_${it.id}_2" value="2" ${currentVal == 2 ? 'checked' : ''}>
                                <label for="sk_${it.id}_2" title="2 - Assisted (With guidance)">2</label>

                                <input type="radio" name="skills[${it.id}]" id="sk_${it.id}_3" value="3" ${currentVal == 3 ? 'checked' : ''}>
                                <label for="sk_${it.id}_3" title="3 - Independent (Routine execution)">3</label>

                                <input type="radio" name="skills[${it.id}]" id="sk_${it.id}_4" value="4" ${currentVal == 4 ? 'checked' : ''}>
                                <label for="sk_${it.id}_4" title="4 - Expert (Complex edge cases)">4</label>

                                <input type="radio" name="skills[${it.id}]" id="sk_${it.id}_5" value="5" ${currentVal == 5 ? 'checked' : ''}>
                                <label for="sk_${it.id}_5" title="5 - Master (Mentors &amp; QA lead)">5</label>
                            </div>
                        </td>
                    </tr>`;
                });
                html += '</tbody></table></div>';
                skillsArea.innerHTML = html;
                updateFormProgress();
            })
            .catch(err => {
                skillsArea.innerHTML = '<p class="text-danger p-3 mb-0">Failed to load skills matrix.</p>';
            });
    }

    if (primaryFuncSelect) {
        primaryFuncSelect.addEventListener('change', function () {
            loadSkillsForFunction(this.value);
            triggerAutoSave(false);
        });
        loadSkillsForFunction(primaryFuncSelect.value);
    }

    // Toggle WRL section
    const wrlCheck = document.getElementById('wrlCheck');
    const wrlSection = document.getElementById('wrlSection');
    if (wrlCheck && wrlSection) {
        wrlCheck.addEventListener('change', function () {
            wrlSection.classList.toggle('d-none', !this.checked);
            updateFormProgress();
            triggerAutoSave(false);
        });
    }

    // Toggle Disability details
    const disabilityCheck = document.getElementById('disabilityCheck');
    const adjustmentDetailsDiv = document.getElementById('adjustmentDetailsDiv');
    if (disabilityCheck && adjustmentDetailsDiv) {
        disabilityCheck.addEventListener('change', function () {
            adjustmentDetailsDiv.classList.toggle('d-none', !this.checked);
            updateFormProgress();
            triggerAutoSave(false);
        });
    }

    // Tab navigation buttons
    document.querySelectorAll('.btn-next-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            triggerAutoSave(false);
            const targetTab = document.getElementById(this.dataset.next + '-tab');
            if (targetTab) {
                bootstrap.Tab.getOrCreateInstance(targetTab).show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });

    document.querySelectorAll('.btn-prev-tab').forEach(btn => {
        btn.addEventListener('click', function () {
            triggerAutoSave(false);
            const targetTab = document.getElementById(this.dataset.prev + '-tab');
            if (targetTab) {
                bootstrap.Tab.getOrCreateInstance(targetTab).show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });

    // Repeatable rows handlers
    document.getElementById('btn_add_qual')?.addEventListener('click', function () {
        const container = document.getElementById('qualifications_container');
        const first = container.querySelector('.qual-row');
        if (first) {
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input').forEach(i => i.value = '');
            container.appendChild(clone);
            updateFormProgress();
        }
    });

    document.getElementById('btn_add_work')?.addEventListener('click', function () {
        const container = document.getElementById('work_history_container');
        const first = container.querySelector('.work-row');
        if (first) {
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input, textarea').forEach(i => i.value = '');
            container.appendChild(clone);
            updateFormProgress();
        }
    });

    document.getElementById('btn_add_ref')?.addEventListener('click', function () {
        const container = document.getElementById('referees_container');
        const first = container.querySelector('.ref-row');
        if (first) {
            const clone = first.cloneNode(true);
            clone.querySelectorAll('input').forEach(i => i.value = '');
            container.appendChild(clone);
            updateFormProgress();
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.btn-remove-row')) {
            const row = e.target.closest('.qual-row, .work-row, .ref-row');
            if (row && row.parentNode.children.length > 1) {
                row.remove();
                updateFormProgress();
                triggerAutoSave(false);
            }
        }
    });

    // Manual Save
    document.querySelectorAll('.btn-save-manual').forEach(btn => {
        btn.addEventListener('click', () => triggerAutoSave(true));
    });

    // --- 5. Cross-Tab Multi-Step Form Validation ---
    function validateFormAcrossTabs() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        alertBox.style.display = 'none';

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const stepIds = ['step1', 'step2', 'step3', 'step4', 'step5'];

        for (let i = 0; i < stepIds.length; i++) {
            const stepId = stepIds[i];
            const pane = document.getElementById(stepId);
            if (!pane) continue;

            // 1. Check required fields in this pane
            const reqFields = pane.querySelectorAll('.req-field');
            for (let j = 0; j < reqFields.length; j++) {
                const f = reqFields[j];
                if (f.closest('.d-none')) continue;

                if (!f.value || f.value.trim() === '') {
                    const tabBtn = document.getElementById(stepId + '-tab');
                    if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();

                    f.classList.add('is-invalid');
                    f.focus();

                    const fieldName = f.dataset.label || f.name || 'Required field';
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-danger shadow-sm border-0';
                    alertBox.innerHTML = `<i class="fa fa-exclamation-triangle me-2"></i> Please complete: <strong>${fieldName}</strong> on Step ${i + 1}.`;
                    window.scrollTo({ top: f.offsetTop - 120, behavior: 'smooth' });
                    return false;
                }
            }

            // 2. Check typed email fields in this pane
            const emailFields = pane.querySelectorAll('.email-field');
            for (let k = 0; k < emailFields.length; k++) {
                const ef = emailFields[k];
                if (ef.closest('.d-none')) continue;

                const val = ef.value ? ef.value.trim() : '';
                if (val !== '' && !emailRegex.test(val)) {
                    const tabBtn = document.getElementById(stepId + '-tab');
                    if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();

                    ef.classList.add('is-invalid');
                    ef.focus();

                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-danger shadow-sm border-0';
                    alertBox.innerHTML = `<i class="fa fa-exclamation-triangle me-2"></i> Please enter a valid email address for: <strong>${ef.placeholder || ef.name}</strong> on Step ${i + 1}.`;
                    window.scrollTo({ top: ef.offsetTop - 120, behavior: 'smooth' });
                    return false;
                }
            }

            // 3. Check password fields if guest on Step 1
            const pwd = document.getElementById('field_password');
            const pwdConf = document.getElementById('field_password_confirmation');
            if (pwd && pwdConf && stepId === 'step1') {
                if (!pwd.value || pwd.value.length < 6) {
                    const tabBtn = document.getElementById('step1-tab');
                    if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
                    pwd.classList.add('is-invalid');
                    pwd.focus();
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-danger shadow-sm border-0';
                    alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-2"></i> Account password must be at least 6 characters long.';
                    window.scrollTo({ top: pwd.offsetTop - 120, behavior: 'smooth' });
                    return false;
                }
                if (pwd.value !== pwdConf.value) {
                    const tabBtn = document.getElementById('step1-tab');
                    if (tabBtn) bootstrap.Tab.getOrCreateInstance(tabBtn).show();
                    pwdConf.classList.add('is-invalid');
                    pwdConf.focus();
                    alertBox.style.display = 'block';
                    alertBox.className = 'alert alert-danger shadow-sm border-0';
                    alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-2"></i> Passwords do not match. Please verify your password confirmation.';
                    window.scrollTo({ top: pwdConf.offsetTop - 120, behavior: 'smooth' });
                    return false;
                }
            }
        }

        return true;
    }

    // Final Submit Button Click
    document.getElementById('btn_final_submit')?.addEventListener('click', function () {
        if (!validateFormAcrossTabs()) {
            return;
        }

        if (confirm('Are you sure you want to submit your application for formal vetting review?')) {
            document.getElementById('is_submit_flag').value = '1';
            const formData = new FormData(form);
            
            alertBox.style.display = 'block';
            alertBox.className = 'alert alert-info border-0 shadow-sm';
            alertBox.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Submitting application for vetting review...';

            fetch(siteUrl + '/dashboard/apply/save', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 1) {
                    alertBox.className = 'alert alert-success border-0 shadow-sm';
                    alertBox.innerHTML = '<i class="fa fa-check-circle me-2"></i> ' + data.msg;
                    setTimeout(() => {
                        window.location.href = data.redirect_url || (siteUrl + '/dashboard/application?id=' + data.application_id);
                    }, 1200);
                } else {
                    alertBox.className = 'alert alert-danger border-0 shadow-sm';
                    alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-2"></i> ' + data.msg;
                }
            })
            .catch(err => {
                alertBox.className = 'alert alert-danger border-0 shadow-sm';
                alertBox.innerHTML = '<i class="fa fa-exclamation-triangle me-2"></i> Server error. Please try again.';
            });
        }
    });

    // Initial calculation on load
    updateFormProgress();
});
</script>
