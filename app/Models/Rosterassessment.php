<?php

namespace App\Models;

use App\Models\Model;

class Rosterassessment extends Model
{
    protected $table = 'rosterassessment';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `rosterassessment` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `reviewer` INTEGER DEFAULT NULL,
                `vettingrecommendation` INTEGER DEFAULT NULL,
                `eligibility_gate_passed` BOOLEAN DEFAULT 0,
                `technical_fit_score` DECIMAL(5,2) DEFAULT 0,
                `evidence_score` DECIMAL(5,2) DEFAULT 0,
                `judgement_score` DECIMAL(5,2) DEFAULT 0,
                `availability_score` DECIMAL(5,2) DEFAULT 0,
                `motivation_score` DECIMAL(5,2) DEFAULT 0,
                `total_score` DECIMAL(5,2) DEFAULT 0,
                `automated_red_flags` TEXT DEFAULT NULL,
                `interview_notes` TEXT DEFAULT NULL,
                `technical_test_result` TEXT DEFAULT NULL,
                `vetted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD),
                FOREIGN KEY(`reviewer`) REFERENCES `user`(iD),
                FOREIGN KEY(`vettingrecommendation`) REFERENCES `vettingrecommendation`(iD)
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

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer');
    }

    public function vettingrecommendation()
    {
        return $this->belongsTo(Vettingrecommendation::class, 'vettingrecommendation');
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