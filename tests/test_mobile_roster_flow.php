<?php
/**
 * Automated Verification Script: Mobile Roster Application Flow
 * Tests all 5 stages of Apprentice and Associate intake on the mobile API.
 */

chdir(__DIR__ . '/..');
require_once 'bootstrap.php';

use App\Models\Database;
use App\Models\User;
use App\Models\Rosterapplication;
use App\Models\Rosterdocument;
use App\Models\Rosterskill;
use App\Models\Rosterworkhistory;
use App\Models\Rosterreferee;
use App\Models\Rosterstatusevent;

$db = (new Database())->getPDO();

echo "========================================================\n";
echo " TESTING TSIGIRO MOBILE ROSTER APPLICATION PROCESS\n";
echo "========================================================\n\n";

// Helper function to dispatch mock mobile requests in a clean isolated subprocess
function mockMobileRequest(string $method, string $uri, array $params = [], array $files = []) {
    $code = '
        chdir("/Library/WebServer/Documents/trainit");
        require_once "bootstrap.php";
        $_SERVER["REQUEST_METHOD"] = ' . var_export($method, true) . ';
        $_SERVER["REQUEST_URI"] = ' . var_export($uri, true) . ';
        $_SERVER["REMOTE_ADDR"] = "127.0.0.1";
        $_GET = ' . var_export($method === 'GET' ? $params : [], true) . ';
        $_POST = ' . var_export($method === 'POST' ? $params : [], true) . ';
        $_FILES = ' . var_export($files, true) . ';

        $router = new \App\Router();
        $GLOBALS["router"] = $router;
        foreach (glob(_ROUTES_PATH . "/*.php") as $f) {
            include $f;
        }
        try {
            $router->matchRoute();
        } catch (\Throwable $e) {
            echo json_encode(["status" => 0, "error" => $e->getMessage()]);
        }
    ';

    $cmd = 'php -r ' . escapeshellarg($code);
    $output = shell_exec($cmd);
    $decoded = json_decode($output, true);
    return $decoded ?: ['raw_output' => $output];
}

// Ensure a test candidate user exists
$testEmail = 'candidate.test@tsigiro.co.zw';
$testUser = User::findByQuery("SELECT * FROM user WHERE email = ?", [$testEmail]);
if (empty($testUser)) {
    $u = new User();
    $u->name = 'Tendai Moyo';
    $u->email = $testEmail;
    $u->role = 2; // general candidate
    $u->save();
    $userId = (int)$u->iD;
} else {
    $userId = (int)$testUser[0]->iD;
}

echo "1. Testing GET /api/mobile/roster/config...\n";
$resConfig = mockMobileRequest('GET', '/api/mobile/roster/config');
assert($resConfig['status'] === 1, 'Config status must be 1');
assert(count($resConfig['tracks']) === 2, 'Must have 2 tracks (Apprentice & Associate)');
assert(count($resConfig['functions']) >= 10, 'Must have service functions');
assert(count($resConfig['proficiency_levels']) === 5, 'Must have 5 proficiency levels');
echo "   ✓ Configuration endpoint returned " . count($resConfig['tracks']) . " tracks and " . count($resConfig['functions']) . " service functions.\n\n";

echo "2. Testing GET /api/mobile/roster/skills-catalog (Function 9: Software Development)...\n";
$resSkillsCat = mockMobileRequest('GET', '/api/mobile/roster/skills-catalog', ['function_id' => 9]);
assert($resSkillsCat['status'] === 1, 'Skills catalog status must be 1');
assert(!empty($resSkillsCat['skills']), 'Must have skills for Software Development');
echo "   ✓ Skills catalog returned " . count($resSkillsCat['skills']) . " technical skills.\n\n";

