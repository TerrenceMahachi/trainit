#!/usr/bin/env python3
"""
Tsigiro Portal - Executive Walkthrough & Testing Guide PDF Generator
Converts the comprehensive executive guide into an executive-grade, 4-page printable PDF
document using Headless Microsoft Edge with styled CSS typography, tables, and branding.
"""

import os
import subprocess
import sys
import shutil

OUTPUT_DIR = "/Library/WebServer/Documents/trainit"
MIRROR_DIR = "/Library/WebServer/Documents/tsigiro/portal"
ARTIFACT_DIR = "/Users/terrencemahachi/.gemini/antigravity/brain/1a959035-59a1-410c-8002-1623b7a9cc82"
PDF_FILENAME = "TSIGIROS_EXECUTIVE_WALKTHROUGH_GUIDE.pdf"
HTML_FILENAME = "executive_guide_render.html"

HTML_CONTENT = """<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Tsigiro Portal — Executive Walkthrough & Testing Guide</title>
<style>
  @page {
    size: A4 portrait;
    margin: 10mm 12mm 12mm 12mm;
    @bottom-center {
      content: "Page " counter(page) " of " counter(pages);
      font-size: 7.5pt;
      color: #64748b;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #1e293b;
    background: #ffffff;
    font-size: 8.5pt;
    line-height: 1.38;
  }

  .page-container {
    page-break-after: always;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
  }

  .page-container:last-child {
    page-break-after: avoid;
  }

  /* Cover Banner */
  .cover-banner {
    background: linear-gradient(135deg, #090b0b 0%, #0f2b23 60%, #134e4a 100%);
    color: #ffffff;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }

  .brand-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.15);
    padding-bottom: 10px;
  }

  .brand-left {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .brand-logo-svg {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
  }

  .brand-name {
    font-size: 17pt;
    font-weight: 800;
    letter-spacing: -0.5px;
    color: #ffffff;
    line-height: 1;
  }

  .brand-tag {
    font-size: 7.5pt;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #32c99a;
    font-weight: 700;
    margin-top: 2px;
  }

  .doc-badge {
    background: rgba(50, 201, 154, 0.15);
    border: 1px solid #32c99a;
    color: #32c99a;
    padding: 3px 8px;
    border-radius: 5px;
    font-size: 7.2pt;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.6px;
  }

  .doc-title {
    font-size: 13.5pt;
    font-weight: 800;
    letter-spacing: -0.3px;
    margin-bottom: 4px;
    color: #ffffff;
  }

  .doc-subtitle {
    font-size: 8.3pt;
    color: #cbd5e1;
    line-height: 1.35;
    margin-bottom: 10px;
  }

  .meta-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 6px;
    padding: 7px 12px;
  }

  .meta-item {
    display: flex;
    flex-direction: column;
  }

  .meta-label {
    font-size: 6.5pt;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #94a3b8;
    font-weight: 600;
    margin-bottom: 1px;
  }

  .meta-val {
    font-size: 8pt;
    font-weight: 600;
    color: #ffffff;
  }

  .meta-val.highlight {
    color: #32c99a;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  }

  /* Section Styling */
  .section {
    margin-bottom: 11px;
  }

  .section-title {
    font-size: 10.2pt;
    font-weight: 700;
    color: #0f2b23;
    border-bottom: 2px solid #32c99a;
    padding-bottom: 3px;
    margin-bottom: 7px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .section-title-num {
    background: #0f2b23;
    color: #32c99a;
    font-size: 7.5pt;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 4px;
    margin-right: 5px;
  }

  /* Tables */
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 7.6pt;
    margin-bottom: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 5px;
    overflow: hidden;
  }

  th {
    background: #0f2b23;
    color: #ffffff;
    font-weight: 600;
    text-align: left;
    padding: 4.5px 7px;
    font-size: 7.5pt;
    letter-spacing: 0.2px;
  }

  td {
    padding: 4px 7px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }

  tr:nth-child(even) td {
    background: #f8fafc;
  }

  tr:last-child td {
    border-bottom: none;
  }

  /* Badges */
  .badge {
    display: inline-block;
    padding: 1.5px 5px;
    border-radius: 3px;
    font-size: 6.8pt;
    font-weight: 600;
    white-space: nowrap;
  }

  .badge-live {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #86efac;
  }

  .badge-external {
    background: #f3e8ff;
    color: #7e22ce;
    border: 1px solid #d8b4fe;
  }

  .badge-role {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #7dd3fc;
  }

  /* Code pills */
  code, .url-pill {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 7.2pt;
    background: #f1f5f9;
    color: #0f172a;
    padding: 1px 3.5px;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
  }

  .url-pill {
    color: #0369a1;
    font-weight: 600;
  }

  /* KPI Stat Boxes */
  .kpi-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 10px;
  }

  .kpi-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-top: 3px solid #32c99a;
    border-radius: 5px;
    padding: 7px 9px;
  }

  .kpi-num {
    font-size: 13pt;
    font-weight: 800;
    color: #0f2b23;
    line-height: 1.1;
  }

  .kpi-title {
    font-size: 6.8pt;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 1px;
  }

  .kpi-desc {
    font-size: 6.5pt;
    color: #475569;
    margin-top: 1px;
  }

  /* Journey Cards */
  .journey-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 8px;
  }

  .journey-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 7px 9px;
    border-left: 3px solid #0f2b23;
  }

  .journey-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 3px;
  }

  .journey-title {
    font-size: 8.3pt;
    font-weight: 700;
    color: #0f2b23;
  }

  .journey-persona {
    font-size: 7.2pt;
    color: #64748b;
    margin-bottom: 4px;
  }

  .journey-persona strong {
    color: #1e293b;
  }

  .journey-actions {
    list-style: none;
  }

  .journey-actions li {
    font-size: 7.2pt;
    color: #334155;
    position: relative;
    padding-left: 12px;
    margin-bottom: 2.5px;
    line-height: 1.3;
  }

  .journey-actions li::before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #15803d;
    font-weight: 700;
    font-size: 6.8pt;
  }

  /* Callout Alert */
  .callout {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-left: 4px solid #16a34a;
    border-radius: 6px;
    padding: 7px 11px;
    font-size: 7.6pt;
    color: #166534;
    margin-top: 6px;
    margin-bottom: 6px;
  }

  .callout-title {
    font-weight: 700;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 7.9pt;
  }

  .footer-note {
    font-size: 7pt;
    color: #94a3b8;
    text-align: center;
    border-top: 1px solid #e2e8f0;
    padding-top: 5px;
    margin-top: auto;
  }
</style>
</head>
<body>

  <!-- ==================== PAGE 1: COVER, SUMMARY & STATUS AUDIT ==================== -->
  <div class="page-container">
    <!-- COVER BANNER -->
    <div class="cover-banner">
      <div class="brand-row">
        <div class="brand-left">
          <svg class="brand-logo-svg" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="64" height="64" rx="14" fill="#090b0b"/>
            <path d="M15 22h34v9H37v21H27V31H15z" fill="#ffffff"/>
            <rect x="37" y="10" width="10" height="10" rx="4" fill="#32c99a" transform="rotate(8 42 15)"/>
          </svg>
          <div>
            <div class="brand-name">TSIGIRO</div>
            <div class="brand-tag">Enterprise Talent & Workforce Ecosystem</div>
          </div>
        </div>
        <div class="doc-badge">Official Testing & Evaluation Guide</div>
      </div>

      <div class="doc-title">Executive Walkthrough & System Validation Manual</div>
      <div class="doc-subtitle">
        Comprehensive testing reference covering the normalized data model, 8 role-based evaluation journeys, talent vetting pipeline, corporate client portal, and 1-year multi-client operational simulation with payroll and tax billing.
      </div>

      <div class="meta-grid">
        <div class="meta-item">
          <span class="meta-label">Target Audience</span>
          <span class="meta-val">Executive Evaluators & Auditors</span>
        </div>
        <div class="meta-item">
          <span class="meta-label">Global Demo Password</span>
          <span class="meta-val highlight">Password123!</span>
        </div>
        <div class="meta-item">
          <span class="meta-label">Live Production URL</span>
          <span class="meta-val highlight">portal.tsigiro.co.zw</span>
        </div>
        <div class="meta-item">
          <span class="meta-label">Document Release</span>
          <span class="meta-val">Version 2.4 — Sep 2026</span>
        </div>
      </div>
    </div>

    <!-- SECTION 1: EXECUTIVE SUMMARY -->
    <div class="section">
      <div class="section-title">
        <span><span class="section-title-num">1</span> Executive Summary & Core Platform Capabilities</span>
      </div>
      <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; margin-bottom: 8px;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 6px;">
          <strong style="color: #0f2b23; font-size: 7.5pt;">1. Talent Acquisition</strong>
          <p style="font-size: 6.8pt; color: #475569; margin-top: 2px;">Public career vacancies, WRL attachments, and specialist contractor intake.</p>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 6px;">
          <strong style="color: #0f2b23; font-size: 7.5pt;">2. Compliance Vetting</strong>
          <p style="font-size: 6.8pt; color: #475569; margin-top: 2px;">CID police clearances, academic qualifications, and ZIMRA ITF263 tax checks.</p>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 6px;">
          <strong style="color: #0f2b23; font-size: 7.5pt;">3. Delivery Orchestration</strong>
          <p style="font-size: 6.8pt; color: #475569; margin-top: 2px;">Associates and Apprentices paired under structured supervisor mentorship.</p>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 6px;">
          <strong style="color: #0f2b23; font-size: 7.5pt;">4. Corporate Portal</strong>
          <p style="font-size: 6.8pt; color: #475569; margin-top: 2px;">Client workspaces for briefs, SLAs, team members, and invoice tracking.</p>
        </div>
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 5px; padding: 6px;">
          <strong style="color: #0f2b23; font-size: 7.5pt;">5. Financial Operations</strong>
          <p style="font-size: 6.8pt; color: #475569; margin-top: 2px;">12 pay cycles, NSSA P4 returns, ZIMRA 15% VAT invoices, and bank settlements.</p>
        </div>
      </div>
    </div>

    <!-- SECTION 2: IMPLEMENTATION STATUS AUDIT -->
    <div class="section">
      <div class="section-title">
        <span><span class="section-title-num">2</span> Implementation Status Audit (Live vs. In-Progress)</span>
        <span style="font-size: 7.2pt; font-weight: 500; color: #64748b;">Audited September 2026</span>
      </div>
      <table>
        <thead>
          <tr>
            <th style="width: 25%;">Module / Feature</th>
            <th style="width: 14%;">Status</th>
            <th style="width: 26%;">Testable Screen / Endpoint</th>
            <th style="width: 35%;">Operational Scope & Data Integrity</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>Testing Accounts & Quick Login</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/login</span>, <span class="url-pill">/quick-login?as={role}</span></td>
            <td>8 distinct roles seeded; 1-click instant login buttons and pre-fill helpers active.</td>
          </tr>
          <tr>
            <td><strong>Public Opportunities Hub</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/opportunities</span>, <span class="url-pill">/opportunities/vacancies</span></td>
            <td>Catalog filtering by Vacancies, Apprenticeships, and Associate tracks.</td>
          </tr>
          <tr>
            <td><strong>Express Candidate Application</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/opportunities/apply/*</span></td>
            <td>1-click application submission with automated candidate account creation.</td>
          </tr>
          <tr>
            <td><strong>Unified Candidate Dashboard</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/dashboard</span> (Candidate / Apprentice / Associate)</td>
            <td>Multi-track cards, interview status alerts, stage badges, and onboarding steps.</td>
          </tr>
          <tr>
            <td><strong>Admin Command Center</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/dashboard</span> (Admin)</td>
            <td>Operational metrics, staff rosters, audit trails, and system dictionary controls.</td>
          </tr>
          <tr>
            <td><strong>Vacancy Management</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/admin/vacancies</span>, <span class="url-pill">/admin/vacancies/create</span></td>
            <td>Full CRUD for postings, qualification requirements, deadlines, and applicant review.</td>
          </tr>
          <tr>
            <td><strong>Vetting Pipeline & Scoring</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/admin/roster</span>, <span class="url-pill">/admin/roster/review?id=:id</span></td>
            <td>100-point candidate scoring rubric, CID police clearance, and qualification check.</td>
          </tr>
          <tr>
            <td><strong>Client Operations Desk</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/admin/clients</span>, <span class="url-pill">/admin/requests</span></td>
            <td>Corporate accounts, service plan retainers, request triage, and SLA allocation.</td>
          </tr>
          <tr>
            <td><strong>Client Organization Portal</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/client/portal</span>, <span class="url-pill">/client/plans</span></td>
            <td>Dedicated corporate workspace, team members, and structured request submission.</td>
          </tr>
          <tr>
            <td><strong>Payroll & Disbursements</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/admin/payroll</span>, <span class="url-pill">/admin/payroll/view/:id</span></td>
            <td>12 pay cycles, 84 payslips (PAYE/NSSA/Medical Aid), 84 bank transfers, NSSA P4 export.</td>
          </tr>
          <tr>
            <td><strong>Client Invoicing & Tax Billing</strong></td>
            <td><span class="badge badge-live">✅ 100% Live</span></td>
            <td><span class="url-pill">/client/invoices</span>, <span class="url-pill">/client/invoices/view/:id</span></td>
            <td>48 corporate tax invoices with ZIMRA 15% VAT, line items, and electronic bank receipts.</td>
          </tr>
          <tr>
            <td><strong>Client Self-Onboarding</strong></td>
            <td><span class="badge badge-external">🔄 External</span></td>
            <td>External Partner System</td>
            <td>Intentionally decoupled; client corporate onboarding managed by external partner team.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ==================== PAGE 2: TESTING PERSONAS & JOURNEYS 1-4 ==================== -->
  <div class="page-container">
    <!-- SECTION 3: PRE-CONFIGURED TESTING ACCOUNTS -->
    <div class="section">
      <div class="section-title">
        <span><span class="section-title-num">3</span> Pre-Configured Testing Personas & Quick-Login Directory</span>
      </div>
      <p style="margin-bottom: 5px; font-size: 7.8pt;">
        All accounts share password <code style="font-weight: 700; color: #0f2b23;">Password123!</code>. Evaluators can click the direct quick-login URLs below or use 1-click role buttons on <span class="url-pill">/login</span>.
      </p>
      <table>
        <thead>
          <tr>
            <th style="width: 14%;">Role Persona</th>
            <th style="width: 21%;">Name & Entity</th>
            <th style="width: 21%;">Demo Email</th>
            <th style="width: 22%;">Direct Quick-Login URL</th>
            <th style="width: 22%;">Primary Evaluation Scope</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><span class="badge badge-role">Admin</span></td>
            <td>System Administrator</td>
            <td><code>admin@tsigiro.co.zw</code></td>
            <td><span class="url-pill">/quick-login?as=admin</span></td>
            <td>Master command center & system audit</td>
          </tr>
          <tr>
            <td><span class="badge badge-role">Vetting Officer</span></td>
            <td>Ruvimbo Sithole</td>
            <td><code>vetting@tsigiro.co.zw</code></td>
            <td><span class="url-pill">/quick-login?as=vetting</span></td>
            <td>100-pt scoring & background checks</td>
          </tr>
          <tr>
            <td><span class="badge badge-role">Service Manager</span></td>
            <td>Tafadzwa Mutasa</td>
            <td><code>manager@tsigiro.co.zw</code></td>
            <td><span class="url-pill">/quick-login?as=manager</span></td>
            <td>SLA delivery & talent dispatching</td>
          </tr>
          <tr>
            <td><span class="badge badge-role">Billing Officer</span></td>
            <td>Nyasha Chidziwa</td>
            <td><code>finance@tsigiro.co.zw</code></td>
            <td><span class="url-pill">/quick-login?as=finance</span></td>
            <td>Payroll cycles, NSSA P4 & Invoicing</td>
          </tr>
          <tr>
            <td><span class="badge badge-role">Apprentice</span></td>
            <td>Kudzai Mapfumo (NUST)</td>
            <td><code>apprentice@tsigiro.co.zw</code></td>
            <td><span class="url-pill">/quick-login?as=apprentice</span></td>
            <td>WRL tracking & collaborative tasks</td>
          </tr>
          <tr>
            <td><span class="badge badge-role">Associate</span></td>
            <td>Simbarashe Hove (Cloud)</td>
            <td><code>associate@tsigiro.co.zw</code></td>
            <td><span class="url-pill">/quick-login?as=associate</span></td>
            <td>Rate ($180/day), ITF263 & deliverables</td>
          </tr>
          <tr>
            <td><span class="badge badge-role">Candidate</span></td>
            <td>Farai Chikwanha</td>
            <td><code>candidate@tsigiro.co.zw</code></td>
            <td><span class="url-pill">/quick-login?as=candidate</span></td>
            <td>Vacancy application & interview brief</td>
          </tr>
          <tr>
            <td><span class="badge badge-role">Client Lead</span></td>
            <td>Tinashe Gumbo (EcoSolutions)</td>
            <td><code>client@tsigiro.co.zw</code></td>
            <td><span class="url-pill">/quick-login?as=client</span></td>
            <td>Retainer plans, requests & tax invoices</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- SECTION 4: GUIDED JOURNEYS (PART 1: JOURNEYS 1 - 4) -->
    <div class="section">
      <div class="section-title">
        <span><span class="section-title-num">4</span> Guided Evaluation Journeys — Internal Staff Operations</span>
        <span style="font-size: 7.2pt; font-weight: 500; color: #64748b;">Journeys 1 through 4</span>
      </div>

      <div class="journey-grid">
        <!-- Journey 1 -->
        <div class="journey-card">
          <div class="journey-header">
            <div class="journey-title">Journey 1: System Administrator</div>
            <span class="url-pill">/quick-login?as=admin</span>
          </div>
          <div class="journey-persona">Persona: <strong>Master Command Center</strong></div>
          <ul class="journey-actions">
            <li><strong>Opportunities Management:</strong> Navigate to <span class="url-pill">/admin/vacancies</span> to publish openings, set deadlines, and configure degree requirements.</li>
            <li><strong>Talent Pipeline Roster:</strong> Review candidates across all three intake pipelines at <span class="url-pill">/admin/roster</span>.</li>
            <li><strong>Staff & Organization Directory:</strong> Inspect department rosters across Operations, Vetting, and Finance at <span class="url-pill">/admin/staff</span>.</li>
            <li><strong>System Audit Log:</strong> Verify cryptographic session signatures and user audit activities.</li>
          </ul>
        </div>

        <!-- Journey 2 -->
        <div class="journey-card">
          <div class="journey-header">
            <div class="journey-title">Journey 2: Compliance & Vetting Officer</div>
            <span class="url-pill">/quick-login?as=vetting</span>
          </div>
          <div class="journey-persona">Persona: <strong>Ruvimbo Sithole</strong> (Operations / Vetting)</div>
          <ul class="journey-actions">
            <li><strong>Vetting Queue:</strong> Access the compliance review queue at <span class="url-pill">/admin/roster</span> to inspect pending background checks.</li>
            <li><strong>Candidate Dossier:</strong> Open <span class="url-pill">/admin/roster/review?id=:id</span> to verify CID clearance certificates and university transcripts.</li>
            <li><strong>100-Point Scoring Rubric:</strong> Input weighted scores across Experience, Qualifications, and Panel Interview scores.</li>
            <li><strong>Decision Sign-Off:</strong> Register formal vetting actions (*Shortlist*, *Flag*, *Admit to Roster*).</li>
          </ul>
        </div>

        <!-- Journey 3 -->
        <div class="journey-card">
          <div class="journey-header">
            <div class="journey-title">Journey 3: Service & SLA Manager</div>
            <span class="url-pill">/quick-login?as=manager</span>
          </div>
          <div class="journey-persona">Persona: <strong>Tafadzwa Mutasa</strong> (Operations Desk)</div>
          <ul class="journey-actions">
            <li><strong>Client Account Oversight:</strong> Review corporate client subscriptions and monthly retainer capacities at <span class="url-pill">/admin/clients</span>.</li>
            <li><strong>Request Triage & SLA Allocation:</strong> Manage client service requests, priority tiers, and deliverable milestones at <span class="url-pill">/admin/requests</span>.</li>
            <li><strong>Apprentice Mentorship Pairing:</strong> Allocate verified Associates and Apprentices under supervisory links (<span class="url-pill">assignmentsupervisor</span>).</li>
            <li><strong>Service Catalogue:</strong> Oversee standardized SLA delivery modules at <span class="url-pill">/admin/service-catalogue</span>.</li>
          </ul>
        </div>

        <!-- Journey 4 -->
        <div class="journey-card">
          <div class="journey-header">
            <div class="journey-title">Journey 4: Finance & Billing Officer</div>
            <span class="url-pill">/quick-login?as=finance</span>
          </div>
          <div class="journey-persona">Persona: <strong>Nyasha Chidziwa</strong> (Finance Department)</div>
          <ul class="journey-actions">
            <li><strong>12 Payroll Cycles:</strong> Inspect 12 monthly pay periods (Sep 2025 – Aug 2026) at <span class="url-pill">/admin/payroll</span> with 84 settled payslips.</li>
            <li><strong>Individual Payslip Ledger:</strong> Review itemized deductions (PAYE, NSSA Pension, Medical Aid) at <span class="url-pill">/admin/payroll/view/1</span>.</li>
            <li><strong>Statutory Compliance:</strong> Generate and export the official Zimbabwean NSSA Form P4 return at <span class="url-pill">/admin/staff/export-p4</span>.</li>
            <li><strong>Corporate Invoicing Oversight:</strong> Inspect 48 client tax invoices and electronic bank receipts at <span class="url-pill">/admin/invoices</span>.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- ==================== PAGE 3: JOURNEYS 5-8 & SIMULATION PORTFOLIO ==================== -->
  <div class="page-container">
    <!-- SECTION 4: GUIDED JOURNEYS (PART 2: JOURNEYS 5 - 8) -->
    <div class="section">
      <div class="section-title">
        <span><span class="section-title-num">4b</span> Guided Evaluation Journeys — Talent, Candidates & Clients</span>
        <span style="font-size: 7.2pt; font-weight: 500; color: #64748b;">Journeys 5 through 8</span>
      </div>

      <div class="journey-grid">
        <!-- Journey 5 -->
        <div class="journey-card">
          <div class="journey-header">
            <div class="journey-title">Journey 5: University Apprentice (WRL)</div>
            <span class="url-pill">/quick-login?as=apprentice</span>
          </div>
          <div class="journey-persona">Persona: <strong>Kudzai Mapfumo</strong> (NUST Computer Science)</div>
          <ul class="journey-actions">
            <li><strong>Candidate Dashboard:</strong> View active tracking badge (<span class="badge badge-live">Shortlisted</span>) on <span class="url-pill">/dashboard</span>.</li>
            <li><strong>Academic Attachment:</strong> Review 12-month Work-Related Learning period, academic institution, and faculty records.</li>
            <li><strong>Onboarding Milestones:</strong> Inspect institutional endorsement letter and attachment logbook requirements.</li>
            <li><strong>Collaborative Tasks:</strong> Participate in live request discussions alongside supervising Associates.</li>
          </ul>
        </div>

        <!-- Journey 6 -->
        <div class="journey-card">
          <div class="journey-header">
            <div class="journey-title">Journey 6: Technical Associate Specialist</div>
            <span class="url-pill">/quick-login?as=associate</span>
          </div>
          <div class="journey-persona">Persona: <strong>Simbarashe Hove</strong> (Senior Cloud Specialist)</div>
          <ul class="journey-actions">
            <li><strong>Roster Profile Card:</strong> Track onboarding badge (<span class="badge badge-live">Interview Scheduled</span>) on <span class="url-pill">/dashboard</span>.</li>
            <li><strong>Billing Terms:</strong> Inspect approved consulting rate (<strong>USD $180.00/day</strong>) and ZIMRA ITF263 tax compliance.</li>
            <li><strong>Skill Matrix:</strong> AWS, Kubernetes, Terraform, CI/CD, and Enterprise Linux competencies.</li>
            <li><strong>Client Engagement:</strong> Review paired project tickets and supervisory apprentice reviews.</li>
          </ul>
        </div>

        <!-- Journey 7 -->
        <div class="journey-card">
          <div class="journey-header">
            <div class="journey-title">Journey 7: Career Vacancy Candidate</div>
            <span class="url-pill">/quick-login?as=candidate</span>
          </div>
          <div class="journey-persona">Persona: <strong>Farai Chikwanha</strong> (Ref: APP-TSG-VAC-2026-DEMO)</div>
          <ul class="journey-actions">
            <li><strong>Express Application Flow:</strong> Evaluates automated account generation after applying via the public portal.</li>
            <li><strong>Application Tracking Card:</strong> Real-time status pill (<span class="badge badge-live">Interview Scheduled</span>) on <span class="url-pill">/dashboard</span>.</li>
            <li><strong>Panel Interview Brief:</strong> Scheduled interview timestamp, interview location/link, and panel instructions.</li>
            <li><strong>Document Library:</strong> Submitted CV, national ID, and degree certificate archive.</li>
          </ul>
        </div>

        <!-- Journey 8 -->
        <div class="journey-card">
          <div class="journey-header">
            <div class="journey-title">Journey 8: Corporate Client Lead</div>
            <span class="url-pill">/quick-login?as=client</span>
          </div>
          <div class="journey-persona">Persona: <strong>Tinashe Gumbo</strong> (Director, EcoSolutions Zimbabwe)</div>
          <ul class="journey-actions">
            <li><strong>Client Workspace:</strong> Access organization overview (Reg: ZW-CO-2023-8871, BP: BP20098177) at <span class="url-pill">/client/portal</span>.</li>
            <li><strong>Subscribed Retainer Plans:</strong> Inspect 80 hrs/month Enterprise SLA retainer details at <span class="url-pill">/client/plans</span>.</li>
            <li><strong>Invoices & Billing Ledger:</strong> Inspect 12 months of invoices with billed/paid balances at <span class="url-pill">/client/invoices</span>.</li>
            <li><strong>Printable Tax Statements:</strong> Open <span class="url-pill">/client/invoices/view/1</span> for branded ZIMRA 15% VAT statements and bank receipts.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- SECTION 5: 1-YEAR MULTI-CLIENT SIMULATION (PORTFOLIO OVERVIEW) -->
    <div class="section">
      <div class="section-title">
        <span><span class="section-title-num">5</span> 1-Year Multi-Client & Workforce Simulation (Sep 2025 – Aug 2026)</span>
      </div>
      <p style="margin-bottom: 7px; font-size: 7.8pt;">
        To prove real-world operational scalability, the database contains a fully populated <strong>1-year operational history</strong> across 4 corporate clients, technical associates, university apprentices, and finance teams:
      </p>

      <!-- KPI STAT BOXES -->
      <div class="kpi-row">
        <div class="kpi-card">
          <div class="kpi-num">4</div>
          <div class="kpi-title">Corporate Clients</div>
          <div class="kpi-desc">CleanTech, Banking, Logistics, Health</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-num">24</div>
          <div class="kpi-title">Service Requests</div>
          <div class="kpi-desc">100% paired with Apprentices</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-num">84</div>
          <div class="kpi-title">Payslips & Transfers</div>
          <div class="kpi-desc">12 monthly payroll cycles finalized</div>
        </div>
        <div class="kpi-card">
          <div class="kpi-num">48</div>
          <div class="kpi-title">Corporate Invoices</div>
          <div class="kpi-desc">ZIMRA 15% VAT & 44 settled payments</div>
        </div>
      </div>

      <!-- CLIENT PORTFOLIO TABLE -->
      <div style="font-size: 8pt; font-weight: 700; color: #0f2b23; margin-bottom: 4px;">Corporate Client Portfolio</div>
      <table>
        <thead>
          <tr>
            <th style="width: 28%;">Client Organization</th>
            <th style="width: 16%;">Sector</th>
            <th style="width: 22%;">Registration & Tax BP</th>
            <th style="width: 24%;">Subscribed Service Plan</th>
            <th style="width: 10%;">Monthly Fee</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>EcoSolutions Zimbabwe (Pvt) Ltd</strong></td>
            <td>CleanTech & Solar</td>
            <td><code>ZW-CO-2023-8871</code> / <code>BP20098177</code></td>
            <td>Enterprise DevOps & Cloud (80 hrs/mo)</td>
            <td><strong>USD $3,500</strong></td>
          </tr>
          <tr>
            <td><strong>ZimFin Microfinance Bank</strong></td>
            <td>Banking & Fintech</td>
            <td><code>ZW-BK-2021-4402</code> / <code>BP20044182</code></td>
            <td>Core Banking Security & SLA (120 hrs/mo)</td>
            <td><strong>USD $4,800</strong></td>
          </tr>
          <tr>
            <td><strong>Delta Tech Logistics</strong></td>
            <td>Supply Chain & IoT</td>
            <td><code>ZW-LT-2022-3199</code> / <code>BP20077391</code></td>
            <td>Fleet Telematics & APIs (60 hrs/mo)</td>
            <td><strong>USD $2,800</strong></td>
          </tr>
          <tr>
            <td><strong>AfriHealth Telemedicine</strong></td>
            <td>HealthTech</td>
            <td><code>ZW-MD-2024-1188</code> / <code>BP20011504</code></td>
            <td>Telehealth Backend & Compliance (75 hrs/mo)</td>
            <td><strong>USD $3,200</strong></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ==================== PAGE 4: SIMULATION OPERATIONS, ARCHITECTURE & CERTIFICATION ==================== -->
  <div class="page-container">
    <!-- SECTION 5 CONT: SIMULATION DEEP DIVE -->
    <div class="section">
      <div class="section-title">
        <span><span class="section-title-num">5b</span> Simulation Operations — Mentorship, Payroll & Tax Billing</span>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 9px;">
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 7px 9px;">
          <strong style="color: #0f2b23; font-size: 8pt;">Apprentice Mentorship & Delivery</strong>
          <ul style="font-size: 7.2pt; color: #334155; margin-top: 3px; padding-left: 12px; line-height: 1.32;">
            <li><strong>Supervised Resource Pairing:</strong> Every service request pairs a Senior Associate with a university Apprentice, tracked via normalized <span class="url-pill">assignmentsupervisor</span> records.</li>
            <li><strong>139+ Threaded Messages:</strong> Direct collaboration messages (<span class="url-pill">requestmessage</span>) linking clients, associates, apprentices, and managers.</li>
            <li><strong>Formal Ticket Sign-Offs:</strong> Tickets conclude with stakeholder completion notes and 5-star ratings (<span class="url-pill">servicerequestclosure</span>).</li>
          </ul>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 7px 9px;">
          <strong style="color: #0f2b23; font-size: 8pt;">Financial Cycles & Tax Invoicing</strong>
          <ul style="font-size: 7.2pt; color: #334155; margin-top: 3px; padding-left: 12px; line-height: 1.32;">
            <li><strong>12 Payroll Runs (Sep 2025 – Aug 2026):</strong> 84 payslips covering staff salaries, apprentice stipends/bonuses, and associate deliverable milestones.</li>
            <li><strong>Itemized Corporate Invoices:</strong> Base retainer, associate hours, apprentice hours, and excess capacity hours (<span class="url-pill">clientinvoiceitem</span>).</li>
            <li><strong>ZIMRA 15% VAT & Electronic Receipts:</strong> 44 completed bank settlements via Stanbic, CABS, CBZ, and EcoBank. Month 12 is active as *Payment Due*.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- SECTION 6: SECURITY & SESSION INTEGRITY -->
    <div class="section">
      <div class="section-title">
        <span><span class="section-title-num">6</span> Security & Session Integrity Architecture</span>
      </div>
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 7px; margin-bottom: 9px;">
        <div style="border: 1px solid #e2e8f0; border-radius: 5px; padding: 7px; background: #ffffff;">
          <strong style="font-size: 7.6pt; color: #0f2b23;">HMAC-SHA256 Signatures</strong>
          <p style="font-size: 6.9pt; color: #475569; margin-top: 2px;">Cryptographically binds session payloads to user IP and User-Agent, preventing cookie tampering and replay attacks.</p>
        </div>
        <div style="border: 1px solid #e2e8f0; border-radius: 5px; padding: 7px; background: #ffffff;">
          <strong style="font-size: 7.6pt; color: #0f2b23;">Idle Session Soft-Lock</strong>
          <p style="font-size: 6.9pt; color: #475569; margin-top: 2px;">Automatic protection after 30 minutes of inactivity; securely preserves UI state while requiring password verification.</p>
        </div>
        <div style="border: 1px solid #e2e8f0; border-radius: 5px; padding: 7px; background: #ffffff;">
          <strong style="font-size: 7.6pt; color: #0f2b23;">Granular RBAC Policies</strong>
          <p style="font-size: 6.9pt; color: #475569; margin-top: 2px;">Strict database-level segregation separating internal staff tiers, corporate clients, and public talent applicants.</p>
        </div>
      </div>
    </div>

    <!-- SECTION 7: REGULATORY COMPLIANCE & TECH STACK -->
    <div class="section">
      <div class="section-title">
        <span><span class="section-title-num">7</span> Regulatory Standards & Architecture Stack</span>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 7px; margin-bottom: 9px;">
        <div style="border: 1px solid #e2e8f0; border-radius: 5px; padding: 7px; background: #f8fafc;">
          <strong style="font-size: 7.6pt; color: #0f2b23;">Zimbabwe Regulatory Alignment</strong>
          <ul style="font-size: 6.9pt; color: #334155; margin-top: 3px; padding-left: 12px; line-height: 1.3;">
            <li><strong>ZIMRA VAT Compliance:</strong> Standard 15% VAT on all corporate invoices with Business Partner (BP) validation.</li>
            <li><strong>NSSA Statutory P4:</strong> Monthly automated export for National Social Security Authority pension filings.</li>
            <li><strong>CID Police Vetting:</strong> Criminal vetting and clearance certificate validation for all placed talent.</li>
          </ul>
        </div>
        <div style="border: 1px solid #e2e8f0; border-radius: 5px; padding: 7px; background: #f8fafc;">
          <strong style="font-size: 7.6pt; color: #0f2b23;">Core Technology Stack</strong>
          <ul style="font-size: 6.9pt; color: #334155; margin-top: 3px; padding-left: 12px; line-height: 1.3;">
            <li><strong>Application:</strong> PHP 8.2+ custom MVC architecture, PSR-4 autoloading, and Active-Record ORM.</li>
            <li><strong>Database:</strong> Relational ACID SQLite 3 with normalized schema and auto-migrating tables.</li>
            <li><strong>Hosting:</strong> Enterprise Linux / Apache Web Server with TLS/HTTPS cryptographic enforcement.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- CALLOUT CHECKLIST & SIGN OFF -->
    <div class="callout">
      <div class="callout-title">
        <span>🔐 Evaluator Instructions & Verification Checklist</span>
      </div>
      <div style="font-size: 7.2pt; line-height: 1.32;">
        To evaluate any role, navigate to <span class="url-pill">https://portal.tsigiro.co.zw/login</span> and click any persona under <strong>Role-Based Demo Accounts</strong> to sign in automatically. All records, invoices, payslips, tickets, and supervisory links are cross-referenced across the relational database.
      </div>
    </div>

    <!-- EXECUTIVE CERTIFICATION BADGE -->
    <div style="display: flex; justify-content: space-between; align-items: center; border: 1px dashed #cbd5e1; border-radius: 5px; padding: 6px 12px; margin-top: 6px; background: #ffffff;">
      <div style="font-size: 7pt; color: #64748b;">
        <strong>Audit Certification:</strong> System Architecture, Relational Schema, and Live Production Services Validated.
      </div>
      <div style="display: flex; gap: 8px; align-items: center;">
        <span style="font-size: 7pt; font-weight: 700; color: #0f2b23;">Status:</span>
        <span class="badge badge-live" style="font-size: 7pt;">PRODUCTION READY & CERTIFIED</span>
      </div>
    </div>

    <!-- FOOTER -->
    <div class="footer-note" style="margin-top: 8px;">
      Tsigiro Portal Executive Walkthrough & Testing Guide &bull; Generated September 2026 &bull; Production: <a href="https://portal.tsigiro.co.zw" style="color: #0369a1; text-decoration: none;">portal.tsigiro.co.zw</a> &bull; Confidential & Proprietary
    </div>
  </div>

</body>
</html>
"""

