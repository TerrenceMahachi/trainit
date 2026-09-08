<?php

namespace App\Models;

use App\Models\Model;


class Clientmembership extends Model
{
    protected $table = 'clientmembership';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `clientmembership` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `clientorganization` INTEGER NOT NULL,
                `user` INTEGER NOT NULL,
                `clientmemberrole` INTEGER NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`clientorganization`) REFERENCES `clientorganization`(iD)
                 ,FOREIGN KEY(`user`) REFERENCES `user`(iD)
                 ,FOREIGN KEY(`clientmemberrole`) REFERENCES `clientmemberrole`(iD)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    public function clientorganization()
{
    return $this->belongsTo(Clientorganization::class, 'clientorganization');
}
public function user()
{
    return $this->belongsTo(User::class, 'user');
}
public function clientmemberrole()
{
    return $this->belongsTo(Clientmemberrole::class, 'clientmemberrole');
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