<?php

namespace App\Models;

use App\Models\Model;


class Clientorganization extends Model
{
    protected $table = 'clientorganization';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `clientorganization` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `legal_name` TEXT NOT NULL,
                `trading_name` TEXT NOT NULL,
                `registration_number` TEXT NOT NULL,
                `tax_number` TEXT NOT NULL,
                `billing_email` TEXT NOT NULL,
                `address` TEXT NOT NULL,
                `city` TEXT NOT NULL,
                `country` TEXT NOT NULL,
                `primary_phone` TEXT NOT NULL,
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