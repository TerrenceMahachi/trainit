<?php

namespace App\Models;

use App\Models\Model;


class Payrollperiodapproval extends Model
{
    protected $table = 'payrollperiodapproval';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `payrollperiodapproval` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `payrollperiod` INTEGER NOT NULL,
                `payperiodstatus` INTEGER NOT NULL,
                `notes` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`payrollperiod`) REFERENCES `payrollperiod`(iD)
                 ,FOREIGN KEY(`payperiodstatus`) REFERENCES `payperiodstatus`(iD)
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
public function payperiodstatus()
{
    return $this->belongsTo(Payperiodstatus::class, 'payperiodstatus');
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