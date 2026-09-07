<?php

namespace App\Models;

use App\Models\Model;

class Rosterjudgementresponse extends Model
{
    protected $table = 'rosterjudgementresponse';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `rosterjudgementresponse` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `motivation_narrative` TEXT DEFAULT NULL,
                `primary_function_evidence` TEXT DEFAULT NULL,
                `shared_client_management_plan` TEXT DEFAULT NULL,
                `error_discovery_resolution` TEXT DEFAULT NULL,
                `urgent_friday_deadline_dilemma` TEXT DEFAULT NULL,
                `associate_apprentice_qa_methodology` TEXT DEFAULT NULL,
                `associate_unethical_client_solution` TEXT DEFAULT NULL,
                `apprentice_twelve_month_goal` TEXT DEFAULT NULL,
                `additional_notes` TEXT DEFAULT NULL,
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