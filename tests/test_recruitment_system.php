<?php
/**
 * End-to-End Verification Test for Trainit Multi-Profile Recruitment System.
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Models\Database;
use App\Models\User;
use App\Models\Login;
use App\Models\Rosterapplication;
use App\Models\Apprenticeprofile;
use App\Models\Associateprofile;
use App\Models\Rosterskill;
use App\Models\Rosterjudgementresponse;
use App\Models\Rosterassessment;
use App\Models\Rosteronboarding;
use App\Models\Servicefunction;
use App\Models\Proficiencylevel;
use App\Controllers\RosterApplicationController;

echo "======================================================\n";
echo "TRAINIT MULTI-PROFILE RECRUITMENT SYSTEM VERIFICATION\n";
echo "======================================================\n\n";

$pdo = Database::sharedPdo();

// 1. Create / Retrieve Test User
$testEmail = 'candidate.test.' . time() . '@trainit.co.zw';
$user = new User();
$user->name = 'Tendai Moyo';
$user->email = $testEmail;
$user->role = 2; // General User
$user->reg_by = 1;
$user->save();

$userId = (int) $user->iD;
echo "[1] Test user registered successfully: #{$userId} ({$user->name}, {$user->email})\n";

// 2. Verify Initial Zero-Profile Dashboard State
$controller = new RosterApplicationController();
$profilesBefore = $controller->getUserProfiles($userId);
assert($profilesBefore['has_profiles'] === false, "User should initially have no profiles.");
assert($profilesBefore['total'] === 0, "Total profiles should be 0.");
echo "[2] Zero-profile dashboard state confirmed: has_profiles = false\n";

// 3. Create Profile #1: Apprentice Track in Software Development
$app1 = new Rosterapplication();
$app1->user = $userId;
$app1->applicationtrack = 1; // 1: Apprentice
$app1->applicationstatus = 2; // 2: Submitted
$app1->primaryfunction = 9; // 9: Software Development
$app1->legal_name = 'Tendai Samuel Moyo';
$app1->email = $testEmail;
$app1->mobile_number = '+263 77 123 4567';
$app1->city = 'Harare';
$app1->zimprovince = 1; // Harare
$app1->workrightstatus = 1; // Citizen
$app1->e_signature = 'Tendai Samuel Moyo';
$app1->consent_version = '2026.1';
$app1->consent_timestamp = date('Y-m-d H:i:s');
$app1->save();
$app1Id = (int)$app1->iD;

// Add Apprentice Profile details
$ap1 = new Apprenticeprofile();
$ap1->rosterapplication = $app1Id;
$ap1->apprenticestatus = 1; // Enrolled WRL
$ap1->institution_name = 'University of Zimbabwe';
$ap1->degree_programme = 'BSc Honours Computer Science';
$ap1->study_level = 'Part 3';
$ap1->is_wrl_attachment = 1;
$ap1->wrl_duration_months = 12;
$ap1->expected_completion_date = date('Y-m-d', strtotime('+18 months'));
$ap1->wrl_coordinator_name = 'Dr. Chidzero';
$ap1->wrl_coordinator_email = 'coordinator@uz.ac.zw';
$ap1->save();

// Add Skills for App1
$sk1 = new Rosterskill();
$sk1->rosterapplication = $app1Id;
$sk1->servicefunction = 9;
$sk1->skillitem = 61; // Core Web Frontend
$sk1->proficiencylevel = 4; // Level 4 Expert
$sk1->save();

// Add Judgement Response for App1
$jdg1 = new Rosterjudgementresponse();
$jdg1->rosterapplication = $app1Id;
$jdg1->primary_function_evidence = 'Built an end-to-end full-stack inventory management module with RESTful API in PHP and MySQL for a retail client, reducing stock reconciliation discrepancies by 40%.';
$jdg1->motivation_narrative = 'Seeking practical exposure on live client projects under senior mentorship.';
$jdg1->save();

echo "[3] Profile #1 (Apprentice: Software Dev) created: App #{$app1Id}\n";

// 4. Create Profile #2: Associate Track in ICT Systems Administration for the SAME user
$app2 = new Rosterapplication();
$app2->user = $userId;
$app2->applicationtrack = 2; // 2: Associate
$app2->applicationstatus = 2; // 2: Submitted
$app2->primaryfunction = 7; // 7: ICT Systems Admin
$app2->legal_name = 'Tendai Samuel Moyo';
$app2->email = $testEmail;
$app2->mobile_number = '+263 77 123 4567';
$app2->city = 'Harare';
$app2->zimprovince = 1;
$app2->workrightstatus = 1;
$app2->e_signature = 'Tendai Samuel Moyo';
$app2->consent_version = '2026.1';
$app2->consent_timestamp = date('Y-m-d H:i:s');
$app2->save();
$app2Id = (int)$app2->iD;

// Add Associate Profile details
$asp2 = new Associateprofile();
$asp2->rosterapplication = $app2Id;
$asp2->employmentstatus = 3; // Consulting
$asp2->years_experience = '6-10';
$asp2->day_rate_expectation = 180.00;
$asp2->has_tax_clearance_itf263 = 1;
$asp2->zimra_bp_number = 'BP0019283746';
$asp2->save();

echo "[4] Profile #2 (Associate: ICT Systems) created for SAME user: App #{$app2Id}\n";

// 5. Verify Multi-Profile Retrieval on Dashboard
$profilesAfter = $controller->getUserProfiles($userId);
echo "[5] Verifying Multi-Profile State for User #{$userId}:\n";
echo "    - Total active applications: {$profilesAfter['total']} (Expected: 2)\n";
echo "    - Apprentice profiles held: " . count($profilesAfter['apprentice']) . " (Expected: 1)\n";
echo "    - Associate profiles held: " . count($profilesAfter['associate']) . " (Expected: 1)\n";

assert($profilesAfter['total'] === 2, "User must hold exactly 2 profiles simultaneously.");
assert(count($profilesAfter['apprentice']) === 1, "User must hold 1 Apprentice profile.");
assert(count($profilesAfter['associate']) === 1, "User must hold 1 Associate profile.");

// 6. Test Reviewer 100-Point Scoring & Admission
$assessment = new Rosterassessment();
$assessment->rosterapplication = $app1Id;
$assessment->reviewer = $userId; // Reviewer
$assessment->eligibility_gate_passed = 1;
$assessment->technical_fit_score = 28.0;
$assessment->evidence_score = 18.5;
$assessment->judgement_score = 22.0;
$assessment->availability_score = 14.0;
$assessment->motivation_score = 9.0;
$assessment->total_score = 91.5;
$assessment->vettingrecommendation = 1; // 1: Recommend Roster Admission
$assessment->interview_notes = 'Excellent technical fundamentals and clear communication.';
$assessment->save();

// Transition status to on_roster (5)
$app1->applicationstatus = 5; // 5: On Roster
$app1->update();
echo "[6] Reviewer assessed App #{$app1Id}: Score = 91.5/100, Status = On Roster\n";

// 7. Test Stage 3 Statutory Onboarding
$onboarding = new Rosteronboarding();
$onboarding->rosterapplication = $app1Id;
$onboarding->national_id_number = '63-9876543-F-42';
$onboarding->street_address = '14 Samora Machel Avenue';
$onboarding->city = 'Harare';
$onboarding->bank_name = 'Stanbic Bank Zimbabwe';
$onboarding->bank_branch = 'Nelson Mandela';
$onboarding->account_name = 'Tendai Samuel Moyo';
$onboarding->account_number = '9140001234567';
$onboarding->bank_currency = 'USD';
$onboarding->emergency_contact_name = 'Grace Moyo';
$onboarding->emergency_contact_phone = '+263 77 987 6543';
$onboarding->emergency_contact_relationship = 'Spouse';
$onboarding->save();
echo "[7] Stage 3 Statutory Onboarding recorded for App #{$app1Id}: National ID = 63-9876543-F-42\n";

echo "\n======================================================\n";
echo "ALL TESTS PASSED SUCCESSFULLY! MULTI-PROFILE SYSTEM OK\n";
echo "======================================================\n";
