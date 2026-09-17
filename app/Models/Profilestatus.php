<?php

namespace App\Models;

use App\Models\Model;


class Profilestatus extends Model
{
    protected $table = 'profilestatus';

    protected function getTableCreationQuery()
    {
        return "
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
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [
            "INSERT INTO `profilestatus` (`iD`, `code`, `name`, `badge_class`, `can_access_portal`) VALUES (1, 'draft', 'Draft', 'secondary', 0)",
            "INSERT INTO `profilestatus` (`iD`, `code`, `name`, `badge_class`, `can_access_portal`) VALUES (2, 'pending', 'Pending Approval', 'warning', 0)",
            "INSERT INTO `profilestatus` (`iD`, `code`, `name`, `badge_class`, `can_access_portal`) VALUES (3, 'approved', 'Approved & Active', 'success', 1)",
            "INSERT INTO `profilestatus` (`iD`, `code`, `name`, `badge_class`, `can_access_portal`) VALUES (4, 'rejected', 'Rejected', 'danger', 0)",
            "INSERT INTO `profilestatus` (`iD`, `code`, `name`, `badge_class`, `can_access_portal`) VALUES (5, 'suspended', 'Suspended', 'dark', 0)"
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