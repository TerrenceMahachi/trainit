<?php

namespace App\Models;

use App\Models\Model;

class Rosterskill extends Model
{
    protected $table = 'rosterskill';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `rosterskill` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `servicefunction` INTEGER NOT NULL,
                `skillitem` INTEGER NOT NULL,
                `proficiencylevel` INTEGER NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD),
                FOREIGN KEY(`servicefunction`) REFERENCES `servicefunction`(iD),
                FOREIGN KEY(`skillitem`) REFERENCES `skillitem`(iD),
                FOREIGN KEY(`proficiencylevel`) REFERENCES `proficiencylevel`(iD)
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

    public function servicefunction()
    {
        return $this->belongsTo(Servicefunction::class, 'servicefunction');
    }

    public function skillitem()
    {
        return $this->belongsTo(Skillitem::class, 'skillitem');
    }

    public function proficiencylevel()
    {
        return $this->belongsTo(Proficiencylevel::class, 'proficiencylevel');
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