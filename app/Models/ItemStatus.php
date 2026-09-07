<?php

namespace App\Models;

use App\Models\Model;

class ItemStatus extends Model
{
    protected $table = 'item_status';
    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `item_status` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `name` TEXT NOT NULL
            
            ) 
        ";
    }
    protected function getDefaultDataInsertionQuery()
    {
     
        return [
            " INSERT INTO `item_status` (`iD`, `name`) VALUES (1, 'Active')",
            " INSERT INTO `item_status` (`iD`, `name`) VALUES (2, 'Inactive')"
        ];
    }

}