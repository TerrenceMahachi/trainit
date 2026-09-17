<?php

namespace App\Helpers;

use App\Models\Database;
use PDO;
use Throwable;

/**
 * Bridges the talent pipeline (rosterapplication.applicationstatus) to the
 * account model (userprofile.profilestatus + user.role).
 *
 * Before this helper the two were disconnected: an application could be taken
 * all the way to "On Roster" while the applicant's apprentice/associate profile
 * sat pending forever, so an approved candidate never actually received the
 * account. Every place that decides an application's fate now funnels through
 * here, so web and mobile grant accounts identically.
 *
 * profiletype: 1 general, 2 apprentice, 3 associate, 4 staff, 5 client
 * profilestatus: 2 pending, 3 approved, 4 declined
 */
class AccountElevation
{
    public const PROFILE_PENDING  = 2;
    public const PROFILE_APPROVED = 3;
    public const PROFILE_DECLINED = 4;

    public const TYPE_APPRENTICE = 2;
    public const TYPE_ASSOCIATE  = 3;

    /** user.role granted once a track profile is approved. */
    private const ROLE_FOR_TYPE = [
        self::TYPE_APPRENTICE => 5,
        self::TYPE_ASSOCIATE  => 4,
    ];

    /**
     * Map an application track (1 apprentice, 2 associate) to a profiletype.
     */
    public static function trackTypeFor($applicationTrack): ?int
    {
        $track = (int) $applicationTrack;
        if ($track === 1) {
            return self::TYPE_APPRENTICE;
        }
        if ($track === 2) {
            return self::TYPE_ASSOCIATE;
        }
        return null;
    }

    public static function titleFor(int $trackType): string
    {
        return $trackType === self::TYPE_ASSOCIATE ? 'Associate Consultant' : 'Apprentice';
    }

