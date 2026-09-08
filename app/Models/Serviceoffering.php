<?php

namespace App\Models;

use App\Models\Model;


class Serviceoffering extends Model
{
    protected $table = 'serviceoffering';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `serviceoffering` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `servicecategory` INTEGER NOT NULL,
                `code` TEXT NOT NULL,
                `name` TEXT NOT NULL,
                `description` TEXT NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`servicecategory`) REFERENCES `servicecategory`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function servicecategory()
{
    return $this->belongsTo(Servicecategory::class, 'servicecategory');
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