<?php

namespace App\Models;

use App\Models\Model;

class CandidateJobAlert extends Model
{
    protected $table = 'candidate_job_alert';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `candidate_job_alert` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `user` INTEGER DEFAULT NULL,
                `email` TEXT NOT NULL,
                `name` TEXT DEFAULT NULL,
                `keywords` TEXT DEFAULT NULL,
                `department` INTEGER DEFAULT NULL,
                `engagementbasis` INTEGER DEFAULT NULL,
                `unsubscribe_token` TEXT NOT NULL UNIQUE,
                `is_active` BOOLEAN NOT NULL DEFAULT 1,
                `last_matched_at` DATETIME DEFAULT NULL,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`user`) REFERENCES `user`(`iD`),
                FOREIGN KEY(`department`) REFERENCES `department`(`iD`),
                FOREIGN KEY(`engagementbasis`) REFERENCES `engagementbasis`(`iD`)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department');
    }

    public function engagementbasis()
    {
        return $this->belongsTo(Engagementbasis::class, 'engagementbasis');
    }
}
