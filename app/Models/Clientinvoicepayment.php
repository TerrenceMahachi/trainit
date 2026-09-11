<?php

namespace App\Models;

use App\Models\Model;

class Clientinvoicepayment extends Model
{
    protected $table = 'clientinvoicepayment';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `clientinvoicepayment` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `clientinvoice` INTEGER NOT NULL,
                `payment_method` TEXT NOT NULL,
                `transaction_reference` TEXT NOT NULL,
                `amount` DECIMAL(10,2) NOT NULL,
                `paid_at` DATETIME NOT NULL,
                `notes` TEXT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT 1,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`clientinvoice`) REFERENCES `clientinvoice`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    public function clientinvoice()
    {
        return $this->belongsTo(Clientinvoice::class, 'clientinvoice');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }
}
