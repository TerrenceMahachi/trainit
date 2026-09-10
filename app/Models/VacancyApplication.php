<?php

namespace App\Models;

use App\Models\Model;

class VacancyApplication extends Model
{
    protected $table = 'vacancy_application';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `vacancy_application` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `vacancy` INTEGER NOT NULL,
                `application_number` TEXT NOT NULL UNIQUE,
                `user` INTEGER DEFAULT NULL,
                `first_name` TEXT NOT NULL,
                `last_name` TEXT NOT NULL,
                `email` TEXT NOT NULL,
                `phone` TEXT NOT NULL,
                `city` TEXT NOT NULL,
                `country` TEXT NOT NULL DEFAULT 'Zimbabwe',
                `years_of_experience` INTEGER NOT NULL DEFAULT 0,
                `highest_qualification` TEXT DEFAULT NULL,
                `current_employer` TEXT DEFAULT NULL,
                `current_job_title` TEXT DEFAULT NULL,
                `expected_salary` TEXT DEFAULT NULL,
                `notice_period_days` INTEGER DEFAULT 30,
                `cover_letter` TEXT DEFAULT NULL,
                `cv_path` TEXT NOT NULL,
                `application_status` INTEGER NOT NULL DEFAULT 1,
                `rating_score` INTEGER DEFAULT NULL,
                `admin_notes` TEXT DEFAULT NULL,
                `interview_at` DATETIME DEFAULT NULL,
                `staff_invite` INTEGER DEFAULT NULL,
                `appointed_at` DATETIME DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT 1,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`vacancy`) REFERENCES `vacancy`(`iD`),
                FOREIGN KEY(`user`) REFERENCES `user`(`iD`),
                FOREIGN KEY(`application_status`) REFERENCES `vacancyapplicationstatus`(`iD`),
                FOREIGN KEY(`staff_invite`) REFERENCES `staff_invite`(`iD`)
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function statusRecord()
    {
        return $this->belongsTo(Vacancyapplicationstatus::class, 'application_status');
    }

    public function fullName(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function staffInvite()
    {
        if (!$this->staff_invite) {
            return null;
        }
        $stmt = $this->query("SELECT * FROM staff_invite WHERE iD = ?", [$this->staff_invite]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
