<?php

namespace App\Models;

use App\Models\Model;

class Role extends Model
{
    protected $table = 'user_role';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `user_role` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `name` TEXT NOT NULL
            
            ) 
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [
            " INSERT INTO `user_role` (`iD`, `name`) VALUES (1, 'Administrator')",
            " INSERT INTO `user_role` (`iD`, `name`) VALUES (2, 'General User')",
            " INSERT INTO `user_role` (`iD`, `name`) VALUES (3, 'Client User')",
            " INSERT INTO `user_role` (`iD`, `name`) VALUES (4, 'Associate')",
            " INSERT INTO `user_role` (`iD`, `name`) VALUES (5, 'Apprentice')",
            " INSERT INTO `user_role` (`iD`, `name`) VALUES (6, 'Service Manager')",
            " INSERT INTO `user_role` (`iD`, `name`) VALUES (7, 'Billing Officer')",
            " INSERT INTO `user_role` (`iD`, `name`) VALUES (8, 'Vetting Officer')"
        ];
    }

}