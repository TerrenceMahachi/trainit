<?php

namespace App\Models;

use App\Models\Model;


class Skillitem extends Model
{
    protected $table = 'skillitem';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `skillitem` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `servicefunction` INTEGER NOT NULL,
                `code` TEXT NOT NULL,
                `name` TEXT NOT NULL,
                `sort_order` INTEGER NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`servicefunction`) REFERENCES `servicefunction`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return \App\Helpers\ReferenceDataSeeder::getInsertQueriesForTable($this->table);
    }
    public function servicefunction()
{
    return $this->belongsTo(Servicefunction::class, 'servicefunction');
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