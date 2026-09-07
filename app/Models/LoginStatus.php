<?php

namespace App\Models;

use App\Models\Model;

class LoginStatus extends Model
{
    protected $table = 'login_status';
   
    protected function getTableCreationQuery()
    {
        return "
        CREATE TABLE IF NOT EXISTS `login_status` (
            `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
            `name` TEXT NOT NULL
        )
        ";
    }
   
    protected function getDefaultDataInsertionQuery()
    {
        return [
            "INSERT INTO `login_status` (`iD`, `name`) VALUES (1, 'Pending activation')",
            "INSERT INTO `login_status` (`iD`, `name`) VALUES (2, 'Active')",
            "INSERT INTO `login_status` (`iD`, `name`) VALUES (3, 'Suspended by Administrator')",
            "INSERT INTO `login_status` (`iD`, `name`) VALUES (4, 'Deactivated by User')",
            "INSERT INTO `login_status` (`iD`, `name`) VALUES (5, 'Suspended for Login Abuse')",
            "INSERT INTO `login_status` (`iD`, `name`) VALUES (6, 'Deactivated')"
         ];
    }
}
