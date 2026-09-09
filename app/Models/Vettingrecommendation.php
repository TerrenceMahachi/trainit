<?php

namespace App\Models;

use App\Models\Model;


class Vettingrecommendation extends Model
{
    protected $table = 'vettingrecommendation';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `vettingrecommendation` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `code` TEXT NOT NULL,
                `name` TEXT NOT NULL,
                `description` TEXT NOT NULL,
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