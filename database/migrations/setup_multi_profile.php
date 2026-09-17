<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Models\Database;

$pdo = Database::sharedPdo();

echo "Running multi-profile database migration...\n";

// 1. Create tables
$pdo->exec("
CREATE TABLE IF NOT EXISTS `profiletype` (
    `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
    `code` TEXT NOT NULL UNIQUE,
    `name` TEXT NOT NULL,
    `description` TEXT DEFAULT NULL,
    `icon` TEXT DEFAULT NULL,
    `requires_approval` BOOLEAN NOT NULL DEFAULT 1,
    `sort_order` INTEGER NOT NULL DEFAULT 0,
    `reg_by` INTEGER NOT NULL DEFAULT '1',
    `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` INTEGER NOT NULL DEFAULT 1
);
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS `profilestatus` (
    `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
    `code` TEXT NOT NULL UNIQUE,
    `name` TEXT NOT NULL,
    `badge_class` TEXT NOT NULL DEFAULT 'secondary',
    `can_access_portal` BOOLEAN NOT NULL DEFAULT 0,
    `reg_by` INTEGER NOT NULL DEFAULT '1',
    `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` INTEGER NOT NULL DEFAULT 1
);
");
// Drop legacy incompatible userprofile table if it lacks profiletype column
$existingCols = $pdo->query("PRAGMA table_info(userprofile)")->fetchAll(PDO::FETCH_COLUMN, 1);
if (!empty($existingCols) && !in_array('profiletype', $existingCols)) {
    $pdo->exec("DROP TABLE IF EXISTS `userprofile`;");
}

$pdo->exec("
CREATE TABLE IF NOT EXISTS `userprofile` (
    `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
    `user` INTEGER NOT NULL,
    `profiletype` INTEGER NOT NULL,
    `profilestatus` INTEGER NOT NULL,
    `display_title` TEXT DEFAULT NULL,
    `is_default` BOOLEAN NOT NULL DEFAULT 0,
    `request_notes` TEXT DEFAULT NULL,
    `reviewer_notes` TEXT DEFAULT NULL,
    `reviewed_by` INTEGER DEFAULT NULL,
    `reviewed_at` DATETIME DEFAULT NULL,
    `reg_by` INTEGER NOT NULL DEFAULT '1',
    `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY(`user`) REFERENCES `user`(`iD`),
    FOREIGN KEY(`profiletype`) REFERENCES `profiletype`(`iD`),
    FOREIGN KEY(`profilestatus`) REFERENCES `profilestatus`(`iD`),
    FOREIGN KEY(`reviewed_by`) REFERENCES `user`(`iD`)
);
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS `profilerequestaudit` (
    `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
    `userprofile` INTEGER NOT NULL,
    `action` TEXT NOT NULL,
    `from_status` INTEGER DEFAULT NULL,
    `to_status` INTEGER NOT NULL,
    `performed_by` INTEGER NOT NULL,
    `notes` TEXT DEFAULT NULL,
    `reg_by` INTEGER NOT NULL DEFAULT '1',
    `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY(`userprofile`) REFERENCES `userprofile`(`iD`),
    FOREIGN KEY(`from_status`) REFERENCES `profilestatus`(`iD`),
    FOREIGN KEY(`to_status`) REFERENCES `profilestatus`(`iD`),
    FOREIGN KEY(`performed_by`) REFERENCES `user`(`iD`)
);
");

// 2. Seed profiletype
$types = [
    [1, 'general', 'General User', 'Standard registered user profile', 'fas fa-user', 0, 1],
    [2, 'apprentice', 'Apprentice', 'Apprentice / Candidate profile undergoing training and assignments', 'fas fa-user-graduate', 1, 2],
    [3, 'associate', 'Associate Consultant', 'Vetted expert associate handling client service requests and consulting', 'fas fa-user-tie', 1, 3],
    [4, 'staff', 'Staff Member', 'Internal Tsigiro staff and department team member', 'fas fa-id-badge', 1, 4],
    [5, 'client', 'Client Representative', 'Corporate client account manager and representative', 'fas fa-building', 1, 5]
];

$stmtType = $pdo->prepare("INSERT OR IGNORE INTO `profiletype` (`iD`, `code`, `name`, `description`, `icon`, `requires_approval`, `sort_order`) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($types as $t) {
    $stmtType->execute($t);
}

// 3. Seed profilestatus
$statuses = [
    [1, 'draft', 'Draft', 'secondary', 0],
    [2, 'pending', 'Pending Approval', 'warning', 0],
    [3, 'approved', 'Approved & Active', 'success', 1],
    [4, 'rejected', 'Rejected', 'danger', 0],
    [5, 'suspended', 'Suspended', 'dark', 0]
];

$stmtStatus = $pdo->prepare("INSERT OR IGNORE INTO `profilestatus` (`iD`, `code`, `name`, `badge_class`, `can_access_portal`) VALUES (?, ?, ?, ?, ?)");
foreach ($statuses as $s) {
    $stmtStatus->execute($s);
}

// 4. Backfill existing users into userprofile
$users = $pdo->query("SELECT iD, name, email, role FROM user")->fetchAll(PDO::FETCH_ASSOC);

$checkProf = $pdo->prepare("SELECT iD FROM userprofile WHERE user = ? AND profiletype = ?");
$insertProf = $pdo->prepare("INSERT INTO userprofile (user, profiletype, profilestatus, display_title, is_default, reg_by, reg_date) VALUES (?, ?, 3, ?, ?, 1, CURRENT_TIMESTAMP)");

$backfilled = 0;
foreach ($users as $u) {
    $userId = (int) $u['iD'];
    $role = (int) ($u['role'] ?? 2);

    // 1) Everyone has a General profile
    $checkProf->execute([$userId, 1]);
    if (!$checkProf->fetch()) {
        $isDefault = ($role === 2) ? 1 : 0;
        $insertProf->execute([$userId, 1, 'General User', $isDefault]);
        $backfilled++;
    }

    // 2) Staff (role 1, 6, 7, 8 or has staffprofile)
    $hasStaff = ($role === 1 || $role === 6 || $role === 7 || $role === 8);
    if (!$hasStaff) {
        $stf = $pdo->query("SELECT iD FROM staffprofile WHERE user = {$userId} LIMIT 1")->fetch();
        if ($stf) $hasStaff = true;
    }
    if ($hasStaff) {
        $checkProf->execute([$userId, 4]);
        if (!$checkProf->fetch()) {
            $isDefault = ($role === 1 || $role === 6 || $role === 7 || $role === 8) ? 1 : 0;
            $insertProf->execute([$userId, 4, 'Staff Member', $isDefault]);
            $backfilled++;
        }
    }

    // 3) Client (role 3 or has clientmembership)
    $hasClient = ($role === 3);
    if (!$hasClient) {
        $cli = $pdo->query("SELECT iD FROM clientmembership WHERE user = {$userId} LIMIT 1")->fetch();
        if ($cli) $hasClient = true;
    }
    if ($hasClient) {
        $checkProf->execute([$userId, 5]);
        if (!$checkProf->fetch()) {
            $isDefault = ($role === 3) ? 1 : 0;
            $insertProf->execute([$userId, 5, 'Client Representative', $isDefault]);
            $backfilled++;
        }
    }

    // 4) Associate (role 4 or has associateprofile)
    $hasAssoc = ($role === 4);
    if (!$hasAssoc) {
        $asc = $pdo->query("
            SELECT a.iD FROM associateprofile a
            JOIN rosterapplication r ON a.rosterapplication = r.iD
            WHERE r.user = {$userId} LIMIT 1
        ")->fetch();
        if ($asc) $hasAssoc = true;
    }
    if ($hasAssoc) {
        $checkProf->execute([$userId, 3]);
        if (!$checkProf->fetch()) {
            $isDefault = ($role === 4) ? 1 : 0;
            $insertProf->execute([$userId, 3, 'Associate Consultant', $isDefault]);
            $backfilled++;
        }
    }

    // 5) Apprentice (role 5 or has apprenticeprofile)
    $hasAppr = ($role === 5);
    if (!$hasAppr) {
        $apr = $pdo->query("
            SELECT a.iD FROM apprenticeprofile a
            JOIN rosterapplication r ON a.rosterapplication = r.iD
            WHERE r.user = {$userId} LIMIT 1
        ")->fetch();
        if ($apr) $hasAppr = true;
    }
    if ($hasAppr) {
        $checkProf->execute([$userId, 2]);
        if (!$checkProf->fetch()) {
            $isDefault = ($role === 5) ? 1 : 0;
            $insertProf->execute([$userId, 2, 'Apprentice', $isDefault]);
            $backfilled++;
        }
    }

    // Ensure at least one is default
    $defaultCheck = $pdo->query("SELECT iD FROM userprofile WHERE user = {$userId} AND is_default = 1 LIMIT 1")->fetch();
    if (!$defaultCheck) {
        $pdo->exec("UPDATE userprofile SET is_default = 1 WHERE user = {$userId} ORDER BY profiletype DESC LIMIT 1");
    }
}

// 5. Create index
$pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS idx_userprofile_user_type ON userprofile(user, profiletype);");

echo "Migration completed successfully! Inserted/backfilled user profiles: {$backfilled}\n";
