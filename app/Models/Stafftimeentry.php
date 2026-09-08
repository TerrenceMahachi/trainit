<?php

namespace App\Models;

use App\Models\Model;


class Stafftimeentry extends Model
{
    protected $table = 'stafftimeentry';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `stafftimeentry` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `staffprofile` INTEGER NOT NULL,
                `activitycategory` INTEGER NOT NULL,
                `work_date` DATE NOT NULL,
                `hours` DECIMAL NOT NULL,
                `task_summary` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`staffprofile`) REFERENCES `staffprofile`(iD)
                 ,FOREIGN KEY(`activitycategory`) REFERENCES `activitycategory`(iD)
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
public function activitycategory()
{
    return $this->belongsTo(Activitycategory::class, 'activitycategory');
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