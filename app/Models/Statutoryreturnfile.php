<?php

namespace App\Models;

use App\Models\Model;


class Statutoryreturnfile extends Model
{
    protected $table = 'statutoryreturnfile';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `statutoryreturnfile` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `statutoryreturn` INTEGER NOT NULL,
                `file_path` TEXT NOT NULL,
                `file_name` TEXT NOT NULL,
                `file_size` INTEGER NOT NULL,
                `mime_type` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`statutoryreturn`) REFERENCES `statutoryreturn`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function statutoryreturn()
{
    return $this->belongsTo(Statutoryreturn::class, 'statutoryreturn');
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