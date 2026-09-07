<?php

namespace App\Models;

use App\Models\Model;

class Rosteronboarding extends Model
{
    protected $table = 'rosteronboarding';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `rosteronboarding` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `national_id_number` TEXT NOT NULL,
                `national_id_doc` TEXT DEFAULT NULL,
                `passport_number` TEXT DEFAULT NULL,
                `passport_expiry` DATE DEFAULT NULL,
                `street_address` TEXT NOT NULL,
                `city` TEXT NOT NULL,
                `country` TEXT DEFAULT 'Zimbabwe',
                `bank_name` TEXT NOT NULL,
                `bank_branch` TEXT NOT NULL,
                `account_name` TEXT NOT NULL,
                `account_number` TEXT NOT NULL,
                `bank_currency` TEXT DEFAULT 'USD',
                `emergency_contact_name` TEXT NOT NULL,
                `emergency_contact_phone` TEXT NOT NULL,
                `emergency_contact_relationship` TEXT NOT NULL,
                `nssa_number` TEXT DEFAULT NULL,
                `police_clearance_doc` TEXT DEFAULT NULL,
                `police_clearance_date` DATE DEFAULT NULL,
                `signed_contract_doc` TEXT DEFAULT NULL,
                `signed_nda_doc` TEXT DEFAULT NULL,
                `odoo_applicant_id` INTEGER DEFAULT NULL,
                `odoo_employee_id` INTEGER DEFAULT NULL,
                `synced_to_odoo_at` DATETIME DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    public function rosterapplication()
    {
        return $this->belongsTo(Rosterapplication::class, 'rosterapplication');
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