    /**
     * Ensure the applicant has a track profile sitting in "under review".
     * Called when an application is submitted (web and mobile alike) so the
     * request shows up in the admin approvals queue.
     */
    public static function markUnderReview(int $userId, int $trackType, ?int $actorId = null): bool
    {
        if ($userId <= 0 || !isset(self::ROLE_FOR_TYPE[$trackType])) {
            return false;
        }

        try {
            $pdo = Database::sharedPdo();
            $existing = self::findProfile($pdo, $userId, $trackType);
            $notes = 'Application submitted and under review.';

            if ($existing) {
                // Never demote an already-approved account back to pending.
                if ((int) $existing['profilestatus'] === self::PROFILE_APPROVED) {
                    return true;
                }
                $pdo->prepare(
                    "UPDATE userprofile
                        SET profilestatus = ?, request_notes = ?, reviewer_notes = NULL,
                            reviewed_by = NULL, reviewed_at = NULL, status = 1
                      WHERE iD = ?"
                )->execute([self::PROFILE_PENDING, $notes, (int) $existing['iD']]);
                $profileId = (int) $existing['iD'];
            } else {
                $pdo->prepare(
                    "INSERT INTO userprofile
                        (user, profiletype, profilestatus, display_title, request_notes, is_default, reg_by, reg_date, status)
                     VALUES (?, ?, ?, ?, ?, 0, ?, CURRENT_TIMESTAMP, 1)"
                )->execute([
                    $userId,
                    $trackType,
                    self::PROFILE_PENDING,
                    self::titleFor($trackType),
                    $notes,
                    $actorId ?: $userId,
                ]);
                $profileId = (int) $pdo->lastInsertId();
            }

            self::audit($pdo, $profileId, 'request_submitted', null, self::PROFILE_PENDING, $actorId ?: $userId, $notes);
            return true;
        } catch (Throwable $e) {
            error_log('AccountElevation::markUnderReview failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Approve the track account: profile approved, made the active profile, and
     * the legacy user.role synced so role-gated screens agree with the persona.
     */
    public static function grant(int $userId, int $trackType, ?int $reviewerId = null, string $notes = ''): bool
    {
        if ($userId <= 0 || !isset(self::ROLE_FOR_TYPE[$trackType])) {
            return false;
        }

        $notes = $notes !== '' ? $notes : 'Roster application approved — account activated.';

        try {
            $pdo = Database::sharedPdo();
            $existing = self::findProfile($pdo, $userId, $trackType);
            $from = $existing ? (int) $existing['profilestatus'] : null;
            $now = date('Y-m-d H:i:s');

            if ($existing) {
                $pdo->prepare(
                    "UPDATE userprofile
                        SET profilestatus = ?, reviewer_notes = ?, reviewed_by = ?, reviewed_at = ?, status = 1
                      WHERE iD = ?"
                )->execute([self::PROFILE_APPROVED, $notes, $reviewerId, $now, (int) $existing['iD']]);
                $profileId = (int) $existing['iD'];
            } else {
                $pdo->prepare(
                    "INSERT INTO userprofile
                        (user, profiletype, profilestatus, display_title, reviewer_notes, reviewed_by, reviewed_at, is_default, reg_by, reg_date, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?, CURRENT_TIMESTAMP, 1)"
                )->execute([
                    $userId,
                    $trackType,
                    self::PROFILE_APPROVED,
                    self::titleFor($trackType),
                    $notes,
                    $reviewerId,
                    $now,
                    $reviewerId ?: $userId,
                ]);
                $profileId = (int) $pdo->lastInsertId();
            }

            // Make the newly granted profile the active one, otherwise the user
            // keeps landing in their old "General User" workspace.
            $pdo->prepare("UPDATE userprofile SET is_default = 0 WHERE user = ?")->execute([$userId]);
            $pdo->prepare("UPDATE userprofile SET is_default = 1 WHERE iD = ?")->execute([$profileId]);

            // Sync the legacy role column (only from plain candidate, so we
            // never downgrade an admin/staff account that also holds a track).
            $pdo->prepare("UPDATE user SET role = ? WHERE iD = ? AND role = 2")
                ->execute([self::ROLE_FOR_TYPE[$trackType], $userId]);

            self::audit($pdo, $profileId, 'request_approved', $from, self::PROFILE_APPROVED, $reviewerId, $notes);

            NotificationHelper::notify(
                $userId,
                'Account Approved — Welcome to the Roster',
                'Your ' . self::titleFor($trackType) . ' account has been approved and activated. Your new workspace is ready.',
                'dashboard/home',
                'success',
                'fa-circle-check'
            );

            return true;
        } catch (Throwable $e) {
            error_log('AccountElevation::grant failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Decline the track account (unsuccessful application).
     */
    public static function decline(int $userId, int $trackType, ?int $reviewerId = null, string $notes = ''): bool
    {
        if ($userId <= 0 || !isset(self::ROLE_FOR_TYPE[$trackType])) {
            return false;
        }

        $notes = $notes !== '' ? $notes : 'Application did not meet the admission criteria.';

        try {
            $pdo = Database::sharedPdo();
            $existing = self::findProfile($pdo, $userId, $trackType);
            if (!$existing) {
                return false;
            }

            // Declining one application must not silently revoke an account the
            // candidate already holds — applicants often have more than one
            // application on the same track. Revoking access is an explicit
            // account action, not a side effect of closing an application.
            if ((int) $existing['profilestatus'] === self::PROFILE_APPROVED) {
                error_log(sprintf(
                    'AccountElevation::decline skipped for user %d (%s): account already approved.',
                    $userId,
                    self::titleFor($trackType)
                ));
                return false;
            }

            $pdo->prepare(
                "UPDATE userprofile
                    SET profilestatus = ?, reviewer_notes = ?, reviewed_by = ?, reviewed_at = ?
                  WHERE iD = ?"
            )->execute([self::PROFILE_DECLINED, $notes, $reviewerId, date('Y-m-d H:i:s'), (int) $existing['iD']]);

            self::audit(
                $pdo,
                (int) $existing['iD'],
                'request_rejected',
                (int) $existing['profilestatus'],
                self::PROFILE_DECLINED,
                $reviewerId,
                $notes
            );

            NotificationHelper::notify(
                $userId,
                'Application Outcome',
                'Your ' . self::titleFor($trackType) . ' application was not successful. Notes: ' . $notes,
                'profile/view',
                'warning',
                'fa-circle-xmark'
            );

            return true;
        } catch (Throwable $e) {
            error_log('AccountElevation::decline failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Resolve the account outcome implied by an application status.
     * 5 On Roster => grant, 8 Ineligible => decline, anything else => no change.
     */
    public static function applyApplicationStatus(
        int $userId,
        $applicationTrack,
        int $applicationStatus,
        ?int $reviewerId = null,
        string $notes = ''
    ): bool {
        $trackType = self::trackTypeFor($applicationTrack);
        if (!$trackType) {
            return false;
        }

        if ($applicationStatus === 5) {
            return self::grant($userId, $trackType, $reviewerId, $notes);
        }
        if ($applicationStatus === 8) {
            return self::decline($userId, $trackType, $reviewerId, $notes);
        }
        return false;
    }

    private static function findProfile(PDO $pdo, int $userId, int $trackType): ?array
    {
        $stmt = $pdo->prepare("SELECT iD, profilestatus FROM userprofile WHERE user = ? AND profiletype = ? LIMIT 1");
        $stmt->execute([$userId, $trackType]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private static function audit(
        PDO $pdo,
        int $profileId,
        string $action,
        ?int $fromStatus,
        int $toStatus,
        ?int $actorId,
        string $notes
    ): void {
        try {
            $pdo->prepare(
                "INSERT INTO profilerequestaudit
                    (userprofile, action, from_status, to_status, performed_by, notes, reg_by, reg_date, status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)"
            )->execute([$profileId, $action, $fromStatus, $toStatus, $actorId, $notes, $actorId]);
        } catch (Throwable $e) {
            // Audit table is best-effort; never block the decision on it.
            error_log('AccountElevation::audit skipped: ' . $e->getMessage());
        }
    }
}
