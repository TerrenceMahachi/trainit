<?php

namespace App\Models;

use App\Models\Model;

class Clientinvoice extends Model
{
    protected $table = 'clientinvoice';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `clientinvoice` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `invoice_number` TEXT NOT NULL UNIQUE,
                `clientorganization` INTEGER NOT NULL,
                `clientserviceplan` INTEGER NULL,
                `billing_period_start` DATE NOT NULL,
                `billing_period_end` DATE NOT NULL,
                `currency` TEXT NOT NULL DEFAULT 'USD',
                `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `vat_rate` DECIMAL(5,2) NOT NULL DEFAULT 15.00,
                `vat_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `payment_status` INTEGER NOT NULL DEFAULT 1,
                `issue_date` DATE NOT NULL,
                `due_date` DATE NOT NULL,
                `paid_date` DATE NULL,
                `notes` TEXT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT 1,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`clientorganization`) REFERENCES `clientorganization`(iD),
                FOREIGN KEY(`clientserviceplan`) REFERENCES `clientserviceplan`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    public function clientorganization()
    {
        return $this->belongsTo(Clientorganization::class, 'clientorganization');
    }

    public function clientserviceplan()
    {
        return $this->belongsTo(Clientserviceplan::class, 'clientserviceplan');
    }

    public function items()
    {
        return $this->hasMany(Clientinvoiceitem::class, 'clientinvoice');
    }

    public function payments()
    {
        return $this->hasMany(Clientinvoicepayment::class, 'clientinvoice');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }
}
