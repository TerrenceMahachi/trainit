<?php

namespace App\Models;

use App\Models\Model;

class ClientOnboardingRequest extends Model
{
    protected $table = 'client_onboarding_request';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `client_onboarding_request` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `company_name` TEXT NOT NULL,
                `trading_name` TEXT DEFAULT NULL,
                `registration_number` TEXT DEFAULT NULL,
                `tax_number` TEXT DEFAULT NULL,
                `sectortype` INTEGER DEFAULT NULL,
                `website` TEXT DEFAULT NULL,
                `street_address` TEXT DEFAULT NULL,
                `city` TEXT DEFAULT NULL,
                `country` TEXT DEFAULT 'Zimbabwe',
                `contact_name` TEXT NOT NULL,
                `contact_email` TEXT NOT NULL,
                `contact_phone` TEXT NOT NULL,
                `contact_title` TEXT DEFAULT NULL,
                `serviceoffering` INTEGER DEFAULT NULL,
                `engagementmodel` INTEGER DEFAULT NULL,
                `estimated_monthly_hours` INTEGER DEFAULT 40,
                `currency_preference` TEXT DEFAULT 'USD',
                `notes` TEXT DEFAULT NULL,
                `status` TEXT NOT NULL DEFAULT 'pending',
                `reviewed_by` INTEGER DEFAULT NULL,
                `review_notes` TEXT DEFAULT NULL,
                `reviewed_at` DATETIME DEFAULT NULL,
                `provisioned_clientorganization` INTEGER DEFAULT NULL,
                `provisioned_user` INTEGER DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT 1,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status_id` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`sectortype`) REFERENCES `sectortype`(iD),
                FOREIGN KEY(`serviceoffering`) REFERENCES `serviceoffering`(iD),
                FOREIGN KEY(`engagementmodel`) REFERENCES `engagementmodel`(iD),
                FOREIGN KEY(`reviewed_by`) REFERENCES `user`(iD),
                FOREIGN KEY(`provisioned_clientorganization`) REFERENCES `clientorganization`(iD),
                FOREIGN KEY(`provisioned_user`) REFERENCES `user`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    public function sectortype()
    {
        return $this->belongsTo(Sectortype::class, 'sectortype');
    }

    public function serviceoffering()
    {
        return $this->belongsTo(Serviceoffering::class, 'serviceoffering');
    }

    public function engagementmodel()
    {
        return $this->belongsTo(Engagementmodel::class, 'engagementmodel');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function clientorganization()
    {
        return $this->belongsTo(Clientorganization::class, 'provisioned_clientorganization');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'provisioned_user');
    }
}
