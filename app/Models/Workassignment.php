<?php

namespace App\Models;

use App\Models\Model;


class Workassignment extends Model
{
    protected $table = 'workassignment';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `workassignment` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `servicerequest` INTEGER NOT NULL,
                `user` INTEGER NOT NULL,
                `assigned_role` INTEGER NOT NULL,
                `rate_currency` TEXT NOT NULL,
                `hourly_rate_snapshot` DECIMAL NOT NULL,
                `due_date` DATE NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`servicerequest`) REFERENCES `servicerequest`(iD)
                 ,FOREIGN KEY(`user`) REFERENCES `user`(iD)
                 ,FOREIGN KEY(`assigned_role`) REFERENCES `user_role`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function servicerequest()
{
    return $this->belongsTo(Servicerequest::class, 'servicerequest');
}
public function user()
{
    return $this->belongsTo(User::class, 'user');
}
public function assigned_role()
{
    return $this->belongsTo(Role::class, 'assigned_role');
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