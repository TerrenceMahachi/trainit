<?php

namespace App\Models;

use App\Models\Model;


class Servicerequeststatusevent extends Model
{
    protected $table = 'servicerequeststatusevent';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `servicerequeststatusevent` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `servicerequest` INTEGER NOT NULL,
                `servicerequeststatus` INTEGER NOT NULL,
                `notes` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`servicerequest`) REFERENCES `servicerequest`(iD)
                 ,FOREIGN KEY(`servicerequeststatus`) REFERENCES `servicerequeststatus`(iD)
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
public function servicerequeststatus()
{
    return $this->belongsTo(Servicerequeststatus::class, 'servicerequeststatus');
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