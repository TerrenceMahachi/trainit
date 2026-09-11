# Tsigiro Portal — Executive Walkthrough & Testing Guide

**Audience:** Executive Leadership, Project Evaluators, Stakeholders, and Systems Auditors  
**Standard Password for All Demo Accounts:** `Password123!`  

---

## 1. Executive Summary

**Tsigiro Portal** is an enterprise workforce, talent vetting, and client engagement platform operating in Zimbabwe. The platform unifies five core business capabilities:

1. **Talent Acquisition & Opportunities:** Public-facing recruitment portal for career vacancies, Work-Related Learning (WRL) university apprenticeships, and independent Associate specialists.
2. **Vetting & Compliance Governance:** Comprehensive verification workflows enforcing Zimbabwe legal standards (police clearances, academic qualifications, and ZIMRA ITF263 tax clearances).
3. **Roster & Engagement Management:** Service delivery orchestration matching verified specialists and apprentices to corporate client projects under managed service plans.
4. **Client Enterprise Portal:** Self-service portal for corporate clients to oversee engaged resources, contract milestones, service tickets, and billing.
5. **Financial Operations & Invoicing:** Normalized billing pipelines handling associate daily-rate disbursements and corporate invoicing.

---

## 2. Pre-Configured Testing Accounts

Each account below has been seeded with realistic relational records, operational statuses, and documents. **All accounts share the password `Password123!`**, or can be accessed instantly using the **One-Click Quick Login** links on the login screen or via the direct URLs below.

| Role | Business Persona | Demo Email | Direct Quick-Login URL | Primary Evaluation Scope |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** (Role 1) | System Administrator | `admin@tsigiro.co.zw` | `/quick-login?as=admin` | Master command center, opportunity publishing, user roles, system audit log |
| **Vetting Officer** (Role 8) | Ruvimbo Sithole | `vetting@tsigiro.co.zw` | `/quick-login?as=vetting` | Candidate vetting, police clearances, qualification checks, checklist sign-offs |
| **Service Manager** (Role 6) | Tafadzwa Mutasa | `manager@tsigiro.co.zw` | `/quick-login?as=manager` | Client service plans, SLA delivery, resource allocation, roster management |
| **Billing Officer** (Role 7) | Nyasha Chidziwa | `finance@tsigiro.co.zw` | `/quick-login?as=finance` | Client invoices, ITF263 tax compliance, associate payout rates, payroll periods |
| **Apprentice** (Role 2) | Kudzai Mapfumo (NUST) | `apprentice@tsigiro.co.zw` | `/quick-login?as=apprentice` | University WRL attachment tracker, application status (*Shortlisted*), onboarding steps |
| **Associate** (Role 2) | Simbarashe Hove (Cloud) | `associate@tsigiro.co.zw` | `/quick-login?as=associate` | Specialist roster profile, daily rate ($180/day), tax compliance, interview schedule |
| **Job Candidate** (Role 2) | Farai Chikwanha | `candidate@tsigiro.co.zw` | `/quick-login?as=candidate` | Vacancy applicant tracking (*APP-TSG-VAC-2026-DEMO*), panel interview status |
| **Client Owner** (Role 3) | Tinashe Gumbo (EcoSolutions) | `client@tsigiro.co.zw` | `/quick-login?as=client` | Corporate Client Portal, organization profile, team members, service plan oversight |

---

## 3. Quick-Access Login Page Feature

On the web login screen (`/login`):
* **One-Click Role Sign In:** Click any role button in the **Role-Based Demo Accounts** column to authenticate and jump directly into that user's customized workspace without typing.
* **Pre-fill Helper:** Click the **Pre-fill** button next to any user to automatically populate the sign-in form with that email and `Password123!`.

---

## 4. Guided Evaluation Journeys by Role

### Journey 1: Corporate Governance & System Administrator
* **Sign In:** `/quick-login?as=admin`
* **Landing Screen:** Administrator Command Center (`/dashboard`)
* **Key Capabilities to Evaluate:**
  1. **Opportunities & Vacancies:** Navigate to **Recruitment / Vacancies** to publish new job openings, configure qualification requirements, and set application deadlines.
  2. **Applicant Oversight:** Review submitted applications across all three pipelines (Job Vacancies, Apprentices, and Associates).
  3. **Staff & Role Management:** View internal staff profiles across Operations, Finance, and Vetting departments.
  4. **System Metrics:** High-level summary of active clients, deployed specialists, and pending vetting cases.

