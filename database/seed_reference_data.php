<?php
/**
 * Reference Data Seeder for Trainit Roster & Recruitment System.
 *
 * Automatically boots models to ensure SQLite tables exist,
 * ensures admin user #1 exists, and populates all 19 normalized
 * lookup tables with standardized categories via ReferenceDataSeeder.
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Models\Database;
use App\Helpers\ReferenceDataSeeder;

echo "=== Booting Models to Create SQLite Tables ===\n";
ReferenceDataSeeder::ensureTablesExist();

// Domain models
new \App\Models\Gender();
new \App\Models\Role();
new \App\Models\ItemStatus();
new \App\Models\LoginStatus();
new \App\Models\Rosterapplication();
new \App\Models\Apprenticeprofile();
new \App\Models\Associateprofile();
new \App\Models\Rosterskill();
new \App\Models\Rosterqualification();
new \App\Models\Rosterworkhistory();
new \App\Models\Rosterreferee();
new \App\Models\Rosterjudgementresponse();
new \App\Models\Rosterassessment();
new \App\Models\Rosteronboarding();

$pdo = Database::sharedPdo();

// Ensure User ID 1 exists and has login credentials
$adminCheck = $pdo->query("SELECT * FROM user WHERE iD = 1")->fetch(PDO::FETCH_ASSOC);
if (!$adminCheck) {
    echo "Creating System Administrator User #1...\n";
    $pdo->exec("INSERT INTO user (iD, name, email, role, reg_by, reg_date, status) VALUES (1, 'System Administrator', 'admin@trainit.co.zw', 1, 1, CURRENT_TIMESTAMP, 1)");
}
$adminLoginCheck = $pdo->query("SELECT * FROM user_login WHERE user = 1")->fetch(PDO::FETCH_ASSOC);
if (!$adminLoginCheck) {
    echo "Creating System Administrator Login credentials...\n";
    $hash = password_hash('Admin123!', PASSWORD_BCRYPT);
    $pdo->prepare("INSERT INTO user_login (user, password, status, failed_login, reg_by, reg_date) VALUES (1, ?, 1, 0, 1, CURRENT_TIMESTAMP)")->execute([$hash]);
    if (class_exists('App\Helpers\PasswordResume')) {
        \App\Helpers\PasswordResume::enroll(1, 'Admin123!');
    }
}

// Migrate rosterassessment table to ensure reviewer is nullable if needed
$assessmentTableInfo = $pdo->query("PRAGMA table_info(rosterassessment)")->fetchAll(PDO::FETCH_ASSOC);
foreach ($assessmentTableInfo as $col) {
    if ($col['name'] === 'reviewer' && $col['notnull'] == 1) {
        echo "Migrating rosterassessment table to allow NULL reviewer...\n";
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS rosterassessment_temp (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `reviewer` INTEGER DEFAULT NULL,
                `vettingrecommendation` INTEGER DEFAULT NULL,
                `eligibility_gate_passed` BOOLEAN DEFAULT 0,
                `technical_fit_score` DECIMAL(5,2) DEFAULT 0,
                `evidence_score` DECIMAL(5,2) DEFAULT 0,
                `judgement_score` DECIMAL(5,2) DEFAULT 0,
                `availability_score` DECIMAL(5,2) DEFAULT 0,
                `motivation_score` DECIMAL(5,2) DEFAULT 0,
                `total_score` DECIMAL(5,2) DEFAULT 0,
                `automated_red_flags` TEXT DEFAULT NULL,
                `interview_notes` TEXT DEFAULT NULL,
                `technical_test_result` TEXT DEFAULT NULL,
                `vetted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD),
                FOREIGN KEY(`reviewer`) REFERENCES `user`(iD),
                FOREIGN KEY(`vettingrecommendation`) REFERENCES `vettingrecommendation`(iD)
            );
            INSERT INTO rosterassessment_temp SELECT * FROM rosterassessment;
            DROP TABLE rosterassessment;
            ALTER TABLE rosterassessment_temp RENAME TO rosterassessment;
        ");
        break;
    }
}

echo "=== Seeding Reference & Lookup Tables ===\n";
$results = ReferenceDataSeeder::seedAll($pdo);

foreach ($results['tables'] as $tbl => $info) {
    echo "Seeding {$tbl}... ";
    if ($info['status'] === 'already_seeded') {
        echo "[EXISTS - {$info['count']} records]\n";
    } else {
        echo "[OK - {$info['inserted']} inserted]\n";
    }
}

echo "=== Seeding Complete ===\n";
echo "Tables seeded: {$results['tables_seeded_count']} | Total records inserted: {$results['total_records_inserted']} | Already populated: {$results['already_seeded_count']}\n";
