<?php

namespace App\Helpers;

use App\Models\ComplianceReminderLog;
use App\Models\Database;
use App\Helpers\Mailer;
use App\Helpers\NotificationHelper;
use PDO;

class ComplianceExpiryService
{
    /**
     * Scan all compliance documents and return their expiry status.
     *
     * @param int|null $maxDays Only return documents expiring within $maxDays (null for all with expiry)
     * @return array
     */
    public static function scanDocuments(?int $maxDays = 90): array
    {
        // Ensure table exists
        new ComplianceReminderLog();
        $db = Database::sharedPdo();

        $items = [];
        $today = new \DateTime('today');

        // 1. Staff Documents (documentvalidity)
        try {
            $stmt = $db->query("
                SELECT 
                    dv.iD AS validity_id,
                    dv.staffdocument AS document_id,
                    dv.issue_date,
                    dv.expiry_date,
                    sd.title AS doc_title,
                    COALESCE(dt.name, 'Staff Credential') AS doc_type_name,
                    sp.iD AS staff_id,
                    sp.first_name,
                    sp.surname,
                    sp.work_email,
                    sp.user AS user_id
                FROM documentvalidity dv
                JOIN staffdocument sd ON dv.staffdocument = sd.iD
                LEFT JOIN documenttype dt ON sd.documenttype = dt.iD
                JOIN staffprofile sp ON sd.staffprofile = sp.iD
                WHERE dv.status = 1 AND dv.expiry_date IS NOT NULL AND dv.expiry_date != ''
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $exp = new \DateTime($row['expiry_date']);
                $diff = $today->diff($exp);
                $daysLeft = (int)$diff->format('%r%a');

                if ($maxDays !== null && $daysLeft > $maxDays) {
                    continue;
                }

                $items[] = [
                    'document_type'  => 'staff_document',
                    'document_id'    => (int)$row['validity_id'],
                    'title'          => $row['doc_title'] ?: $row['doc_type_name'],
                    'category'       => $row['doc_type_name'],
                    'expiry_date'    => $row['expiry_date'],
                    'issue_date'     => $row['issue_date'],
                    'days_left'      => $daysLeft,
                    'is_expired'     => $daysLeft < 0,
                    'status_level'   => self::determineStatusLevel($daysLeft),
                    'recipient_name' => trim($row['first_name'] . ' ' . $row['surname']),
                    'recipient_email'=> $row['work_email'],
                    'user_id'        => (int)$row['user_id'],
                    'entity_type'    => 'Staff Member',
                    'link'           => '/staff/portal',
                ];
            }
        } catch (\Throwable $e) {
            error_log("Compliance scan error (staff documents): " . $e->getMessage());
        }

        // 2. Candidate Onboarding Passports & Police Clearances (rosteronboarding)
        try {
            $stmt = $db->query("
                SELECT 
                    ro.iD AS onboarding_id,
                    ro.passport_number,
                    ro.passport_expiry,
                    ro.police_clearance_date,
                    ra.iD AS rosterapplication_id,
                    ra.legal_name,
                    ra.email,
                    ra.user AS user_id
                FROM rosteronboarding ro
                JOIN rosterapplication ra ON ro.rosterapplication = ra.iD
                WHERE ro.status = 1
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                // Passport
                if (!empty($row['passport_expiry'])) {
                    $exp = new \DateTime($row['passport_expiry']);
                    $diff = $today->diff($exp);
                    $daysLeft = (int)$diff->format('%r%a');

                    if ($maxDays === null || $daysLeft <= $maxDays) {
                        $items[] = [
                            'document_type'  => 'passport',
                            'document_id'    => (int)$row['onboarding_id'],
                            'title'          => 'Passport (' . ($row['passport_number'] ?: 'Credential') . ')',
                            'category'       => 'Travel / ID',
                            'expiry_date'    => $row['passport_expiry'],
                            'issue_date'     => null,
                            'days_left'      => $daysLeft,
                            'is_expired'     => $daysLeft < 0,
                            'status_level'   => self::determineStatusLevel($daysLeft),
                            'recipient_name' => $row['legal_name'],
                            'recipient_email'=> $row['email'],
                            'user_id'        => (int)$row['user_id'],
                            'entity_type'    => 'Roster Associate',
                            'link'           => '/candidate/portal',
                        ];
                    }
                }

                // Police Clearance (valid for 1 year = 365 days from issue date)
                if (!empty($row['police_clearance_date'])) {
                    $issue = new \DateTime($row['police_clearance_date']);
                    $exp = clone $issue;
                    $exp->modify('+1 year');
                    $expFormatted = $exp->format('Y-m-d');
                    $diff = $today->diff($exp);
                    $daysLeft = (int)$diff->format('%r%a');

                    if ($maxDays === null || $daysLeft <= $maxDays) {
                        $items[] = [
                            'document_type'  => 'police_clearance',
                            'document_id'    => (int)$row['onboarding_id'],
                            'title'          => 'Police Clearance Certificate',
                            'category'       => 'Vetting & Security',
                            'expiry_date'    => $expFormatted,
                            'issue_date'     => $row['police_clearance_date'],
                            'days_left'      => $daysLeft,
                            'is_expired'     => $daysLeft < 0,
                            'status_level'   => self::determineStatusLevel($daysLeft),
                            'recipient_name' => $row['legal_name'],
                            'recipient_email'=> $row['email'],
                            'user_id'        => (int)$row['user_id'],
                            'entity_type'    => 'Roster Associate',
                            'link'           => '/candidate/portal',
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            error_log("Compliance scan error (roster onboarding): " . $e->getMessage());
        }

        // 3. Work Permits (rosterapplication)
        try {
            $stmt = $db->query("
                SELECT 
                    ra.iD AS rosterapplication_id,
                    ra.work_permit_number,
                    ra.work_permit_expiry,
                    ra.legal_name,
                    ra.email,
                    ra.user AS user_id
                FROM rosterapplication ra
                WHERE ra.status = 1 AND ra.work_permit_expiry IS NOT NULL AND ra.work_permit_expiry != ''
            ");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $exp = new \DateTime($row['work_permit_expiry']);
                $diff = $today->diff($exp);
                $daysLeft = (int)$diff->format('%r%a');

                if ($maxDays === null || $daysLeft <= $maxDays) {
                    $items[] = [
                        'document_type'  => 'work_permit',
                        'document_id'    => (int)$row['rosterapplication_id'],
                        'title'          => 'Work Permit (' . ($row['work_permit_number'] ?: 'Statutory') . ')',
                        'category'       => 'Legal Right to Work',
                        'expiry_date'    => $row['work_permit_expiry'],
                        'issue_date'     => null,
                        'days_left'      => $daysLeft,
                        'is_expired'     => $daysLeft < 0,
                        'status_level'   => self::determineStatusLevel($daysLeft),
                        'recipient_name' => $row['legal_name'],
                        'recipient_email'=> $row['email'],
                        'user_id'        => (int)$row['user_id'],
                        'entity_type'    => 'Candidate',
                        'link'           => '/candidate/portal',
                    ];
                }
            }
        } catch (\Throwable $e) {
            error_log("Compliance scan error (work permits): " . $e->getMessage());
        }

        // Sort by days_left ascending (most urgent first)
        usort($items, fn($a, $b) => $a['days_left'] <=> $b['days_left']);

        return $items;
    }

    /**
     * Determine status severity level based on days remaining.
     */
    public static function determineStatusLevel(int $daysLeft): string
    {
        if ($daysLeft < 0) {
            return 'danger'; // Expired
        }
        if ($daysLeft <= 30) {
            return 'danger'; // Critical
        }
        if ($daysLeft <= 60) {
            return 'warning'; // Attention needed
        }
        return 'info'; // Upcoming
    }

    /**
     * Compute aggregated compliance stats.
     */
    public static function getComplianceStats(): array
    {
        $all = self::scanDocuments(null);
        $expired = 0;
        $critical = 0; // 0 - 30 days
        $warning = 0;  // 31 - 60 days
        $valid = 0;    // > 60 days

        foreach ($all as $doc) {
            if ($doc['days_left'] < 0) {
                $expired++;
            } elseif ($doc['days_left'] <= 30) {
                $critical++;
            } elseif ($doc['days_left'] <= 60) {
                $warning++;
            } else {
                $valid++;
            }
        }

        return [
            'total'    => count($all),
            'expired'  => $expired,
            'critical' => $critical,
            'warning'  => $warning,
            'valid'    => $valid,
        ];
    }

    /**
     * Dispatch reminders for expiring documents with cooldown throttling.
     *
     * @param int $thresholdDays Remind if expiring within this number of days (default: 60)
     * @param int $cooldownDays Days to wait before sending a repeat reminder (default: 7)
     * @return array Summary of actions taken
     */
    public static function dispatchReminders(int $thresholdDays = 60, int $cooldownDays = 7): array
    {
        $documents = self::scanDocuments($thresholdDays);
        new ComplianceReminderLog();
        $db = Database::sharedPdo();

        $dispatched = 0;
        $skipped = 0;
        $details = [];

        foreach ($documents as $doc) {
            // Check throttling
            $stmt = $db->prepare("
                SELECT iD, sent_at FROM compliance_reminder_log
                WHERE document_type = ? AND document_id = ?
                ORDER BY sent_at DESC LIMIT 1
            ");
            $stmt->execute([$doc['document_type'], $doc['document_id']]);
            $lastSent = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($lastSent) {
                $lastSentTime = strtotime($lastSent['sent_at']);
                $cooldownSeconds = $cooldownDays * 86400;
                if ((time() - $lastSentTime) < $cooldownSeconds) {
                    $skipped++;
                    continue;
                }
            }

            // Dispatch Email Alert
            if (!empty($doc['recipient_email'])) {
                Mailer::sendDocumentExpiryAlert(
                    $doc['recipient_email'],
                    $doc['recipient_name'],
                    $doc['title'],
                    $doc['expiry_date'],
                    $doc['days_left'],
                    $doc['is_expired']
                );
            }

            // Dispatch In-App Notification if user_id exists
            if (!empty($doc['user_id'])) {
                $title = $doc['is_expired']
                    ? "Compliance Alert: '{$doc['title']}' has EXPIRED"
                    : "Compliance Alert: '{$doc['title']}' expires in {$doc['days_left']} days";
                $msg = $doc['is_expired']
                    ? "Your {$doc['title']} expired on {$doc['expiry_date']}. Please upload an updated credential immediately to maintain active status."
                    : "Your {$doc['title']} is due to expire on {$doc['expiry_date']}. Please upload a renewal copy.";

                NotificationHelper::notify(
                    userId: (int)$doc['user_id'],
                    title: $title,
                    message: $msg,
                    link: $doc['link'],
                    type: $doc['is_expired'] ? 'danger' : ($doc['days_left'] <= 30 ? 'warning' : 'info'),
                    icon: 'clock'
                );
            }

            // If expired or <= 7 days, notify compliance admins
            if ($doc['is_expired'] || $doc['days_left'] <= 7) {
                NotificationHelper::notifyAdmins(
                    title: "Compliance Risk: " . $doc['recipient_name'],
                    message: "{$doc['recipient_name']}'s {$doc['title']} " . ($doc['is_expired'] ? "has expired ({$doc['expiry_date']})" : "expires in {$doc['days_left']} days"),
                    link: "/admin/compliance",
                    type: 'danger',
                    icon: 'shield-exclamation'
                );
            }

            // Log the reminder
            $insertStmt = $db->prepare("
                INSERT INTO compliance_reminder_log 
                (document_type, document_id, recipient_email, recipient_name, expiry_date, days_left, channel, sent_at)
                VALUES (?, ?, ?, ?, ?, ?, 'email+inapp', CURRENT_TIMESTAMP)
            ");
            $insertStmt->execute([
                $doc['document_type'],
                $doc['document_id'],
                $doc['recipient_email'] ?? '',
                $doc['recipient_name'] ?? '',
                $doc['expiry_date'],
                $doc['days_left']
            ]);

            $dispatched++;
            $details[] = [
                'recipient' => $doc['recipient_name'],
                'document'  => $doc['title'],
                'days_left' => $doc['days_left'],
            ];
        }

        return [
            'total_scanned' => count($documents),
            'dispatched'    => $dispatched,
            'skipped'       => $skipped,
            'details'       => $details
        ];
    }
}
