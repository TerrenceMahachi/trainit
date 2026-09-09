<?php

namespace App\Helpers;

use App\Models\Database;
use PDO;

class ReferenceDataSeeder
{
    /**
     * Complete dictionary dataset configurations.
     */
    public static function getDataset(): array
    {
        $skills = [
            // Finance & Accounting (servicefunction: 1)
            [1, 'bookkeeping', 'Bookkeeping & Journal Entries', 10],
            [1, 'reconciliation', 'Bank & Cash Reconciliation', 20],
            [1, 'ap_supplier', 'Accounts Payable & Supplier Ledger', 30],
            [1, 'petty_cash', 'Petty Cash & Float Management', 40],
            [1, 'payroll_processing', 'Payroll Processing & Payslips', 50],
            [1, 'donor_bva', 'Donor Budget-versus-Actual (BVA) Tracking', 60],
            [1, 'mgmt_accounts', 'Monthly Management Accounts Preparation', 70],
            [1, 'zimra_returns', 'ZIMRA Statutory Returns (PAYE, VAT, QPDs, WHT)', 80],
            [1, 'nssa_nec_returns', 'NSSA, ZIMDEF, SDF & NEC Statutory Returns', 90],
            [1, 'audit_file', 'Audit File Preparation & External Audit Support', 100],
            [1, 'multicurrency', 'Multi-currency (USD/ZWG) Accounting & Exchange Gain/Loss', 110],
            [1, 'fixed_assets', 'Fixed Asset Register & Depreciation', 120],

            // HR & Payroll (servicefunction: 2)
            [2, 'recruitment_admin', 'Recruitment Administration & Interview Logistics', 10],
            [2, 'contract_admin', 'Employment Contract Administration', 20],
            [2, 'payroll_runs', 'Payroll Runs & Salary Calculation', 30],
            [2, 'leave_admin', 'Leave Administration & Tracking', 40],
            [2, 'hr_records', 'HR Records, Personnel Files & Confidentiality', 50],
            [2, 'statutory_compliance', 'Statutory Compliance (NSSA, NEC, Ministry of Labour)', 60],
            [2, 'performance_mgmt', 'Performance Management Administration & Appraisals', 70],
            [2, 'onboarding_offboarding', 'Staff Onboarding & Exit Procedures', 80],
            [2, 'hr_policies', 'HR Policy & Handbook Drafting', 90],
            [2, 'disciplinary_grievance', 'Disciplinary Hearings & Grievance Procedures', 100],

            // Grants & Donor Compliance (servicefunction: 3)
            [3, 'donor_regulations', 'Donor Rules & Regulations (USAID/BHA, EU, FCDO, Global Fund)', 10],
            [3, 'subgrantee_dd', 'Sub-grantee Pre-Award Assessment & Due Diligence', 20],
            [3, 'subgrantee_monitoring', 'Sub-grantee Monitoring & Financial Verification Visits', 30],
            [3, 'donor_reporting', 'Donor Financial Reporting & Liquidation', 40],
            [3, 'procurement_compliance', 'Procurement Compliance under Specific Donor Rules', 50],
            [3, 'partner_capacity', 'Partner Capacity Assessment & Institutional Strengthening', 60],
            [3, 'proposal_budgeting', 'Proposal Budget Development & Narrative Costing', 70],
            [3, 'cost_allocation', 'Cost Allocation & Shared Direct Cost Methodologies', 80],

            // MEAL (servicefunction: 4)
            [4, 'logframe_design', 'Logframe, Theory of Change & Indicator Design', 10],
            [4, 'data_collection_tools', 'Data Collection Tool Design & Protocol Development', 20],
            [4, 'kobo_odk', 'Digital Data Collection (KoBoToolbox, ODK, SurveyCTO)', 30],
            [4, 'enumerator_supervision', 'Enumerator Training & Field Survey Supervision', 40],
            [4, 'quantitative_cleaning', 'Quantitative Data Cleaning & Statistical Analysis', 50],
            [4, 'qualitative_analysis', 'Qualitative Analysis (FGDs, KIIs, Thematic Coding)', 60],
            [4, 'dashboards_viz', 'Dashboards, Visualizations & Infographics', 70],
            [4, 'meal_reporting', 'MEAL Report Writing & Donor Progress Updates', 80],
            [4, 'accountability_crm', 'Accountability, Complaints & Feedback Mechanisms (CRM)', 90],
            [4, 'evaluation_mgmt', 'Baseline, Midterm & Endline Evaluation Management', 100],

            // Procurement & Logistics (servicefunction: 5)
            [5, 'rfq_rfp_itb', 'RFQ, RFP and ITB Tendering Processes', 10],
            [5, 'supplier_vetting', 'Supplier Vetting, Due Diligence & Database Management', 20],
            [5, 'bid_analysis', 'Bid Analysis, Comparative Tables & Evaluation Reports', 30],
            [5, 'procurement_contracts', 'Procurement Contract Administration & Purchase Orders', 40],
            [5, 'asset_tagging', 'Asset Tagging, Verification & Register Maintenance', 50],
            [5, 'fleet_fuel', 'Fleet Scheduling, Vehicle Maintenance & Fuel Management', 60],
            [5, 'stores_warehouse', 'Stores, Inventory Control & Warehouse Management', 70],
            [5, 'praz_public', 'PRAZ Regulations & Public Procurement Processes', 80],

            // Internal Audit & Risk (servicefunction: 6)
            [6, 'internal_controls', 'Internal Control Testing & Walkthroughs', 10],
            [6, 'risk_registers', 'Risk Register Development & Matrix Assessment', 20],
            [6, 'compliance_reviews', 'Regulatory & Organizational Compliance Reviews', 30],
            [6, 'fraud_investigation', 'Fraud Investigation Support & Forensic Review', 40],
            [6, 'sop_policy_review', 'Standard Operating Procedures (SOP) & Policy Reviews', 50],

            // ICT / Systems Admin (servicefunction: 7)
            [7, 'm365_admin', 'Microsoft 365 Administration & Tenant Configuration', 10],
            [7, 'exchange_mail', 'Exchange Online, Mail Routing, DNS (SPF, DKIM, DMARC)', 20],
            [7, 'azure_entra', 'Microsoft Entra ID / Azure AD & Identity Management', 30],
            [7, 'intune_mdm', 'Microsoft Intune / Mobile Device Management (MDM)', 40],
            [7, 'google_workspace', 'Google Workspace Administration', 50],
            [7, 'networking_lan_wifi', 'Networking (LAN, WiFi, Routers, Firewalls, VLANs)', 60],
            [7, 'helpdesk_support', 'End-User Support, Ticketing & Helpdesk Management', 70],
            [7, 'backup_dr', 'Backup Systems, Disaster Recovery & Business Continuity', 80],
            [7, 'cybersecurity_mfa', 'Cybersecurity Fundamentals, MFA & Conditional Access', 90],
            [7, 'voip_telephony', 'VoIP Telephony & PBX Systems', 100],
            [7, 'hardware_repair', 'Hardware Diagnostics, Maintenance & Component Repair', 110],
            [7, 'server_admin', 'Server Administration (Windows Server / Linux)', 120],

            // Data & BI (servicefunction: 8)
            [8, 'excel_advanced', 'Advanced Excel (Power Query, Power Pivot, Macros)', 10],
            [8, 'power_bi', 'Power BI Dashboard Design & DAX Modeling', 20],
            [8, 'sql_querying', 'SQL Querying, Views & Relational Database Extraction', 30],
            [8, 'google_sheets_apps', 'Google Sheets, Formulae & Apps Script Automation', 40],
            [8, 'data_cleaning', 'Data Cleaning, Normalization & ETL Pipelines', 50],
            [8, 'statistical_software', 'Statistical Modeling (SPSS, Stata, R, Python)', 60],
            [8, 'bi_visualizations', 'Data Storytelling & Interactive Visual Dashboards', 70],

            // Software Development (servicefunction: 9)
            [9, 'html_css_js', 'Core Web Frontend (HTML5, Responsive CSS, Vanilla JS)', 10],
            [9, 'react_vue', 'Frontend Frameworks (React, Vue.js)', 20],
            [9, 'python_dev', 'Python Backend & Scripting', 30],
            [9, 'php_laravel', 'PHP & MVC Frameworks (Laravel, Vanilla PHP)', 40],
            [9, 'nodejs', 'Node.js Backend & API Development', 50],
            [9, 'relational_dbs', 'Relational Databases (PostgreSQL, MySQL, SQLite)', 60],
            [9, 'odoo_customization', 'Odoo ERP Development (Python, XML, QWeb, OWL)', 70],
            [9, 'api_integration', 'API Design, REST Services & Third-Party Integration', 80],
            [9, 'git_version_control', 'Git & Version Control Workflows', 90],
            [9, 'wordpress_cms', 'WordPress CMS & Custom Theme/Plugin Development', 100],
            [9, 'mobile_development', 'Mobile App Development (Android Kotlin / Flutter / Hybrid)', 110],
            [9, 'testing_qa', 'Automated & Manual Software QA / Unit Testing', 120],

            // Communications & Digital (servicefunction: 10)
            [10, 'content_copywriting', 'Professional Content Writing, Articles & Press Releases', 10],
            [10, 'social_media', 'Social Media Strategy, Scheduling & Community Engagement', 20],
            [10, 'graphic_design', 'Graphic Design (Canva, Adobe Photoshop, Illustrator)', 30],
            [10, 'photography', 'Professional Photography & Image Post-Processing', 40],
            [10, 'videography_editing', 'Videography, Video Editing & Short-form Video Production', 50],
            [10, 'web_cms_content', 'Website Content Management & Basic SEO', 60],
            [10, 'email_newsletters', 'Email Marketing Campaigns & Newsletter Design', 70],
            [10, 'donor_branding', 'Donor Visibility, Branding Guidelines & Compliance', 80],
            [10, 'storytelling_cases', 'Impact Storytelling, Human-Interest Profiles & Case Studies', 90],

            // Administration & Executive Support (servicefunction: 11)
            [11, 'diary_travel', 'Executive Diary, Calendar & Travel Logistics Management', 10],
            [11, 'minute_taking', 'Formal Meeting Minute Taking & Action Tracking', 20],
            [11, 'document_formatting', 'Executive Document Formatting, Proofreading & Templates', 30],
            [11, 'workshop_events', 'Workshop, Seminar & Corporate Event Logistics', 40],
            [11, 'filing_systems', 'Physical & Electronic Records Management Systems', 50],
            [11, 'front_office', 'Front Office, Customer Reception & Telephone Etiquette', 60],
            [11, 'board_governance', 'Board & Committee Governance Secretariat Support', 70],

            // Programme / Project Support (servicefunction: 12)
            [12, 'workplans', 'Project Workplan Development & Gantt Chart Tracking', 10],
            [12, 'activity_budgeting', 'Activity-Based Budgeting & Expense Projections', 20],
            [12, 'field_coordination', 'Field Mission & Community Activity Coordination', 30],
            [12, 'community_mobilization', 'Community Mobilization & Local Leadership Engagement', 40],
            [12, 'stakeholder_liaison', 'Government & Stakeholder Liaison Support', 50],
            [12, 'training_facilitation', 'Workshop & Community Training Facilitation', 60],
            [12, 'report_compilation', 'Field Narrative & Monthly Project Report Compilation', 70]
        ];

        $skillRows = [];
        $skillId = 1;
        foreach ($skills as $s) {
            $skillRows[] = [$skillId++, $s[0], $s[1], $s[2], $s[3]];
        }

        return [
            // 1. Taxonomy & Skills
            'servicefunction' => [
                'columns' => ['iD', 'code', 'name', 'description', 'sort_order'],
                'rows' => [
                    [1, 'finance_accounting', 'Finance & Accounting', 'Bookkeeping, management accounts, statutory returns (ZIMRA/NSSA), audit prep, multi-currency.', 10],
                    [2, 'hr_payroll', 'Human Resources & Payroll', 'Recruitment, payroll runs, statutory compliance (NSSA/NEC), contract and leave admin.', 20],
                    [3, 'grants_donor_compliance', 'Grants & Donor Compliance', 'Donor regulations (USAID/EU/FCDO), sub-grantee due diligence, donor reporting, budget development.', 30],
                    [4, 'meal', 'MEAL', 'Monitoring, Evaluation, Accountability & Learning, logframes, KoBoToolbox, data cleaning, dashboards.', 40],
                    [5, 'procurement_logistics', 'Procurement & Logistics', 'RFQ/RFP tenders, supplier vetting, PRAZ public procurement, stores and fleet management.', 50],
                    [6, 'internal_audit_risk', 'Internal Audit & Risk', 'Internal control testing, risk registers, compliance reviews, fraud investigation support.', 60],
                    [7, 'ict_systems_admin', 'ICT / Systems Administration', 'Microsoft 365, Azure AD / Entra, Google Workspace, LAN/WiFi, cybersecurity, helpdesk, backups.', 70],
                    [8, 'data_reporting_bi', 'Data, Reporting & Business Intelligence', 'Advanced Excel, Power BI, SQL, statistical modeling (SPSS/Stata/R/Python), visual dashboards.', 80],
                    [9, 'software_development', 'Software Development', 'Web/mobile dev (PHP/Laravel, React, Node, Python, MySQL/Postgres), Odoo ERP customization (OWL, QWeb), APIs.', 90],
                    [10, 'communications_digital', 'Communications & Digital', 'Content writing, social media, graphic design (Canva/Adobe), videography, donor visibility & branding.', 100],
                    [11, 'admin_executive_support', 'Administration & Executive Support', 'Executive diary management, minute taking, document formatting, workshop logistics, records management.', 110],
                    [12, 'programme_project_support', 'Programme / Project Support', 'Workplan development, activity budgeting, field activity coordination, community liaison, reporting.', 120]
                ]
            ],

            'skillitem' => [
                'columns' => ['iD', 'servicefunction', 'code', 'name', 'sort_order'],
                'rows' => $skillRows
            ],

            'proficiencylevel' => [
                'columns' => ['iD', 'level_number', 'code', 'name', 'definition'],
                'rows' => [
                    [1, 1, 'aware', '1 - Aware', 'I understand the concepts and principles but have not delivered this work independently.'],
                    [2, 2, 'assisted', '2 - Assisted', 'I have done this work with close supervision or as part of a guided team.'],
                    [3, 3, 'independent', '3 - Independent', 'I can deliver this work start to finish reliably without supervision.'],
                    [4, 4, 'expert', '4 - Expert', 'I handle complex or non-standard edge cases, solve blockers, and others consult me.'],
                    [5, 5, 'master', '5 - Can train others', 'I design the methodology, set organizational standards, and train/review others.']
                ]
            ],

            'vettingrecommendation' => [
                'columns' => ['iD', 'code', 'name', 'description'],
                'rows' => [
                    [1, 'recommend_admit', 'Recommend Admission to Roster', 'Candidate meets all standards for active roster admission.'],
                    [2, 'shortlist_interview', 'Shortlist for Technical Interview', 'Candidate passed initial vetting; schedule structured interview.'],
                    [3, 'talent_pool', 'Hold in Talent Pool', 'Not suitable for current tier economics or active opportunities; hold for future review.'],
                    [4, 'reject', 'Ineligible / Unsuccessful', 'Candidate failed eligibility gates or scoring benchmark.']
                ]
            ],

            // 2. Classifications
            'applicationtrack' => [
                'columns' => ['iD', 'code', 'name', 'description'],
                'rows' => [
                    [1, 'apprentice', 'Apprentice', 'For students on attachment, recent graduates, or early-career professionals placed with client organisations under Tsigiro supervision.'],
                    [2, 'associate', 'Associate', 'For experienced specialists providing specialist oversight, review, quality assurance, and technical expertise on Tsigiro engagements.']
                ]
            ],

            'applicationstatus' => [
                'columns' => ['iD', 'code', 'name', 'description', 'sort_order'],
                'rows' => [
                    [1, 'draft', 'Draft / In Progress', 'Application started but not yet submitted.', 10],
                    [2, 'submitted', 'Submitted', 'Application submitted, awaiting initial screening.', 20],
                    [3, 'screened', 'Screened / Shortlisted', 'Applicant passed eligibility gates and initial evaluation.', 30],
                    [4, 'interviewed', 'Interview / Verification', 'Technical verification and structured interview in progress.', 40],
                    [5, 'on_roster', 'On Roster (Active)', 'Admitted to the talent roster and eligible for client engagements.', 50],
                    [6, 'deployed', 'Deployed', 'Currently placed on active client assignment.', 60],
                    [7, 'dormant', 'Dormant', 'Temporarily inactive on the roster.', 70],
                    [8, 'rejected', 'Ineligible / Unsuccessful', 'Application did not meet admission criteria.', 80],
                    [9, 'withdrawn', 'Withdrawn', 'Application withdrawn by applicant.', 90]
                ]
            ],

            'apprenticestatus' => [
                'columns' => ['iD', 'code', 'name', 'description'],
                'rows' => [
                    [1, 'enrolled_wrl', 'Currently Enrolled (Seeking Work-Related Learning / Attachment)', 'Student actively enrolled needing institutional attachment.'],
                    [2, 'enrolled_parttime', 'Currently Enrolled (Seeking Part-time / Vacation Work)', 'Student seeking flexible part-time work.'],
                    [3, 'awaiting_graduation', 'Awaiting Graduation', 'Studies complete, pending graduation ceremony.'],
                    [4, 'graduated_recent', 'Graduated within last 24 months', 'Recent tertiary graduate.'],
                    [5, 'graduated_past', 'Graduated more than 24 months ago', 'Early-career professional.']
                ]
            ],

            'employmentstatus' => [
                'columns' => ['iD', 'code', 'name', 'description'],
                'rows' => [
                    [1, 'employed_fulltime', 'Employed Full-time', 'Currently in full-time employment.'],
                    [2, 'employed_parttime', 'Employed Part-time', 'Currently in part-time employment.'],
                    [3, 'consulting', 'Self-employed / Consulting', 'Independent consultant or practitioner.'],
                    [4, 'between_assignments', 'Between Assignments', 'Available immediately for full capacity.'],
                    [5, 'retired', 'Retired / Semi-retired', 'Experienced veteran offering advisory/review.']
                ]
            ],

            'zimprovince' => [
                'columns' => ['iD', 'code', 'name', 'sort_order'],
                'rows' => [
                    [1, 'harare', 'Harare', 10],
                    [2, 'bulawayo', 'Bulawayo', 20],
                    [3, 'manicaland', 'Manicaland', 30],
                    [4, 'mash_central', 'Mashonaland Central', 40],
                    [5, 'mash_east', 'Mashonaland East', 50],
                    [6, 'mash_west', 'Mashonaland West', 60],
                    [7, 'masvingo', 'Masvingo', 70],
                    [8, 'mat_north', 'Matabeleland North', 80],
                    [9, 'mat_south', 'Matabeleland South', 90],
                    [10, 'midlands', 'Midlands', 100]
                ]
            ],

            'workrightstatus' => [
                'columns' => ['iD', 'code', 'name', 'description'],
                'rows' => [
                    [1, 'citizen', 'Zimbabwean Citizen', 'Full right to work in Zimbabwe without restriction.'],
                    [2, 'permanent_resident', 'Permanent Resident', 'Permanent residence status with right to work.'],
                    [3, 'work_permit', 'Valid Work Permit', 'Holder of a current, valid Zimbabwean work permit/visa.'],
                    [4, 'not_eligible', 'Not Yet Eligible', 'Requires visa or work permit before placement.']
                ]
            ],

            // 3. Qualifications & Bodies
            'qualificationtype' => [
                'columns' => ['iD', 'code', 'name', 'sort_order'],
                'rows' => [
                    [1, 'olevel', 'Ordinary Level (O-Level)', 10],
                    [2, 'alevel', 'Advanced Level (A-Level)', 20],
                    [3, 'national_cert', 'National Certificate (NC)', 30],
                    [4, 'national_diploma', 'National Diploma (ND)', 40],
                    [5, 'hnd', 'Higher National Diploma (HND)', 50],
                    [6, 'bachelor', "Bachelor's Degree", 60],
                    [7, 'postgrad_diploma', 'Postgraduate Diploma', 70],
                    [8, 'master', "Master's Degree", 80],
                    [9, 'doctorate', 'Doctorate / PhD', 90],
                    [10, 'professional', 'Professional Qualification / Certificate', 100]
                ]
            ],

            'qualificationstatus' => [
                'columns' => ['iD', 'code', 'name'],
                'rows' => [
                    [1, 'completed', 'Completed / Conferred'],
                    [2, 'student', 'Student / Candidate'],
                    [3, 'part_qualified', 'Part-qualified'],
                    [4, 'finalist', 'Finalist'],
                    [5, 'member', 'Associate / Full Member'],
                    [6, 'fellow', 'Fellow']
                ]
            ],

            'professionalbody' => [
                'columns' => ['iD', 'code', 'name', 'description'],
                'rows' => [
                    [1, 'ACCA', 'ACCA - Association of Chartered Certified Accountants', 'Accounting & Finance'],
                    [2, 'CIMA', 'CIMA - Chartered Institute of Management Accountants', 'Management Accounting'],
                    [3, 'ICAZ', 'ICAZ - Institute of Chartered Accountants of Zimbabwe (CA)', 'Chartered Accountancy'],
                    [4, 'ICSAZ', 'ICSAZ - Chartered Governance and Accountancy Institute in Zimbabwe', 'Corporate Governance'],
                    [5, 'IPMZ', 'IPMZ - Institute of People Management of Zimbabwe', 'Human Resources'],
                    [6, 'CIPS', 'CIPS - Chartered Institute of Procurement & Supply', 'Procurement & Supply'],
                    [7, 'IIA', 'IIA - Institute of Internal Auditors', 'Internal Audit'],
                    [8, 'PMI', 'PMI - Project Management Institute (PMP/CAPM)', 'Project Management'],
                    [9, 'PRINCE2', 'PRINCE2 Practitioner', 'Project Management'],
                    [10, 'Microsoft', 'Microsoft Certified Professional / Azure', 'IT & Cloud'],
                    [11, 'CompTIA', 'CompTIA (Security+, Network+, A+)', 'IT & Cyber'],
                    [12, 'Cisco', 'Cisco (CCNA, CCNP)', 'Networking'],
                    [13, 'AWS', 'AWS Certified (Cloud Practitioner, Solutions Architect)', 'Cloud Computing'],
                    [14, 'Google', 'Google Cloud / Workspace Certified', 'Cloud & Data'],
                    [15, 'Other', 'Other Professional Body / Certifying Institute', 'General']
                ]
            ],

            'sectortype' => [
                'columns' => ['iD', 'code', 'name'],
                'rows' => [
                    [1, 'ngo', 'Non-Governmental Organization (NGO)'],
                    [2, 'cso', 'Civil Society / Community-Based Organization (CSO/CBO)'],
                    [3, 'un', 'United Nations / Multilateral Agency'],
                    [4, 'government', 'Public Sector / Government / Parastatal'],
                    [5, 'private', 'Private Corporate / Commercial Enterprise'],
                    [6, 'consultancy', 'Consultancy / Professional Advisory Firm'],
                    [7, 'academic', 'Academic / Research Institution'],
                    [8, 'self_employed', 'Self-employed / Independent Practice']
                ]
            ],

            // 4. Commercials & Users
            'engagementbasis' => [
                'columns' => ['iD', 'code', 'name'],
                'rows' => [
                    [1, 'fulltime', 'Full-time'],
                    [2, 'parttime', 'Part-time'],
                    [3, 'consultancy', 'Consultancy / Contract'],
                    [4, 'volunteer', 'Volunteer / Internship'],
                    [5, 'attachment', 'Work-Related Learning / Attachment']
                ]
            ],

            'engagementmodel' => [
                'columns' => ['iD', 'code', 'name', 'description'],
                'rows' => [
                    [1, 'shared', 'Shared Tier (Up to 5 client organisations at 20% level of effort each)', 'Apprentice placement model across multiple clients.'],
                    [2, 'dedicated', 'Dedicated Tier (Single client organisation at 100% level of effort)', 'Dedicated placement model for a single client.'],
                    [3, 'either', 'Flexible / Either Tier', 'Open to both shared and dedicated engagements.'],
                    [4, 'advisory', 'Advisory & Quality Sign-Off (On-demand Associate Model)', 'Associate expert review layer.']
                ]
            ],

            'worklocationpreference' => [
                'columns' => ['iD', 'code', 'name'],
                'rows' => [
                    [1, 'client_site', 'On-site at client offices'],
                    [2, 'remote', 'Remote (Tsigiro-coordinated)'],
                    [3, 'hybrid', 'Hybrid (Mix of on-site and remote)'],
                    [4, 'no_preference', 'No preference / Flexible']
                ]
            ],

            'invoiceentitytype' => [
                'columns' => ['iD', 'code', 'name'],
                'rows' => [
                    [1, 'pvt_ltd', 'Registered Private Limited Company (Pvt Ltd)'],
                    [2, 'sole_trader', 'Sole Trader / Registered Business Name'],
                    [3, 'individual', 'Individual / Natural Person (Withholding tax applies if no ITF263)']
                ]
            ],

            'refereecontacttiming' => [
                'columns' => ['iD', 'code', 'name'],
                'rows' => [
                    [1, 'now', 'At any time (Now)'],
                    [2, 'after_interview', 'Only after an interview has been conducted'],
                    [3, 'before_offer', 'Only immediately prior to a formal placement offer']
                ]
            ],

            'refereeverificationstatus' => [
                'columns' => ['iD', 'code', 'name'],
                'rows' => [
                    [1, 'pending', 'Pending Outreach'],
                    [2, 'contacted', 'Contacted / Reference Form Sent'],
                    [3, 'verified', 'Verified / Satisfactory Feedback Received'],
                    [4, 'flagged', 'Flagged / Discrepancy Found']
                ]
            ]
        ];
    }

