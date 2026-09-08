<?php

namespace App\Models;

use App\Models\Model;

class StaffInvite extends Model
{
    protected $table = 'staff_invite';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `staff_invite` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `email` TEXT NOT NULL,
                `name` TEXT NOT NULL,
                `role` INTEGER NOT NULL,
                `department` TEXT NOT NULL,
                `job_title` TEXT NOT NULL,
                `token` TEXT NOT NULL UNIQUE,
                `expires_at` DATETIME NOT NULL,
                `invited_by` INTEGER NOT NULL,
                `used` BOOLEAN NOT NULL DEFAULT 0,
                `used_at` DATETIME DEFAULT NULL,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY(`invited_by`) REFERENCES `user`(`iD`)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
