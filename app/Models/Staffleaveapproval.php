<?php

namespace App\Models;

use App\Models\Model;


class Staffleaveapproval extends Model
{
    protected $table = 'staffleaveapproval';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `staffleaveapproval` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `staffleave` INTEGER NOT NULL,
                `leavestatus` INTEGER NOT NULL,
                `decision_notes` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`staffleave`) REFERENCES `staffleave`(iD)
                 ,FOREIGN KEY(`leavestatus`) REFERENCES `leavestatus`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function staffleave()
{
    return $this->belongsTo(Staffleave::class, 'staffleave');
}
public function leavestatus()
{
    return $this->belongsTo(Leavestatus::class, 'leavestatus');
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