<?php

namespace App\Models;

use App\Models\Model;

class Vacancystatus extends Model
{
    protected $table = 'vacancystatus';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `vacancystatus` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `code` TEXT NOT NULL UNIQUE,
                `name` TEXT NOT NULL,
                `description` TEXT NOT NULL,
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
            "INSERT OR IGNORE INTO `vacancystatus` (iD, code, name, description, badge_class, sort_order) VALUES (1, 'draft', 'Draft', 'Vacancy created but not visible publicly', 'bg-secondary', 1)",
            "INSERT OR IGNORE INTO `vacancystatus` (iD, code, name, description, badge_class, sort_order) VALUES (2, 'published', 'Published', 'Active position published on Opportunities page', 'bg-success', 2)",
            "INSERT OR IGNORE INTO `vacancystatus` (iD, code, name, description, badge_class, sort_order) VALUES (3, 'under_review', 'Under Review', 'Application deadline reached; screening underway', 'bg-warning text-dark', 3)",
            "INSERT OR IGNORE INTO `vacancystatus` (iD, code, name, description, badge_class, sort_order) VALUES (4, 'closed', 'Closed / Filled', 'Position filled and closed', 'bg-dark', 4)",
            "INSERT OR IGNORE INTO `vacancystatus` (iD, code, name, description, badge_class, sort_order) VALUES (5, 'archived', 'Archived', 'Historical vacancy archive', 'bg-secondary', 5)",
        ];
    }

    public function vacancies()
    {
        return $this->hasMany(Vacancy::class, 'vacancystatus');
    }
}
