<?php

namespace App\Models;

use App\Models\Model;


class Documentverification extends Model
{
    protected $table = 'documentverification';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `documentverification` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `staffdocument` INTEGER NOT NULL,
                `verificationstatus` INTEGER NOT NULL,
                `notes` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`staffdocument`) REFERENCES `staffdocument`(iD)
                 ,FOREIGN KEY(`verificationstatus`) REFERENCES `verificationstatus`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function staffdocument()
{
    return $this->belongsTo(Staffdocument::class, 'staffdocument');
}
public function verificationstatus()
{
    return $this->belongsTo(Verificationstatus::class, 'verificationstatus');
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