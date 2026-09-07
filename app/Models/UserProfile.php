<?php

namespace App\Models;

use App\Models\Model;


class UserProfile extends Model
{
    protected $table = 'userProfile';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `userProfile` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `name` TEXT NOT NULL,
                `user` INTEGER NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`user`) REFERENCES `user`(iD)
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

    public function status()
    {
        return $this->belongsTo(ItemStatus::class, 'status');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }

   
}