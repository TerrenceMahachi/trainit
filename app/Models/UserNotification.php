<?php

namespace App\Models;

use App\Models\Model;

class UserNotification extends Model
{
    protected $table = 'user_notification';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `user_notification` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `user` INTEGER NOT NULL,
                `title` TEXT NOT NULL,
                `message` TEXT NOT NULL,
                `link` TEXT DEFAULT NULL,
                `type` TEXT NOT NULL DEFAULT 'info',
                `icon` TEXT NOT NULL DEFAULT 'fa-bell',
                `is_read` BOOLEAN NOT NULL DEFAULT 0,
                `read_at` DATETIME DEFAULT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT 1,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`user`) REFERENCES `user`(iD)
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

    public function isUnread(): bool
    {
        return empty($this->is_read);
    }
}
