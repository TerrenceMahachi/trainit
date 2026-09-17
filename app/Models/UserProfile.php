<?php

namespace App\Models;

use App\Models\Model;


class Userprofile extends Model
{
    protected $table = 'userprofile';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `userprofile` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `user` INTEGER NOT NULL,
                `profiletype` INTEGER NOT NULL,
                `profilestatus` INTEGER NOT NULL,
                `display_title` TEXT DEFAULT NULL,
                `is_default` BOOLEAN NOT NULL DEFAULT 0,
                `request_notes` TEXT DEFAULT NULL,
                `reviewer_notes` TEXT DEFAULT NULL,
                `reviewed_by` INTEGER DEFAULT NULL,
                `reviewed_at` DATETIME DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`user`) REFERENCES `user`(`iD`),
                FOREIGN KEY(`profiletype`) REFERENCES `profiletype`(`iD`),
                FOREIGN KEY(`profilestatus`) REFERENCES `profilestatus`(`iD`),
                FOREIGN KEY(`reviewed_by`) REFERENCES `user`(`iD`)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function user()
{
    return $this->belongsTo(User::class, 'user');
}
public function profiletype()
{
    return $this->belongsTo(Profiletype::class, 'profiletype');
}
public function profilestatus()
{
    return $this->belongsTo(Profilestatus::class, 'profilestatus');
}
public function reviewed_by()
{
    return $this->belongsTo(User::class, 'reviewed_by');
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