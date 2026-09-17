<?php

/**
 * Mobile admin console for the talent roster.
 *
 * Gives reviewers on the phone the same decision surface the web vetting
 * console has: the submitted-application queue, the full candidate dossier,
 * and the approve/decline action — which routes through AccountElevation so a
 * decision made here grants (or declines) the applicant's account exactly the
 * way the web console does.
 */

use App\Helpers\AccountElevation;
use App\Helpers\NotificationHelper;
use App\Models\Database;

global $router;

if (!function_exists('sendMobileJson')) {
    function sendMobileJson($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

/**
 * Resolve the acting reviewer.
 *
 * Uses the same identity resolution as the rest of the mobile API
 * (getMobileUser), then enforces a reviewer role. This matches the existing
 * /api/mobile/admin/review-profile endpoint exactly.
 *
 * SECURITY (known, tracked): getMobileUser() accepts a user_id parameter when
 * no session is present, so these endpoints are only as strong as the rest of
 * the mobile API — which is to say, not strong. It cannot simply be made
 * session-only here: the WebView loads from a file:// origin, so the Lax
 * session cookie is never sent on its cross-site fetches, which is why the app
 * passes user_id at all. The real fix is token auth (Bearer at login) applied
 * across the whole mobile API; until then these endpoints must not be treated
 * as a trust boundary.
 */
if (!function_exists('getMobileReviewer')) {
    function getMobileReviewer(): ?array
    {
        $user = getMobileUser();
        if (!$user) {
            return null;
        }

        // 1 admin, 6 service manager, 8 vetting officer
        if (!in_array((int) $user->role, [1, 6, 8], true)) {
            return null;
        }

        return [
            'iD'    => (int) $user->iD,
            'name'  => $user->name ?? '',
            'email' => $user->email ?? '',
            'role'  => (int) $user->role,
        ];
    }
}

if (!function_exists('rosterApplicationNumber')) {
    function rosterApplicationNumber($track, $regDate, $appId): string
    {
        $prefix = ((int) $track === 2) ? 'TSG-ASC' : 'TSG-APP';
        $year = !empty($regDate) ? date('Y', strtotime($regDate)) : date('Y');
        return $prefix . '-' . $year . '-' . str_pad((string) $appId, 4, '0', STR_PAD_LEFT);
    }
}

// --------------------------------------------------------------------------
// 1. Review queue — submitted applications awaiting a decision
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/admin/roster-applications', function () {
    $reviewer = getMobileReviewer();
    if (!$reviewer) {
        sendMobileJson(['status' => 0, 'message' => 'Forbidden. Reviewer privileges required.'], 403);
    }

    $db = (new Database())->getPDO();

    $where = ['ra.status = 1'];
    $params = [];

    $statusFilter = trim($_GET['status'] ?? '');
    if ($statusFilter !== '' && $statusFilter !== 'all') {
        if ($statusFilter === 'queue') {
            // Anything still awaiting a decision.
            $where[] = 'ra.applicationstatus IN (2, 3, 4)';
        } else {
            $where[] = 'ra.applicationstatus = ?';
            $params[] = (int) $statusFilter;
        }
    }

    if (!empty($_GET['track'])) {
        $where[] = 'ra.applicationtrack = ?';
        $params[] = (int) $_GET['track'];
    }

    $q = trim($_GET['q'] ?? '');
    if ($q !== '') {
        $where[] = '(ra.legal_name LIKE ? OR ra.email LIKE ? OR u.name LIKE ?)';
        $like = '%' . $q . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $limit = max(1, min(100, (int) ($_GET['limit'] ?? 50)));

    $sql = "
        SELECT ra.iD, ra.user, ra.applicationtrack, ra.applicationstatus, ra.legal_name,
               ra.preferred_name, ra.email, ra.mobile_number, ra.city, ra.reg_date,
               at.name AS track_name,
               ast.code AS status_code, ast.name AS status_name,
               sf.name AS function_name,
               u.name AS account_name, u.email AS account_email,
               asm.total_score,
               (SELECT COUNT(*) FROM rosterdocument rd WHERE rd.rosterapplication = ra.iD) AS document_count,
               (SELECT ps.code FROM userprofile up
                  JOIN profilestatus ps ON up.profilestatus = ps.iD
                 WHERE up.user = ra.user
                   AND up.profiletype = CASE WHEN ra.applicationtrack = 2 THEN 3 ELSE 2 END
                 LIMIT 1) AS account_status
          FROM rosterapplication ra
          LEFT JOIN applicationtrack at ON ra.applicationtrack = at.iD
          LEFT JOIN applicationstatus ast ON ra.applicationstatus = ast.iD
          LEFT JOIN servicefunction sf ON ra.primaryfunction = sf.iD
          LEFT JOIN user u ON ra.user = u.iD
          LEFT JOIN rosterassessment asm ON asm.rosterapplication = ra.iD
         WHERE " . implode(' AND ', $where) . "
         ORDER BY ra.iD DESC
         LIMIT {$limit}
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$row) {
        $row['application_number'] = rosterApplicationNumber(
            $row['applicationtrack'],
            $row['reg_date'],
            $row['iD']
        );
    }
    unset($row);

    // Queue counters for the console header.
    $counts = [
        'submitted'   => 0,
        'shortlisted' => 0,
        'interview'   => 0,
        'on_roster'   => 0,
        'rejected'    => 0,
    ];
    $cStmt = $db->query("
        SELECT applicationstatus, COUNT(*) AS n
          FROM rosterapplication
         WHERE status = 1
         GROUP BY applicationstatus
    ");
    foreach ($cStmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
        switch ((int) $c['applicationstatus']) {
            case 2: $counts['submitted']   = (int) $c['n']; break;
            case 3: $counts['shortlisted'] = (int) $c['n']; break;
            case 4: $counts['interview']   = (int) $c['n']; break;
            case 5: $counts['on_roster']   = (int) $c['n']; break;
            case 8: $counts['rejected']    = (int) $c['n']; break;
        }
    }
    $counts['queue'] = $counts['submitted'] + $counts['shortlisted'] + $counts['interview'];

    sendMobileJson([
        'status'       => 1,
        'counts'       => $counts,
        'applications' => $rows,
    ]);
});

// --------------------------------------------------------------------------
// 2. Full candidate dossier
// --------------------------------------------------------------------------
$router->addRoute('GET', '/api/mobile/admin/roster-application/:id', function ($id) {
    $reviewer = getMobileReviewer();
    if (!$reviewer) {
        sendMobileJson(['status' => 0, 'message' => 'Forbidden. Reviewer privileges required.'], 403);
    }

    $db = (new Database())->getPDO();
    $id = (int) $id;

    $stmt = $db->prepare("
        SELECT ra.*,
               at.code AS track_code, at.name AS track_name,
               ast.code AS status_code, ast.name AS status_name, ast.description AS status_description,
               sf.name AS function_name,
               zp.name AS province_name,
               u.name AS account_name, u.email AS account_email, u.role AS account_role
          FROM rosterapplication ra
          LEFT JOIN applicationtrack at ON ra.applicationtrack = at.iD
          LEFT JOIN applicationstatus ast ON ra.applicationstatus = ast.iD
          LEFT JOIN servicefunction sf ON ra.primaryfunction = sf.iD
          LEFT JOIN zimprovince zp ON ra.zimprovince = zp.iD
          LEFT JOIN user u ON ra.user = u.iD
         WHERE ra.iD = ?
    ");
    $stmt->execute([$id]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$app) {
        sendMobileJson(['status' => 0, 'message' => 'Application not found.'], 404);
    }

    $app['application_number'] = rosterApplicationNumber($app['applicationtrack'], $app['reg_date'], $id);

    $dStmt = $db->prepare("
        SELECT rd.iD AS id, dt.code AS doc_type_code, dt.name AS doc_type_name,
               rd.original_name, rd.file_size_kb, rd.reg_date
          FROM rosterdocument rd
          JOIN documenttype dt ON rd.documenttype = dt.iD
         WHERE rd.rosterapplication = ?
         ORDER BY rd.iD ASC
    ");
    $dStmt->execute([$id]);
    $documents = $dStmt->fetchAll(PDO::FETCH_ASSOC);

    $qStmt = $db->prepare("
        SELECT rq.title, rq.institution_name, rq.field_of_study, rq.date_obtained,
               qt.name AS type_name
          FROM rosterqualification rq
          LEFT JOIN qualificationtype qt ON rq.qualificationtype = qt.iD
         WHERE rq.rosterapplication = ?
         ORDER BY rq.date_obtained DESC
    ");
    $qStmt->execute([$id]);
    $qualifications = $qStmt->fetchAll(PDO::FETCH_ASSOC);

    $sStmt = $db->prepare("
        SELECT si.name AS skill_name, pl.level_number, pl.name AS proficiency_name
          FROM rosterskill rs
          JOIN skillitem si ON rs.skillitem = si.iD
          JOIN proficiencylevel pl ON rs.proficiencylevel = pl.iD
         WHERE rs.rosterapplication = ?
         ORDER BY pl.level_number DESC, si.name ASC
    ");
    $sStmt->execute([$id]);
    $skills = $sStmt->fetchAll(PDO::FETCH_ASSOC);

    $whStmt = $db->prepare("
        SELECT organization_name, position_title, start_date, end_date, is_current, key_deliverables
          FROM rosterworkhistory
         WHERE rosterapplication = ?
         ORDER BY start_date DESC
    ");
    $whStmt->execute([$id]);
    $workHistory = $whStmt->fetchAll(PDO::FETCH_ASSOC);

    $rStmt = $db->prepare("
        SELECT referee_name, organization, position, relationship, email, phone
          FROM rosterreferee
         WHERE rosterapplication = ?
    ");
    $rStmt->execute([$id]);
    $referees = $rStmt->fetchAll(PDO::FETCH_ASSOC);

    $judgement = null;
    try {
        $jStmt = $db->prepare("SELECT * FROM rosterjudgementresponse WHERE rosterapplication = ? LIMIT 1");
        $jStmt->execute([$id]);
        $judgement = $jStmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (\Throwable $e) {
        $judgement = null;
    }

    $assessment = null;
    try {
        $aStmt = $db->prepare("SELECT * FROM rosterassessment WHERE rosterapplication = ? LIMIT 1");
        $aStmt->execute([$id]);
        $assessment = $aStmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if ($assessment && !empty($assessment['automated_red_flags'])) {
            $decoded = json_decode($assessment['automated_red_flags'], true);
            $assessment['red_flags'] = is_array($decoded) ? $decoded : [];
        }
    } catch (\Throwable $e) {
        $assessment = null;
    }

    $eStmt = $db->prepare("
        SELECT rse.applicationstatus, rse.remarks, rse.reg_date,
               ast.name AS status_name, u.name AS actor_name
          FROM rosterstatusevent rse
          LEFT JOIN applicationstatus ast ON rse.applicationstatus = ast.iD
          LEFT JOIN user u ON rse.reg_by = u.iD
         WHERE rse.rosterapplication = ?
         ORDER BY rse.iD DESC
    ");
    $eStmt->execute([$id]);
    $timeline = $eStmt->fetchAll(PDO::FETCH_ASSOC);

    // The account request this application should resolve.
    $trackType = AccountElevation::trackTypeFor($app['applicationtrack']);
    $account = null;
    if ($trackType) {
        $pStmt = $db->prepare("
            SELECT up.iD AS profile_id, up.display_title, up.request_notes, up.reviewer_notes,
                   up.reviewed_at, ps.code AS status_code, ps.name AS status_name,
                   pt.code AS type_code
              FROM userprofile up
              JOIN profilestatus ps ON up.profilestatus = ps.iD
              JOIN profiletype pt ON up.profiletype = pt.iD
             WHERE up.user = ? AND up.profiletype = ?
             LIMIT 1
        ");
        $pStmt->execute([(int) $app['user'], $trackType]);
        $account = $pStmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    sendMobileJson([
        'status'         => 1,
        'application'    => $app,
        'documents'      => $documents,
        'qualifications' => $qualifications,
        'skills'         => $skills,
        'work_history'   => $workHistory,
        'referees'       => $referees,
        'judgement'      => $judgement,
        'assessment'     => $assessment,
        'timeline'       => $timeline,
        'account'        => $account,
    ]);
});

// --------------------------------------------------------------------------
// 3. Record a decision (and grant/decline the account with it)
// --------------------------------------------------------------------------
$router->addRoute('POST', '/api/mobile/admin/roster-review', function () {
    $reviewer = getMobileReviewer();
    if (!$reviewer) {
        sendMobileJson(['status' => 0, 'message' => 'Forbidden. Reviewer privileges required.'], 403);
    }

    $db = (new Database())->getPDO();
    $reviewerId = (int) $reviewer['iD'];

    $appId = (int) ($_POST['application_id'] ?? 0);
    $action = strtolower(trim($_POST['action'] ?? ''));
    $notes = trim($_POST['notes'] ?? '');

    if ($appId <= 0) {
        sendMobileJson(['status' => 0, 'message' => 'Application ID is required.'], 400);
    }

    $statusForAction = [
        'shortlist' => 3,
        'interview' => 4,
        'approve'   => 5,
        'reject'    => 8,
    ];

    if (!isset($statusForAction[$action])) {
        sendMobileJson([
            'status'  => 0,
            'message' => 'Invalid action. Use shortlist, interview, approve or reject.',
        ], 400);
    }

    $newStatus = $statusForAction[$action];

    $stmt = $db->prepare("SELECT * FROM rosterapplication WHERE iD = ?");
    $stmt->execute([$appId]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$app) {
        sendMobileJson(['status' => 0, 'message' => 'Application not found.'], 404);
    }

    $applicantId = (int) $app['user'];
    $oldStatus = (int) $app['applicationstatus'];

    if ($oldStatus === 1) {
        sendMobileJson([
            'status'  => 0,
            'message' => 'This application is still a draft and has not been submitted yet.',
        ], 400);
    }

    // Optional scoring, so a reviewer can score and decide in one action.
    $scoreFields = [
        'technical_fit_score',
        'evidence_score',
        'judgement_score',
        'availability_score',
        'motivation_score',
    ];
    $hasScores = false;
    foreach ($scoreFields as $f) {
        if (isset($_POST[$f]) && $_POST[$f] !== '') {
            $hasScores = true;
            break;
        }
    }

    if ($hasScores || $notes !== '') {
        try {
            $total = 0.0;
            $values = [];
            foreach ($scoreFields as $f) {
                $values[$f] = (float) ($_POST[$f] ?? 0);
                $total += $values[$f];
            }

            $exists = $db->prepare("SELECT iD FROM rosterassessment WHERE rosterapplication = ? LIMIT 1");
            $exists->execute([$appId]);
            $row = $exists->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $db->prepare("
                    UPDATE rosterassessment
                       SET reviewer = ?, technical_fit_score = ?, evidence_score = ?, judgement_score = ?,
                           availability_score = ?, motivation_score = ?, total_score = ?,
                           interview_notes = ?, vetted_at = CURRENT_TIMESTAMP
                     WHERE iD = ?
                ")->execute([
                    $reviewerId,
                    $values['technical_fit_score'],
                    $values['evidence_score'],
                    $values['judgement_score'],
                    $values['availability_score'],
                    $values['motivation_score'],
                    $total,
                    $notes,
                    (int) $row['iD'],
                ]);
            } elseif ($hasScores) {
                $db->prepare("
                    INSERT INTO rosterassessment
                        (rosterapplication, reviewer, technical_fit_score, evidence_score, judgement_score,
                         availability_score, motivation_score, total_score, interview_notes,
                         vetted_at, reg_by, reg_date, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?, CURRENT_TIMESTAMP, 1)
                ")->execute([
                    $appId,
                    $reviewerId,
                    $values['technical_fit_score'],
                    $values['evidence_score'],
                    $values['judgement_score'],
                    $values['availability_score'],
                    $values['motivation_score'],
                    $total,
                    $notes,
                    $reviewerId,
                ]);
            }
        } catch (\Throwable $e) {
            error_log('roster-review: assessment save failed: ' . $e->getMessage());
        }
    }

    // Status transition + audit trail
    $statusName = 'Status #' . $newStatus;
    try {
        $sn = $db->prepare("SELECT name FROM applicationstatus WHERE iD = ?");
        $sn->execute([$newStatus]);
        $found = $sn->fetchColumn();
        if ($found) {
            $statusName = $found;
        }
    } catch (\Throwable $e) {
        // fall back to the generic label
    }

    if ($newStatus !== $oldStatus) {
        $db->prepare("UPDATE rosterapplication SET applicationstatus = ? WHERE iD = ?")
            ->execute([$newStatus, $appId]);

        $remark = $notes !== ''
            ? "Status updated to {$statusName} via mobile review console. {$notes}"
            : "Status updated to {$statusName} via mobile review console.";

        $db->prepare("
            INSERT INTO rosterstatusevent
                (rosterapplication, applicationstatus, remarks, reg_by, reg_date, status)
             VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, 1)
        ")->execute([$appId, $newStatus, $remark, $reviewerId]);
    }

    // The decision carries through to the account.
    $accountChanged = AccountElevation::applyApplicationStatus(
        $applicantId,
        $app['applicationtrack'],
        $newStatus,
        $reviewerId,
        $notes
    );

    // Interim stages still tell the candidate what happened (grant/decline
    // notifications are raised inside AccountElevation).
    if (in_array($newStatus, [3, 4], true)) {
        NotificationHelper::notify(
            $applicantId,
            $newStatus === 3 ? 'You have been shortlisted' : 'Interview / verification stage',
            $newStatus === 3
                ? 'Your roster application passed initial screening and has been shortlisted for verification.'
                : 'Your application has moved to the interview and verification stage.',
            'roster/status',
            'info',
            'fa-clipboard-check'
        );
    }

    sendMobileJson([
        'status'          => 1,
        'message'         => 'Decision recorded: ' . $statusName . '.',
        'application_id'  => $appId,
        'new_status'      => $newStatus,
        'new_status_name' => $statusName,
        'account_changed' => $accountChanged,
    ]);
});
