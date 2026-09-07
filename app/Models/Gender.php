<?php

namespace App\Models;

use App\Models\Model;

class Gender extends Model
{
    protected $table = 'gender';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `gender` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `name` TEXT NOT NULL
            
            ) 
        ";
    }
    protected function getDefaultDataInsertionQuery()
    {
     
        return [
            " INSERT INTO `gender` (`iD`, `name`) VALUES (1, 'Male')",
            " INSERT INTO `gender` (`iD`, `name`) VALUES (2, 'Female')",
            " INSERT INTO `gender` (`iD`, `name`) VALUES (3, 'Other')"
        ];
    }

}