<?php

namespace App\Models;

use App\Models\Model;


class Servicerequesttriage extends Model
{
    protected $table = 'servicerequesttriage';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `servicerequesttriage` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `servicerequest` INTEGER NOT NULL,
                `sla_due_date` DATETIME NOT NULL,
                `triage_notes` TEXT NOT NULL,
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