<?php

namespace App\Models;

use App\Models\Model;


class Staffdocument extends Model
{
    protected $table = 'staffdocument';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `staffdocument` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `staffprofile` INTEGER NOT NULL,
                `documenttype` INTEGER NOT NULL,
                `title` TEXT NOT NULL,
                `file_path` TEXT NOT NULL,
                `file_size` INTEGER NOT NULL,
                `mime_type` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`staffprofile`) REFERENCES `staffprofile`(iD)
                 ,FOREIGN KEY(`documenttype`) REFERENCES `documenttype`(iD)
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
public function documenttype()
{
    return $this->belongsTo(Documenttype::class, 'documenttype');
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