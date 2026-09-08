<?php

namespace App\Models;

use App\Models\Model;


class Payslip extends Model
{
    protected $table = 'payslip';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `payslip` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `payrollperiod` INTEGER NOT NULL,
                `staffprofile` INTEGER NOT NULL,
                `currency` TEXT NOT NULL,
                `gross_pay` DECIMAL NOT NULL,
                `total_deductions` DECIMAL NOT NULL,
                `net_pay` DECIMAL NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`payrollperiod`) REFERENCES `payrollperiod`(iD)
                 ,FOREIGN KEY(`staffprofile`) REFERENCES `staffprofile`(iD)
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
public function staffprofile()
{
    return $this->belongsTo(Staffprofile::class, 'staffprofile');
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