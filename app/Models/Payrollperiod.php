<?php

namespace App\Models;

use App\Models\Model;


class Payrollperiod extends Model
{
    protected $table = 'payrollperiod';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `payrollperiod` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `period_code` TEXT NOT NULL,
                `period_name` TEXT NOT NULL,
                `start_date` DATE NOT NULL,
                `end_date` DATE NOT NULL,
                `pay_date` DATE NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
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