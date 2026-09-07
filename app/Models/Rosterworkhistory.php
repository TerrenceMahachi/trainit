<?php

namespace App\Models;

use App\Models\Model;

class Rosterworkhistory extends Model
{
    protected $table = 'rosterworkhistory';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `rosterworkhistory` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `sectortype` INTEGER DEFAULT NULL,
                `engagementbasis` INTEGER DEFAULT NULL,
                `organization_name` TEXT NOT NULL,
                `position_title` TEXT NOT NULL,
                `start_date` DATE DEFAULT NULL,
                `end_date` DATE DEFAULT NULL,
                `is_current` BOOLEAN DEFAULT 0,
                `key_deliverables` TEXT DEFAULT NULL,
                `reason_for_leaving` TEXT DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD),
                FOREIGN KEY(`sectortype`) REFERENCES `sectortype`(iD),
                FOREIGN KEY(`engagementbasis`) REFERENCES `engagementbasis`(iD)
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

    public function sectortype()
    {
        return $this->belongsTo(Sectortype::class, 'sectortype');
    }

    public function engagementbasis()
    {
        return $this->belongsTo(Engagementbasis::class, 'engagementbasis');
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