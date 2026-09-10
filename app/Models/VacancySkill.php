<?php

namespace App\Models;

use App\Models\Model;

class VacancySkill extends Model
{
    protected $table = 'vacancy_skill';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `vacancy_skill` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `vacancy` INTEGER NOT NULL,
                `skillitem` INTEGER NOT NULL,
                `is_mandatory` BOOLEAN NOT NULL DEFAULT 1,
                `reg_by` INTEGER NOT NULL DEFAULT 1,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`vacancy`) REFERENCES `vacancy`(`iD`) ON DELETE CASCADE,
                FOREIGN KEY(`skillitem`) REFERENCES `skillitem`(`iD`) ON DELETE CASCADE,
                UNIQUE(`vacancy`, `skillitem`)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class, 'vacancy');
    }

    public function skillitem()
    {
        return $this->belongsTo(Skillitem::class, 'skillitem');
    }
}
