<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Mailer;
use App\Models\Servicerequest;
use App\Models\Servicerequeststatus;
use App\Models\Servicerequesttriage;
use App\Models\Servicerequeststatusevent;
use App\Models\Servicerequestattachment;
use App\Models\Servicerequestclosure;
use App\Models\Workassignment;
use App\Models\Assignmentsupervisor;
use App\Models\Workassignmentstatus;
use App\Models\Assignmentstatus;
use App\Models\Requestmessage;
use App\Models\Requestmessageattachment;
use App\Models\Messagevisibility;
use App\Models\Clientorganization;
use App\Models\Clientserviceplan;
use App\Models\Prioritylevel;
use App\Models\User;
use App\Models\Role;

class RequestController extends Controller
{
    /**
     * Admin: Service Requests Delivery Desk
     */
    public function index()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $allRequests = Servicerequest::all();
        $priorities = Prioritylevel::all();
        $clients = Clientorganization::all();
        $statusList = Servicerequeststatus::all();

        // Enriched requests with client name, status, and assignment counts
        $newQueue = [];
        $activeQueue = [];
        $resolvedQueue = [];
        $closedQueue = [];

        foreach ($allRequests as $r) {
            $rId = is_object($r) ? $r->iD : $r['iD'];
            $cId = is_object($r) ? $r->clientorganization : $r['clientorganization'];
            $pId = is_object($r) ? $r->prioritylevel : $r['prioritylevel'];

            $client = Clientorganization::find($cId);
            $priority = Prioritylevel::find($pId);
            $triage = Servicerequesttriage::where('servicerequest', $rId);
            $assignments = Workassignment::where('servicerequest', $rId);
            $closure = Servicerequestclosure::where('servicerequest', $rId);

            // Latest status event
            $statusEvents = Servicerequeststatusevent::where('servicerequest', $rId);
            $latestStatus = !empty($statusEvents) ? Servicerequeststatus::find(is_object(end($statusEvents)) ? end($statusEvents)->servicerequeststatus : end($statusEvents)['servicerequeststatus']) : null;
            $statusCode = $latestStatus ? (is_object($latestStatus) ? $latestStatus->code : $latestStatus['code']) : 'NEW';

            $item = [
                'request' => $r,
                'client' => $client,
                'priority' => $priority,
                'triage' => !empty($triage) ? $triage[0] : null,
                'assignments_count' => count($assignments),
                'status_code' => $statusCode,
                'status_name' => $latestStatus ? (is_object($latestStatus) ? $latestStatus->name : $latestStatus['name']) : 'New',
                'is_closed' => !empty($closure),
            ];

            if ($item['is_closed'] || $statusCode === 'CLOSED') {
                $closedQueue[] = $item;
            } elseif ($statusCode === 'RESOLVED' || $statusCode === 'WAITING_CLIENT') {
                $resolvedQueue[] = $item;
            } elseif ($statusCode === 'NEW' && empty($triage)) {
                $newQueue[] = $item;
            } else {
                $activeQueue[] = $item;
            }
        }

