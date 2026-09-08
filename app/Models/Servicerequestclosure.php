<?php

namespace App\Models;

use App\Models\Model;


class Servicerequestclosure extends Model
{
    protected $table = 'servicerequestclosure';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `servicerequestclosure` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `servicerequest` INTEGER NOT NULL,
                `closure_notes` TEXT NOT NULL,
                `satisfaction_rating` INTEGER NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`servicerequest`) REFERENCES `servicerequest`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function servicerequest()
{
    return $this->belongsTo(Servicerequest::class, 'servicerequest');
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