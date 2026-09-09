<?php
/**
 * Automated verification test for normalized dictionary tables seeding.
 */

require __DIR__ . '/../bootstrap.php';

use App\Helpers\ReferenceDataSeeder;
use App\Models\Database;
use App\Models\Servicefunction;
use App\Models\Skillitem;
use App\Models\Proficiencylevel;
use App\Models\Vettingrecommendation;
use App\Models\Applicationtrack;
use App\Models\Applicationstatus;
use App\Models\Apprenticestatus;
use App\Models\Employmentstatus;
use App\Models\Zimprovince;
use App\Models\Workrightstatus;
use App\Models\Qualificationtype;
use App\Models\Qualificationstatus;
use App\Models\Professionalbody;
use App\Models\Sectortype;
use App\Models\Engagementbasis;
use App\Models\Engagementmodel;
use App\Models\Worklocationpreference;
use App\Models\Invoiceentitytype;
use App\Models\Refereecontacttiming;
use App\Models\Refereeverificationstatus;

echo "=== 1. Testing Idempotent Seeding via ReferenceDataSeeder ===\n";
$seedResult = ReferenceDataSeeder::seedAll();
assert($seedResult['status'] === 'success', 'Seeding did not return success');
echo "PASS: Seeding completed successfully. Checked " . $seedResult['total_tables_checked'] . " tables.\n";

echo "=== 2. Verifying Table Record Counts ===\n";
$expectedCounts = [
    'servicefunction' => 12,
    'skillitem' => 107,
    'proficiencylevel' => 5,
    'vettingrecommendation' => 4,
    'applicationtrack' => 2,
    'applicationstatus' => 9,
    'apprenticestatus' => 5,
    'employmentstatus' => 5,
    'zimprovince' => 10,
    'workrightstatus' => 4,
    'qualificationtype' => 10,
    'qualificationstatus' => 6,
    'professionalbody' => 15,
    'sectortype' => 8,
    'engagementbasis' => 5,
    'engagementmodel' => 4,
    'worklocationpreference' => 4,
    'invoiceentitytype' => 3,
    'refereecontacttiming' => 3,
    'refereeverificationstatus' => 4
];

$pdo = Database::sharedPdo();
foreach ($expectedCounts as $table => $expected) {
    $count = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    assert($count >= $expected, "Table {$table} has {$count} records, expected at least {$expected}");
    echo "  * Table `{$table}`: {$count} records (PASS)\n";
}

echo "=== 3. Testing Model countAll() Method Matches Expected Counts ===\n";
assert(Servicefunction::countAll() >= 12, 'Servicefunction::countAll failed');
assert(Skillitem::countAll() >= 107, 'Skillitem::countAll failed');
assert(Proficiencylevel::countAll() >= 5, 'Proficiencylevel::countAll failed');
assert(Vettingrecommendation::countAll() >= 4, 'Vettingrecommendation::countAll failed');
assert(Applicationtrack::countAll() >= 2, 'Applicationtrack::countAll failed');
assert(Applicationstatus::countAll() >= 9, 'Applicationstatus::countAll failed');
assert(Apprenticestatus::countAll() >= 5, 'Apprenticestatus::countAll failed');
assert(Employmentstatus::countAll() >= 5, 'Employmentstatus::countAll failed');
assert(Zimprovince::countAll() >= 10, 'Zimprovince::countAll failed');
assert(Workrightstatus::countAll() >= 4, 'Workrightstatus::countAll failed');
assert(Qualificationtype::countAll() >= 10, 'Qualificationtype::countAll failed');
assert(Qualificationstatus::countAll() >= 6, 'Qualificationstatus::countAll failed');
assert(Professionalbody::countAll() >= 15, 'Professionalbody::countAll failed');
assert(Sectortype::countAll() >= 8, 'Sectortype::countAll failed');
assert(Engagementbasis::countAll() >= 5, 'Engagementbasis::countAll failed');
assert(Engagementmodel::countAll() >= 4, 'Engagementmodel::countAll failed');
assert(Worklocationpreference::countAll() >= 4, 'Worklocationpreference::countAll failed');
assert(Invoiceentitytype::countAll() >= 3, 'Invoiceentitytype::countAll failed');
assert(Refereecontacttiming::countAll() >= 3, 'Refereecontacttiming::countAll failed');
assert(Refereeverificationstatus::countAll() >= 4, 'Refereeverificationstatus::countAll failed');
echo "PASS: All Model::countAll() queries verified.\n";

echo "=== 4. Testing Status Helper Methods ===\n";
assert(ReferenceDataSeeder::isFullySeeded($pdo) === true, 'isFullySeeded should be true');
$unseeded = ReferenceDataSeeder::getUnseededTables($pdo);
assert(empty($unseeded), 'Unseeded tables should be empty');
echo "PASS: Status helpers verified.\n";

echo "=== 5. Testing Dashboard View Compilation ===\n";
$mockAdmin = \App\Models\User::find(1);
$data = ['title' => 'Admin Dashboard', 'user' => $mockAdmin];
ob_start();
echo view('dashboard.admin', compact('data'));
$html = ob_get_clean();
assert(strpos($html, 'System Administration & Normalized Dictionaries') !== false, 'Dashboard title missing');
assert(strpos($html, 'Service Functions') !== false, 'Service Functions card entry missing');
assert(strpos($html, 'Seed Dictionaries') !== false, 'Seed Dictionaries button missing');
echo "PASS: Dashboard view compiled and contains Seed Dictionaries elements.\n";

echo "\nALL TESTS PASSED SUCCESSFULLY!\n";
