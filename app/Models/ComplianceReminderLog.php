<?php

namespace App\Models;

use App\Models\Model;

class ComplianceReminderLog extends Model
{
    protected $table = 'compliance_reminder_log';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `compliance_reminder_log` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `document_type` TEXT NOT NULL,
                `document_id` INTEGER NOT NULL,
                `recipient_email` TEXT NOT NULL,
                `recipient_name` TEXT DEFAULT NULL,
                `expiry_date` DATE NOT NULL,
                `days_left` INTEGER NOT NULL,
                `channel` TEXT NOT NULL DEFAULT 'email',
                `sent_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
}
