<?php

namespace App\Models;

use App\Models\Model;


class Statutoryreturn extends Model
{
    protected $table = 'statutoryreturn';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `statutoryreturn` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `payrollperiod` INTEGER NOT NULL,
                `return_type` TEXT NOT NULL,
                `reference_number` TEXT NOT NULL,
                `total_contribution` DECIMAL NOT NULL,
                `submission_date` DATE NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`payrollperiod`) REFERENCES `payrollperiod`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function payrollperiod()
{
    return $this->belongsTo(Payrollperiod::class, 'payrollperiod');
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