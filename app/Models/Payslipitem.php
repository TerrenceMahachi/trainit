<?php

namespace App\Models;

use App\Models\Model;


class Payslipitem extends Model
{
    protected $table = 'payslipitem';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `payslipitem` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `payslip` INTEGER NOT NULL,
                `payrollitemtype` INTEGER NOT NULL,
                `item_name` TEXT NOT NULL,
                `amount` DECIMAL NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`payslip`) REFERENCES `payslip`(iD)
                 ,FOREIGN KEY(`payrollitemtype`) REFERENCES `payrollitemtype`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function payslip()
{
    return $this->belongsTo(Payslip::class, 'payslip');
}
public function payrollitemtype()
{
    return $this->belongsTo(Payrollitemtype::class, 'payrollitemtype');
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