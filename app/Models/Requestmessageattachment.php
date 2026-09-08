<?php

namespace App\Models;

use App\Models\Model;


class Requestmessageattachment extends Model
{
    protected $table = 'requestmessageattachment';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `requestmessageattachment` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `requestmessage` INTEGER NOT NULL,
                `file_path` TEXT NOT NULL,
                `file_name` TEXT NOT NULL,
                `file_size` INTEGER NOT NULL,
                `mime_type` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`requestmessage`) REFERENCES `requestmessage`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function requestmessage()
{
    return $this->belongsTo(Requestmessage::class, 'requestmessage');
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