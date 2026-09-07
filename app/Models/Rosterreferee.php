<?php

namespace App\Models;

use App\Models\Model;

class Rosterreferee extends Model
{
    protected $table = 'rosterreferee';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `rosterreferee` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `refereecontacttiming` INTEGER DEFAULT NULL,
                `refereeverificationstatus` INTEGER DEFAULT 1,
                `referee_name` TEXT NOT NULL,
                `organization` TEXT NOT NULL,
                `position` TEXT NOT NULL,
                `relationship` TEXT NOT NULL,
                `email` TEXT NOT NULL,
                `phone` TEXT NOT NULL,
                `verification_notes` TEXT DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD),
                FOREIGN KEY(`refereecontacttiming`) REFERENCES `refereecontacttiming`(iD),
                FOREIGN KEY(`refereeverificationstatus`) REFERENCES `refereeverificationstatus`(iD)
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

    public function refereecontacttiming()
    {
        return $this->belongsTo(Refereecontacttiming::class, 'refereecontacttiming');
    }

    public function refereeverificationstatus()
    {
        return $this->belongsTo(Refereeverificationstatus::class, 'refereeverificationstatus');
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