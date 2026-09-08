<?php

namespace App\Models;

use App\Models\Model;


class Clientserviceplan extends Model
{
    protected $table = 'clientserviceplan';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `clientserviceplan` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `clientorganization` INTEGER NOT NULL,
                `serviceoffering` INTEGER NOT NULL,
                `plan_name` TEXT NOT NULL,
                `currency` TEXT NOT NULL,
                `monthly_fee` DECIMAL NOT NULL,
                `included_hours` DECIMAL NOT NULL,
                `associate_rate` DECIMAL NOT NULL,
                `apprentice_rate` DECIMAL NOT NULL,
                `billing_cycle_day` INTEGER NOT NULL,
                `service_manager` INTEGER NOT NULL,
                `billing_owner` INTEGER NOT NULL,
                `excesspolicy` INTEGER NOT NULL,
                `start_date` DATE NOT NULL,
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 
                 ,FOREIGN KEY(`clientorganization`) REFERENCES `clientorganization`(iD)
                 ,FOREIGN KEY(`serviceoffering`) REFERENCES `serviceoffering`(iD)
                 ,FOREIGN KEY(`service_manager`) REFERENCES `user`(iD)
                 ,FOREIGN KEY(`billing_owner`) REFERENCES `user`(iD)
                 ,FOREIGN KEY(`excesspolicy`) REFERENCES `excesspolicy`(iD)
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
public function serviceoffering()
{
    return $this->belongsTo(Serviceoffering::class, 'serviceoffering');
}
public function service_manager()
{
    return $this->belongsTo(User::class, 'service_manager');
}
public function billing_owner()
{
    return $this->belongsTo(User::class, 'billing_owner');
}
public function excesspolicy()
{
    return $this->belongsTo(Excesspolicy::class, 'excesspolicy');
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