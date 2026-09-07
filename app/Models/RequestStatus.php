<?php

namespace App\Models;

use App\Models\Model;

class RequestStatus extends Model
{
    protected $table = 'request_status';
    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `request_status` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `name` varchar(50) NOT NULL
            ) 
        ";
    }
    protected function getDefaultDataInsertionQuery()
    {
  
        return [
            "INSERT INTO `request_status` (`iD`, `name`) VALUES (1, 'Pending Approval')",
            "INSERT INTO `request_status` (`iD`, `name`) VALUES (2, 'Approved')",
            "INSERT INTO `request_status` (`iD`, `name`) VALUES (3, 'Rejected')",
            "INSERT INTO `request_status` (`iD`, `name`) VALUES (4, 'Cancelled')"
                    // Add the remaining INSERT statements here...
        ];
    }
}