<?php

namespace App\Models;

use App\Models\Model;


class Profilerequestaudit extends Model
{
    protected $table = 'profilerequestaudit';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `profilerequestaudit` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `userprofile` INTEGER NOT NULL,
                `action` TEXT NOT NULL,
                `from_status` INTEGER DEFAULT NULL,
                `to_status` INTEGER NOT NULL,
                `performed_by` INTEGER NOT NULL,
                `notes` TEXT DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`userprofile`) REFERENCES `userprofile`(`iD`),
                FOREIGN KEY(`from_status`) REFERENCES `profilestatus`(`iD`),
                FOREIGN KEY(`to_status`) REFERENCES `profilestatus`(`iD`),
                FOREIGN KEY(`performed_by`) REFERENCES `user`(`iD`)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function userprofile()
{
    return $this->belongsTo(Userprofile::class, 'userprofile');
}
public function from_status()
{
    return $this->belongsTo(Profilestatus::class, 'from_status');
}
public function to_status()
{
    return $this->belongsTo(Profilestatus::class, 'to_status');
}
public function performed_by()
{
    return $this->belongsTo(User::class, 'performed_by');
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