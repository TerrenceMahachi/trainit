# Tsigiro Portal — Executive Walkthrough & Testing Guide

> [!TIP]
> **Executive PDF Edition Available:**
> A formatted 4-page executive PDF edition of this guide has been generated:
> * **Local PDF:** [`TSIGIROS_EXECUTIVE_WALKTHROUGH_GUIDE.pdf`](./TSIGIROS_EXECUTIVE_WALKTHROUGH_GUIDE.pdf)
> * **Live Production URL:** [https://portal.tsigiro.co.zw/TSIGIROS_EXECUTIVE_WALKTHROUGH_GUIDE.pdf](https://portal.tsigiro.co.zw/TSIGIROS_EXECUTIVE_WALKTHROUGH_GUIDE.pdf)

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
| **Client Invoicing & Tax Billing** | ✅ **100% Live** | `/client/invoices`, `/client/invoices/view/:id`, `/admin/invoices` | 48 itemized corporate tax invoices across 4 clients with ZIMRA 15% VAT, line items, and electronic bank settlements. |
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
  1. **Payroll Cycles:** Navigate to Payroll Management (`/admin/payroll`) to inspect 12 monthly pay periods (Sep 2025 – Aug 2026), total gross pay, statutory deductions, and net disbursements across 84 payslips.
  2. **Pay Period Ledger:** View individual staff and associate payslips (`/admin/payroll/view/:id`) with breakdowns of PAYE tax, NSSA pension, and medical aid.
  3. **Client Invoicing Ledger:** Review corporate client invoices, payment statuses, and ZIMRA 15% VAT breakdown at `/admin/invoices` or `/client/invoices`.
  4. **Statutory Export:** Export the official Zimbabwean NSSA Form P4 return (`/admin/staff/export-p4`).
  5. **Tax Compliance Check:** Inspect Associate profiles for ZIMRA ITF263 tax clearance certificates and registered billing rates.

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
  3. **Invoices & Billing Ledger:** Access the **Invoices & Billing** tab (`/client/invoices`) to inspect 12 months of itemized tax invoices with total billed metrics, settled amounts, and pending balances.
  4. **Printable Tax Invoices:** Click **View Invoice** on any record (e.g. `/client/invoices/view/1`) to render the branded Zimbabwean tax invoice statement complete with ZIMRA 15% VAT, line item hours, banking settlement details, and electronic receipt proof.
  5. **Work Request Brief Builder:** Submit new project briefs with priority tiers and deliverable milestones (`/client/requests/new`).
  6. **Team Roster:** Manage authorized organization contacts and their access tiers (`/client/team`).

---

## 6. Security & Session Integrity Architecture

The evaluation environment incorporates Tsigiro's security safeguards:
* **Tamper-Proof Authentication:** Cryptographic HMAC-SHA256 signature binding user sessions to prevent cookie manipulation.
* **Idle Session Soft-Lock:** Automatic protection after 30 minutes of inactivity redirecting to the `/resume` challenge.
* **Granular Role-Based Access Control (RBAC):** Strict separation between internal staff tiers, corporate clients, and talent applicants.

---

## 7. 1-Year Multi-Client & Workforce Simulation (Sep 2025 – Aug 2026)

To demonstrate real-world operational scale, the database has been seeded with a full **1-year simulated operational history** across 4 corporate clients, associates, apprentices, and finance teams:

### 1. Corporate Client Portfolio
| Client Organization | Sector | Registration / Tax BP | Service Plan | Monthly Fee |
| :--- | :--- | :--- | :--- | :--- |
| **EcoSolutions Zimbabwe (Pvt) Ltd** | CleanTech & Solar | `ZW-CO-2023-8871` / `BP20098177` | Enterprise DevOps & Infrastructure (80 hrs/mo) | USD \$3,500.00 |
| **ZimFin Microfinance Bank** | Banking & Fintech | `ZW-BK-2021-4402` / `BP20044182` | Core Banking Security & Cloud SLA (120 hrs/mo) | USD \$4,800.00 |
| **Delta Tech Logistics** | Supply Chain & IoT | `ZW-LT-2022-3199` / `BP20077391` | Fleet Telematics & API Reliability (60 hrs/mo) | USD \$2,800.00 |
| **AfriHealth Telemedicine** | HealthTech & Clinical | `ZW-MD-2024-1188` / `BP20011504` | Telehealth Mobile Backend & Compliance (75 hrs/mo) | USD \$3,200.00 |

### 2. Service Requests & Collaborative Interactions
* **24 Full-Lifecycle Service Requests:** Spanning infrastructure modernization, microfinance integrations, biometric telemetry, and clinic portals.
* **Paired Apprenticeship Mentorship:** Every request pairs an experienced Associate Specialist (`Simbarashe Hove`, `Ruvimbo Chitepo`) with a university Apprentice (`Kudzai Mapfumo`, `Tariro Moyo`), linked via normalized supervisor oversight records (`assignmentsupervisor`).
* **139+ Live Threaded Messages:** Client stakeholders, service managers, associates, and apprentices collaborate directly via contextual request communication threads (`requestmessage`).
* **Closed Ticket Sign-Offs:** Tickets conclude with formal stakeholder sign-offs, closure notes, and 5-star quality ratings (`servicerequestclosure`).

### 3. Payroll Operations & Worker Disbursements
* **12 Monthly Payroll Cycles:** September 2025 through August 2026 (`TSG-PAY-2025-09` to `TSG-PAY-2026-08`), each formally approved and finalized.
* **84 Detailed Payslips:**
  - Internal Staff: Base executive salaries with PAYE, NSSA pension, and medical aid deductions.
  - Apprentices: Monthly living stipends plus performance project bonuses.
  - Associates: Billable deliverable milestones and engagement payouts.
* **84 Bank Disbursements:** Each payslip is accompanied by a settled bank disbursement record (`payslipdisbursement`) with unique transaction references.

### 4. Client Tax Invoicing & Settlements
* **48 Itemized Corporate Invoices:** Monthly tax invoices (`clientinvoice`) generated across all 4 clients.
* **ZIMRA Statutory Compliance:** Standard 15% Zimbabwean Value Added Tax (VAT) itemized on every invoice with Tsigiro's and Client's Tax BP numbers.
* **Itemized Service Lines:** Retainer baseline fees, Associate specialist consulting hours, Apprentice support hours, and approved SLA excess hours (`clientinvoiceitem`).
* **44 Bank Settlement Records:** Months 1 through 11 feature settled electronic transfers via Stanbic Bank, CABS, CBZ Bank, and EcoBank (`clientinvoicepayment`). Month 12 (August 2026) remains active with status *Payment Due* to test live payment flows.

---

## 8. Dynamic Tax Invoicing, POP Submission & Reconciliation Desk (NEW)

The invoicing pipeline now features full-lifecycle dynamic document generation, client settlement submissions, and finance officer reconciliation:

### 1. Dynamic 1-Click Tax Invoice PDF / Print Generation
* **Direct Access Endpoints:**
  - Client Portal: [`/client/invoices/pdf/:id`](https://portal.tsigiro.co.zw/client/invoices/pdf/45)
  - Staff / Admin Console: [`/admin/invoices/pdf/:id`](https://portal.tsigiro.co.zw/admin/invoices/pdf/45)
* **Official ZIMRA Layout Features:**
  - Tsigiro brand identity with vector emblem, registration details, and ZIMRA Tax BP `BP20088921` / VAT `10049281`.
  - Client organization details with legal name, company registration number, and tax clearance ID.
  - Itemized service breakdown with billable hours, hourly rates, and deliverables.
  - Value Added Tax computation (standard 15% Zimbabwe VAT) and Nostro USD grand total.
  - Official Stanbic Bank Nostro USD settlement account details (Account: `9140003882910`, Swift: `SBICZWHX`).
  - Native browser auto-print preview with a responsive floating action bar (`Print / Save as PDF` / `Close`).

### 2. Client Proof of Payment (POP) Submission
* **Endpoint:** `POST /client/invoices/submit-payment`
* **Workflow:**
  1. Corporate client navigates to an unpaid invoice (e.g., [Invoice #45](https://portal.tsigiro.co.zw/client/invoices/view/45)).
  2. The dedicated **"Settle Invoice / Submit Proof of Payment (POP)"** card allows the client to provide their payment method (Stanbic Bank, EcoCash Nostro, CABS, etc.), bank transaction reference, amount, payment date, optional internal notes, and receipt document (`.pdf`, `.jpg`, `.png`).
  3. Uploaded receipts are securely stored in `uploads/receipts/` and linked directly to the audit log.
  4. The invoice instantly transitions to **PAID & SETTLED** (`payment_status = 2`), recording a permanent electronic confirmation badge visible on both the web view and downloaded PDF statement.

### 3. Billing Desk Reconciliation Controls (Internal Staff)
* **Endpoint:** `POST /admin/invoices/reconcile-payment`
* **Access Control:** Restricted strictly to internal staff and finance controllers (`Auth::isStaff()`).
* **Capabilities:**
  - **Certify & Settle:** Verify incoming Nostro bank statements against pending invoices and certify them as settled.
  - **Reopen Invoice:** If a client submits invalid or disputed payment details, finance officers can reopen the invoice back to **Payment Due** (`payment_status = 1`) with one click, resetting the paid date and allowing re-submission.

### 4. Verified Testing Scenarios
| Role | Account | Testing Link | Expected Result |
| :--- | :--- | :--- | :--- |
| **Client Lead** | `client@tsigiro.co.zw` | [Invoice #45 View](https://portal.tsigiro.co.zw/client/invoices/view/45) | Inspect settled invoice with POP confirmation badge and receipt details. |
| **Client Lead** | `client@tsigiro.co.zw` | [Invoice #45 Official PDF](https://portal.tsigiro.co.zw/client/invoices/pdf/45) | View print-ready A4 Tax Invoice with ZIMRA BP and Stanbic Nostro settlement box. |
| **Finance Officer** | `finance@tsigiro.co.zw` | [Invoice #46 Reconcile](https://portal.tsigiro.co.zw/client/invoices/view/46) | Test the Billing Desk reconciliation card to certify or reopen unpaid invoices. |
| **Admin** | `admin@tsigiro.co.zw` | [Admin Invoices Ledger](https://portal.tsigiro.co.zw/admin/invoices) | Oversee portfolio-wide billing, download PDF statements, and inspect audit logs. |

