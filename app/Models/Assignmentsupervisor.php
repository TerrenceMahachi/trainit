<?php

namespace App\Models;

use App\Models\Model;


class Assignmentsupervisor extends Model
{
    protected $table = 'assignmentsupervisor';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `assignmentsupervisor` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `workassignment` INTEGER NOT NULL,
                `supervisor_user` INTEGER NOT NULL,
                `supervision_notes` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`workassignment`) REFERENCES `workassignment`(iD)
                 ,FOREIGN KEY(`supervisor_user`) REFERENCES `user`(iD)
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
public function supervisor_user()
{
    return $this->belongsTo(User::class, 'supervisor_user');
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