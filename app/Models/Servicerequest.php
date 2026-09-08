<?php

namespace App\Models;

use App\Models\Model;


class Servicerequest extends Model
{
    protected $table = 'servicerequest';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `servicerequest` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `request_number` TEXT NOT NULL,
                `clientorganization` INTEGER NOT NULL,
                `clientserviceplan` INTEGER NOT NULL,
                `requester` INTEGER NOT NULL,
                `prioritylevel` INTEGER NOT NULL,
                `title` TEXT NOT NULL,
                `description` TEXT NOT NULL,
                `desired_due_date` DATE NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`clientorganization`) REFERENCES `clientorganization`(iD)
                 ,FOREIGN KEY(`clientserviceplan`) REFERENCES `clientserviceplan`(iD)
                 ,FOREIGN KEY(`requester`) REFERENCES `user`(iD)
                 ,FOREIGN KEY(`prioritylevel`) REFERENCES `prioritylevel`(iD)
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
public function clientserviceplan()
{
    return $this->belongsTo(Clientserviceplan::class, 'clientserviceplan');
}
public function requester()
{
    return $this->belongsTo(User::class, 'requester');
}
public function prioritylevel()
{
    return $this->belongsTo(Prioritylevel::class, 'prioritylevel');
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