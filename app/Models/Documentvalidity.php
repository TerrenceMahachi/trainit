<?php

namespace App\Models;

use App\Models\Model;


class Documentvalidity extends Model
{
    protected $table = 'documentvalidity';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `documentvalidity` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `staffdocument` INTEGER NOT NULL,
                `issue_date` DATE NOT NULL,
                `expiry_date` DATE NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`staffdocument`) REFERENCES `staffdocument`(iD)
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

    public function status()
    {
        return $this->belongsTo(ItemStatus::class, 'status');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }

   
}