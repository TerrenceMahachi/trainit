<?php

namespace App\Models;

use App\Models\Model;


class Rosterstatusevent extends Model
{
    protected $table = 'Rosterstatusevent';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `Rosterstatusevent` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `applicationstatus` INTEGER NOT NULL,
                `remarks` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD)
                 ,FOREIGN KEY(`applicationstatus`) REFERENCES `applicationstatus`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function rosterapplication()
{
    return $this->belongsTo(Rosterapplication::class, 'rosterapplication');
}
public function applicationstatus()
{
    return $this->belongsTo(Applicationstatus::class, 'applicationstatus');
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