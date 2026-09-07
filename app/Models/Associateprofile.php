<?php

namespace App\Models;

use App\Models\Model;

class Associateprofile extends Model
{
    protected $table = 'associateprofile';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `associateprofile` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `employmentstatus` INTEGER DEFAULT NULL,
                `years_experience` TEXT DEFAULT NULL,
                `donor_experience_years` TEXT DEFAULT NULL,
                `donors_worked_with` TEXT DEFAULT NULL,
                `largest_budget_handled` TEXT DEFAULT NULL,
                `largest_team_supervised` INTEGER DEFAULT NULL,
                `largest_endpoints_supported` INTEGER DEFAULT NULL,
                `largest_dataset_managed` TEXT DEFAULT NULL,
                `supervised_juniors_before` TEXT DEFAULT NULL,
                `led_audits_or_evaluations` TEXT DEFAULT NULL,
                `rejected_work_experience` TEXT DEFAULT NULL,
                `day_rate_expectation` DECIMAL(10,2) DEFAULT NULL,
                `capacity_days_per_month` TEXT DEFAULT NULL,
                `notice_period` TEXT DEFAULT NULL,
                `invoiceentitytype` INTEGER DEFAULT NULL,
                `has_tax_clearance_itf263` BOOLEAN DEFAULT 0,
                `zimra_bp_number` TEXT DEFAULT NULL,
                `tax_clearance_doc` TEXT DEFAULT NULL,
                `is_vat_registered` BOOLEAN DEFAULT 0,
                `vat_number` TEXT DEFAULT NULL,
                `has_indemnity_insurance` BOOLEAN DEFAULT 0,
                `insurance_cover_amount` DECIMAL(10,2) DEFAULT NULL,
                `conflict_of_interest` TEXT DEFAULT NULL,
                `moonlighting_restrictions` TEXT DEFAULT NULL,
                `cv_bid_consent` TEXT DEFAULT 'yes',
                `restricted_sectors_or_donors` TEXT DEFAULT NULL,
                `public_website_listing_consent` BOOLEAN DEFAULT 1,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD),
                FOREIGN KEY(`employmentstatus`) REFERENCES `employmentstatus`(iD),
                FOREIGN KEY(`invoiceentitytype`) REFERENCES `invoiceentitytype`(iD)
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

    public function employmentstatus()
    {
        return $this->belongsTo(Employmentstatus::class, 'employmentstatus');
    }

    public function invoiceentitytype()
    {
        return $this->belongsTo(Invoiceentitytype::class, 'invoiceentitytype');
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