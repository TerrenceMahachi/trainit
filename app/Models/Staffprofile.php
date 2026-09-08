<?php

namespace App\Models;

use App\Models\Model;

class Staffprofile extends Model
{
    protected $table = 'staffprofile';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `staffprofile` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `user` INTEGER NOT NULL UNIQUE,
                `employee_number` TEXT DEFAULT NULL,
                `job_title` TEXT NOT NULL,
                `department` TEXT NOT NULL,
                `nature_of_employment` TEXT DEFAULT 'ORDINARY',
                `employee_type` TEXT DEFAULT 'Employee',
                `date_of_employment` DATE DEFAULT NULL,
                `station` TEXT DEFAULT 'Harare HQ',
                `salary` TEXT DEFAULT NULL,
                `employment_status` TEXT DEFAULT 'active',
                
                `title` TEXT DEFAULT NULL,
                `first_name` TEXT NOT NULL,
                `other_names` TEXT DEFAULT NULL,
                `surname` TEXT NOT NULL,
                `national_id_number` TEXT DEFAULT NULL,
                `ssr_number` TEXT DEFAULT NULL,
                `birth_certificate_number` TEXT DEFAULT NULL,
                `drivers_licence_number` TEXT DEFAULT NULL,
                `passport_number` TEXT DEFAULT NULL,
                `date_of_birth` DATE DEFAULT NULL,
                `gender` TEXT DEFAULT NULL,
                `marital_status` TEXT DEFAULT NULL,
                `nationality` TEXT DEFAULT 'Zimbabwean',
                `citizenship` TEXT DEFAULT 'ZW',
                
                `street_number` TEXT DEFAULT NULL,
                `street_name` TEXT DEFAULT NULL,
                `suburb` TEXT DEFAULT NULL,
                `town` TEXT DEFAULT 'HARARE',
                `region` TEXT DEFAULT 'HRE',
                `country` TEXT DEFAULT 'Zimbabwe',
                `postal_code` TEXT DEFAULT NULL,
                `telephone_number` TEXT DEFAULT NULL,
                `work_email` TEXT NOT NULL,
                `personal_email` TEXT DEFAULT NULL,
                
                `bank_name` TEXT DEFAULT NULL,
                `bank_branch` TEXT DEFAULT NULL,
                `account_name` TEXT DEFAULT NULL,
                `account_number` TEXT DEFAULT NULL,
                `bank_currency` TEXT DEFAULT 'USD',
                
                `emergency_contact_name` TEXT DEFAULT NULL,
                `emergency_contact_phone` TEXT DEFAULT NULL,
                `emergency_contact_relationship` TEXT DEFAULT NULL,
                
                `national_id_doc` TEXT DEFAULT NULL,
                `signed_contract_doc` TEXT DEFAULT NULL,
                `cv_doc` TEXT DEFAULT NULL,
                `police_clearance_doc` TEXT DEFAULT NULL,
                
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`user`) REFERENCES `user`(`iD`) ON DELETE CASCADE
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }

    public function __get($key)
    {
        if ($key === 'name') {
            return trim(($this->attributes['first_name'] ?? '') . ' ' . ($this->attributes['surname'] ?? ''));
        }
        return parent::__get($key);
    }
}
