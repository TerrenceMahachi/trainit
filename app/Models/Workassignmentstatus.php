<?php

namespace App\Models;

use App\Models\Model;


class Workassignmentstatus extends Model
{
    protected $table = 'workassignmentstatus';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `workassignmentstatus` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `workassignment` INTEGER NOT NULL,
                `assignmentstatus` INTEGER NOT NULL,
                `notes` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`workassignment`) REFERENCES `workassignment`(iD)
                 ,FOREIGN KEY(`assignmentstatus`) REFERENCES `assignmentstatus`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function workassignment()
{
    return $this->belongsTo(Workassignment::class, 'workassignment');
}
public function assignmentstatus()
{
    return $this->belongsTo(Assignmentstatus::class, 'assignmentstatus');
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