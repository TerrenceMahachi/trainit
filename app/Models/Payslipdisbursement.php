<?php

namespace App\Models;

use App\Models\Model;


class Payslipdisbursement extends Model
{
    protected $table = 'payslipdisbursement';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `payslipdisbursement` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `payslip` INTEGER NOT NULL,
                `payment_method` TEXT NOT NULL,
                `transaction_reference` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`payslip`) REFERENCES `payslip`(iD)
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

    public function status()
    {
        return $this->belongsTo(ItemStatus::class, 'status');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }

   
}