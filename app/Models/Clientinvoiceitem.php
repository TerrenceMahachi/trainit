<?php

namespace App\Models;

use App\Models\Model;

class Clientinvoiceitem extends Model
{
    protected $table = 'clientinvoiceitem';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `clientinvoiceitem` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `clientinvoice` INTEGER NOT NULL,
                `item_type` TEXT NOT NULL,
                `servicerequest` INTEGER NULL,
                `description` TEXT NOT NULL,
                `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
                `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                `reg_by` INTEGER NOT NULL DEFAULT 1,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`clientinvoice`) REFERENCES `clientinvoice`(iD),
                FOREIGN KEY(`servicerequest`) REFERENCES `servicerequest`(iD)
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

    public function servicerequest()
    {
        return $this->belongsTo(Servicerequest::class, 'servicerequest');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }
}
