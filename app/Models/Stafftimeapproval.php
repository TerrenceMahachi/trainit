<?php

namespace App\Models;

use App\Models\Model;


class Stafftimeapproval extends Model
{
    protected $table = 'stafftimeapproval';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `stafftimeapproval` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `stafftimeentry` INTEGER NOT NULL,
                `is_approved` BOOLEAN NOT NULL,
                `review_notes` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`stafftimeentry`) REFERENCES `stafftimeentry`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function stafftimeentry()
{
    return $this->belongsTo(Stafftimeentry::class, 'stafftimeentry');
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