<?php

namespace App\Helpers;

use App\Models\CandidateJobAlert;
use App\Models\Database;
use App\Models\Vacancy;
use App\Helpers\Mailer;
use App\Helpers\NotificationHelper;
use PDO;

class JobAlertService
{
    /**
     * Subscribe a candidate to vacancy alerts.
     */
    public static function subscribe(array $data): array
    {
        $email = strtolower(trim($data['email'] ?? ''));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => 0, 'msg' => 'A valid email address is required.'];
        }

        $name = trim($data['name'] ?? '');
        $keywords = trim($data['keywords'] ?? '');
        $dept = !empty($data['department']) ? (int)$data['department'] : null;
        $basis = !empty($data['engagementbasis']) ? (int)$data['engagementbasis'] : null;
        $userId = !empty($data['user_id']) ? (int)$data['user_id'] : (Auth::check() ? Auth::id() : null);

        // Ensure model table is initialized
        new CandidateJobAlert();
        $db = Database::sharedPdo();

        // Check for existing matching alert for this email
        $stmt = $db->prepare("
            SELECT * FROM candidate_job_alert 
            WHERE email = ? AND COALESCE(keywords, '') = ? AND COALESCE(department, 0) = ?
            LIMIT 1
        ");
        $stmt->execute([$email, $keywords, (int)$dept]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            if (!$existing['is_active']) {
                $upd = $db->prepare("UPDATE candidate_job_alert SET is_active = 1 WHERE iD = ?");
                $upd->execute([$existing['iD']]);
            }
            return [
                'status' => 1,
                'msg'    => 'Your job alert preferences have been refreshed and are active!',
                'token'  => $existing['unsubscribe_token']
            ];
        }

        $token = bin2hex(random_bytes(24));

        $alert = new CandidateJobAlert();
        $alert->user = $userId;
        $alert->email = $email;
        $alert->name = $name ?: null;
        $alert->keywords = $keywords ?: null;
        $alert->department = $dept;
        $alert->engagementbasis = $basis;
        $alert->unsubscribe_token = $token;
        $alert->is_active = 1;
        $alert->status = 1;
        $alert->save();

        // Send a welcome / confirmation in-app notification if candidate user is logged in
        if ($userId) {
            NotificationHelper::notify(
                userId: $userId,
                title: "Vacancy Alert Saved",
                message: "You are subscribed to alerts" . ($keywords ? " for '{$keywords}'" : "") . ". We will notify you the moment a matching role is published.",
                link: "/opportunities/vacancies",
                type: "success",
                icon: "bell"
            );
        }

        return [
            'status' => 1,
            'msg'    => 'Success! You will receive instant notifications when matching positions are published.',
            'token'  => $token
        ];
    }

    /**
     * Match newly published vacancy against candidate alert subscriptions and dispatch.
     *
     * @param Vacancy|int $vacancy
     * @return int Number of notifications dispatched
     */
    public static function matchAndNotify($vacancy): int
    {
        if (is_numeric($vacancy)) {
            $vacancy = Vacancy::find((int)$vacancy);
        }

        if (!$vacancy || (int)$vacancy->vacancystatus !== 2) {
            return 0; // Not published
        }

        new CandidateJobAlert();
        $db = Database::sharedPdo();

        $stmt = $db->query("
            SELECT * FROM candidate_job_alert 
            WHERE is_active = 1 AND status = 1
        ");
        $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $dispatchedCount = 0;
        $searchCorpus = mb_strtolower(
            $vacancy->title . ' ' .
            $vacancy->summary . ' ' .
            $vacancy->description . ' ' .
            $vacancy->responsibilities . ' ' .
            $vacancy->requirements
        );

        foreach ($alerts as $a) {
            // 1. Department match
            if (!empty($a['department']) && (int)$a['department'] !== (int)$vacancy->department) {
                continue;
            }

            // 2. Engagement basis match
            if (!empty($a['engagementbasis']) && (int)$a['engagementbasis'] !== (int)$vacancy->engagementbasis) {
                continue;
            }

            // 3. Keywords match (case-insensitive substring check)
            if (!empty($a['keywords'])) {
                $kw = mb_strtolower(trim($a['keywords']));
                if ($kw !== '' && strpos($searchCorpus, $kw) === false) {
                    // Also test individual tokens if multiple words provided
                    $tokens = preg_split('/\s+/', $kw);
                    $anyMatched = false;
                    foreach ($tokens as $tok) {
                        if (strlen($tok) >= 3 && strpos($searchCorpus, $tok) !== false) {
                            $anyMatched = true;
                            break;
                        }
                    }
                    if (!$anyMatched) {
                        continue;
                    }
                }
            }

            // Dispatched Match!
            $recipientEmail = $a['email'];
            $recipientName  = $a['name'] ?: 'Valued Candidate';

            // Send Email
            Mailer::sendVacancyAlertMatch($recipientEmail, $recipientName, $vacancy);

            // Send In-App Notification if user ID linked
            if (!empty($a['user'])) {
                NotificationHelper::notify(
                    userId: (int)$a['user'],
                    title: "New Opportunity: " . $vacancy->title,
                    message: "A new position matching your saved preferences has been published: " . $vacancy->title . ". Click to apply.",
                    link: "/opportunities/vacancy/" . urlencode($vacancy->slug),
                    type: "info",
                    icon: "briefcase"
                );
            }

            // Update timestamp
            $upd = $db->prepare("UPDATE candidate_job_alert SET last_matched_at = CURRENT_TIMESTAMP WHERE iD = ?");
            $upd->execute([$a['iD']]);

            $dispatchedCount++;
        }

        return $dispatchedCount;
    }

    /**
     * Unsubscribe via secure token.
     */
    public static function unsubscribe(string $token): bool
    {
        new CandidateJobAlert();
        $db = Database::sharedPdo();

        $stmt = $db->prepare("UPDATE candidate_job_alert SET is_active = 0 WHERE unsubscribe_token = ?");
        $stmt->execute([$token]);

        return $stmt->rowCount() > 0;
    }
}
