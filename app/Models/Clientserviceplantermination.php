<?php

namespace App\Models;

use App\Models\Model;


class Clientserviceplantermination extends Model
{
    protected $table = 'clientserviceplantermination';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `clientserviceplantermination` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `clientserviceplan` INTEGER NOT NULL,
                `end_date` DATE NOT NULL,
                `reason` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`clientserviceplan`) REFERENCES `clientserviceplan`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function clientserviceplan()
{
    return $this->belongsTo(Clientserviceplan::class, 'clientserviceplan');
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