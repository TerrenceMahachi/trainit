<?php

namespace App\Models;

use App\Models\Model;

class Rosterqualification extends Model
{
    protected $table = 'rosterqualification';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `rosterqualification` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `qualificationtype` INTEGER DEFAULT NULL,
                `professionalbody` INTEGER DEFAULT NULL,
                `qualificationstatus` INTEGER DEFAULT NULL,
                `title` TEXT NOT NULL,
                `institution_name` TEXT DEFAULT NULL,
                `field_of_study` TEXT DEFAULT NULL,
                `date_obtained` DATE DEFAULT NULL,
                `expiry_date` DATE DEFAULT NULL,
                `certificate_doc` TEXT DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD),
                FOREIGN KEY(`qualificationtype`) REFERENCES `qualificationtype`(iD),
                FOREIGN KEY(`professionalbody`) REFERENCES `professionalbody`(iD),
                FOREIGN KEY(`qualificationstatus`) REFERENCES `qualificationstatus`(iD)
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

    public function qualificationtype()
    {
        return $this->belongsTo(Qualificationtype::class, 'qualificationtype');
    }

    public function professionalbody()
    {
        return $this->belongsTo(Professionalbody::class, 'professionalbody');
    }

    public function qualificationstatus()
    {
        return $this->belongsTo(Qualificationstatus::class, 'qualificationstatus');
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