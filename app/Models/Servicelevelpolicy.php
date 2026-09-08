<?php

namespace App\Models;

use App\Models\Model;


class Servicelevelpolicy extends Model
{
    protected $table = 'servicelevelpolicy';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `servicelevelpolicy` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `serviceoffering` INTEGER NOT NULL,
                `prioritylevel` INTEGER NOT NULL,
                `response_minutes` INTEGER NOT NULL,
                `resolution_hours` DECIMAL NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`serviceoffering`) REFERENCES `serviceoffering`(iD)
                 ,FOREIGN KEY(`prioritylevel`) REFERENCES `prioritylevel`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function serviceoffering()
{
    return $this->belongsTo(Serviceoffering::class, 'serviceoffering');
}
public function prioritylevel()
{
    return $this->belongsTo(Prioritylevel::class, 'prioritylevel');
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