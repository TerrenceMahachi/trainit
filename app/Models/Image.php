<?php

namespace App\Models;

use App\Models\Model;


class Image extends Model
{
    protected $table = 'image';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `image` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `name` TEXT NOT NULL,
                `location` TEXT NOT NULL,
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