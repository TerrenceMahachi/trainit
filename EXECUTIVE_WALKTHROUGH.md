# Tsigiro Portal — Executive Walkthrough & Testing Guide

**Audience:** Executive Leadership, Project Evaluators, Stakeholders, and Systems Auditors  
**Standard Password for All Demo Accounts:** `Password123!`  

---

## 1. Executive Summary

**Tsigiro Portal** is an enterprise workforce, talent vetting, and client engagement platform operating in Zimbabwe. The platform unifies five core business capabilities:

1. **Talent Acquisition & Opportunities:** Public-facing recruitment portal for career vacancies, Work-Related Learning (WRL) university apprenticeships, and independent Associate specialists.
2. **Vetting & Compliance Governance:** Comprehensive verification workflows enforcing Zimbabwe legal standards (police clearances, academic qualifications, and ZIMRA ITF263 tax clearances).
3. **Roster & Engagement Management:** Service delivery orchestration matching verified specialists and apprentices to corporate client projects under managed service plans.
4. **Client Enterprise Portal:** Self-service portal for corporate clients to oversee engaged resources, contract milestones, service tickets, and retainers.
5. **Financial Operations & Payroll:** Normalized salary and disbursement pipelines handling associate payouts and statutory filings.

---

## 2. Implementation Status Audit (Live vs. In-Progress)

To provide full transparency to evaluators, here is the current operational status of every subsystem referenced in this guide:

| Module / Feature | Implementation Status | Testable Screens / URLs | Notes |
| :--- | :---: | :--- | :--- |
| **Testing Accounts & Instant Login** | ✅ **100% Live** | `/login`, `/quick-login?as={role}` | All 8 roles seeded with relational data; 1-click login and pre-fill buttons active. |
| **Public Opportunities Hub** | ✅ **100% Live** | `/opportunities`, `/opportunities/vacancies` | Responsive public catalog with search, filters, and track selection. |
| **Express Candidate Applications** | ✅ **100% Live** | `/opportunities/vacancy/:slug`, `/opportunities/apply/*` | Single-click application submission with automated candidate account creation. |
| **Unified Candidate Dashboard** | ✅ **100% Live** | `/dashboard` (as Candidate / Apprentice / Associate) | Multi-track application cards, interview alerts, status badges, and onboarding links. |
| **Admin Command Center** | ✅ **100% Live** | `/dashboard` (as Admin) | Real-time intake metrics, staff directory links, and reference dictionary controls. |
| **Vacancy Management** | ✅ **100% Live** | `/admin/vacancies`, `/admin/vacancies/create` | Full CRUD for job postings, qualification definitions, and applicant review. |
| **Vetting Pipeline & Scoring** | ✅ **100% Live** | `/admin/roster`, `/admin/roster/review?id=:id` | 100-point applicant scoring, police clearance checks, and vetting checklists. |
| **Client Operations Desk** | ✅ **100% Live** | `/admin/clients`, `/admin/requests` | Corporate client profiles, retainer subscriptions, and SLA service catalogue. |
| **Client Organization Portal** | ✅ **100% Live** | `/client/portal`, `/client/requests`, `/client/plans` | EcoSolutions Zimbabwe workspace, authorized team roster, and work request builder. |
| **Payroll & Disbursements** | ✅ **100% Live** | `/admin/payroll`, `/admin/payroll/view/:id` | Pay cycle calculation, executive approval, bank disbursement ledger, and NSSA P4 export. |
| **Corporate PDF Invoicing** | ⏳ **Roadmap** | `/invoiceentitytypes` (Normalized dictionary live) | Retainer plans and hours are tracked live; automated client PDF invoice generation is scheduled for Phase 2. |
| **Client Self-Onboarding** | 🔄 **External** | External onboarding system | Intentionally decoupled per scope; client onboarding workflow is maintained by a separate partner team. |

---

## 3. Pre-Configured Testing Accounts

Each account below has been seeded with realistic relational records, operational statuses, and documents. **All accounts share the password `Password123!`**, or can be accessed instantly using the **One-Click Quick Login** links on the login screen or via the direct URLs below.

