<?php

namespace App\Models;

use App\Models\Model;


class {{MODEL_NAME}} extends Model
{
    protected $table = '{{TABLE_NAME}}';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE `{{TABLE_NAME}}` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                {{FIELDS_SQL}},
                `reg_by` INTEGER NOT NULL DEFAULT '1',
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1
                 {{FOREIGN_KEYS_SQL}}
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }
    {{HAS_FIELDS}}
    public function status()
    {
        return $this->belongsTo(ItemStatus::class, 'status');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }

   
}