<?php

namespace App\Models;

use App\Models\Model;


class Staffdepartmentassignment extends Model
{
    protected $table = 'staffdepartmentassignment';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `staffdepartmentassignment` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `staffprofile` INTEGER NOT NULL,
                `department` INTEGER NOT NULL,
                `is_head` BOOLEAN NOT NULL,
                `effective_from` DATE NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`staffprofile`) REFERENCES `staffprofile`(iD)
                 ,FOREIGN KEY(`department`) REFERENCES `department`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function staffprofile()
{
    return $this->belongsTo(Staffprofile::class, 'staffprofile');
}
public function department()
{
    return $this->belongsTo(Department::class, 'department');
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