| Role | Business Persona | Demo Email | Direct Quick-Login URL | Primary Evaluation Scope |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** (Role 1) | System Administrator | `admin@tsigiro.co.zw` | [`/quick-login?as=admin`](http://localhost/tsigiro/portal/quick-login?as=admin) | Master command center, opportunity publishing, user roles, system audit log |
| **Vetting Officer** (Role 8) | Ruvimbo Sithole | `vetting@tsigiro.co.zw` | [`/quick-login?as=vetting`](http://localhost/tsigiro/portal/quick-login?as=vetting) | Candidate vetting, police clearances, qualification checks, checklist sign-offs |
| **Service Manager** (Role 6) | Tafadzwa Mutasa | `manager@tsigiro.co.zw` | [`/quick-login?as=manager`](http://localhost/tsigiro/portal/quick-login?as=manager) | Client service plans, SLA delivery, resource allocation, roster management |
| **Billing Officer** (Role 7) | Nyasha Chidziwa | `finance@tsigiro.co.zw` | [`/quick-login?as=finance`](http://localhost/tsigiro/portal/quick-login?as=finance) | Payroll calculation, approval, disbursements, ITF263 tax compliance, NSSA P4 export |
| **Apprentice** (Role 2) | Kudzai Mapfumo (NUST) | `apprentice@tsigiro.co.zw` | [`/quick-login?as=apprentice`](http://localhost/tsigiro/portal/quick-login?as=apprentice) | University WRL attachment tracker, application status (*Shortlisted*), onboarding steps |
| **Associate** (Role 2) | Simbarashe Hove (Cloud) | `associate@tsigiro.co.zw` | [`/quick-login?as=associate`](http://localhost/tsigiro/portal/quick-login?as=associate) | Specialist roster profile, daily rate ($180/day), tax compliance, interview schedule |
| **Job Candidate** (Role 2) | Farai Chikwanha | `candidate@tsigiro.co.zw` | [`/quick-login?as=candidate`](http://localhost/tsigiro/portal/quick-login?as=candidate) | Vacancy applicant tracking (*APP-TSG-VAC-2026-DEMO*), panel interview status |
| **Client Owner** (Role 3) | Tinashe Gumbo (EcoSolutions) | `client@tsigiro.co.zw` | [`/quick-login?as=client`](http://localhost/tsigiro/portal/quick-login?as=client) | Corporate Client Portal, organization profile, team members, service plan oversight |

---

## 4. Quick-Access Login Page Feature

On the web login screen (`/login`):
* **One-Click Role Sign In:** Click any role button in the **Role-Based Demo Accounts** column to authenticate and jump directly into that user's customized workspace without typing.
* **Pre-fill Helper:** Click the **Pre-fill** button next to any user to automatically populate the sign-in form with that email and `Password123!`.

---

## 5. Guided Evaluation Journeys by Role

### Journey 1: Corporate Governance & System Administrator
* **Sign In:** `/quick-login?as=admin`
* **Landing Screen:** Administrator Command Center (`/dashboard`)
* **Key Capabilities to Evaluate:**
  1. **Opportunities & Vacancies:** Navigate to **Recruitment / Vacancies** (`/admin/vacancies`) to publish new job openings, configure qualification requirements, and set application deadlines.
  2. **Applicant Oversight:** Review submitted applications across all three pipelines via the Talent Pipeline Console (`/admin/roster`).
  3. **Staff & Role Management:** View internal staff profiles across Operations, Finance, and Vetting departments (`/admin/staff`).
  4. **System Metrics:** High-level summary of active clients, deployed specialists, and pending vetting cases.

---

### Journey 2: Compliance & Candidate Vetting (Vetting Officer)
* **Sign In:** `/quick-login?as=vetting`
* **Persona:** Ruvimbo Sithole (`TSG-STF-008`, Operations / Vetting Department)
* **Landing Screen:** Vetting Officer Dashboard (`/dashboard`)
* **Key Capabilities to Evaluate:**
  1. **Vetting Queue:** Open the Talent Pipeline Console (`/admin/roster`) to inspect applicants requiring background checks.
  2. **Candidate Review:** Click **Review** on any candidate (`/admin/roster/review?id=:id`) to inspect submitted credentials, police clearances, and qualification certificates.
  3. **100-Point Scoring:** Input objective scores across criteria (experience, qualifications, interview panel) and select vetting recommendations (*Shortlist*, *Flag*, *Admit to Roster*).

---

### Journey 3: Client Operations & Service Delivery (Service Manager)
* **Sign In:** `/quick-login?as=manager`
* **Persona:** Tafadzwa Mutasa (`TSG-STF-006`, Operations Department)
* **Landing Screen:** Service Manager Command Center (`/dashboard`)
* **Key Capabilities to Evaluate:**
  1. **Client Service Plans:** Access Client Accounts (`/admin/clients`) to monitor corporate client retainers and SLA parameters.
  2. **Service Catalogue:** View standard service modules and SLA policies (`/admin/service-catalogue`).
  3. **Talent Allocation:** Review active talent roster bench (Associates & Apprentices) available for client deployment.

---

### Journey 4: Billing & Financial Controls (Billing Officer)
* **Sign In:** `/quick-login?as=finance`
* **Persona:** Nyasha Chidziwa (`TSG-STF-007`, Finance Department)
* **Landing Screen:** Finance Command Center (`/dashboard`)
* **Key Capabilities to Evaluate:**
  1. **Payroll Cycles:** Navigate to Payroll Management (`/admin/payroll`) to inspect active pay periods, total gross pay, statutory deductions, and net disbursements.
  2. **Pay Period Ledger:** View individual staff payslips (`/admin/payroll/payslip/:id`) with breakdowns of PAYE tax, NSSA pension, and medical aid.
  3. **Statutory Export:** Export the official Zimbabwean NSSA Form P4 return (`/admin/staff/export-p4`).
  4. **Tax Compliance Check:** Inspect Associate profiles for ZIMRA ITF263 tax clearance certificates and registered billing rates.

---

### Journey 5: University Apprentice Experience (WRL Student)
* **Sign In:** `/quick-login?as=apprentice`
* **Persona:** Kudzai Mapfumo (National University of Science and Technology — NUST, Computer Science)
* **Landing Screen:** Candidate Tracking Dashboard (`/dashboard`)
* **Key Capabilities to Evaluate:**
  1. **Application Status Card:** View real-time tracking badge (*Status: Shortlisted*).
  2. **Academic Attachment Details:** 12-month Work-Related Learning period, academic institution, degree program.
  3. **Onboarding Milestones:** Submission of institutional introductory letter and logbook tracking.

---

### Journey 6: Technical Specialist (Associate Specialist)
* **Sign In:** `/quick-login?as=associate`
* **Persona:** Simbarashe Hove (Senior Cloud Specialist, 8+ years experience)
* **Landing Screen:** Candidate Tracking Dashboard (`/dashboard`)
* **Key Capabilities to Evaluate:**
  1. **Specialist Portal Card:** Status badge (*Interview Scheduled* with upcoming panel interview timestamp).
  2. **Contract & Billing Parameters:** Standard daily billing rate of **USD \$180.00**, verified ITF263 tax compliance.
  3. **Skill Matrix:** AWS, Kubernetes, Terraform, and Enterprise Linux capabilities.

---

### Journey 7: Job Vacancy Candidate
* **Sign In:** `/quick-login?as=candidate`
* **Persona:** Farai Chikwanha (Applicant for *Talent Operations & Vetting Officer*, Ref: `APP-TSG-VAC-2026-DEMO`)
* **Landing Screen:** Candidate Tracking Dashboard (`/dashboard`)
* **Key Capabilities to Evaluate:**
  1. **Application Tracking:** Visual status badge (*Interview Scheduled*).
  2. **Interview Brief:** Scheduled panel interview time, interview location/link, and evaluation notes.
  3. **Seamless Self-Service:** Automatic applicant account creation upon applying via the public `/opportunities` portal.

---

### Journey 8: Corporate Client Organization Lead
* **Sign In:** `/quick-login?as=client`
* **Persona:** Tinashe Gumbo (Director / Owner at **EcoSolutions Zimbabwe (Pvt) Ltd**)
* **Landing Screen:** Client Organization Portal (`/client/portal`)
* **Key Capabilities to Evaluate:**
  1. **Corporate Dashboard:** Overview of EcoSolutions Zimbabwe (Reg: `ZW-CO-2023-8871`, BP: `BP20098177`).
  2. **Client Retainer Plans:** View subscribed service plans, monthly capacity hours, and active retainers (`/client/plans`).
  3. **Work Request Brief Builder:** Submit new project briefs with priority tiers and deliverable milestones (`/client/requests/new`).
  4. **Team Roster:** Manage authorized organization contacts and their access tiers (`/client/team`).

---

## 6. Security & Session Integrity Architecture

The evaluation environment incorporates Tsigiro's security safeguards:
* **Tamper-Proof Authentication:** Cryptographic HMAC-SHA256 signature binding user sessions to prevent cookie manipulation.
* **Idle Session Soft-Lock:** Automatic protection after 30 minutes of inactivity redirecting to the `/resume` challenge.
* **Granular Role-Based Access Control (RBAC):** Strict separation between internal staff tiers, corporate clients, and talent applicants.
