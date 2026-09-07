<?php

namespace App\Models;

use App\Models\Model;

class Apprenticeprofile extends Model
{
    protected $table = 'apprenticeprofile';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `apprenticeprofile` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `rosterapplication` INTEGER NOT NULL,
                `apprenticestatus` INTEGER DEFAULT NULL,
                `institution_name` TEXT DEFAULT NULL,
                `degree_programme` TEXT DEFAULT NULL,
                `study_level` TEXT DEFAULT NULL,
                `student_reg_number` TEXT DEFAULT NULL,
                `expected_completion_date` DATE DEFAULT NULL,
                `is_wrl_attachment` BOOLEAN DEFAULT 0,
                `wrl_start_date` DATE DEFAULT NULL,
                `wrl_end_date` DATE DEFAULT NULL,
                `wrl_duration_months` INTEGER DEFAULT NULL,
                `wrl_coordinator_name` TEXT DEFAULT NULL,
                `wrl_coordinator_email` TEXT DEFAULT NULL,
                `wrl_coordinator_phone` TEXT DEFAULT NULL,
                `requires_placement_letter` BOOLEAN DEFAULT 0,
                `requires_host_mou` BOOLEAN DEFAULT 0,
                `requires_logbook_visits` BOOLEAN DEFAULT 0,
                `requires_host_insurance` BOOLEAN DEFAULT 0,
                `min_stipend_required` DECIMAL(10,2) DEFAULT NULL,
                `engagementmodel` INTEGER DEFAULT NULL,
                `worklocationpreference` INTEGER DEFAULT NULL,
                `proof_of_registration_doc` TEXT DEFAULT NULL,
                `transcript_doc` TEXT DEFAULT NULL,
                `current_average_grade` TEXT DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`rosterapplication`) REFERENCES `rosterapplication`(iD),
                FOREIGN KEY(`apprenticestatus`) REFERENCES `apprenticestatus`(iD),
                FOREIGN KEY(`engagementmodel`) REFERENCES `engagementmodel`(iD),
                FOREIGN KEY(`worklocationpreference`) REFERENCES `worklocationpreference`(iD)
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

    public function apprenticestatus()
    {
        return $this->belongsTo(Apprenticestatus::class, 'apprenticestatus');
    }

    public function engagementmodel()
    {
        return $this->belongsTo(Engagementmodel::class, 'engagementmodel');
    }

    public function worklocationpreference()
    {
        return $this->belongsTo(Worklocationpreference::class, 'worklocationpreference');
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