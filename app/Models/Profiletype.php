<?php

namespace App\Models;

use App\Models\Model;


class Profiletype extends Model
{
    protected $table = 'profiletype';

    protected function getTableCreationQuery()
    {
        return "
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
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [
            "INSERT INTO `profiletype` (`iD`, `code`, `name`, `description`, `icon`, `requires_approval`, `sort_order`) VALUES (1, 'general', 'General User', 'Standard registered user profile', 'fas fa-user', 0, 1)",
            "INSERT INTO `profiletype` (`iD`, `code`, `name`, `description`, `icon`, `requires_approval`, `sort_order`) VALUES (2, 'apprentice', 'Apprentice', 'Apprentice / Candidate profile undergoing training and assignments', 'fas fa-user-graduate', 1, 2)",
            "INSERT INTO `profiletype` (`iD`, `code`, `name`, `description`, `icon`, `requires_approval`, `sort_order`) VALUES (3, 'associate', 'Associate Consultant', 'Vetted expert associate handling client service requests and consulting', 'fas fa-user-tie', 1, 3)",
            "INSERT INTO `profiletype` (`iD`, `code`, `name`, `description`, `icon`, `requires_approval`, `sort_order`) VALUES (4, 'staff', 'Staff Member', 'Internal Tsigiro staff and department team member', 'fas fa-id-badge', 1, 4)",
            "INSERT INTO `profiletype` (`iD`, `code`, `name`, `description`, `icon`, `requires_approval`, `sort_order`) VALUES (5, 'client', 'Client Representative', 'Corporate client account manager and representative', 'fas fa-building', 1, 5)"
        ];
    }
    
    public function status()
    {
        return $this->belongsTo(ItemStatus::class, 'status');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }

   
}