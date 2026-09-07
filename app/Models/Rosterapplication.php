<?php

namespace App\Models;

use App\Models\Model;

class Rosterapplication extends Model
{
    protected $table = 'rosterapplication';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `rosterapplication` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `user` INTEGER NOT NULL,
                `applicationtrack` INTEGER NOT NULL,
                `applicationstatus` INTEGER NOT NULL DEFAULT 1,
                `primaryfunction` INTEGER NOT NULL,
                `secondary_functions` TEXT DEFAULT NULL,
                `legal_name` TEXT NOT NULL,
                `preferred_name` TEXT DEFAULT NULL,
                `email` TEXT NOT NULL,
                `mobile_number` TEXT NOT NULL,
                `whatsapp_number` TEXT DEFAULT NULL,
                `date_of_birth` DATE DEFAULT NULL,
                `gender` INTEGER DEFAULT NULL,
                `city` TEXT DEFAULT NULL,
                `suburb` TEXT DEFAULT NULL,
                `zimprovince` INTEGER DEFAULT NULL,
                `country` TEXT DEFAULT 'Zimbabwe',
                `nationality` TEXT DEFAULT 'Zimbabwean',
                `workrightstatus` INTEGER DEFAULT NULL,
                `work_permit_number` TEXT DEFAULT NULL,
                `work_permit_expiry` DATE DEFAULT NULL,
                `has_disability_adjustment` BOOLEAN DEFAULT 0,
                `adjustment_details` TEXT DEFAULT NULL,
                `how_heard` TEXT DEFAULT NULL,
                `referred_by` TEXT DEFAULT NULL,
                `consent_version` TEXT DEFAULT '2026.1',
                `consent_timestamp` DATETIME DEFAULT NULL,
                `consent_ip_address` TEXT DEFAULT NULL,
                `e_signature` TEXT DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`user`) REFERENCES `user`(iD),
                FOREIGN KEY(`applicationtrack`) REFERENCES `applicationtrack`(iD),
                FOREIGN KEY(`applicationstatus`) REFERENCES `applicationstatus`(iD),
                FOREIGN KEY(`primaryfunction`) REFERENCES `servicefunction`(iD),
                FOREIGN KEY(`gender`) REFERENCES `gender`(iD),
                FOREIGN KEY(`zimprovince`) REFERENCES `zimprovince`(iD),
                FOREIGN KEY(`workrightstatus`) REFERENCES `workrightstatus`(iD)
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

    public function applicationtrack()
    {
        return $this->belongsTo(Applicationtrack::class, 'applicationtrack');
    }

    public function applicationstatus()
    {
        return $this->belongsTo(Applicationstatus::class, 'applicationstatus');
    }

    public function primaryfunction()
    {
        return $this->belongsTo(Servicefunction::class, 'primaryfunction');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender');
    }

    public function zimprovince()
    {
        return $this->belongsTo(Zimprovince::class, 'zimprovince');
    }

    public function workrightstatus()
    {
        return $this->belongsTo(Workrightstatus::class, 'workrightstatus');
    }

    public function apprenticeProfile()
    {
        $profiles = Apprenticeprofile::findByQuery("SELECT * FROM apprenticeprofile WHERE rosterapplication = ?", [$this->iD]);
        return count($profiles) > 0 ? $profiles[0] : null;
    }

    public function associateProfile()
    {
        $profiles = Associateprofile::findByQuery("SELECT * FROM associateprofile WHERE rosterapplication = ?", [$this->iD]);
        return count($profiles) > 0 ? $profiles[0] : null;
    }

    public function skills()
    {
        return Rosterskill::findByQuery("SELECT * FROM rosterskill WHERE rosterapplication = ?", [$this->iD]);
    }

    public function qualifications()
    {
        return Rosterqualification::findByQuery("SELECT * FROM rosterqualification WHERE rosterapplication = ?", [$this->iD]);
    }

    public function workHistories()
    {
        return Rosterworkhistory::findByQuery("SELECT * FROM rosterworkhistory WHERE rosterapplication = ?", [$this->iD]);
    }

    public function referees()
    {
        return Rosterreferee::findByQuery("SELECT * FROM rosterreferee WHERE rosterapplication = ?", [$this->iD]);
    }

    public function judgementResponse()
    {
        $res = Rosterjudgementresponse::findByQuery("SELECT * FROM rosterjudgementresponse WHERE rosterapplication = ?", [$this->iD]);
        return count($res) > 0 ? $res[0] : null;
    }

    public function assessment()
    {
        $res = Rosterassessment::findByQuery("SELECT * FROM rosterassessment WHERE rosterapplication = ? ORDER BY iD DESC LIMIT 1", [$this->iD]);
        return count($res) > 0 ? $res[0] : null;
    }

    public function onboarding()
    {
        $res = Rosteronboarding::findByQuery("SELECT * FROM rosteronboarding WHERE rosterapplication = ?", [$this->iD]);
        return count($res) > 0 ? $res[0] : null;
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