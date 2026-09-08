<?php

namespace App\Models;

use App\Models\Model;


class Requestmessage extends Model
{
    protected $table = 'requestmessage';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `requestmessage` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `servicerequest` INTEGER NOT NULL,
                `messagevisibility` INTEGER NOT NULL,
                `body` TEXT NOT NULL,
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