<?php

namespace App\Models;

use App\Models\Model;

class Vacancy extends Model
{
    protected $table = 'vacancy';

    protected function getTableCreationQuery()
    {
        return "
            CREATE TABLE IF NOT EXISTS `vacancy` (
                `iD` INTEGER PRIMARY KEY AUTOINCREMENT,
                `reference_number` TEXT NOT NULL UNIQUE,
                `title` TEXT NOT NULL,
                `slug` TEXT NOT NULL UNIQUE,
                `department` INTEGER NOT NULL,
                `engagementbasis` INTEGER NOT NULL,
                `worklocationpreference` INTEGER NOT NULL,
                `target_role` INTEGER NOT NULL,
                `summary` TEXT NOT NULL,
                `description` TEXT NOT NULL,
                `responsibilities` TEXT NOT NULL,
                `requirements` TEXT NOT NULL,
                `remuneration_display` TEXT DEFAULT NULL,
                `open_slots` INTEGER NOT NULL DEFAULT 1,
                `publish_date` DATE NOT NULL,
                `closing_date` DATE NOT NULL,
                `is_featured` BOOLEAN NOT NULL DEFAULT 0,
                `vacancystatus` INTEGER NOT NULL DEFAULT 1,
                `reg_by` INTEGER NOT NULL DEFAULT 1,
                `reg_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `status` INTEGER NOT NULL DEFAULT 1,
                FOREIGN KEY(`department`) REFERENCES `department`(`iD`),
                FOREIGN KEY(`engagementbasis`) REFERENCES `engagementbasis`(`iD`),
                FOREIGN KEY(`worklocationpreference`) REFERENCES `worklocationpreference`(`iD`),
                FOREIGN KEY(`target_role`) REFERENCES `user_role`(`iD`),
                FOREIGN KEY(`vacancystatus`) REFERENCES `vacancystatus`(`iD`),
                FOREIGN KEY(`reg_by`) REFERENCES `user`(`iD`)
            );
        ";
    }

    protected function getDefaultDataInsertionQuery()
    {
        return [];
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department');
    }

    public function engagementbasis()
    {
        return $this->belongsTo(Engagementbasis::class, 'engagementbasis');
    }

    public function worklocationpreference()
    {
        return $this->belongsTo(Worklocationpreference::class, 'worklocationpreference');
    }

    public function targetRole()
    {
        return $this->belongsTo(Role::class, 'target_role');
    }

    public function vacancyStatus()
    {
        return $this->belongsTo(Vacancystatus::class, 'vacancystatus');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'reg_by');
    }

    public function applications()
    {
        return $this->hasMany(VacancyApplication::class, 'vacancy');
    }

    public function applicationCount(): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM `vacancy_application` WHERE vacancy = ? AND status = 1";
        $stmt = $this->query($sql, [$this->iD]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }

    /**
     * Retrieve all associated skill items through vacancy_skill junction table.
     * @return array
     */
    public function skills(): array
    {
        if (!$this->iD) {
            return [];
        }
        $sql = "
            SELECT s.*, vs.is_mandatory 
            FROM `vacancy_skill` vs
            JOIN `skillitem` s ON vs.skillitem = s.iD
            WHERE vs.vacancy = ? AND vs.status = 1
            ORDER BY vs.is_mandatory DESC, s.name ASC
        ";
        $stmt = $this->query($sql, [$this->iD]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function daysRemaining(): int
    {
        if (empty($this->closing_date)) {
            return 0;
        }
        $closing = strtotime($this->closing_date . ' 23:59:59');
        $diff = $closing - time();
        return (int)ceil($diff / 86400);
    }

    public function isClosed(): bool
    {
        if ((int)$this->vacancystatus === 4) {
            return true;
        }
        return $this->daysRemaining() < 0;
    }

    public function isClosingSoon(): bool
    {
        $days = $this->daysRemaining();
        return $days >= 0 && $days <= 5;
    }
}
