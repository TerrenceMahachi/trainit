<?php

namespace App\Models;

use App\Models\Model;


class Proficiencylevel extends Model
{
    protected $table = 'proficiencylevel';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `proficiencylevel` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `level_number` INTEGER NOT NULL,
                `code` TEXT NOT NULL,
                `name` TEXT NOT NULL,
                `definition` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return \App\Helpers\ReferenceDataSeeder::getInsertQueriesForTable($this->table);
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