def main():
    print("Writing temporary rendering HTML...")
    html_path = os.path.join("/tmp", HTML_FILENAME)
    with open(html_path, "w", encoding="utf-8") as f:
        f.write(HTML_CONTENT)

    target_pdf = os.path.join(OUTPUT_DIR, PDF_FILENAME)
    print(f"Rendering PDF to {target_pdf} using Microsoft Edge headless...")

    edge_bin = "/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge"
    if not os.path.exists(edge_bin):
        print(f"Error: Microsoft Edge not found at {edge_bin}")
        sys.exit(1)

    cmd = [
        edge_bin,
        "--headless",
        "--disable-gpu",
        "--no-pdf-header-footer",
        f"--print-to-pdf={target_pdf}",
        f"file://{html_path}"
    ]

    result = subprocess.run(cmd, capture_output=True, text=True)
    if not os.path.exists(target_pdf) or os.path.getsize(target_pdf) == 0:
        print(f"Failed to generate PDF. Return code: {result.returncode}")
        print("Stderr:", result.stderr)
        sys.exit(1)

    size = os.path.getsize(target_pdf)
    print(f"Successfully generated {target_pdf} ({size:,} bytes).")

    # Sync to local mirror
    if os.path.exists(MIRROR_DIR):
        mirror_pdf = os.path.join(MIRROR_DIR, PDF_FILENAME)
        shutil.copy2(target_pdf, mirror_pdf)
        print(f"Synced to local mirror: {mirror_pdf}")

    # Copy to artifact directory
    if os.path.exists(ARTIFACT_DIR):
        artifact_pdf = os.path.join(ARTIFACT_DIR, PDF_FILENAME)
        shutil.copy2(target_pdf, artifact_pdf)
        print(f"Copied to artifact directory: {artifact_pdf}")

if __name__ == "__main__":
    main()