echo "3. Testing POST /api/mobile/roster/apply/stage1-intake (Apprentice Track)...\n";
$stage1Data = [
    'user_id' => $userId,
    'track' => 'apprentice',
    'primaryfunction' => 9, // Software Development
    'legal_name' => 'Tendai Moyo',
    'preferred_name' => 'Tendai',
    'email' => $testEmail,
    'mobile_number' => '+263771234567',
    'whatsapp_number' => '+263771234567',
    'date_of_birth' => '2001-05-14',
    'gender' => 1, // Male
    'national_id' => '63-1234567-A-89',
    'city' => 'Harare',
    'suburb' => 'Mount Pleasant',
    'zimprovince' => 1, // Harare
    'country' => 'Zimbabwe',
    'nationality' => 'Zimbabwean',
    'workrightstatus' => 1,
    'highest_qualification' => 'Bachelor\'s Degree in Computer Science',
    'current_employment_status' => 'Full-time student (Seeking attachment)'
];
$resStage1 = mockMobileRequest('POST', '/api/mobile/roster/apply/stage1-intake', $stage1Data);
assert($resStage1['status'] === 1, 'Stage 1 intake must succeed');
$appId = $resStage1['application_id'];
$appNumber = $resStage1['application_number'];
echo "   ✓ Draft Apprentice Application created with ID: {$appId} (Ref: {$appNumber})\n\n";

echo "4. Testing POST /api/mobile/roster/apply/stage2-upload (Uploading CV & National ID)...\n";
// Create temporary test files
$tempDir = sys_get_temp_dir();
$testCv = $tempDir . '/test_cv_tendai.pdf';
file_put_contents($testCv, "%PDF-1.4 Mock CV Content for Tendai Moyo");

$testIdScan = $tempDir . '/test_id_tendai.jpg';
file_put_contents($testIdScan, "Mock National ID Image Content");

$mockCvFile = [
    'file' => [
        'name' => 'Tendai_Moyo_CV.pdf',
        'type' => 'application/pdf',
        'tmp_name' => $testCv,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($testCv)
    ]
];
$resCvUpload = mockMobileRequest('POST', '/api/mobile/roster/apply/stage2-upload', [
    'user_id' => $userId,
    'application_id' => $appId,
    'doc_type_code' => 'CV_RESUME'
], $mockCvFile);
assert($resCvUpload['status'] === 1, 'CV upload must succeed');

$mockIdFile = [
    'file' => [
        'name' => 'Tendai_National_ID.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => $testIdScan,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($testIdScan)
    ]
];
$resIdUpload = mockMobileRequest('POST', '/api/mobile/roster/apply/stage2-upload', [
    'user_id' => $userId,
    'application_id' => $appId,
    'doc_type_code' => 'NAT_ID'
], $mockIdFile);
assert($resIdUpload['status'] === 1, 'ID upload must succeed');
assert(count($resIdUpload['documents']) === 2, 'Must have 2 documents uploaded');
echo "   ✓ Uploaded CV and National ID. Total uploaded documents on draft: " . count($resIdUpload['documents']) . "\n\n";

echo "5. Testing GET /api/mobile/roster/apply/documents/{$appId}...\n";
$resDocsList = mockMobileRequest('GET', "/api/mobile/roster/apply/documents/{$appId}", ['user_id' => $userId]);
assert($resDocsList['status'] === 1, 'Document list must return status 1');
assert(count($resDocsList['documents']) === 2, 'Must contain 2 documents');
echo "   ✓ Verified document list endpoint returns 2 uploaded items.\n\n";

echo "6. Testing POST /api/mobile/roster/apply/stage3-skills...\n";
$sampleSkills = [
    ['skill_id' => $resSkillsCat['skills'][0]['id'], 'proficiency' => 4, 'years' => 2],
    ['skill_id' => $resSkillsCat['skills'][1]['id'], 'proficiency' => 3, 'years' => 1]
];
$resSkills = mockMobileRequest('POST', '/api/mobile/roster/apply/stage3-skills', [
    'user_id' => $userId,
    'application_id' => $appId,
    'skills' => json_encode($sampleSkills)
]);
assert($resSkills['status'] === 1, 'Skills submission must succeed');
assert($resSkills['skills_count'] === 2, 'Must save 2 skills');
echo "   ✓ Saved {$resSkills['skills_count']} competency matrix ratings.\n\n";

