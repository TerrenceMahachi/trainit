<?php
/**
 * 1-Year Comprehensive Simulation Seeder for Tsigiro Portal
 * 
 * Simulates 12 months (September 2025 - August 2026) of multi-client operations:
 * - 4 Diverse Corporate Client Organizations
 * - Service Retainer Agreements with capacity hours & blended rate cards
 * - 2 Associates & 2 Apprentices collaborating on real deliverables
 * - 24+ Service Requests with paired work assignments & mentorship supervision
 * - 80+ Collaborative message threads & status progressions
 * - 12 Monthly Payroll Cycles with payslips & disbursements for staff, apprentices & associates
 * - 48 Itemized Client Invoices with VAT, line items, and electronic bank payments
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Models\Database;
use App\Models\User;
use App\Models\Login;
use App\Models\Staffprofile;
use App\Models\Rosterapplication;
use App\Models\Apprenticeprofile;
use App\Models\Associateprofile;
use App\Models\Clientorganization;
use App\Models\Clientmembership;
use App\Models\Clientserviceplan;
use App\Models\Serviceoffering;
use App\Models\Servicerequest;
use App\Models\Servicerequeststatus;
use App\Models\Servicerequeststatusevent;
use App\Models\Servicerequesttriage;
use App\Models\Servicerequestclosure;
use App\Models\Workassignment;
use App\Models\Assignmentsupervisor;
use App\Models\Requestmessage;
use App\Models\Stafftimeentry;
use App\Models\Payrollperiod;
use App\Models\Payrollperiodapproval;
use App\Models\Payslip;
use App\Models\Payslipitem;
use App\Models\Payslipdisbursement;
use App\Models\Clientinvoice;
use App\Models\Clientinvoiceitem;
use App\Models\Clientinvoicepayment;

echo "=== Starting Tsigiro 1-Year Multi-Client Simulation Seeder ===\n\n";

$db = new Database();
$pdo = $db->getPDO();

// Helper: provision user + login
function getOrCreateUser(string $name, string $email, int $role, string $password = 'Password123!'): int {
    $users = User::findByQuery("SELECT * FROM user WHERE email = ?", [$email]);
    if (!empty($users)) {
        $u = $users[0];
        $userId = (int)$u->iD;
        // Ensure active credentials
        $logins = Login::where('user', $userId);
        if (!empty($logins)) {
            $l = $logins[0];
            $l->password = password_hash($password, PASSWORD_BCRYPT);
            $l->status = 1;
            $l->update();
        } else {
            $l = new Login();
            $l->user = $userId;
            $l->password = password_hash($password, PASSWORD_BCRYPT);
            $l->status = 1;
            $l->reg_by = $userId;
            $l->save();
        }
        return $userId;
    }

    $u = new User();
    $u->name = $name;
    $u->email = $email;
    $u->role = $role;
    $u->reg_by = 1;
    $u->save();
    $userId = (int)$u->iD;

    $l = new Login();
    $l->user = $userId;
    $l->password = password_hash($password, PASSWORD_BCRYPT);
    $l->status = 1;
    $l->reg_by = 1;
    $l->save();

    return $userId;
}

// 1. Provision Key Actors
echo "--- 1. Provisioning Users & Core Personas ---\n";
$adminId      = getOrCreateUser('System Administrator', 'admin@tsigiro.co.zw', 1);
$managerId    = getOrCreateUser('Tafadzwa Mutasa', 'manager@tsigiro.co.zw', 6);
$financeId    = getOrCreateUser('Nyasha Chidziwa', 'finance@tsigiro.co.zw', 7);
$vettingId    = getOrCreateUser('Ruvimbo Sithole', 'vetting@tsigiro.co.zw', 8);

// Associates
$associate1Id = getOrCreateUser('Simbarashe Hove', 'associate@tsigiro.co.zw', 2);
$associate2Id = getOrCreateUser('Ruvimbo Chitepo', 'rchitepo@tsigiro.co.zw', 2);

// Apprentices
$apprentice1Id = getOrCreateUser('Kudzai Mapfumo', 'apprentice@tsigiro.co.zw', 2);
$apprentice2Id = getOrCreateUser('Tariro Moyo', 'tmoyo@tsigiro.co.zw', 2);

// Client Leads
$client1LeadId = getOrCreateUser('Tinashe Gumbo', 'client@tsigiro.co.zw', 3);
$client2LeadId = getOrCreateUser('Tariro Chidzero', 'tariro@zimfin.co.zw', 3);
$client3LeadId = getOrCreateUser('Blessed Ndlovu', 'blessed@deltatech.co.zw', 3);
$client4LeadId = getOrCreateUser('Dr. Rutendo Manyika', 'rutendo@afrihealth.co.zw', 3);

echo "  + Core users active.\n\n";

// 2. Provision Staff Profiles for internal staff
echo "--- 2. Checking Internal Staff Profiles ---\n";
$staffConfig = [
    ['user' => $managerId, 'code' => 'TSG-STF-006', 'title' => 'Service Delivery Manager', 'dept' => 2, 'salary' => 2400.00],
    ['user' => $financeId, 'code' => 'TSG-STF-007', 'title' => 'Financial Controller & Billing Officer', 'dept' => 3, 'salary' => 2200.00],
    ['user' => $vettingId, 'code' => 'TSG-STF-008', 'title' => 'Vetting & Compliance Officer', 'dept' => 2, 'salary' => 1900.00],
];
$staffProfileMap = [];
foreach ($staffConfig as $sc) {
    $existing = Staffprofile::where('user', $sc['user']);
    if (!empty($existing)) {
        $staffProfileMap[$sc['user']] = (int)$existing[0]->iD;
    } else {
        $sp = new Staffprofile();
        $sp->user = $sc['user'];
        $sp->staff_number = $sc['code'];
        $sp->job_title = $sc['title'];
        $sp->department = $sc['dept'];
        $sp->employmentstatus = 1;
        $sp->hire_date = '2024-01-15';
        $sp->basic_salary = $sc['salary'];
        $sp->currency = 'USD';
        $sp->bank_name = 'Stanbic Bank Zimbabwe';
        $sp->bank_account_number = '914000' . rand(100000, 999999);
        $sp->onboarding_status = 3;
        $sp->reg_by = 1;
        $sp->status = 1;
        $sp->save();
        $staffProfileMap[$sc['user']] = (int)$sp->iD;
    }
}
echo "  + Staff profiles verified.\n\n";

// 3. Provision 4 Corporate Clients
echo "--- 3. Provisioning 4 Corporate Client Organizations & Retainers ---\n";
$clientsData = [
    [
        'legal_name' => 'EcoSolutions Zimbabwe (Pvt) Ltd',
        'trading_name' => 'EcoSolutions',
        'reg_num' => 'ZW-CO-2023-8871',
        'tax_num' => 'BP20098177',
        'email' => 'billing@ecosolutions.co.zw',
        'address' => '12 Enterprise Road, Newlands',
        'city' => 'Harare',
        'lead_user' => $client1LeadId,
        'plan_name' => 'Enterprise Agrotech & IoT Retainer',
        'monthly_fee' => 3500.00,
        'hours' => 60.0,
        'assoc_rate' => 45.00,
        'appr_rate' => 18.00,
        'renewal_day' => 1,
    ],
    [
        'legal_name' => 'ZimFin Microfinance Bank (Pvt) Ltd',
        'trading_name' => 'ZimFin Bank',
        'reg_num' => 'ZW-CO-2021-4491',
        'tax_num' => 'BP20076122',
        'email' => 'accounts@zimfin.co.zw',
        'address' => '10 Samora Machel Avenue, CBD',
        'city' => 'Harare',
        'lead_user' => $client2LeadId,
        'plan_name' => 'Fintech Cloud & Compliance Retainer',
        'monthly_fee' => 4800.00,
        'hours' => 80.0,
        'assoc_rate' => 50.00,
        'appr_rate' => 20.00,
        'renewal_day' => 5,
    ],
    [
        'legal_name' => 'Delta Tech Logistics (Pvt) Ltd',
        'trading_name' => 'Delta Logistics',
        'reg_num' => 'ZW-CO-2022-3810',
        'tax_num' => 'BP20081044',
        'email' => 'accounts@deltatech.co.zw',
        'address' => '45 Joshua Nkomo Street',
        'city' => 'Bulawayo',
        'lead_user' => $client3LeadId,
        'plan_name' => 'Fleet Telematics & Systems Retainer',
        'monthly_fee' => 2800.00,
        'hours' => 45.0,
        'assoc_rate' => 40.00,
        'appr_rate' => 16.00,
        'renewal_day' => 10,
    ],
    [
        'legal_name' => 'AfriHealth Systems (Pvt) Ltd',
        'trading_name' => 'AfriHealth Telemedicine',
        'reg_num' => 'ZW-CO-2023-9912',
        'tax_num' => 'BP20094580',
        'email' => 'finance@afrihealth.co.zw',
        'address' => '88 Herbert Chitepo Street',
        'city' => 'Mutare',
        'lead_user' => $client4LeadId,
        'plan_name' => 'HealthTech SLA & Data Retainer',
        'monthly_fee' => 3200.00,
        'hours' => 50.0,
        'assoc_rate' => 45.00,
        'appr_rate' => 18.00,
        'renewal_day' => 15,
    ],
];

$clientEntities = [];
foreach ($clientsData as $cd) {
    $existing = Clientorganization::where('legal_name', $cd['legal_name']);
    if (!empty($existing)) {
        $cObj = $existing[0];
        $cId = (int)$cObj->iD;
    } else {
        $cObj = new Clientorganization();
        $cObj->legal_name = $cd['legal_name'];
        $cObj->trading_name = $cd['trading_name'];
        $cObj->registration_number = $cd['reg_num'];
        $cObj->tax_number = $cd['tax_num'];
        $cObj->billing_email = $cd['email'];
        $cObj->address = $cd['address'];
        $cObj->city = $cd['city'];
        $cObj->country = 'Zimbabwe';
        $cObj->primary_phone = '+263 242 ' . rand(700000, 799999);
        $cObj->status = 1;
        $cObj->reg_by = 1;
        $cObj->save();
        $cId = (int)$cObj->iD;
    }

    // Ensure client membership as Owner
    $mem = Clientmembership::findByQuery("SELECT * FROM clientmembership WHERE clientorganization = ? AND user = ?", [$cId, $cd['lead_user']]);
    if (empty($mem)) {
        $m = new Clientmembership();
        $m->clientorganization = $cId;
        $m->user = $cd['lead_user'];
        $m->clientmemberrole = 1; // Owner
        $m->status = 1;
        $m->reg_by = 1;
        $m->save();
    }

    // Ensure service plan
    $plans = Clientserviceplan::where('clientorganization', $cId);
    if (!empty($plans)) {
        $planObj = $plans[0];
        $planId = (int)$planObj->iD;
    } else {
        $p = new Clientserviceplan();
        $p->clientorganization = $cId;
        $p->serviceoffering = 1;
        $p->plan_name = $cd['plan_name'];
        $p->currency = 'USD';
        $p->monthly_fee = $cd['monthly_fee'];
        $p->included_hours = $cd['hours'];
        $p->associate_rate = $cd['assoc_rate'];
        $p->apprentice_rate = $cd['appr_rate'];
        $p->billing_cycle_day = $cd['renewal_day'];
        $p->service_manager = $managerId;
        $p->billing_owner = $financeId;
        $p->excesspolicy = 1;
        $p->start_date = '2025-09-01';
        $p->status = 1;
        $p->reg_by = 1;
        $p->save();
        $planId = (int)$p->iD;
        $planObj = $p;
    }

    $clientEntities[] = [
        'id' => $cId,
        'client' => $cObj,
        'plan_id' => $planId,
        'plan' => $planObj,
        'lead_user' => $cd['lead_user'],
        'data' => $cd,
    ];
    echo "  + Client: {$cd['trading_name']} (#{$cId}) with Plan #{$planId}\n";
}
echo "\n";

// 4. Provision 24 Service Requests across the 12-Month Period (Sep 2025 - Aug 2026)
echo "--- 4. Provisioning 12-Month Interaction Requests (Sep 2025 – Aug 2026) ---\n";

$months = [
    ['code' => '2025-09', 'name' => 'September 2025', 'start' => '2025-09-01', 'end' => '2025-09-30', 'pay' => '2025-09-28'],
    ['code' => '2025-10', 'name' => 'October 2025',   'start' => '2025-10-01', 'end' => '2025-10-31', 'pay' => '2025-10-28'],
    ['code' => '2025-11', 'name' => 'November 2025',  'start' => '2025-11-01', 'end' => '2025-11-30', 'pay' => '2025-11-28'],
    ['code' => '2025-12', 'name' => 'December 2025',  'start' => '2025-12-01', 'end' => '2025-12-31', 'pay' => '2025-12-22'],
    ['code' => '2026-01', 'name' => 'January 2026',   'start' => '2026-01-01', 'end' => '2026-01-31', 'pay' => '2026-01-28'],
    ['code' => '2026-02', 'name' => 'February 2026',  'start' => '2026-02-01', 'end' => '2026-02-28', 'pay' => '2026-02-27'],
    ['code' => '2026-03', 'name' => 'March 2026',     'start' => '2026-03-01', 'end' => '2026-03-31', 'pay' => '2026-03-27'],
    ['code' => '2026-04', 'name' => 'April 2026',     'start' => '2026-04-01', 'end' => '2026-04-30', 'pay' => '2026-04-28'],
    ['code' => '2026-05', 'name' => 'May 2026',       'start' => '2026-05-01', 'end' => '2026-05-31', 'pay' => '2026-05-28'],
    ['code' => '2026-06', 'name' => 'June 2026',      'start' => '2026-06-01', 'end' => '2026-06-30', 'pay' => '2026-06-26'],
    ['code' => '2026-07', 'name' => 'July 2026',      'start' => '2026-07-01', 'end' => '2026-07-31', 'pay' => '2026-07-28'],
    ['code' => '2026-08', 'name' => 'August 2026',    'start' => '2026-08-01', 'end' => '2026-08-31', 'pay' => '2026-08-28'],
];

// Project scenarios: 2 per month = 24 projects
$projectScenarios = [
    // Month 1 (Sep 2025)
    [
        'client_idx' => 0, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Solar Telemetry IoT Ingestion Pipeline & AWS Migration',
        'desc' => 'Architect and implement scalable MQTT broker and AWS Lambda pipeline to ingest inverter telemetry from 14 solar farm installations across Mashonaland.',
        'day' => 4, 'duration_days' => 18,
        'mentor_note' => 'Guided Kudzai on setting up AWS IoT Core rules and DynamoDB time-series partitions. Kudzai implemented the ingestion Lambdas with clean async error handling.',
        'assoc_hours' => 24.0, 'appr_hours' => 32.0,
    ],
    [
        'client_idx' => 1, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'RBZ Core Banking Interoperability & API Security Audit',
        'desc' => 'Perform rigorous penetration test, OWASP vulnerability scan, and implement HMAC signature validation on outbound micro-loan disbursement APIs.',
        'day' => 8, 'duration_days' => 16,
        'mentor_note' => 'Tariro conducted the static code analysis with SonarQube. Reviewed Tariros report and co-authored the mitigation patch for token revocation.',
        'assoc_hours' => 28.0, 'appr_hours' => 36.0,
    ],
    // Month 2 (Oct 2025)
    [
        'client_idx' => 2, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Bulawayo-Harare Route GPS Fleet Tracking API Integration',
        'desc' => 'Integrate Ruptela GPS telematics with Delta Tech central dispatch ERP. Implement geofence triggers for tollgate arrival notifications.',
        'day' => 3, 'duration_days' => 20,
        'mentor_note' => 'Supervised Kudzais integration of real-time WebSocket feeds for the dispatcher dashboard. Code quality was excellent.',
        'assoc_hours' => 22.0, 'appr_hours' => 30.0,
    ],
    [
        'client_idx' => 3, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'Offline-First Sync for Remote Rural Health Clinics',
        'desc' => 'Design SQLite client-side synchronization for mobile nursing tablets visiting remote clinics in Manicaland with intermittent connectivity.',
        'day' => 7, 'duration_days' => 19,
        'mentor_note' => 'Tariro designed the conflict-resolution algorithm using vector clocks. Assisted with testing edge cases under low-bandwidth simulation.',
        'assoc_hours' => 25.0, 'appr_hours' => 34.0,
    ],
    // Month 3 (Nov 2025)
    [
        'client_idx' => 0, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Agri-Weather Sensor Anomaly Detection Dashboard',
        'desc' => 'Build automated alert system for humidity and soil temperature anomalies across irrigation pivots in Mazowe.',
        'day' => 5, 'duration_days' => 17,
        'mentor_note' => 'Kudzai completed the frontend charts and scheduled cron triggers. Reviewed SQL aggregations to prevent table lockups.',
        'assoc_hours' => 20.0, 'appr_hours' => 28.0,
    ],
    [
        'client_idx' => 1, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'ZIMRA Fiscal Tax Integration for Micro-Loan Disbursements',
        'desc' => 'Develop automated XML tax submission bridge communicating with ZIMRA TaRMS server for electronic invoice verification.',
        'day' => 11, 'duration_days' => 15,
        'mentor_note' => 'Supervised Tariro through the ZIMRA public-key exchange ceremony and payload signing. Delivery was certified on first sandbox run.',
        'assoc_hours' => 26.0, 'appr_hours' => 35.0,
    ],
    // Month 4 (Dec 2025)
    [
        'client_idx' => 2, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Cold-Chain Temperature Sensor Telematics Alert System',
        'desc' => 'Deploy IoT alerts for refrigerated meat transport trailers travelling between Gweru and Harare. Immediate SMS/WhatsApp notification on threshold breach.',
        'day' => 2, 'duration_days' => 16,
        'mentor_note' => 'Kudzai implemented Twilio/AfricaTalking WhatsApp webhooks. Coached on queue idempotency and retry backoff.',
        'assoc_hours' => 18.0, 'appr_hours' => 26.0,
    ],
    [
        'client_idx' => 3, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'Patient EMR Health Record Encryption & Compliance Hardening',
        'desc' => 'Implement field-level AES-256 encryption on patient medical history fields and audit-log access trails.',
        'day' => 6, 'duration_days' => 18,
        'mentor_note' => 'Tariro configured database transparent data encryption (TDE) and key rotation scripts. Demonstrated solid grasp of cybersecurity standards.',
        'assoc_hours' => 24.0, 'appr_hours' => 32.0,
    ],
    // Month 5 (Jan 2026)
    [
        'client_idx' => 0, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Enterprise Solar Retainer Capacity & Firmware OTA Updates',
        'desc' => 'Develop over-the-air firmware distribution service for remote solar controller hardware with rollback protection.',
        'day' => 6, 'duration_days' => 21,
        'mentor_note' => 'Kudzai handled binary chunking and checksum verification. Co-authored integration test suite covering flaky mobile network drops.',
        'assoc_hours' => 25.0, 'appr_hours' => 34.0,
    ],
    [
        'client_idx' => 1, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'USSD Mobile Banking Gateway Failover & Load Balancer',
        'desc' => 'Configure HAProxy load balancing and automated health probes across Econet and NetOne USSD aggregators.',
        'day' => 10, 'duration_days' => 18,
        'mentor_note' => 'Tariro set up automated keepalived failover pairs. Conducted load injection simulating 250 requests/sec with zero packet loss.',
        'assoc_hours' => 28.0, 'appr_hours' => 38.0,
    ],
    // Month 6 (Feb 2026)
    [
        'client_idx' => 2, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Fleet Dispatcher Automated Fuel Consumption Analytics',
        'desc' => 'Build automated variance reports comparing driver fuel card receipts against CAN-bus fuel level readings.',
        'day' => 4, 'duration_days' => 19,
        'mentor_note' => 'Kudzai built the Python Pandas data reconciliation pipeline. Mentored on edge case handling for fuel siphon false-alarms.',
        'assoc_hours' => 21.0, 'appr_hours' => 29.0,
    ],
    [
        'client_idx' => 3, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'Telemedicine Video Consultation WebRTC Integration',
        'desc' => 'Implement secure browser-based peer-to-peer video rooms connecting rural nurses to specialist doctors in Harare.',
        'day' => 8, 'duration_days' => 17,
        'mentor_note' => 'Tariro configured coturn STUN/TURN servers on Ubuntu. Video latency measured under 120ms over 4G connections.',
        'assoc_hours' => 26.0, 'appr_hours' => 36.0,
    ],
    // Month 7 (Mar 2026)
    [
        'client_idx' => 0, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Smart Irrigation Soil Moisture Telemetry Controller',
        'desc' => 'Automated valve control system triggered by capacitive soil moisture sensors and forecasted rainfall probabilities.',
        'day' => 5, 'duration_days' => 20,
        'mentor_note' => 'Kudzai designed the scheduled cron dispatcher and automated valve duty-cycle safety shutoffs. Exceptional diligence.',
        'assoc_hours' => 23.0, 'appr_hours' => 31.0,
    ],
    [
        'client_idx' => 1, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'Micro-Lending Credit Scoring Automated Decision Engine',
        'desc' => 'Deploy rule-based credit scoring model utilizing mobile money transaction statements and historical repayment habits.',
        'day' => 9, 'duration_days' => 18,
        'mentor_note' => 'Tariro implemented the scoring decision tree. Validated score consistency across 5,000 back-tested customer records.',
        'assoc_hours' => 27.0, 'appr_hours' => 37.0,
    ],
    // Month 8 (Apr 2026)
    [
        'client_idx' => 2, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Logistics Driver Mobile App Bluetooth Geofencing',
        'desc' => 'Develop React Native mobile app component for automated proof-of-delivery logging when trucks enter depot perimeter.',
        'day' => 4, 'duration_days' => 21,
        'mentor_note' => 'Kudzai built the offline SQLite sync and Bluetooth beacon listener. Conducted successful field trial at Bulawayo central depot.',
        'assoc_hours' => 24.0, 'appr_hours' => 33.0,
    ],
    [
        'client_idx' => 3, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'Pharmacy Inventory Low-Stock Automated Push Notifications',
        'desc' => 'Automated restocking alert system tracking essential medicines and cold-chain vaccine inventories across 8 clinics.',
        'day' => 8, 'duration_days' => 17,
        'mentor_note' => 'Tariro built the inventory consumption projection model and WhatsApp automated supplier RFQ dispatch.',
        'assoc_hours' => 22.0, 'appr_hours' => 30.0,
    ],
    // Month 9 (May 2026)
    [
        'client_idx' => 0, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Agro-Processing Biogas Digester Telemetry Controller',
        'desc' => 'Monitor methane production pressure, temperature, and automated flare ignition for agro-waste processing plant.',
        'day' => 4, 'duration_days' => 20,
        'mentor_note' => 'Kudzai implemented safety threshold alarms with automated email and SMS notification. Clean, robust architectural pattern.',
        'assoc_hours' => 22.0, 'appr_hours' => 30.0,
    ],
    [
        'client_idx' => 1, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'Anti-Money Laundering (AML) Transaction Monitoring Pipeline',
        'desc' => 'Implement threshold alert rules for rapid peer-to-peer micro-loan recycling and suspicious structuring patterns.',
        'day' => 7, 'duration_days' => 19,
        'mentor_note' => 'Tariro developed the SQL analytical queries detecting circular transfers. Passed internal compliance verification without remarks.',
        'assoc_hours' => 26.0, 'appr_hours' => 36.0,
    ],
    // Month 10 (Jun 2026)
    [
        'client_idx' => 2, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Cross-Border Transit Customs Manifest Sync API',
        'desc' => 'Integrate freight manifests with Beitbridge border clearing agent XML format for accelerated pre-clearance.',
        'day' => 3, 'duration_days' => 18,
        'mentor_note' => 'Kudzai implemented the secure SFTP file transmission and XML schema validation. Handover to logistics team completed smoothly.',
        'assoc_hours' => 20.0, 'appr_hours' => 28.0,
    ],
    [
        'client_idx' => 3, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'Laboratory Specimen Tracking & QR Code Scanning Portal',
        'desc' => 'Web portal for scanning pathology specimen transport containers with timestamped GPS handover logging.',
        'day' => 8, 'duration_days' => 18,
        'mentor_note' => 'Tariro built the responsive web scanner utilizing HTML5 camera APIs. Tested on low-cost Android devices with high accuracy.',
        'assoc_hours' => 24.0, 'appr_hours' => 33.0,
    ],
    // Month 11 (Jul 2026)
    [
        'client_idx' => 0, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'BESS Battery Storage Degradation Analytics Dashboard',
        'desc' => 'Predictive battery health dashboard calculating remaining useful life for commercial lithium-ion storage units.',
        'day' => 5, 'duration_days' => 20,
        'mentor_note' => 'Kudzai completed the Python linear degradation models. Co-presented findings in executive client monthly review.',
        'assoc_hours' => 25.0, 'appr_hours' => 34.0,
    ],
    [
        'client_idx' => 1, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'EcoCash & OneMoney Real-Time Reversal Reconciliation',
        'desc' => 'Automated reconciliation worker matching bank settlement files against mobile wallet gateway webhooks.',
        'day' => 9, 'duration_days' => 18,
        'mentor_note' => 'Tariro resolved timing desynchronization edge cases. Reconciles 12,000 daily transactions in under 4 minutes.',
        'assoc_hours' => 27.0, 'appr_hours' => 37.0,
    ],
    // Month 12 (Aug 2026)
    [
        'client_idx' => 2, 'assoc' => $associate1Id, 'appr' => $apprentice1Id,
        'title' => 'Warehouse Pallet Barcode Scanning Mobile App Optimization',
        'desc' => 'Performance optimization of depot barcode scanner cutting recognition latency from 800ms to 110ms.',
        'day' => 4, 'duration_days' => 17,
        'mentor_note' => 'Kudzai optimized image processing buffers in WebAssembly. Warehouse staff reported immediate productivity boost.',
        'assoc_hours' => 19.0, 'appr_hours' => 27.0,
    ],
    [
        'client_idx' => 3, 'assoc' => $associate2Id, 'appr' => $apprentice2Id,
        'title' => 'Provincial Health Directorate Monthly Disease Surveillance Report',
        'desc' => 'Automated generation and encrypted dispatch of weekly epidemiology indicators to the Ministry of Health portal.',
        'day' => 7, 'duration_days' => 18,
        'mentor_note' => 'Tariro built the PDF export templates and cryptographic signature verification. Successfully delivered on deadline.',
        'assoc_hours' => 23.0, 'appr_hours' => 31.0,
    ],
];

$createdRequests = [];
foreach ($projectScenarios as $idx => $sc) {
    $cEntity = $clientEntities[$sc['client_idx']];
    $monthIdx = (int)floor($idx / 2);
    $mInfo = $months[$monthIdx];
    $reqNum = sprintf('REQ-%s-%s-%03d', strtoupper(substr($cEntity['data']['trading_name'], 0, 3)), str_replace('-', '', $mInfo['code']), ($idx % 2) + 1);

    // Check existing
    $existingReq = Servicerequest::where('request_number', $reqNum);
    if (!empty($existingReq)) {
        $req = $existingReq[0];
        $reqId = (int)$req->iD;
    } else {
        $reqDate = sprintf('%s-%02d 09:30:00', $mInfo['code'], $sc['day']);
        $dueDate = date('Y-m-d', strtotime($reqDate . " + {$sc['duration_days']} days"));

        $req = new Servicerequest();
        $req->request_number = $reqNum;
        $req->clientorganization = $cEntity['id'];
        $req->clientserviceplan = $cEntity['plan_id'];
        $req->requester = $cEntity['lead_user'];
        $req->prioritylevel = (($idx % 3) === 0) ? 1 : 2; // Critical or High
        $req->title = $sc['title'];
        $req->description = $sc['desc'];
        $req->desired_due_date = $dueDate;
        $req->reg_by = $cEntity['lead_user'];
        $req->reg_date = $reqDate;
        $req->status = 1;
        $req->save();
        $reqId = (int)$req->iD;

        // Status events: NEW -> TRIAGED -> IN_PROGRESS -> RESOLVED -> CLOSED
        $events = [
            ['code' => 1, 'delay' => '0 hours', 'by' => $cEntity['lead_user'], 'notes' => 'Work brief logged by client.'],
            ['code' => 2, 'delay' => '3 hours', 'by' => $managerId, 'notes' => 'Request triaged and SLA priority assigned.'],
            ['code' => 3, 'delay' => '1 day',   'by' => $sc['assoc'], 'notes' => 'Assigned talent team commenced active delivery.'],
            ['code' => 4, 'delay' => $sc['duration_days'] - 2 . ' days', 'by' => $sc['assoc'], 'notes' => 'Implementation completed; staged for client review.'],
            ['code' => 5, 'delay' => $sc['duration_days'] . ' days',     'by' => $cEntity['lead_user'], 'notes' => 'Client approved deliverable and closed request.'],
        ];
        foreach ($events as $ev) {
            $evDate = date('Y-m-d H:i:s', strtotime($reqDate . " + {$ev['delay']}"));
            $db->query(
                "INSERT INTO servicerequeststatusevent (servicerequest, servicerequeststatus, notes, reg_by, reg_date, status)
                 VALUES (?, ?, ?, ?, ?, 1)",
                [$reqId, $ev['code'], $ev['notes'], $ev['by'], $evDate]
            );
        }

        // Triage
        $slaDate = date('Y-m-d H:i:s', strtotime($reqDate . ' + 4 hours'));
        $db->query(
            "INSERT INTO servicerequesttriage (servicerequest, sla_due_date, triage_notes, reg_by, reg_date, status)
             VALUES (?, ?, ?, ?, ?, 1)",
            [$reqId, $slaDate, 'Triaged under client monthly retainer plan. Paired specialist and apprentice assigned.', $managerId, $slaDate]
        );

        // Closure with 5-star rating
        $closeDate = date('Y-m-d H:i:s', strtotime($reqDate . " + {$sc['duration_days']} days"));
        $db->query(
            "INSERT INTO servicerequestclosure (servicerequest, closure_notes, satisfaction_rating, reg_by, reg_date, status)
             VALUES (?, ?, 5, ?, ?, 1)",
            [$reqId, 'Deliverables verified against SLA. Code reviewed and transferred to production with full documentation.', $cEntity['lead_user'], $closeDate]
        );

        // Work Assignment 1: Associate
        $assocDueDate = date('Y-m-d', strtotime($reqDate . " + {$sc['duration_days']} days"));
        $db->query(
            "INSERT INTO workassignment (servicerequest, user, assigned_role, rate_currency, hourly_rate_snapshot, due_date, reg_by, reg_date, status)
             VALUES (?, ?, 2, 'USD', ?, ?, ?, ?, 1)",
            [$reqId, $sc['assoc'], $cEntity['data']['assoc_rate'], $assocDueDate, $managerId, $reqDate]
        );
        $waAssocId = (int)$pdo->query("SELECT last_insert_rowid()")->fetchColumn();

        // Work Assignment 2: Apprentice
        $db->query(
            "INSERT INTO workassignment (servicerequest, user, assigned_role, rate_currency, hourly_rate_snapshot, due_date, reg_by, reg_date, status)
             VALUES (?, ?, 2, 'USD', ?, ?, ?, ?, 1)",
            [$reqId, $sc['appr'], $cEntity['data']['appr_rate'], $assocDueDate, $managerId, $reqDate]
        );
        $waApprId = (int)$pdo->query("SELECT last_insert_rowid()")->fetchColumn();

        // Mentorship Supervisor Link (Associate supervising Apprentice)
        $db->query(
            "INSERT INTO assignmentsupervisor (workassignment, supervisor_user, supervision_notes, reg_by, reg_date, status)
             VALUES (?, ?, ?, ?, ?, 1)",
            [$waApprId, $sc['assoc'], $sc['mentor_note'], $managerId, $reqDate]
        );

        // Collaborative Messages
        $assocUser = User::find($sc['assoc']);
        $apprUser = User::find($sc['appr']);
        $clientLeadUser = User::find($cEntity['lead_user']);

        $thread = [
            [
                'by' => $cEntity['lead_user'],
                'delay' => '+1 hour',
                'body' => "Hello Tsigiro team, please review this work request. Staging environment credentials and API specs have been attached in the Jira/GitLab repository."
            ],
            [
                'by' => $managerId,
                'delay' => '+3 hours',
                'body' => "Received and triaged under your monthly retainer agreement. Senior Specialist {$assocUser->name} has been assigned as lead mentor alongside {$apprUser->name} as implementation engineer."
            ],
            [
                'by' => $sc['assoc'],
                'delay' => '+1 day',
                'body' => "We have analyzed the architecture. {$apprUser->name} has set up the feature branch and automated unit tests. We will have the preliminary milestone staged by Friday."
            ],
            [
                'by' => $sc['appr'],
                'delay' => '+8 days',
                'body' => "Pull request #12 submitted with complete implementation. 98% test coverage achieved across all endpoints. Deployed to staging cluster for peer review."
            ],
            [
                'by' => $sc['assoc'],
                'delay' => '+12 days',
                'body' => "Code review completed and signed off. Performance testing confirms sub-150ms response times. Deliverable handed over for client acceptance testing."
            ],
            [
                'by' => $cEntity['lead_user'],
                'delay' => "+{$sc['duration_days']} days",
                'body' => "Tested thoroughly in our staging environment and promoted to production. Excellent work by {$assocUser->name} and {$apprUser->name}! Milestone accepted and signed off."
            ],
        ];

        foreach ($thread as $msg) {
            $msgDate = date('Y-m-d H:i:s', strtotime($reqDate . " {$msg['delay']}"));
            $db->query(
                "INSERT INTO requestmessage (servicerequest, messagevisibility, body, reg_by, reg_date, status)
                 VALUES (?, 1, ?, ?, ?, 1)",
                [$reqId, $msg['body'], $msg['by'], $msgDate]
            );
        }
    }

    $createdRequests[] = [
        'id' => $reqId,
        'num' => $reqNum,
        'client_id' => $cEntity['id'],
        'assoc_id' => $sc['assoc'],
        'appr_id' => $sc['appr'],
        'month_idx' => $monthIdx,
        'assoc_hours' => $sc['assoc_hours'],
        'appr_hours' => $sc['appr_hours'],
    ];
}
echo "  + 24 service requests, assignments, mentorship supervisor links, and collaboration threads provisioned.\n\n";

// 5. Provision 12 Monthly Payroll Periods with Payslips & Disbursements
echo "--- 5. Provisioning 12 Monthly Payroll Cycles (Sep 2025 – Aug 2026) ---\n";

foreach ($months as $mIdx => $m) {
    $periodCode = 'TSG-PAY-' . $m['code'];
    $existingPeriod = Payrollperiod::where('period_code', $periodCode);
    if (!empty($existingPeriod)) {
        $pPeriod = $existingPeriod[0];
        $pPeriodId = (int)$pPeriod->iD;
    } else {
        $p = new Payrollperiod();
        $p->period_code = $periodCode;
        $p->period_name = $m['name'] . ' Regular Payroll Cycle';
        $p->start_date = $m['start'];
        $p->end_date = $m['end'];
        $p->pay_date = $m['pay'];
        $p->status = 1;
        $p->reg_by = 1;
        $p->save();
        $pPeriodId = (int)$p->iD;
        $pPeriod = $p;

        // Executive Approval
        $db->query(
            "INSERT INTO payrollperiodapproval (payrollperiod, payperiodstatus, notes, reg_by, reg_date, status)
             VALUES (?, 3, 'Approved by executive operations desk after automated audit reconciliation.', ?, ?, 1)",
            [$pPeriodId, $adminId, $m['pay'] . ' 14:00:00']
        );
    }

    // Personnel payroll definitions for this month:
    // Staff (Manager, Finance, Vetting), Apprentices (Stipend + Project Participation), Associates (Billable Deliverables)
    $personnelPayroll = [
        // Staff
        [
            'staff_user' => $managerId,
            'gross' => 2400.00,
            'items' => [
                ['name' => 'Basic Salary - Service Delivery Manager', 'type' => 1, 'amt' => 2400.00],
                ['name' => 'PAYE Income Tax', 'type' => 2, 'amt' => -380.00],
                ['name' => 'NSSA Pension (3.5% Employee)', 'type' => 2, 'amt' => -35.00],
                ['name' => 'First Mutual Medical Aid', 'type' => 2, 'amt' => -120.00],
            ],
            'bank' => 'Stanbic Bank',
        ],
        [
            'staff_user' => $financeId,
            'gross' => 2200.00,
            'items' => [
                ['name' => 'Basic Salary - Financial Controller', 'type' => 1, 'amt' => 2200.00],
                ['name' => 'PAYE Income Tax', 'type' => 2, 'amt' => -340.00],
                ['name' => 'NSSA Pension (3.5% Employee)', 'type' => 2, 'amt' => -35.00],
                ['name' => 'CABS Medical Aid Plan', 'type' => 2, 'amt' => -110.00],
            ],
            'bank' => 'CABS',
        ],
        [
            'staff_user' => $vettingId,
            'gross' => 1900.00,
            'items' => [
                ['name' => 'Basic Salary - Vetting & Compliance Officer', 'type' => 1, 'amt' => 1900.00],
                ['name' => 'PAYE Income Tax', 'type' => 2, 'amt' => -280.00],
                ['name' => 'NSSA Pension (3.5% Employee)', 'type' => 2, 'amt' => -35.00],
                ['name' => 'Medical Aid Scheme', 'type' => 2, 'amt' => -95.00],
            ],
            'bank' => 'CBZ Bank',
        ],
        // Associates (Simbarashe Hove & Ruvimbo Chitepo) - Project Deliverables
        [
            'staff_user' => $associate1Id,
            'gross' => 2160.00 + ($mIdx * 40.00), // ~$2,200 - $2,600
            'items' => [
                ['name' => 'Retainer Project Delivery & Senior Architecture', 'type' => 1, 'amt' => 2160.00 + ($mIdx * 40.00)],
                ['name' => 'ZIMRA 10% Withholding Tax (ITF263 Cleared)', 'type' => 2, 'amt' => 0.00],
                ['name' => 'Professional Indemnity Insurance Cover', 'type' => 2, 'amt' => -85.00],
            ],
            'bank' => 'Stanbic Bank (Nostro USD)',
        ],
        [
            'staff_user' => $associate2Id,
            'gross' => 1980.00 + ($mIdx * 35.00),
            'items' => [
                ['name' => 'Fullstack Engineering & Code Review Deliverables', 'type' => 1, 'amt' => 1980.00 + ($mIdx * 35.00)],
                ['name' => 'Professional Group Cover Contribution', 'type' => 2, 'amt' => -75.00],
            ],
            'bank' => 'CABS (Nostro USD)',
        ],
        // Apprentices (Kudzai Mapfumo & Tariro Moyo) - WRL Attachment Stipends + Project Bonuses
        [
            'staff_user' => $apprentice1Id,
            'gross' => 520.00,
            'items' => [
                ['name' => 'NUST WRL Attachment Base Stipend', 'type' => 1, 'amt' => 350.00],
                ['name' => 'Client Project Implementation Deliverable Bonus', 'type' => 1, 'amt' => 170.00],
                ['name' => 'Student Medical & Incident Cover', 'type' => 2, 'amt' => -20.00],
            ],
            'bank' => 'EcoCash Business / CABS',
        ],
        [
            'staff_user' => $apprentice2Id,
            'gross' => 500.00,
            'items' => [
                ['name' => 'UZ WRL Attachment Base Stipend', 'type' => 1, 'amt' => 350.00],
                ['name' => 'Client Engineering Milestone Bonus', 'type' => 1, 'amt' => 150.00],
                ['name' => 'Student Medical & Incident Cover', 'type' => 2, 'amt' => -20.00],
            ],
            'bank' => 'EcoCash Business / CBZ',
        ],
    ];

    foreach ($personnelPayroll as $pp) {
        // Staffprofile ID
        $spId = $staffProfileMap[$pp['staff_user']] ?? null;
        if (!$spId) {
            // Create dummy staffprofile for talent to link payslip
            $existingSp = Staffprofile::where('user', $pp['staff_user']);
            if (!empty($existingSp)) {
                $spId = (int)$existingSp[0]->iD;
            } else {
                $uObj = User::find($pp['staff_user']);
                $nameParts = explode(' ', $uObj->name);
                $firstName = $nameParts[0];
                $surname = $nameParts[1] ?? 'Talent';
                $db->query(
                    "INSERT INTO staffprofile (user, employee_number, job_title, department, nature_of_employment, employee_type, date_of_employment, work_email, first_name, surname, town, region, country, status)
                     VALUES (?, ?, ?, 'Talent Roster', 'CONTRACTOR', 'Talent', '2025-08-15', ?, ?, ?, 'HARARE', 'HRE', 'Zimbabwe', 1)",
                    [$pp['staff_user'], 'TSG-TLT-' . $pp['staff_user'], $uObj->name, $uObj->email, $firstName, $surname]
                );
                $spId = (int)$pdo->query("SELECT last_insert_rowid()")->fetchColumn();
                $staffProfileMap[$pp['staff_user']] = $spId;
            }
        }

        // Check existing payslip
        $existingPs = Payslip::findByQuery(
            "SELECT * FROM payslip WHERE payrollperiod = ? AND staffprofile = ?",
            [$pPeriodId, $spId]
        );
        if (!empty($existingPs)) {
            continue;
        }

        $totalDeds = 0;
        foreach ($pp['items'] as $it) {
            if ($it['amt'] < 0) {
                $totalDeds += abs($it['amt']);
            }
        }
        $netPay = $pp['gross'] - $totalDeds;

        $ps = new Payslip();
        $ps->payrollperiod = $pPeriodId;
        $ps->staffprofile = $spId;
        $ps->currency = 'USD';
        $ps->gross_pay = $pp['gross'];
        $ps->total_deductions = $totalDeds;
        $ps->net_pay = $netPay;
        $ps->reg_by = $financeId;
        $ps->reg_date = $m['pay'] . ' 10:00:00';
        $ps->status = 1;
        $ps->save();
        $psId = (int)$ps->iD;

        // Line items
        foreach ($pp['items'] as $it) {
            $db->query(
                "INSERT INTO payslipitem (payslip, payrollitemtype, item_name, amount, reg_by, reg_date, status)
                 VALUES (?, ?, ?, ?, ?, ?, 1)",
                [$psId, $it['type'], $it['name'], abs($it['amt']), $financeId, $m['pay'] . ' 10:00:00']
            );
        }

        // Disbursement record
        $ref = sprintf('DISB-%s-%s-%04d', strtoupper(substr($pp['bank'], 0, 3)), str_replace('-', '', $m['code']), rand(1000, 9999));
        $db->query(
            "INSERT INTO payslipdisbursement (payslip, payment_method, transaction_reference, reg_by, reg_date, status)
             VALUES (?, 'Electronic Funds Transfer', ?, ?, ?, 1)",
            [$psId, $ref, $financeId, $m['pay'] . ' 16:30:00']
        );
    }
    echo "  + Cycle {$periodCode} ({$m['name']}): 7 payslips & bank disbursements generated.\n";
}
echo "\n";

// 6. Provision 48 Itemized Client Invoices (4 Clients x 12 Months)
echo "--- 6. Provisioning 48 Monthly Client Invoices (4 Clients x 12 Months) ---\n";

foreach ($months as $mIdx => $m) {
    foreach ($clientEntities as $cIdx => $cEntity) {
        $invNum = sprintf('TSG-INV-%s-%03d', str_replace('-', '', $m['code']), ($cIdx + 1) * 10 + 1);

        $existingInv = Clientinvoice::where('invoice_number', $invNum);
        if (!empty($existingInv)) {
            continue;
        }

        $isCurrentMonth = ($mIdx === 11); // Month 12 (Aug 2026) is Payment Due / Current
        $payStatus = $isCurrentMonth ? 1 : 2; // 1 = Due/Pending, 2 = Paid

        $retainerFee = $cEntity['data']['monthly_fee'];
        $assocRate = $cEntity['data']['assoc_rate'];
        $apprRate = $cEntity['data']['appr_rate'];

        // Associate & apprentice hours for this client this month
        $assocHours = 20.0 + (($mIdx + $cIdx) % 8);
        $apprHours = 28.0 + (($mIdx + $cIdx) % 10);
        $assocCost = $assocHours * $assocRate;
        $apprCost = $apprHours * $apprRate;

        // In months 4, 7, 10, simulate project scope expansion (excess billable hours)
        $excessHours = 0;
        $excessCost = 0;
        if (in_array($mIdx, [3, 6, 9])) {
            $excessHours = 8.5;
            $excessCost = $excessHours * $assocRate;
        }

        $subtotal = $retainerFee + $excessCost;
        $vatRate = 15.0; // ZIMRA standard VAT
        $vatAmount = round($subtotal * ($vatRate / 100), 2);
        $totalAmount = $subtotal + $vatAmount;

        $issueDate = $m['end'];
        $dueDate = date('Y-m-d', strtotime($issueDate . ' + 15 days'));
        $paidDate = $isCurrentMonth ? null : date('Y-m-d', strtotime($issueDate . ' + ' . rand(3, 12) . ' days'));

        $inv = new Clientinvoice();
        $inv->invoice_number = $invNum;
        $inv->clientorganization = $cEntity['id'];
        $inv->clientserviceplan = $cEntity['plan_id'];
        $inv->billing_period_start = $m['start'];
        $inv->billing_period_end = $m['end'];
        $inv->currency = 'USD';
        $inv->subtotal = $subtotal;
        $inv->vat_rate = $vatRate;
        $inv->vat_amount = $vatAmount;
        $inv->total_amount = $totalAmount;
        $inv->payment_status = $payStatus;
        $inv->issue_date = $issueDate;
        $inv->due_date = $dueDate;
        $inv->paid_date = $paidDate;
        $inv->notes = "Official Tax Invoice for {$m['name']} services under {$cEntity['data']['plan_name']}.";
        $inv->reg_by = $financeId;
        $inv->reg_date = $issueDate . ' 17:00:00';
        $inv->status = 1;
        $inv->save();
        $invId = (int)$inv->iD;

        // Line Item 1: Monthly Retainer Fee
        $item1 = new Clientinvoiceitem();
        $item1->clientinvoice = $invId;
        $item1->item_type = 'retainer';
        $item1->description = "Monthly Managed Retainer Fee — {$cEntity['data']['plan_name']} (Includes up to {$cEntity['data']['hours']} Dedicated Engineering Hours)";
        $item1->quantity = 1.0;
        $item1->unit_price = $retainerFee;
        $item1->total_price = $retainerFee;
        $item1->reg_by = $financeId;
        $item1->reg_date = $issueDate . ' 17:00:00';
        $item1->status = 1;
        $item1->save();

        // Line Item 2: Associate Lead Hours (included in retainer)
        $item2 = new Clientinvoiceitem();
        $item2->clientinvoice = $invId;
        $item2->item_type = 'associate_hours';
        $item2->description = "Senior Associate Lead Architecture & Code Review ({$assocHours} Hours Delivered — Covered by Retainer)";
        $item2->quantity = $assocHours;
        $item2->unit_price = 0.00;
        $item2->total_price = 0.00;
        $item2->reg_by = $financeId;
        $item2->reg_date = $issueDate . ' 17:00:00';
        $item2->status = 1;
        $item2->save();

        // Line Item 3: Apprentice Implementation Hours (included in retainer)
        $item3 = new Clientinvoiceitem();
        $item3->clientinvoice = $invId;
        $item3->item_type = 'apprentice_hours';
        $item3->description = "University Apprentice Implementation & Test Automation ({$apprHours} Hours Delivered — Covered by Retainer)";
        $item3->quantity = $apprHours;
        $item3->unit_price = 0.00;
        $item3->total_price = 0.00;
        $item3->reg_by = $financeId;
        $item3->reg_date = $issueDate . ' 17:00:00';
        $item3->status = 1;
        $item3->save();

        // Line Item 4: Excess Billable Hours (if applicable)
        if ($excessHours > 0) {
            $item4 = new Clientinvoiceitem();
            $item4->clientinvoice = $invId;
            $item4->item_type = 'excess_hours';
            $item4->description = "Approved Scope Expansion & SLA Extended Hours ({$excessHours} Hours @ \${$assocRate}/hr)";
            $item4->quantity = $excessHours;
            $item4->unit_price = $assocRate;
            $item4->total_price = $excessCost;
            $item4->reg_by = $financeId;
            $item4->reg_date = $issueDate . ' 17:00:00';
            $item4->status = 1;
            $item4->save();
        }

        // Electronic Bank Payment (for months 1–11)
        if ($payStatus === 2 && $paidDate) {
            $pay = new Clientinvoicepayment();
            $pay->clientinvoice = $invId;
            $pay->payment_method = ($cIdx % 2 === 0) ? 'Stanbic Bank Nostro Transfer' : 'CABS Direct Electronic Settlement';
            $pay->transaction_reference = sprintf('TXN-STB-%s-%04d', str_replace('-', '', $paidDate), rand(1000, 9999));
            $pay->amount = $totalAmount;
            $pay->paid_at = $paidDate . ' 11:15:00';
            $pay->notes = "Settlement received in full. Verified by Tsigiro Finance Desk.";
            $pay->reg_by = $financeId;
            $pay->reg_date = $paidDate . ' 11:30:00';
            $pay->status = 1;
            $pay->save();
        }
    }
    echo "  + Invoices for {$m['name']} generated across all 4 corporate clients.\n";
}

echo "\n=== Annual Simulation Data Seeding Successfully Completed! ===\n";
echo "Summary of simulated assets across 12 months:\n";
echo " - 4 Client Organizations active with tailored retainer agreements\n";
echo " - 2 Associates & 2 Apprentices actively delivering work\n";
echo " - 24 Service Requests with paired work assignments & mentorship supervision\n";
echo " - 84 Payslips generated across 12 approved payroll cycles with bank disbursements\n";
echo " - 48 Itemized Tax Invoices with ZIMRA 15% VAT and electronic settlement receipts\n";
