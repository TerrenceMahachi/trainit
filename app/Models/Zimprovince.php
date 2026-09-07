<?php

namespace App\Models;

use App\Models\Model;


class Zimprovince extends Model
{
    protected $table = 'zimprovince';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `zimprovince` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `code` TEXT NOT NULL,
                `name` TEXT NOT NULL,
                `sort_order` INTEGER NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
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