echo "7. Testing POST /api/mobile/roster/apply/stage4-experience...\n";
$workHistory = [
    [
        'organization_name' => 'University Tech Hub',
        'job_title' => 'Junior Web Developer Intern',
        'start_date' => '2024-01-15',
        'end_date' => '2024-12-20',
        'is_current' => 0,
        'key_achievements' => 'Built student course registration portal using PHP and MySQL.'
    ]
];
$referees = [
    [
        'referee_name' => 'Dr. K. Nyoni',
        'organization' => 'University of Zimbabwe',
        'designation' => 'Senior Lecturer & Attachment Coordinator',
        'email' => 'nyoni@example.com',
        'phone' => '+263772111222',
        'timing_id' => 1
    ]
];
$resExp = mockMobileRequest('POST', '/api/mobile/roster/apply/stage4-experience', [
    'user_id' => $userId,
    'application_id' => $appId,
    'work_history' => json_encode($workHistory),
    'referees' => json_encode($referees)
]);
assert($resExp['status'] === 1, 'Experience & referees submission must succeed');
echo "   ✓ Saved work history and referee contact details.\n\n";

echo "8. Testing POST /api/mobile/roster/apply/stage5-submit (Final Submission & Consent)...\n";
$resSubmit = mockMobileRequest('POST', '/api/mobile/roster/apply/stage5-submit', [
    'user_id' => $userId,
    'application_id' => $appId,
    'consent_accuracy' => 1,
    'consent_vetting' => 1,
    'e_signature' => 'Tendai Moyo'
]);
if (empty($resSubmit['status'])) {
    echo "ERROR RES_SUBMIT: " . json_encode($resSubmit) . "\n";
}
assert(!empty($resSubmit['status']) && $resSubmit['status'] === 1, 'Final submission must succeed');
echo "   ✓ Application officially submitted! Response message: {$resSubmit['message']}\n\n";

echo "9. Testing GET /api/mobile/roster/status/{$appId}...\n";
$resStatus = mockMobileRequest('GET', "/api/mobile/roster/status/{$appId}", ['user_id' => $userId]);
assert($resStatus['status'] === 1, 'Status endpoint must return 1');
assert($resStatus['application']['applicationstatus'] == 2, 'Status must be 2 (Submitted)');
assert(count($resStatus['documents']) === 2, 'Documents count must be 2');
assert(count($resStatus['skills']) === 2, 'Skills count must be 2');
assert(count($resStatus['work_history']) === 1, 'Work history count must be 1');
assert(count($resStatus['referees']) === 1, 'Referees count must be 1');
assert(count($resStatus['events']) >= 2, 'Must have at least 2 audit events (Draft + Submitted)');
echo "   ✓ Verified full dossier: Status = {$resStatus['application']['status_name']} ({$resStatus['application']['status_code']})\n";
echo "   ✓ Progress Milestones: " . count($resStatus['milestones']) . " stages tracked.\n\n";

echo "10. Testing GET /api/mobile/roster/my-applications...\n";
$resMyApps = mockMobileRequest('GET', '/api/mobile/roster/my-applications', ['user_id' => $userId]);
assert($resMyApps['status'] === 1, 'My applications must return status 1');
assert(count($resMyApps['applications']) >= 1, 'Must contain at least 1 application');
echo "   ✓ Candidate applications list returned " . count($resMyApps['applications']) . " applications for user.\n\n";

// Cleanup temp files
@unlink($testCv);
@unlink($testIdScan);

echo "========================================================\n";
echo " ALL 10 MOBILE ROSTER PIPELINE TESTS PASSED (100% OK)!\n";
echo "========================================================\n";