    /**
     * Ensure all underlying tables exist by invoking models.
     */
    public static function ensureTablesExist(): void
    {
        $models = [
            new \App\Models\Servicefunction(),
            new \App\Models\Skillitem(),
            new \App\Models\Proficiencylevel(),
            new \App\Models\Vettingrecommendation(),
            new \App\Models\Applicationtrack(),
            new \App\Models\Applicationstatus(),
            new \App\Models\Apprenticestatus(),
            new \App\Models\Employmentstatus(),
            new \App\Models\Zimprovince(),
            new \App\Models\Workrightstatus(),
            new \App\Models\Qualificationtype(),
            new \App\Models\Qualificationstatus(),
            new \App\Models\Professionalbody(),
            new \App\Models\Sectortype(),
            new \App\Models\Engagementbasis(),
            new \App\Models\Engagementmodel(),
            new \App\Models\Worklocationpreference(),
            new \App\Models\Invoiceentitytype(),
            new \App\Models\Refereecontacttiming(),
            new \App\Models\Refereeverificationstatus(),
        ];
        unset($models);
    }

    /**
     * Seeds all 19 dictionary tables idempotently.
     */
    public static function seedAll(?PDO $pdo = null): array
    {
        self::ensureTablesExist();

        if ($pdo === null) {
            $pdo = Database::sharedPdo();
        }

        $dataset = self::getDataset();
        $results = [
            'status' => 'success',
            'tables' => [],
            'total_tables_checked' => count($dataset),
            'tables_seeded_count' => 0,
            'total_records_inserted' => 0,
            'already_seeded_count' => 0
        ];

        foreach ($dataset as $tableName => $tableData) {
            $columns = $tableData['columns'];
            $rows = $tableData['rows'];

            // Check existing records count
            $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$tableName}`")->fetchColumn();

            if ($count > 0) {
                $results['tables'][$tableName] = [
                    'status' => 'already_seeded',
                    'count' => $count,
                    'inserted' => 0
                ];
                $results['already_seeded_count']++;
                continue;
            }

            // Insert default rows
            $colList = implode(', ', array_map(fn($c) => "`{$c}`", $columns));
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $sql = "INSERT INTO `{$tableName}` ({$colList}) VALUES ({$placeholders})";
            $stmt = $pdo->prepare($sql);

            $inserted = 0;
            foreach ($rows as $row) {
                $stmt->execute($row);
                $inserted++;
            }

            $results['tables'][$tableName] = [
                'status' => 'seeded',
                'count' => $inserted,
                'inserted' => $inserted
            ];
            $results['tables_seeded_count']++;
            $results['total_records_inserted'] += $inserted;
        }

        return $results;
    }

    /**
     * Returns a list of dictionary tables that have 0 records.
     */
    public static function getUnseededTables(?PDO $pdo = null): array
    {
        self::ensureTablesExist();

        if ($pdo === null) {
            $pdo = Database::sharedPdo();
        }

        $dataset = self::getDataset();
        $unseeded = [];

        foreach (array_keys($dataset) as $tableName) {
            $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$tableName}`")->fetchColumn();
            if ($count === 0) {
                $unseeded[] = $tableName;
            }
        }

        return $unseeded;
    }

    /**
     * Checks whether all dictionary tables have at least 1 record.
     */
    public static function isFullySeeded(?PDO $pdo = null): bool
    {
        return count(self::getUnseededTables($pdo)) === 0;
    }

    /**
     * Generate raw SQL INSERT statements for table initialization in models.
     */
    public static function getInsertQueriesForTable(string $table): array
    {
        $dataset = self::getDataset()[$table] ?? null;
        if (!$dataset) {
            return [];
        }

        $queries = [];
        $columns = implode(', ', array_map(fn($c) => "`{$c}`", $dataset['columns']));

        foreach ($dataset['rows'] as $row) {
            $escaped = array_map(function ($val) {
                if ($val === null) {
                    return 'NULL';
                }
                if (is_numeric($val)) {
                    return (string) $val;
                }
                return "'" . str_replace("'", "''", (string) $val) . "'";
            }, $row);

            $queries[] = "INSERT INTO `{$table}` ({$columns}) VALUES (" . implode(', ', $escaped) . ")";
        }

        return $queries;
    }
}

