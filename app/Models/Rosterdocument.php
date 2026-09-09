<?php

namespace App\Models;

use App\Models\Model;


class Rosterdocument extends Model
{
    protected $table = 'Rosterdocument';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `Rosterdocument` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `documenttype` INTEGER NOT NULL,
                `file_path` TEXT NOT NULL,
                `original_name` TEXT NOT NULL,
                `file_size_kb` INTEGER NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD)
                 ,FOREIGN KEY(`documenttype`) REFERENCES `documenttype`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function rosterapplication()
{
    return $this->belongsTo(Rosterapplication::class, 'rosterapplication');
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