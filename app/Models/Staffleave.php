<?php

namespace App\Models;

use App\Models\Model;


class Staffleave extends Model
{
    protected $table = 'staffleave';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `staffleave` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `staffprofile` INTEGER NOT NULL,
                `leavetype` INTEGER NOT NULL,
                `start_date` DATE NOT NULL,
                `end_date` DATE NOT NULL,
                `days_requested` DECIMAL NOT NULL,
                `reason` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`staffprofile`) REFERENCES `staffprofile`(iD)
                 ,FOREIGN KEY(`leavetype`) REFERENCES `leavetype`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function staffprofile()
{
    return $this->belongsTo(Staffprofile::class, 'staffprofile');
}
public function leavetype()
{
    return $this->belongsTo(Leavetype::class, 'leavetype');
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