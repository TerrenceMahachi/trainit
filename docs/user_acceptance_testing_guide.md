# User Acceptance Testing (UAT) Guide & Test Matrix
**Platform:** Trainit Digital Operations & Talent Roster  
**Live URL:** [https://trainit.co.zw](https://trainit.co.zw)  
**Version:** 1.0 (Release Candidate)  
**Target Audience:** Client Project Leads, QA Testers, and System Reviewers

---

## 1. Executive Summary

This document provides a structured, step-by-step User Acceptance Testing (UAT) framework for the **Trainit Platform**. Testers can follow the test scenarios below to validate all system modules, including the responsive public website, candidate application wizards, administrator vetting consoles, statutory onboarding workflows, and domain email notifications.

---

## 2. Test Environment & Access Matrix

### 2.1 Live System Links
- **Production Website:** [https://trainit.co.zw](https://trainit.co.zw)
- **Talent Opportunities:** [https://trainit.co.zw/opportunities](https://trainit.co.zw/opportunities)
- **Member Sign In:** [https://trainit.co.zw/login](https://trainit.co.zw/login)
- **Account Registration:** [https://trainit.co.zw/register](https://trainit.co.zw/register)
- **Admin Review Console:** [https://trainit.co.zw/admin/roster](https://trainit.co.zw/admin/roster)

### 2.2 Test Accounts & Credentials

| Role | Username / Email | Password | Access Scope |
|---|---|---|---|
| **System Administrator** | `admin@trainit.co.zw` | `Admin123!` | Full Command Center, Talent Pipeline, 100-pt Vetting Scoring Console, Dictionary Governance |
| **New Candidate / Applicant** | Register your own email at `/register` | Self-created | Multi-step Apprentice & Associate Wizards, Auto-Save Drafts, Real-time Progress Tracking, Stage 3 Onboarding |
| **Demo Candidate (Existing)** | `moosab@mail.com` | `Moosa123!` | Existing Candidate Dashboard & Tracked Submissions |

---

## 3. Test Suites & Detailed Test Scenarios

### Test Suite 1: Brand Aesthetics & Responsive Navigation

| Test ID | Test Scenario | Steps to Execute | Expected Result | Status |
|---|---|---|---|---|
| **TS1-01** | **Brand Theme Consistency** | 1. Navigate to [https://trainit.co.zw](https://trainit.co.zw)<br>2. Inspect header, buttons, cards, and footer | The site presents the official **Tsigiro** palette: Deep Midnight Plum (`#2A114B`), Golden Amber accents (`#FFCC00`), and warm lilac tinted surfaces (`#F8F5FC`). | [ ] Pass<br>[ ] Fail |
| **TS1-02** | **Navbar Logo Badge** | 1. Inspect the logo in the top navbar | The logo badge displays with generous padding and crisp rounded corners (no distortion or cropping). | [ ] Pass<br>[ ] Fail |
| **TS1-03** | **Mobile Responsiveness** | 1. Open [https://trainit.co.zw](https://trainit.co.zw) on a mobile device or reduce browser width to &lt; 576px<br>2. Tap hamburger menu & scroll through pages | Content adjusts seamlessly with no horizontal scrollbars. Navbar toggles smoothly, hero CTA buttons stack neatly, and tables remain fluidly scrollable. | [ ] Pass<br>[ ] Fail |
| **TS1-04** | **Rich Footer Layout** | 1. Scroll to the bottom of any page | Footer displays a 4-column layout (Brand, Services, Company, Account) with quick links, copyright, and direct email contacts. | [ ] Pass<br>[ ] Fail |

---

### Test Suite 2: Authentication, Security & Password Recovery

| Test ID | Test Scenario | Steps to Execute | Expected Result | Status |
|---|---|---|---|---|
| **TS2-01** | **User Registration** | 1. Go to `/register`<br>2. Fill in Name, Email, Password, and solve CAPTCHA<br>3. Submit form | Account is created, user is automatically logged in and redirected to `/dashboard`. Welcome email is dispatched to candidate. | [ ] Pass<br>[ ] Fail |
| **TS2-02** | **Sign In Validation** | 1. Go to `/login`<br>2. Enter valid credentials | User enters their personalized dashboard based on role (Admin Command Center vs Candidate Portal). | [ ] Pass<br>[ ] Fail |
| **TS2-03** | **Password Reset Request** | 1. Go to `/reset`<br>2. Enter registered email address and submit | Confirmation alert is shown; a secure 1-hour reset link is dispatched from `noreply@trainit.co.zw`. | [ ] Pass<br>[ ] Fail |
| **TS2-04** | **Complete Password Reset** | 1. Click reset link from email (or `/set-password?token=...`)<br>2. Enter new password and submit | Password updates successfully in database; confirmation email is sent; user can sign in with new password. | [ ] Pass<br>[ ] Fail |
| **TS2-05** | **Soft-Lock Idle Session Resume** | 1. Log in to dashboard and remain inactive for 3 minutes | Session safely soft-locks to `/resume` requiring quick 3-character security verification to continue without losing work. | [ ] Pass<br>[ ] Fail |

---

### Test Suite 3: Candidate Intake Application Wizards

| Test ID | Test Scenario | Steps to Execute | Expected Result | Status |
|---|---|---|---|---|
| **TS3-01** | **Track Selection** | 1. Go to `/opportunities`<br>2. Select either **Apprentice Track** or **Associate Track** | Application opens the tailored 5-step intake wizard for the selected career stream. | [ ] Pass<br>[ ] Fail |
| **TS3-02** | **Sticky Header & Compact Stepper** | 1. Scroll through form steps | Top progress bar stays sticky at top of screen with compact step icons and clear active step indication. | [ ] Pass<br>[ ] Fail |
| **TS3-03** | **Real-Time Progress Tracker** | 1. Fill in fields across Step 1 (Personal & Education)<br>2. Observe top progress bar | Progress percentage dynamically recalculates and increments in real-time as required fields are completed. | [ ] Pass<br>[ ] Fail |
| **TS3-04** | **Background Auto-Save** | 1. Fill in form values and pause typing<br>2. Refresh page or return later | Form automatically saves draft in the background with timestamp toast. Upon reload, all previously entered data is restored. | [ ] Pass<br>[ ] Fail |
| **TS3-05** | **Dynamic Skills Selector** | 1. On Step 2, pick Primary Service Function<br>2. Add core competencies and proficiency ratings | Skills filter dynamically by chosen service function. Custom skills can be added on the fly. | [ ] Pass<br>[ ] Fail |
| **TS3-06** | **Document Uploads** | 1. On Step 5, attach CV/Resume, Certificates, and Transcripts | Files upload securely with progress indicators and store linked to application reference ID. | [ ] Pass<br>[ ] Fail |
| **TS3-07** | **Final Application Submission** | 1. Check declarations and click **Submit Application** | Application locks draft status &rarr; sets status to *Pending Vetting Review*. Candidate receives confirmation email with `#APP-XXXXX` reference; Review team receives intake notification. | [ ] Pass<br>[ ] Fail |

---

### Test Suite 4: Administrator Talent Pipeline & Vetting Console

| Test ID | Test Scenario | Steps to Execute | Expected Result | Status |
|---|---|---|---|---|
| **TS4-01** | **Talent Pipeline Roster** | 1. Log in as `admin@trainit.co.zw`<br>2. Go to `/admin/roster` | Dashboard lists all candidate applications with status badges, track tags, red-flag indicators, and server-side AJAX pagination. | [ ] Pass<br>[ ] Fail |
| **TS4-02** | **Filters & Search** | 1. Filter by Track (Apprentice/Associate), Status, or search by name | Table updates instantly via AJAX without page reloads. | [ ] Pass<br>[ ] Fail |
| **TS4-03** | **Open Candidate Review Console** | 1. Click **Review Application** on any candidate | Complete candidate dossier opens: profile info, qualifications, work history, situational judgement answers, automated red-flag audit, and scoring form. | [ ] Pass<br>[ ] Fail |
| **TS4-04** | **100-Point Vetting Rubric Scoring** | 1. Enter scores across the 5 dimensions:<br>&bull; Technical Fit (/30)<br>&bull; Evidence Verification (/25)<br>&bull; Situational Judgement (/20)<br>&bull; Availability (/15)<br>&bull; Motivation (/10)<br>2. Click **Save Assessment** | Total score dynamically updates to 100-point scale; feedback toast appears confirming assessment is saved. | [ ] Pass<br>[ ] Fail |
| **TS4-05** | **Interactive Status Action Buttons** | 1. Select a Vetting Recommendation (e.g. *Admitted on Roster*)<br>2. Click status action button | Status updates immediately; candidate is notified of their decision via branded email. | [ ] Pass<br>[ ] Fail |

---

### Test Suite 5: Stage 3 Statutory Onboarding & Compliance

| Test ID | Test Scenario | Steps to Execute | Expected Result | Status |
|---|---|---|---|---|
| **TS5-01** | **Onboarding Unlock for Admitted Candidates** | 1. Set candidate status to *Admitted on Roster*<br>2. Log in as that candidate | Candidate dashboard unlocks **Stage 3 Statutory Onboarding** action card. | [ ] Pass<br>[ ] Fail |
| **TS5-02** | **Statutory & Banking Details Filing** | 1. Candidate fills National ID number, Tax/NSSA number, Banking details, and Emergency contact<br>2. Acknowledges Code of Conduct<br>3. Submits form | Details are encrypted and saved; candidate receives verification email; `admin@trainit.co.zw` & `billing@trainit.co.zw` receive notification. | [ ] Pass<br>[ ] Fail |

---

### Test Suite 6: Automated Email Notifications & Mailbox Routing

| Test ID | Trigger Event | Sender Mailbox | Recipient(s) | Verification Check |
|---|---|---|---|---|
| **TS6-01** | **User Registration** | `noreply@trainit.co.zw` | Candidate | Branded HTML email with account details & direct link to Dashboard. |
| **TS6-02** | **Password Recovery** | `noreply@trainit.co.zw` | Account Owner | 1-hour secure password reset link with security warning. |
| **TS6-03** | **Apprentice Submission** | `apprenticeship@trainit.co.zw` | Candidate & Reviewers (`jobs@`, `admin@`) | Confirmation with Ref `#APP-XXXXX` + Team notification for scoring. |
| **TS6-04** | **Associate Submission** | `associates@trainit.co.zw` | Candidate & Reviewers (`jobs@`, `admin@`) | Specialist intake confirmation + Team alert. |
| **TS6-05** | **Vetting Review Decision** | Track Mailbox (`apprenticeship@` or `associates@`) | Candidate | Decision badge, assessor feedback comments, and next step instructions. |
| **TS6-06** | **Stage 3 Onboarding Filed** | `admin@trainit.co.zw` | Candidate & Finance (`admin@`, `billing@`) | Confirmation of statutory compliance & disbursal bank account filing. |
| **TS6-07** | **Website Contact Form** | `hello@trainit.co.zw` | Inquirer & Sales (`hello@`, `sales@`) | Instant user acknowledgment & sales lead notification. |

---

### Test Suite 7: Public Website Contact Form

| Test ID | Test Scenario | Steps to Execute | Expected Result | Status |
|---|---|---|---|---|
| **TS7-01** | **Inquiry Submission** | 1. Go to `/contact`<br>2. Fill in Name, Email, Phone, Subject, and Message<br>3. Click **Send Message** | Spinner shows during dispatch; green success alert appears; form resets; confirmation email arrives in user inbox. | [ ] Pass<br>[ ] Fail |

---

## 4. Defect Reporting Template

If an issue is identified during testing, please document it using this format:

```text
Defect ID: DEF-001
Test Case Ref: TS3-04
Severity: High / Medium / Low
Browser / Device: Chrome 127 on macOS / Safari on iOS 17
Steps to Reproduce:
1. Open /dashboard/apply/apprentice
2. Fill in Step 1 fields
3. Observe behavior
Expected Result: Form auto-saves and shows timestamp.
Actual Result: [Describe what occurred]
Screenshots / Logs: [Attach screenshot]
```

---

## 5. UAT Sign-Off & Acceptance

| Role | Name | Signature | Date | Decision |
|---|---|---|---|---|
| **Lead QA / Tester** | ____________________ | ____________________ | ____/____/2026 | [ ] Accepted<br>[ ] Conditional |
| **Client Project Manager** | ____________________ | ____________________ | ____/____/2026 | [ ] Accepted<br>[ ] Revisions Requested |
| **Lead Developer** | ____________________ | ____________________ | ____/____/2026 | [ ] Approved |
