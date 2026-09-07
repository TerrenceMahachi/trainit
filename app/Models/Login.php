<?php

namespace App\Models;

use App\Models\Model;

class Login extends Model
{
    protected $table = 'user_login';
    public function user()
    {
        return $this->belongsTo(User::class, 'user');
    }
    public function status()
    {
        return $this->belongsTo(LoginStatus::class, 'status');
    }
    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS user_login ( 
                iD INTEGER PRIMARY KEY AUTOINCREMENT,
                user INTEGER,
                reg_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                reg_by INTEGER NOT NULL DEFAULT '1',
                status INTEGER NOT NULL DEFAULT '1',
                login_count INTEGER NOT NULL DEFAULT '0',
                password TEXT NOT NULL,
                last_login datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                failed_login INTEGER NOT NULL DEFAULT '0',
                -- Idle soft-lock / resume challenge (App\\Helpers\\PasswordResume).
                -- Also added on the fly by PasswordResume::ensureSchema() for
                -- databases created before this column set existed.
                resume_tail_hash TEXT,
                resume_failed INTEGER NOT NULL DEFAULT 0,
                resume_locked_until DATETIME,
                FOREIGN KEY(user) REFERENCES user(iD),
                FOREIGN KEY(status) REFERENCES login_status(iD)
            )
        ";
    }
    protected function getDefaultDataInsertionQuery()
    {
        return [ ];
    }
}