---

### Journey 2: Compliance & Candidate Vetting (Vetting Officer)
* **Sign In:** `/quick-login?as=vetting`
* **Persona:** Ruvimbo Sithole (`TSG-STF-008`, Operations / Vetting Department)
* **Key Capabilities to Evaluate:**
  1. **Vetting Queue:** Inspect pending applicants requiring background checks.
  2. **Checklist Verification:** Validate submitted IDs, academic transcripts, and police clearance certificates.
  3. **Status Progression:** Update candidate vetting records from *Under Review* to *Vetted / Approved* or *Flagged*.

---

### Journey 3: Client Operations & Service Delivery (Service Manager)
* **Sign In:** `/quick-login?as=manager`
* **Persona:** Tafadzwa Mutasa (`TSG-STF-006`, Operations Department)
* **Key Capabilities to Evaluate:**
  1. **Client Service Plans:** Monitor corporate client contracts and SLA performance.
  2. **Talent Allocation:** Review active talent roster bench (Associates & Apprentices) available for client deployment.
  3. **Engagement Tracking:** Oversee milestone completions and operational deliverables.

---

### Journey 4: Billing & Financial Controls (Billing Officer)
* **Sign In:** `/quick-login?as=finance`
* **Persona:** Nyasha Chidziwa (`TSG-STF-007`, Finance Department)
* **Key Capabilities to Evaluate:**
  1. **Tax Verification:** Review associate tax compliance status, including ZIMRA ITF263 valid clearance certificates.
  2. **Daily Rate Structures:** Inspect agreed daily/hourly billing rates (e.g., $180/day for Senior Cloud Specialists).
  3. **Invoicing & Disbursements:** Review generated invoices for corporate accounts and scheduled payroll disbursements.

---

### Journey 5: University Apprentice Experience (WRL Student)
* **Sign In:** `/quick-login?as=apprentice`
* **Persona:** Kudzai Mapfumo (National University of Science and Technology — NUST, Computer Science)
* **Key Capabilities to Evaluate:**
  1. **Application Status Card:** View real-time tracking badge (*Status: Shortlisted*).
  2. **Academic Attachment Details:** 12-month Work-Related Learning period, academic institution, degree program.
  3. **Onboarding Milestones:** Submission of institutional introductory letter and logbook tracking.

---

### Journey 6: Technical Specialist (Associate Specialist)
* **Sign In:** `/quick-login?as=associate`
* **Persona:** Simbarashe Hove (Senior Cloud Specialist, 8+ years experience)
* **Key Capabilities to Evaluate:**
  1. **Specialist Portal Card:** Status badge (*Interview Scheduled* with upcoming panel interview timestamp).
  2. **Contract & Billing Parameters:** Standard daily billing rate of **USD \$180.00**, verified ITF263 tax compliance.
  3. **Skill Matrix:** AWS, Kubernetes, Terraform, and Enterprise Linux capabilities.

---

### Journey 7: Job Vacancy Candidate
* **Sign In:** `/quick-login?as=candidate`
* **Persona:** Farai Chikwanha (Applicant for *Talent Operations & Vetting Officer*, Ref: `APP-TSG-VAC-2026-DEMO`)
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
  2. **Engaged Resources:** Monitor assigned Tsigiro specialists and apprentices delivering services.
  3. **Enterprise Invoicing & Service Plans:** Review active retainers, billing history, and download statements.

---

## 5. Security & Session Integrity Architecture

The evaluation environment incorporates Tsigiro's security safeguards:
* **Tamper-Proof Authentication:** Cryptographic HMAC-SHA256 signature binding user sessions to prevent cookie manipulation.
* **Idle Session Soft-Lock:** Automatic protection after 30 minutes of inactivity redirecting to the `/resume` challenge.
* **Granular Role-Based Access Control (RBAC):** Strict separation between internal staff tiers, corporate clients, and talent applicants.
