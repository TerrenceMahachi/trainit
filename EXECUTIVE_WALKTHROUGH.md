# Tsigiro Portal — Executive Walkthrough & Testing Guide

> [!TIP]
> **Executive PDF Edition Available:**
> A 4-page executive print document has been compiled and published:
> * **Local File:** [TSIGIROS_EXECUTIVE_WALKTHROUGH_GUIDE.pdf](file:///Library/WebServer/Documents/trainit/TSIGIROS_EXECUTIVE_WALKTHROUGH_GUIDE.pdf)
> * **Live Production URL:** [https://portal.tsigiro.co.zw/TSIGIROS_EXECUTIVE_WALKTHROUGH_GUIDE.pdf](https://portal.tsigiro.co.zw/TSIGIROS_EXECUTIVE_WALKTHROUGH_GUIDE.pdf)

````carousel
![Page 1 - Executive Summary & Implementation Status](/Users/terrencemahachi/.gemini/antigravity/brain/1a959035-59a1-410c-8002-1623b7a9cc82/guide_page_1.png)
<!-- slide -->
![Page 2 - Testing Personas & Internal Staff Journeys](/Users/terrencemahachi/.gemini/antigravity/brain/1a959035-59a1-410c-8002-1623b7a9cc82/guide_page_2.png)
<!-- slide -->
![Page 3 - Candidate/Client Journeys & 1-Year Simulation Portfolio](/Users/terrencemahachi/.gemini/antigravity/brain/1a959035-59a1-410c-8002-1623b7a9cc82/guide_page_3.png)
<!-- slide -->
![Page 4 - Operations Deep-Dive, Security Architecture & Certification](/Users/terrencemahachi/.gemini/antigravity/brain/1a959035-59a1-410c-8002-1623b7a9cc82/guide_page_4.png)
````

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

---

## 9. Role-Aware Menus, Navigation & Departmental Dashboards (NEW)

The portal navigation bar and dashboard command centers now dynamically adapt according to the authenticated user's role:

### 1. Granular Role Tags in Navigation (`config/nav.php` & `views/partials/nav.php`)
Menu items and dropdown groups now map to specific role identifiers:
- **Vetting Officer (`vetting`, Role 8):**
  - Top Navigation: `Opportunities`, `Mobile App`, `Dashboard`, `Staff Portal`, and dedicated **`Vetting Desk`** dropdown (`Talent Vetting Pipeline`, `Compliance Radar & Expiry Tracker`, `Recruitment & Vacancies`, `Candidate Vacancy Alerts`).
  - Completely hidden from: `Staff Directory`, `Staff Payroll`, `NSSA P4 Export`, `Client Retainers`, `Delivery Desk`, and boilerplate `System Dictionaries`.
- **Service Delivery Manager (`manager`, Role 6):**
  - Top Navigation: `Opportunities`, `Mobile App`, `Dashboard`, `Staff Portal`, and dedicated **`Service Desk`** dropdown (`Service Requests & Dispatch`, `Client Organizations & Retainers`, `Client Onboarding Queue`, `Service Catalogue & SLA`, `Talent Roster Review`).
  - Completely hidden from: `Staff Payroll`, `NSSA P4 Export`, `System Dictionaries`, etc.
- **Billing & Finance Officer (`finance`, Role 7):**
  - Top Navigation: `Opportunities`, `Mobile App`, `Dashboard`, `Staff Portal`, and dedicated **`Billing Desk`** dropdown (`Client Invoices & Settlements`, `Staff Payroll & Payslips`, `NSSA Form P4 Export`, `Client Organizations & Retainers`).
  - Completely hidden from: `Talent Pipeline scoring rubrics`, `Service dispatch`, and `System Dictionaries`.
- **System Administrator (`admin`, Role 1):**
  - Full governance access with the complete `Admin` command dropdown and `Staff Directory`.
- **Client Organization User (`client`, Role 3):**
  - Dedicated **`Client Desk`** dropdown (`Overview Workspace`, `Work Requests`, `Submit Request`, `Retainer Plans`, `Team Roster`, `Tax Invoices`).
- **Candidates / General Users (`user`, Role 2, 4, 5):**
  - Simplified navigation: `Opportunities`, `Mobile App`, `Dashboard`.

### 2. Departmental Command Center Dashboards (`routes/web.php` & `views/dashboard/`)
The universal `/dashboard` route automatically delivers a tailored operational workspace:
1. **Vetting & Compliance Command Center (`views/dashboard/vetting.php`):**
   - KPI counters: Total Applications, Pending Vetting, Admitted on Roster, and Compliance Radar alerts.
   - Priority review queue with 1-click **"Review Dossier"** links to candidate scoring consoles.
   - Quick tools for Talent Pipeline, Compliance Radar, Vacancies, and Candidate Alerts.
2. **Service Delivery Dashboard (`views/dashboard/manager.php`):**
   - Retainer plan tracking, active service ticket ledger, team allocations, and SLA dispatch tools.
3. **Finance & Billing Dashboard (`views/dashboard/finance.php`):**
   - Total invoiced vs settled collections counters, recent corporate tax invoices, payroll runs, and NSSA P4 exports.
4. **Administrator Dashboard (`views/dashboard/admin.php`):**
   - Multi-departmental overview with executive analytics, staff registration hub, client delivery desk, and dictionary governance.

### 3. Verification & Testing Personas
| Role | Account | Live Dashboard Link | Key Menus Displayed |
| :--- | :--- | :--- | :--- |
| **Vetting Officer** | `vetting@tsigiro.co.zw` | [Vetting Dashboard](https://portal.tsigiro.co.zw/dashboard) | `Vetting Desk`, `Staff Portal` |
| **Service Manager** | `manager@tsigiro.co.zw` | [Service Dashboard](https://portal.tsigiro.co.zw/dashboard) | `Service Desk`, `Staff Portal` |
| **Finance Officer** | `finance@tsigiro.co.zw` | [Finance Dashboard](https://portal.tsigiro.co.zw/dashboard) | `Billing Desk`, `Staff Portal` |
| **Administrator** | `admin@tsigiro.co.zw` | [Admin Dashboard](https://portal.tsigiro.co.zw/dashboard) | `Admin` (16 tools), `Staff Directory` |
| **Client Lead** | `client@tsigiro.co.zw` | [Client Portal](https://portal.tsigiro.co.zw/client/portal) | `Client Desk` (Workspace, Requests, Plans, Invoices) |

---

## 10. Client Representative Provisioning & Management Desk (NEW)

Authorized internal staff (Administrators, Service Delivery Managers, Billing Officers, and Vetting Officers) accessing the Client 360° Profile & Retainer Plans console (`/admin/clients/view/:id`) can now directly provision and manage authorized client representatives on each organization's team roster.

### 1. Flexible Addition Workflows
The interactive **Add Representative** modal (`#modalAddClientMember`) provides three dedicated provisioning modes:
1. **Quick Add (Email Only):**
   - For rapid invites and onboarding. Enter corporate email and select representative role.
   - If the email belongs to an existing registered user, they are linked immediately and promoted to Client User role (`role = 3`).
   - If the email is new, a user account is auto-created with a friendly name derived from their email address and provisioned with default credentials (`Password123!`).
2. **Full Details (Name, Email & Password):**
   - For explicit account setup with custom corporate credentials.
   - Enter Full Name, Corporate Email Address, custom Portal Access Password (with random password generator helper), and Representative Capacity.
   - Creates or updates the user and login credentials with bcrypt hashing, giving the representative immediate login access.
3. **Select Existing User:**
   - For linking an existing registered non-staff user from a searchable dropdown.
   - Allows assigning representative capacity and optionally setting or overriding their password.

### 2. Supported Representative Roles (`clientmemberrole`)
- **Organization Owner / Signatory (Role 1):** Full governance over organization, billing, and all requests.
- **Billing & Financial Officer (Role 2):** Receives invoices, makes payments, and reviews retainers.
- **Authorized Requester (Role 3):** Can submit and collaborate on service requests.
- **General Team Member (Role 4):** Read-only visibility into organization workspace.

### 3. Roster Management & One-Click Unlinking
- **Visual Roster Cards:** Display user avatar initials, full name, corporate email address, and role-colored badges.
- **One-Click Removal:** Staff can unlink any representative via a secure POST request with a confirmation dialog, keeping client rosters accurate and up-to-date.
- **Two-Way Synchronization:** Representatives added or removed on `/admin/clients/view/:id` immediately appear in the client-facing team roster at `/client/team`.

### 4. Verified Live Scenarios on Production (`portal.tsigiro.co.zw`)
| Scenario | Action | Production Target | Result |
| :--- | :--- | :--- | :--- |
| **Email-Only Addition** | Added `ops@acmelogistics.co.zw` | [Acme Logistics Profile](https://portal.tsigiro.co.zw/admin/clients/view/1) | Auto-provisioned account with default password `Password123!` and linked as Organization Owner. |
| **Login Verification** | Signed in as `ops@acmelogistics.co.zw` | `/login` | Successfully logged in and accessed `/client/portal` and `/client/team`. |
| **Full Provisioning** | Added `tendai.moyo@acmelogistics.co.zw` | [Acme Logistics Profile](https://portal.tsigiro.co.zw/admin/clients/view/1) | Created account with custom password `AcmeFleet2026!` and linked as Billing & Financial Officer. |
| **Representative Removal** | Removed `ops@acmelogistics.co.zw` | [Acme Logistics Profile](https://portal.tsigiro.co.zw/admin/clients/view/1) | Unlinked representative; roster updated instantly while preserving historical data. |
| **Client Team View** | Inspected roster as client | [Client Team Roster](https://portal.tsigiro.co.zw/client/team) | Verified `Tendai Moyo` displayed with active status and role badge. |

---

## 11. Explicit HTTP 403 Access Denied Protection (NEW)

Previously, when an authenticated user navigated to an administrative or departmental screen outside their permitted scope (e.g., a Vetting Officer attempting to view `/admin/staff` or `/admin/payroll`), the router silently bounced them back to `/dashboard` without explanation.

The portal now enforces an informative, transparent **HTTP 403 Forbidden Access Denied** system across all routes and controllers.

### 1. User Experience & Messaging
When an authenticated user attempts to access an unauthorized route, the system presents an explicit Access Denied screen ([`views/errors/403.php`](file:///Library/WebServer/Documents/trainit/views/errors/403.php)):
* **Header:** **"You do not have access to this page"** with an amber security badge `HTTP 403 • Access Restricted`.
* **Account Context:** Displays the authenticated user's name and role badge (e.g., *Ruvimbo Sithole • Vetting Officer*), clarifying why access was blocked.
* **Primary Call-to-Action:** **"Click here to go to the dashboard"** button linking directly to `/dashboard`.
* **Secondary Action:** **"Return to Previous Page"** button allowing the user to navigate back safely in browser history.
* **Support Contact:** Direct contact link to `support@tsigiro.co.zw` for privilege escalation requests.

### 2. Centralized Security Architecture
Access denials are consolidated through [`\App\Helpers\Auth::denyAccess(?string $message = null)`](file:///Library/WebServer/Documents/trainit/app/Helpers/Auth.php#L223-L267) and integrated across all layers:
1. **Router Role Protection ([`Router::requireRole()`](file:///Library/WebServer/Documents/trainit/app/Router.php)):**
   - Automatically triggers `Auth::denyAccess()` when a user's role is not in the required whitelist.
2. **ViewAccess Governance ([`ViewAccess::deny()`](file:///Library/WebServer/Documents/trainit/app/Helpers/ViewAccess.php)):**
   - Replaced silent header redirects with `Auth::denyAccess()`.
3. **Controller-Level Guards:**
   - [`StaffController`](file:///Library/WebServer/Documents/trainit/app/Controllers/StaffController.php): Blocks unauthorized viewing or actioning of staff directories, approvals queues, and leave approvals.
   - [`PayrollController`](file:///Library/WebServer/Documents/trainit/app/Controllers/PayrollController.php): Strictly restricts payroll calculations, approvals, and NSSA statutory returns to Administrators and Billing Officers (`Role 1` and `Role 7`).
   - [`ClientController`](file:///Library/WebServer/Documents/trainit/app/Controllers/ClientController.php): Enforces staff-only access on corporate clients, retainer assignments, and payment reconciliations.
   - [`VacancyController`](file:///Library/WebServer/Documents/trainit/app/Controllers/VacancyController.php): Protects vacancy authoring, applicant scoring, and staff appointment consoles.
   - [`ServiceCatalogueController`](file:///Library/WebServer/Documents/trainit/app/Controllers/ServiceCatalogueController.php): Guards service catalog SLA policies.
   - [`RosterApplicationController`](file:///Library/WebServer/Documents/trainit/app/Controllers/RosterApplicationController.php): Denies unauthorized access if a user attempts to view another applicant's private dossier or onboarding form.
4. **Intelligent Request Handling:**
   - **Unauthenticated Visitors:** Automatically 302-redirected to `/login` with the attempted URI preserved in `AuthReturn::captureCurrentRequest()`.
   - **AJAX / POST / API Calls:** Responds with `HTTP 403` and JSON payload `{"status":"forbidden","response_code":403,"message":"You do not have access to this page."}`.
   - **Browser Page Navigation:** Responds with `HTTP 403` and renders the rich Tsigiro 403 error page.

### 3. Production Verification Matrix (`portal.tsigiro.co.zw`)
| Test Case | Persona / State | Target URL | Expected Status | Result |
| :--- | :--- | :--- | :---: | :--- |
| **Unauthenticated Request** | Guest | `/admin/staff` | `302 Found` | Redirects to `/login` with return URL captured. |
| **Unauthorized Web Page** | Vetting Officer (`Role 8`) | `/admin/staff` | `403 Forbidden` | Displays *"You do not have access to this page"* & *"Click here to go to the dashboard"*. |
| **Unauthorized Payroll** | Vetting Officer (`Role 8`) | `/admin/payroll` | `403 Forbidden` | Displays 403 screen with user role context. |
| **Unauthorized AJAX** | Vetting Officer (`Role 8`) | `/admin/staff` (AJAX) | `403 Forbidden` | Returns JSON `{"status":"forbidden","response_code":403,"message":"..."}`. |
| **Authorized Access** | Administrator (`Role 1`) | `/admin/staff` | `200 OK` | Fully renders Staff Management console. |
| **Authorized Payroll** | Administrator (`Role 1`) | `/admin/payroll` | `200 OK` | Fully renders Payroll & Payouts ledger. |
| **Direct Error Route** | Authenticated User | `/403` | `403 Forbidden` | Renders the standard Access Denied view. |

---

## 12. Staff Portal Dropdown & Personal Remuneration Hub (NEW)

Previously, **"Staff Portal"** on the top navigation was a flat, single link directing users to the generic documents view.

The top navigation now renders **"Staff Portal"** as an interactive dropdown exposing all primary self-service functions available to internal personnel, paired with a new dedicated **Personal Remuneration & Payslips Hub**.

### 1. Top Navigation Dropdown Structure ([`config/nav.php`](file:///Library/WebServer/Documents/trainit/config/nav.php))
Internal staff members across all departments (Administrators, Vetting Officers, Service Delivery Managers, and Billing Officers) now have direct access to 5 dedicated self-service options in the top navigation bar:
* **Staff Hub Overview ([`/staff/portal`](https://portal.tsigiro.co.zw/staff/portal)):** Digital staff ID credential card, employment station, and leave balances overview.
* **My Payslips ([`/staff/portal/payslips`](https://portal.tsigiro.co.zw/staff/portal/payslips)):** Monthly compensation statements, gross earnings, itemized statutory deductions (PAYE, NSSA, AIDS Levy), net payout, and printable PDF payslip documents.
* **Apply for Leave ([`/staff/portal/leave`](https://portal.tsigiro.co.zw/staff/portal/leave)):** Real-time tracking of statutory 22-day annual leave balances and vacation/medical leave submission dialog.
* **Compliance Documents ([`/staff/portal/documents`](https://portal.tsigiro.co.zw/staff/portal/documents)):** Upload and track IDs, diplomas, professional certifications, and police clearance validity.
* **Log Timesheets ([`/staff/portal/timesheets`](https://portal.tsigiro.co.zw/staff/portal/timesheets)):** Daily operational hour logging for billable and internal tasks.

### 2. Personal Payslips Hub ([`views/staff/portal_payslips.php`](file:///Library/WebServer/Documents/trainit/views/staff/portal_payslips.php))
* **KPI Header Cards:** Displays Latest Net Payout ($1,490.00), Total Cumulative Remuneration ($17,880.00), and Total Settled Cycles (12 Monthly Statements).
* **Detailed Payslip Table:** Shows period codes, pay dates, gross salaries, statutory deductions, net payouts, and bank EFT disbursement references with green `Paid / Disbursed` badges.
* **Confidential Printable Payslip View:** Each entry has a **"View Payslip"** button opening the official confidential payslip document ([`/admin/payroll/payslip/:id`](https://portal.tsigiro.co.zw/admin/payroll/payslip/81)) with one-click print/save-to-PDF formatting.
* **Strict Privacy Isolation:** Staff members can only view their own confidential remuneration statements; attempts to access another employee's payslip ID are strictly blocked with the HTTP 403 Access Denied screen.

### 3. Verification & Live Scenarios (`portal.tsigiro.co.zw`)
| Scenario | Account / Persona | Action | Live Result |
| :--- | :--- | :--- | :--- |
| **Top Nav Dropdown** | `vetting@tsigiro.co.zw` (Ruvimbo Sithole) | Hover / Click `Staff Portal` | Dropdown renders all 5 options with font-awesome icons. |
| **My Payslips Console** | `vetting@tsigiro.co.zw` | Navigate to `/staff/portal/payslips` | Renders 12 monthly settled statements with gross, deductions, net, and EFT references. |
| **Printable Document** | `vetting@tsigiro.co.zw` | Click "View Payslip" on statement #81 | Opens official confidential printable payslip with full company header and deductions breakdown. |
| **Cross-Staff Security** | `vetting@tsigiro.co.zw` | Attempt to open another employee's payslip | Returns HTTP 403 Forbidden Access Denied page. |
| **Non-Staff Isolation** | `candidate@tsigiro.co.zw` (Candidate) | Check top navigation bar | Staff Portal dropdown is hidden; only public and candidate links are visible. |