        $this->render('requests.index', [
            'newQueue' => $newQueue,
            'activeQueue' => $activeQueue,
            'resolvedQueue' => $resolvedQueue,
            'closedQueue' => $closedQueue,
            'totalRequests' => count($allRequests),
            'priorities' => $priorities,
            'clients' => $clients,
        ]);
    }

    /**
     * Admin & Client: Detailed Request Delivery Workspace
     */
    public function view($id)
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $request = Servicerequest::find($id);
        if (!$request) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests?error=not_found');
            exit;
        }

        $client = Clientorganization::find(is_object($request) ? $request->clientorganization : $request['clientorganization']);
        $plan = Clientserviceplan::find(is_object($request) ? $request->clientserviceplan : $request['clientserviceplan']);
        $priority = Prioritylevel::find(is_object($request) ? $request->prioritylevel : $request['prioritylevel']);
        $requester = User::find(is_object($request) ? $request->requester : $request['requester']);

        // Triage event
        $triageEvents = Servicerequesttriage::where('servicerequest', $id);
        $triage = !empty($triageEvents) ? $triageEvents[0] : null;

        // Status history events
        $statusEvents = Servicerequeststatusevent::where('servicerequest', $id);
        $enrichedStatusEvents = [];
        foreach ($statusEvents as $se) {
            $sId = is_object($se) ? $se->servicerequeststatus : $se['servicerequeststatus'];
            $st = Servicerequeststatus::find($sId);
            $actor = User::find(is_object($se) ? $se->reg_by : $se['reg_by']);
            $enrichedStatusEvents[] = [
                'event' => $se,
                'status' => $st,
                'actor' => $actor,
            ];
        }

        // Attachments
        $attachments = Servicerequestattachment::where('servicerequest', $id);

        // Closure record
        $closures = Servicerequestclosure::where('servicerequest', $id);
        $closure = !empty($closures) ? $closures[0] : null;

        // Talent Assignments with Supervisor pairing
        $assignments = Workassignment::where('servicerequest', $id);
        $enrichedAssignments = [];
        foreach ($assignments as $a) {
            $aId = is_object($a) ? $a->iD : $a['iD'];
            $talent = User::find(is_object($a) ? $a->user : $a['user']);
            $role = Role::find(is_object($a) ? $a->assigned_role : $a['assigned_role']);
            $supervisors = Assignmentsupervisor::where('workassignment', $aId);
            $supervisorUser = !empty($supervisors) ? User::find(is_object($supervisors[0]) ? $supervisors[0]->supervisor_user : $supervisors[0]['supervisor_user']) : null;

            $enrichedAssignments[] = [
                'assignment' => $a,
                'talent' => $talent,
                'role' => $role,
                'supervisor' => $supervisorUser,
                'supervision_notes' => !empty($supervisors) ? (is_object($supervisors[0]) ? $supervisors[0]->supervision_notes : $supervisors[0]['supervision_notes']) : null,
            ];
        }

        // Collaboration messages stream
        $rawMessages = Requestmessage::where('servicerequest', $id);
        $isStaff = Auth::isStaff();
        $enrichedMessages = [];

        foreach ($rawMessages as $m) {
            $mVis = Messagevisibility::find(is_object($m) ? $m->messagevisibility : $m['messagevisibility']);
            $visCode = $mVis ? (is_object($mVis) ? $mVis->code : $mVis['code']) : 'PUBLIC_CLIENT';

            // Non-staff only see public messages
            if (!$isStaff && $visCode === 'INTERNAL_STAFF') {
                continue;
            }

            $author = User::find(is_object($m) ? $m->reg_by : $m['reg_by']);
            $msgAttachments = Requestmessageattachment::where('requestmessage', is_object($m) ? $m->iD : $m['iD']);

            $enrichedMessages[] = [
                'message' => $m,
                'visibility' => $mVis,
                'author' => $author,
                'attachments' => $msgAttachments,
            ];
        }

        // Available talent candidates for assignment (Associates role 4, Apprentices role 5, Admins role 1)
        $associates = User::where('role', 4);
        $apprentices = User::where('role', 5);
        $staffMembers = User::where('role', 1);
        $allCandidateTalent = array_merge($associates, $apprentices, $staffMembers);

        $statuses = Servicerequeststatus::all();

        $this->render('requests.view', [
            'request' => $request,
            'client' => $client,
            'plan' => $plan,
            'priority' => $priority,
            'requester' => $requester,
            'triage' => $triage,
            'statusEvents' => $enrichedStatusEvents,
            'attachments' => $attachments,
            'closure' => $closure,
            'assignments' => $enrichedAssignments,
            'messages' => $enrichedMessages,
            'allCandidateTalent' => $allCandidateTalent,
            'statuses' => $statuses,
            'isStaff' => $isStaff,
        ]);
    }

    /**
     * Submit Service Request (Client or Admin)
     */
    public function submitRequestAction()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $clientId = (int)($_POST['client_id'] ?? 0);
        $planId = (int)($_POST['service_plan_id'] ?? 0);
        $priorityId = (int)($_POST['priority_id'] ?? 1);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $desiredDueDate = $_POST['desired_due_date'] ?? date('Y-m-d', strtotime('+3 days'));

        if (!$clientId || !$planId || !$title || !$description) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/portal?error=missing_fields');
            exit;
        }

        // Generate unique reference ticket code e.g. TRN-REQ-2026-XXXX
        $refNumber = 'TRN-REQ-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        $newReq = Servicerequest::create([
            'request_number' => $refNumber,
            'clientorganization' => $clientId,
            'clientserviceplan' => $planId,
            'requester' => is_object($user) ? $user->iD : $user['iD'],
            'prioritylevel' => $priorityId,
            'title' => $title,
            'description' => $description,
            'desired_due_date' => $desiredDueDate,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => is_object($user) ? $user->iD : $user['iD'],
        ]);

        $reqId = is_object($newReq) ? $newReq->iD : $newReq['iD'];

        // Initial status event: NEW
        $statusNew = Servicerequeststatus::where('code', 'NEW');
        $sNewId = !empty($statusNew) ? (is_object($statusNew[0]) ? $statusNew[0]->iD : $statusNew[0]['iD']) : 1;
        Servicerequeststatusevent::create([
            'servicerequest' => $reqId,
            'servicerequeststatus' => $sNewId,
            'notes' => 'Request submitted via Client Workspace',
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => is_object($user) ? $user->iD : $user['iD'],
        ]);

        // Process file attachment if present
        if (!empty($_FILES['attachment']['name']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $destDir = _BASE_PATH . '/uploads/requests/' . $reqId;
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $fileName = basename($_FILES['attachment']['name']);
            $targetPath = $destDir . '/' . time() . '_' . $fileName;
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
                $visPublic = Messagevisibility::where('code', 'PUBLIC_CLIENT');
                $visId = !empty($visPublic) ? (is_object($visPublic[0]) ? $visPublic[0]->iD : $visPublic[0]['iD']) : 1;

                Servicerequestattachment::create([
                    'servicerequest' => $reqId,
                    'file_path' => str_replace(_BASE_PATH . '/', '', $targetPath),
                    'file_name' => $fileName,
                    'file_size' => (int)$_FILES['attachment']['size'],
                    'mime_type' => $_FILES['attachment']['type'] ?? 'application/octet-stream',
                    'messagevisibility' => $visId,
                    'status' => 1,
                    'reg_date' => date('Y-m-d H:i:s'),
                    'reg_by' => is_object($user) ? $user->iD : $user['iD'],
                ]);
            }
        }

        // Send email alert to client and internal desk
        $clientObj = Clientorganization::find($clientId);
        if ($clientObj && is_object($user)) {
            Mailer::sendServiceRequestSubmitted($newReq, $clientObj, $user);
        }

        if (php_sapi_name() === 'cli') {
            return [
                'status' => 1,
                'msg' => 'Service request submitted successfully',
                'request_id' => $reqId,
                'request_number' => $refNumber,
            ];
        }

        if (Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests/view/' . $reqId . '?msg=request_submitted');
        } else {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/requests/view/' . $reqId . '?msg=request_submitted');
        }
        exit;
    }

    /**
     * Client: Approve Deliverables & Close Engagement with Star Rating
     */
    public function signoffRequestAction()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $userId = is_object($user) ? $user->iD : $user['iD'];
        $reqId = (int)($_POST['request_id'] ?? 0);
        $rating = max(1, min(5, (int)($_POST['satisfaction_rating'] ?? 5)));
        $notes = trim($_POST['closure_notes'] ?? 'Delivered deliverables accepted and approved by client.');

        if (!$reqId) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/requests?error=missing_id');
            exit;
        }

        $request = Servicerequest::find($reqId);
        if (!$request) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/requests?error=not_found');
            exit;
        }

        $clientId = is_object($request) ? $request->clientorganization : $request['clientorganization'];
        $client = Clientorganization::find($clientId);

        // Record formal closure
        $closure = Servicerequestclosure::create([
            'servicerequest' => $reqId,
            'closure_notes' => $notes,
            'satisfaction_rating' => $rating,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => $userId,
        ]);

        // Transition status to CLOSED (ID 6)
        $statusClosed = Servicerequeststatus::where('code', 'CLOSED');
        $sClosedId = !empty($statusClosed) ? (is_object($statusClosed[0]) ? $statusClosed[0]->iD : $statusClosed[0]['iD']) : 6;

        Servicerequeststatusevent::create([
            'servicerequest' => $reqId,
            'servicerequeststatus' => $sClosedId,
            'notes' => "Engagement accepted & closed by client with {$rating}-star rating: {$notes}",
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => $userId,
        ]);

        if ($client && is_object($user)) {
            Mailer::sendServiceRequestClosed($request, $client, $user, $rating, $notes);
        }

        if (php_sapi_name() === 'cli') {
            return [
                'status' => 1,
                'msg' => 'Engagement formally closed and signed off',
                'request_id' => $reqId,
                'rating' => $rating,
            ];
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/requests/view/' . $reqId . '?msg=closed');
        exit;
    }

    /**
     * Admin: Triage Ticket & Commit SLA Due Date
     */
    public function triageAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $reqId = (int)($_POST['request_id'] ?? 0);
        $slaDueDate = $_POST['sla_due_date'] ?? date('Y-m-d H:i:s', strtotime('+24 hours'));
        $notes = trim($_POST['triage_notes'] ?? 'Triaged and SLA committed by Service Manager');

        if (!$reqId) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests?error=missing_id');
            exit;
        }

        // Record triage decision event
        Servicerequesttriage::create([
            'servicerequest' => $reqId,
            'sla_due_date' => $slaDueDate,
            'triage_notes' => $notes,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => Auth::id() ?? 1,
        ]);

        // Transition status to TRIAGED
        $st = Servicerequeststatus::where('code', 'TRIAGED');
        if (!empty($st)) {
            $stId = is_object($st[0]) ? $st[0]->iD : $st[0]['iD'];
            Servicerequeststatusevent::create([
                'servicerequest' => $reqId,
                'servicerequeststatus' => $stId,
                'notes' => 'Ticket triaged with SLA commitment for ' . $slaDueDate,
                'status' => 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'reg_by' => Auth::id() ?? 1,
            ]);
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests/view/' . $reqId . '?msg=triaged');
        exit;
    }

    /**
     * Admin: Assign Talent (Associate/Apprentice) with optional Supervisor pairing
     */
    public function assignTalentAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $reqId = (int)($_POST['request_id'] ?? 0);
        $talentUserId = (int)($_POST['talent_user_id'] ?? 0);
        $hourlyRate = (float)($_POST['hourly_rate_snapshot'] ?? 35.0);
        $dueDate = $_POST['due_date'] ?? date('Y-m-d', strtotime('+5 days'));
        $supervisorUserId = (int)($_POST['supervisor_user_id'] ?? 0);
        $supervisionNotes = trim($_POST['supervision_notes'] ?? '');

        if (!$reqId || !$talentUserId) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests/view/' . $reqId . '?error=missing_talent');
            exit;
        }

        $talent = User::find($talentUserId);
        $talentRole = is_object($talent) ? (int)$talent->role : (int)$talent['role'];

        // 1. Create Workassignment record
        $assignment = Workassignment::create([
            'servicerequest' => $reqId,
            'user' => $talentUserId,
            'assigned_role' => $talentRole ?: 4,
            'rate_currency' => 'USD',
            'hourly_rate_snapshot' => $hourlyRate,
            'due_date' => $dueDate,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => Auth::id() ?? 1,
        ]);

        $assignmentId = is_object($assignment) ? $assignment->iD : $assignment['iD'];

        // 2. If supervisor selected, create Assignmentsupervisor (Zero-null event)
        if ($supervisorUserId > 0) {
            Assignmentsupervisor::create([
                'workassignment' => $assignmentId,
                'supervisor_user' => $supervisorUserId,
                'supervision_notes' => $supervisionNotes ?: 'Assigned for quality review and milestone sign-off',
                'status' => 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'reg_by' => Auth::id() ?? 1,
            ]);
        }

        // 3. Status transition to IN_PROGRESS
        $st = Servicerequeststatus::where('code', 'IN_PROGRESS');
        if (!empty($st)) {
            $stId = is_object($st[0]) ? $st[0]->iD : $st[0]['iD'];
            Servicerequeststatusevent::create([
                'servicerequest' => $reqId,
                'servicerequeststatus' => $stId,
                'notes' => 'Talent assigned: ' . (is_object($talent) ? $talent->name : $talent['name']),
                'status' => 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'reg_by' => Auth::id() ?? 1,
            ]);
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests/view/' . $reqId . '?msg=talent_assigned');
        exit;
    }

    /**
     * Update Request Status
     */
    public function updateStatusAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $reqId = (int)($_POST['request_id'] ?? 0);
        $statusId = (int)($_POST['status_id'] ?? 0);
        $notes = trim($_POST['status_notes'] ?? '');

        if (!$reqId || !$statusId) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests?error=missing_data');
            exit;
        }

        Servicerequeststatusevent::create([
            'servicerequest' => $reqId,
            'servicerequeststatus' => $statusId,
            'notes' => $notes ?: 'Status updated via Workspace',
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => Auth::id() ?? 1,
        ]);

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests/view/' . $reqId . '?msg=status_updated');
        exit;
    }

    /**
     * Post Threaded Collaboration Message (Client vs Internal Staff Visibility)
     */
    public function postMessageAction()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $reqId = (int)($_POST['request_id'] ?? 0);
        $body = trim($_POST['body'] ?? ($_POST['message'] ?? ''));
        $visibilityCode = $_POST['visibility'] ?? 'PUBLIC_CLIENT';

        // Non-staff can only post PUBLIC_CLIENT messages
        if (!Auth::isStaff()) {
            $visibilityCode = 'PUBLIC_CLIENT';
        }

        if (!$reqId || !$body) {
            $redirectUrl = Auth::isStaff() 
                ? ($GLOBALS['siteConfig']->siteUrl ?? '') . '/admin/requests/view/' . $reqId . '?error=empty_message'
                : ($GLOBALS['siteConfig']->siteUrl ?? '') . '/client/requests/view/' . $reqId . '?error=empty_message';
            if (php_sapi_name() === 'cli') {
                return ['status' => 0, 'msg' => 'Message body cannot be empty'];
            }
            header('Location: ' . $redirectUrl);
            exit;
        }

        $vis = Messagevisibility::where('code', $visibilityCode);
        $visId = !empty($vis) ? (is_object($vis[0]) ? $vis[0]->iD : $vis[0]['iD']) : 1;

        $msg = Requestmessage::create([
            'servicerequest' => $reqId,
            'messagevisibility' => $visId,
            'body' => $body,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => is_object($user) ? $user->iD : $user['iD'],
        ]);

        $msgId = is_object($msg) ? $msg->iD : $msg['iD'];

        // Process message attachment
        $fileKey = !empty($_FILES['message_attachment']['name']) ? 'message_attachment' : (!empty($_FILES['attachment']['name']) ? 'attachment' : null);
        if ($fileKey && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $destDir = _BASE_PATH . '/uploads/requests/' . $reqId . '/messages';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            $fileName = basename($_FILES[$fileKey]['name']);
            $targetPath = $destDir . '/' . time() . '_' . $fileName;
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
                Requestmessageattachment::create([
                    'requestmessage' => $msgId,
                    'file_path' => str_replace(_BASE_PATH . '/', '', $targetPath),
                    'file_name' => $fileName,
                    'file_size' => (int)$_FILES[$fileKey]['size'],
                    'mime_type' => $_FILES[$fileKey]['type'] ?? 'application/octet-stream',
                    'status' => 1,
                    'reg_date' => date('Y-m-d H:i:s'),
                    'reg_by' => is_object($user) ? $user->iD : $user['iD'],
                ]);
            }
        }

        if (php_sapi_name() === 'cli') {
            return [
                'status' => 1,
                'msg' => 'Message posted successfully',
                'message_id' => $msgId,
            ];
        }

        if (Auth::isStaff()) {
            header('Location: ' . ($GLOBALS['siteConfig']->siteUrl ?? '') . '/admin/requests/view/' . $reqId . '#messages-area');
        } else {
            header('Location: ' . ($GLOBALS['siteConfig']->siteUrl ?? '') . '/client/requests/view/' . $reqId . '?msg=message_sent#messages-area');
        }
        exit;

    }

    /**
     * Formal Closure of Service Request
     */
    public function closeRequestAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $reqId = (int)($_POST['request_id'] ?? 0);
        $notes = trim($_POST['closure_notes'] ?? 'Deliverable accepted and signed off');
        $rating = (int)($_POST['satisfaction_rating'] ?? 5);

        if (!$reqId) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests?error=missing_id');
            exit;
        }

        // Record closure event (Zero-null event)
        Servicerequestclosure::create([
            'servicerequest' => $reqId,
            'closure_notes' => $notes,
            'satisfaction_rating' => $rating,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => Auth::id() ?? 1,
        ]);

        // Status event to CLOSED
        $st = Servicerequeststatus::where('code', 'CLOSED');
        if (!empty($st)) {
            $stId = is_object($st[0]) ? $st[0]->iD : $st[0]['iD'];
            Servicerequeststatusevent::create([
                'servicerequest' => $reqId,
                'servicerequeststatus' => $stId,
                'notes' => 'Engagement closed: ' . $notes,
                'status' => 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'reg_by' => Auth::id() ?? 1,
            ]);
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/requests/view/' . $reqId . '?msg=closed');
        exit;
    }
}
