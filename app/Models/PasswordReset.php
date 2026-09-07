<?php

namespace App\Models;

use App\Models\Model;

class PasswordReset extends Model
{
    protected $table = 'password_reset';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `password_reset` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `user` INTEGER NOT NULL,
                `token` TEXT NOT NULL,
                `expires` DATETIME NOT NULL,
                `used` INTEGER NOT NULL DEFAULT 0,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`user`) REFERENCES `user`(iD)
            );
        ";
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user');
    }
}
