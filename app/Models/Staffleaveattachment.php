<?php

namespace App\Models;

use App\Models\Model;


class Staffleaveattachment extends Model
{
    protected $table = 'staffleaveattachment';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `staffleaveattachment` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `staffleave` INTEGER NOT NULL,
                `file_path` TEXT NOT NULL,
                `file_size` INTEGER NOT NULL,
                `mime_type` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`staffleave`) REFERENCES `staffleave`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function staffleave()
{
    return $this->belongsTo(Staffleave::class, 'staffleave');
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