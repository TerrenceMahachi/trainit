<?php

namespace App\Models;

use App\Models\Model;

class Vacancyapplicationstatus extends Model
{
    protected $table = 'vacancyapplicationstatus';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `vacancyapplicationstatus` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `code` TEXT NOT NULL UNIQUE,
                `name` TEXT NOT NULL,
                `badge_class` TEXT NOT NULL DEFAULT 'bg-secondary',
                `sort_order` INTEGER NOT NULL DEFAULT 1,
                `reg_by` INTEGER NOT NULL DEFAULT 1,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [
            "INSERT OR IGNORE INTO `vacancyapplicationstatus` (iD, code, name, badge_class, sort_order) VALUES (1, 'submitted', 'Application Received', 'bg-info text-dark', 1)",
            "INSERT OR IGNORE INTO `vacancyapplicationstatus` (iD, code, name, badge_class, sort_order) VALUES (2, 'shortlisted', 'Shortlisted', 'bg-primary text-white', 2)",
            "INSERT OR IGNORE INTO `vacancyapplicationstatus` (iD, code, name, badge_class, sort_order) VALUES (3, 'interview', 'Interview Scheduled', 'bg-warning text-dark', 3)",
            "INSERT OR IGNORE INTO `vacancyapplicationstatus` (iD, code, name, badge_class, sort_order) VALUES (4, 'offer', 'Offer Extended', 'bg-purple text-white', 4)",
            "INSERT OR IGNORE INTO `vacancyapplicationstatus` (iD, code, name, badge_class, sort_order) VALUES (5, 'appointed', 'Appointed to Staff', 'bg-success text-white', 5)",
            "INSERT OR IGNORE INTO `vacancyapplicationstatus` (iD, code, name, badge_class, sort_order) VALUES (6, 'regretted', 'Not Shortlisted', 'bg-danger text-white', 6)",
        ];
    }

    public function applications()
    {
        return $this->hasMany(VacancyApplication::class, 'application_status');
    }
}
