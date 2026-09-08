<?php

namespace App\Models;

use App\Models\Model;


class Servicerequestattachment extends Model
{
    protected $table = 'servicerequestattachment';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `servicerequestattachment` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `servicerequest` INTEGER NOT NULL,
                `file_path` TEXT NOT NULL,
                `file_name` TEXT NOT NULL,
                `file_size` INTEGER NOT NULL,
                `mime_type` TEXT NOT NULL,
                `messagevisibility` INTEGER NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`servicerequest`) REFERENCES `servicerequest`(iD)
                 ,FOREIGN KEY(`messagevisibility`) REFERENCES `messagevisibility`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function servicerequest()
{
    return $this->belongsTo(Servicerequest::class, 'servicerequest');
}
public function messagevisibility()
{
    return $this->belongsTo(Messagevisibility::class, 'messagevisibility');
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