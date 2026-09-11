<?php
namespace App\Controllers;

use App\Helpers\Auth;
use App\Models\Clientorganization;
use App\Models\Clientmembership;
use App\Models\Clientmemberrole;
use App\Models\Clientserviceplan;
use App\Models\Clientserviceplantermination;
use App\Models\Servicecategory;
use App\Models\Serviceoffering;
use App\Models\Excesspolicy;
use App\Models\Servicerequest;
use App\Models\Servicerequeststatus;
use App\Models\Servicerequeststatusevent;
use App\Models\Servicerequesttriage;
use App\Models\Servicerequestattachment;
use App\Models\Servicerequestclosure;
use App\Models\Workassignment;
use App\Models\Assignmentsupervisor;
use App\Models\Requestmessage;
use App\Models\Requestmessageattachment;
use App\Models\Messagevisibility;
use App\Models\Servicefunction;
use App\Models\Prioritylevel;
use App\Models\Role;
use App\Models\User;

class ClientController extends Controller
{
    /**
     * Admin: Client Organizations Directory & Retainers Console
     */
    public function index()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $clients = Clientorganization::all();
        $plans = Clientserviceplan::all();
        $offerings = Serviceoffering::all();
        $excessPolicies = Excesspolicy::all();
        $staffUsers = User::where('role', 1);

        // Map plans and stats per client
        $clientStats = [];
        $totalMonthlyRevenue = 0;
        foreach ($clients as $c) {
            $cId = is_object($c) ? $c->iD : $c['iD'];
            $clientPlans = Clientserviceplan::where('clientorganization', $cId);
            $clientRequests = Servicerequest::where('clientorganization', $cId);
            $members = Clientmembership::where('clientorganization', $cId);

            $monthlyTotal = 0;
            foreach ($clientPlans as $cp) {
                $term = Clientserviceplantermination::where('clientserviceplan', is_object($cp) ? $cp->iD : $cp['iD']);
                if (empty($term)) {
                    $monthlyTotal += (float)(is_object($cp) ? $cp->monthly_fee : $cp['monthly_fee']);
                }
            }
            $totalMonthlyRevenue += $monthlyTotal;

            $clientStats[$cId] = [
                'plans_count' => count($clientPlans),
                'requests_count' => count($clientRequests),
                'members_count' => count($members),
                'monthly_total' => $monthlyTotal,
            ];
        }

        $this->render('clients.index', [
            'clients' => $clients,
            'clientStats' => $clientStats,
            'totalMonthlyRevenue' => $totalMonthlyRevenue,
            'plans' => $plans,
            'offerings' => $offerings,
            'excessPolicies' => $excessPolicies,
            'staffUsers' => $staffUsers,
        ]);
    }

    /**
     * Admin: View Client 360° Profile & Service Retainers
     */
    public function view($id)
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $client = Clientorganization::find($id);
        if (!$client) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/clients?error=not_found');
            exit;
        }

        $plans = Clientserviceplan::where('clientorganization', $id);
        $enrichedPlans = [];
        foreach ($plans as $p) {
            $pId = is_object($p) ? $p->iD : $p['iD'];
            $offeringId = is_object($p) ? $p->serviceoffering : $p['serviceoffering'];
            $offering = Serviceoffering::find($offeringId);
            $managerId = is_object($p) ? $p->service_manager : $p['service_manager'];
            $manager = User::find($managerId);
            $term = Clientserviceplantermination::where('clientserviceplan', $pId);

            $enrichedPlans[] = [
                'plan' => $p,
                'offering' => $offering,
                'manager' => $manager,
                'is_terminated' => !empty($term),
                'termination' => !empty($term) ? $term[0] : null,
            ];
        }

        $members = Clientmembership::where('clientorganization', $id);
        $enrichedMembers = [];
        foreach ($members as $m) {
            $uId = is_object($m) ? $m->user : $m['user'];
            $rId = is_object($m) ? $m->clientmemberrole : $m['clientmemberrole'];
            $user = User::find($uId);
            $role = Clientmemberrole::find($rId);
            $enrichedMembers[] = [
                'membership' => $m,
                'user' => $user,
                'role' => $role,
            ];
        }

        $requests = Servicerequest::where('clientorganization', $id);
        $offerings = Serviceoffering::all();
        $excessPolicies = Excesspolicy::all();
        $staffUsers = User::where('role', 1);

        $this->render('clients.view', [
            'client' => $client,
            'plans' => $enrichedPlans,
            'members' => $enrichedMembers,
            'requests' => $requests,
            'offerings' => $offerings,
            'excessPolicies' => $excessPolicies,
            'staffUsers' => $staffUsers,
        ]);
    }

    /**
     * Admin: Create New Client Organization
     */
    public function createAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $legalName = trim($_POST['legal_name'] ?? '');
        $tradingName = trim($_POST['trading_name'] ?? $legalName);
        $regNumber = trim($_POST['registration_number'] ?? '');
        $taxNumber = trim($_POST['tax_number'] ?? '');
        $billingEmail = trim($_POST['billing_email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? 'Harare');
        $country = trim($_POST['country'] ?? 'Zimbabwe');
        $primaryPhone = trim($_POST['primary_phone'] ?? '');

        if (!$legalName || !$billingEmail) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/clients?error=missing_fields');
            exit;
        }

        $client = Clientorganization::create([
            'legal_name' => $legalName,
            'trading_name' => $tradingName,
            'registration_number' => $regNumber,
            'tax_number' => $taxNumber,
            'billing_email' => $billingEmail,
            'address' => $address,
            'city' => $city,
            'country' => $country,
            'primary_phone' => $primaryPhone,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => Auth::id() ?? 1,
        ]);

        $clientId = is_object($client) ? $client->iD : $client['iD'];
        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/clients/view/' . $clientId . '?msg=client_created');
        exit;
    }

    /**
     * Admin: Assign / Subscribe Retainer Plan
     */
    public function assignPlanAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $clientId = (int)($_POST['client_id'] ?? 0);
        $offeringId = (int)($_POST['service_offering_id'] ?? 0);
        $planName = trim($_POST['plan_name'] ?? '');
        $monthlyFee = (float)($_POST['monthly_fee'] ?? 0);
        $includedHours = (float)($_POST['included_hours'] ?? 0);
        $associateRate = (float)($_POST['associate_rate'] ?? 35.0);
        $apprenticeRate = (float)($_POST['apprentice_rate'] ?? 18.0);
        $billingCycleDay = (int)($_POST['billing_cycle_day'] ?? 1);
        $serviceManager = (int)($_POST['service_manager'] ?? (Auth::id() ?? 1));
        $billingOwner = (int)($_POST['billing_owner'] ?? (Auth::id() ?? 1));
        $excessPolicy = (int)($_POST['excess_policy_id'] ?? 1);
        $startDate = $_POST['start_date'] ?? date('Y-m-d');

        if (!$clientId || !$offeringId || !$planName) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/clients/view/' . $clientId . '?error=missing_plan_data');
            exit;
        }

        Clientserviceplan::create([
            'clientorganization' => $clientId,
            'serviceoffering' => $offeringId,
            'plan_name' => $planName,
            'currency' => 'USD',
            'monthly_fee' => $monthlyFee,
            'included_hours' => $includedHours,
            'associate_rate' => $associateRate,
            'apprentice_rate' => $apprenticeRate,
            'billing_cycle_day' => $billingCycleDay,
            'service_manager' => $serviceManager,
            'billing_owner' => $billingOwner,
            'excesspolicy' => $excessPolicy,
            'start_date' => $startDate,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => Auth::id() ?? 1,
        ]);

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/clients/view/' . $clientId . '?msg=plan_assigned');
        exit;
    }

    /**
     * Admin: Terminate / Deactivate Retainer Plan (Zero-null event)
     */
    public function terminatePlanAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $planId = (int)($_POST['plan_id'] ?? 0);
        $clientId = (int)($_POST['client_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? 'Plan expired or replaced by client request');
        $endDate = $_POST['end_date'] ?? date('Y-m-d');

        if (!$planId) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/clients?error=missing_plan_id');
            exit;
        }

        Clientserviceplantermination::create([
            'clientserviceplan' => $planId,
            'end_date' => $endDate,
            'reason' => $reason,
            'status' => 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'reg_by' => Auth::id() ?? 1,
        ]);

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/clients/view/' . $clientId . '?msg=plan_terminated');
        exit;
    }

    /**
     * Resolve the Clientorganization associated with the current user, or fallback for staff testing.
     */
    public function getClientForUser(?int $userId = null): ?Clientorganization
    {
        $uid = $userId ?: (Auth::id() ? (int)Auth::id() : 0);
        if (!$uid) {
            return null;
        }

        $memberships = Clientmembership::where('user', $uid);
        if (!empty($memberships)) {
            $cId = is_object($memberships[0]) ? $memberships[0]->clientorganization : $memberships[0]['clientorganization'];
            return Clientorganization::find((int)$cId);
        }

        // For internal staff previewing or testing client workspace, default to first client organization
        if (Auth::isStaff()) {
            $clients = Clientorganization::all();
            return !empty($clients) ? (is_object($clients[0]) ? $clients[0] : Clientorganization::find($clients[0]['iD'])) : null;
        }

        return null;
    }

    /**
     * Client Portal: Overview Dashboard
     */
    public function portal()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $client = $this->getClientForUser();
        if (!$client) {
            $this->render('clients.portal_empty', ['user' => $user]);
            return;
        }

        $clientId = is_object($client) ? $client->iD : $client['iD'];
        $plans = Clientserviceplan::where('clientorganization', $clientId);
        $activePlans = [];
        $totalIncludedHours = 0;
        $totalMonthlyFee = 0;

        foreach ($plans as $p) {
            $pId = is_object($p) ? $p->iD : $p['iD'];
            $term = Clientserviceplantermination::where('clientserviceplan', $pId);
            if (empty($term)) {
                $offering = Serviceoffering::find(is_object($p) ? $p->serviceoffering : $p['serviceoffering']);
                $manager = User::find(is_object($p) ? $p->service_manager : $p['service_manager']);
                $incHours = (float)(is_object($p) ? $p->included_hours : $p['included_hours']);
                $mFee = (float)(is_object($p) ? $p->monthly_fee : $p['monthly_fee']);
                $totalIncludedHours += $incHours;
                $totalMonthlyFee += $mFee;

                $activePlans[] = [
                    'plan' => $p,
                    'offering' => $offering,
                    'manager' => $manager,
                    'included_hours' => $incHours,
                    'monthly_fee' => $mFee,
                ];
            }
        }

        $allRequests = Servicerequest::where('clientorganization', $clientId);
        $totalRequests = count($allRequests);
        $openRequests = 0;
        $reviewRequests = 0;
        $closedRequests = 0;

        $enrichedRequests = [];
        foreach ($allRequests as $r) {
            $rId = is_object($r) ? $r->iD : $r['iD'];
            $statusEvents = Servicerequeststatusevent::where('servicerequest', $rId);
            $latestStatus = !empty($statusEvents) ? Servicerequeststatus::find(is_object(end($statusEvents)) ? end($statusEvents)->servicerequeststatus : end($statusEvents)['servicerequeststatus']) : null;
            $statusCode = $latestStatus ? (is_object($latestStatus) ? $latestStatus->code : $latestStatus['code']) : 'NEW';
            $statusName = $latestStatus ? (is_object($latestStatus) ? $latestStatus->name : $latestStatus['name']) : 'New';
            $priority = Prioritylevel::find(is_object($r) ? $r->prioritylevel : $r['prioritylevel']);
            $assignments = Workassignment::where('servicerequest', $rId);
            $closure = Servicerequestclosure::where('servicerequest', $rId);

            $isClosed = !empty($closure) || $statusCode === 'CLOSED';
            $isReview = ($statusCode === 'RESOLVED' || $statusCode === 'WAITING_CLIENT') && !$isClosed;

            if ($isClosed) {
                $closedRequests++;
            } elseif ($isReview) {
                $reviewRequests++;
            } else {
                $openRequests++;
            }

            $enrichedRequests[] = [
                'request' => $r,
                'status_code' => $statusCode,
                'status_name' => $statusName,
                'priority' => $priority,
                'assignments_count' => count($assignments),
                'is_closed' => $isClosed,
                'is_review' => $isReview,
            ];
        }

        usort($enrichedRequests, function ($a, $b) {
            $regA = is_object($a['request']) ? $a['request']->reg_date : $a['request']['reg_date'];
            $regB = is_object($b['request']) ? $b['request']->reg_date : $b['request']['reg_date'];
            return strcmp($regB, $regA);
        });

        $recentRequests = array_slice($enrichedRequests, 0, 5);

        $this->render('clients.portal', [
            'user' => $user,
            'client' => $client,
            'activePlans' => $activePlans,
            'recentRequests' => $recentRequests,
            'totalRequests' => $totalRequests,
            'openRequests' => $openRequests,
            'reviewRequests' => $reviewRequests,
            'closedRequests' => $closedRequests,
            'totalIncludedHours' => $totalIncludedHours,
            'totalMonthlyFee' => $totalMonthlyFee,
            'activeTab' => 'overview',
        ]);
    }

    /**
     * Client Portal: Dedicated Work Request / Brief Builder Form
     */
    public function newRequest()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $client = $this->getClientForUser();
        if (!$client) {
            $this->render('clients.portal_empty', ['user' => $user]);
            return;
        }

        $clientId = is_object($client) ? $client->iD : $client['iD'];
        $plans = Clientserviceplan::where('clientorganization', $clientId);
        $activePlans = [];
        foreach ($plans as $p) {
            $pId = is_object($p) ? $p->iD : $p['iD'];
            $term = Clientserviceplantermination::where('clientserviceplan', $pId);
            if (empty($term)) {
                $offering = Serviceoffering::find(is_object($p) ? $p->serviceoffering : $p['serviceoffering']);
                $activePlans[] = [
                    'plan' => $p,
                    'offering' => $offering,
                ];
            }
        }

        $priorities = Prioritylevel::all();
        $serviceCategories = Servicecategory::all();
        $serviceFunctions = Servicefunction::all();

        $this->render('clients.requests_new', [
            'user' => $user,
            'client' => $client,
            'activePlans' => $activePlans,
            'priorities' => $priorities,
            'serviceCategories' => $serviceCategories,
            'serviceFunctions' => $serviceFunctions,
            'activeTab' => 'request_new',
        ]);
    }

    /**
     * Client Portal: Work Requests Ledger with Status Filters & Search
     */
    public function requests()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $client = $this->getClientForUser();
        if (!$client) {
            $this->render('clients.portal_empty', ['user' => $user]);
            return;
        }

        $clientId = is_object($client) ? $client->iD : $client['iD'];
        $allRequests = Servicerequest::where('clientorganization', $clientId);
        $filterStatus = strtolower(trim($_GET['status'] ?? 'all'));
        $search = strtolower(trim($_GET['search'] ?? ''));

        $enrichedRequests = [];
        $counts = ['all' => 0, 'open' => 0, 'review' => 0, 'closed' => 0];

        foreach ($allRequests as $r) {
            $rId = is_object($r) ? $r->iD : $r['iD'];
            $statusEvents = Servicerequeststatusevent::where('servicerequest', $rId);
            $latestStatus = !empty($statusEvents) ? Servicerequeststatus::find(is_object(end($statusEvents)) ? end($statusEvents)->servicerequeststatus : end($statusEvents)['servicerequeststatus']) : null;
            $statusCode = $latestStatus ? (is_object($latestStatus) ? $latestStatus->code : $latestStatus['code']) : 'NEW';
            $statusName = $latestStatus ? (is_object($latestStatus) ? $latestStatus->name : $latestStatus['name']) : 'New';
            $priority = Prioritylevel::find(is_object($r) ? $r->prioritylevel : $r['prioritylevel']);
            $plan = Clientserviceplan::find(is_object($r) ? $r->clientserviceplan : $r['clientserviceplan']);
            $assignments = Workassignment::where('servicerequest', $rId);
            $closure = Servicerequestclosure::where('servicerequest', $rId);

            $isClosed = !empty($closure) || $statusCode === 'CLOSED';
            $isReview = ($statusCode === 'RESOLVED' || $statusCode === 'WAITING_CLIENT') && !$isClosed;
            $isOpen = !$isClosed && !$isReview;

            $counts['all']++;
            if ($isClosed) $counts['closed']++;
            elseif ($isReview) $counts['review']++;
            else $counts['open']++;

            if ($filterStatus === 'open' && !$isOpen) continue;
            if ($filterStatus === 'review' && !$isReview) continue;
            if ($filterStatus === 'closed' && !$isClosed) continue;

            $title = is_object($r) ? $r->title : $r['title'];
            $reqNum = is_object($r) ? $r->request_number : $r['request_number'];
            $desc = is_object($r) ? $r->description : $r['description'];
            if ($search !== '' && strpos(strtolower($title . ' ' . $reqNum . ' ' . $desc), $search) === false) {
                continue;
            }

            $enrichedRequests[] = [
                'request' => $r,
                'plan' => $plan,
                'status_code' => $statusCode,
                'status_name' => $statusName,
                'priority' => $priority,
                'assignments_count' => count($assignments),
                'is_closed' => $isClosed,
                'is_review' => $isReview,
                'is_open' => $isOpen,
            ];
        }

        usort($enrichedRequests, function ($a, $b) {
            $regA = is_object($a['request']) ? $a['request']->reg_date : $a['request']['reg_date'];
            $regB = is_object($b['request']) ? $b['request']->reg_date : $b['request']['reg_date'];
            return strcmp($regB, $regA);
        });

        $this->render('clients.requests_index', [
            'user' => $user,
            'client' => $client,
            'requests' => $enrichedRequests,
            'counts' => $counts,
            'currentFilter' => $filterStatus,
            'search' => $search,
            'activeTab' => 'requests',
        ]);
    }

    /**
     * Client Portal: Dedicated Request Workspace & Collaboration Stream
     */
    public function viewRequest(int $id)
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $client = $this->getClientForUser();
        if (!$client) {
            $this->render('clients.portal_empty', ['user' => $user]);
            return;
        }

        $request = Servicerequest::find($id);
        if (!$request) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/requests?error=not_found');
            exit;
        }

        $reqClientId = (int)(is_object($request) ? $request->clientorganization : $request['clientorganization']);
        $clientId = (int)(is_object($client) ? $client->iD : $client['iD']);

        if ($reqClientId !== $clientId && !Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/requests?error=unauthorized');
            exit;
        }

        $plan = Clientserviceplan::find(is_object($request) ? $request->clientserviceplan : $request['clientserviceplan']);
        $priority = Prioritylevel::find(is_object($request) ? $request->prioritylevel : $request['prioritylevel']);
        $requester = User::find(is_object($request) ? $request->requester : $request['requester']);

        $triageEvents = Servicerequesttriage::where('servicerequest', $id);
        $triage = !empty($triageEvents) ? $triageEvents[0] : null;

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

        $latestStatus = !empty($statusEvents) ? Servicerequeststatus::find(is_object(end($statusEvents)) ? end($statusEvents)->servicerequeststatus : end($statusEvents)['servicerequeststatus']) : null;
        $statusCode = $latestStatus ? (is_object($latestStatus) ? $latestStatus->code : $latestStatus['code']) : 'NEW';
        $statusName = $latestStatus ? (is_object($latestStatus) ? $latestStatus->name : $latestStatus['name']) : 'New';

        $milestoneStep = 1;
        if ($statusCode === 'CLOSED') $milestoneStep = 5;
        elseif ($statusCode === 'RESOLVED' || $statusCode === 'WAITING_CLIENT') $milestoneStep = 4;
        elseif ($statusCode === 'IN_PROGRESS') $milestoneStep = 3;
        elseif ($statusCode === 'TRIAGED') $milestoneStep = 2;

        $attachments = Servicerequestattachment::where('servicerequest', $id);
        $closures = Servicerequestclosure::where('servicerequest', $id);
        $closure = !empty($closures) ? $closures[0] : null;

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

        // Strictly public client messages only
        $rawMessages = Requestmessage::where('servicerequest', $id);
        $enrichedMessages = [];
        foreach ($rawMessages as $m) {
            $mVis = Messagevisibility::find(is_object($m) ? $m->messagevisibility : $m['messagevisibility']);
            $visCode = $mVis ? (is_object($mVis) ? $mVis->code : $mVis['code']) : 'PUBLIC_CLIENT';
            if ($visCode === 'INTERNAL_STAFF' && !Auth::isStaff()) {
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

        $this->render('clients.request_view', [
            'user' => $user,
            'client' => $client,
            'request' => $request,
            'plan' => $plan,
            'priority' => $priority,
            'requester' => $requester,
            'triage' => $triage,
            'statusEvents' => $enrichedStatusEvents,
            'latestStatusCode' => $statusCode,
            'latestStatusName' => $statusName,
            'milestoneStep' => $milestoneStep,
            'attachments' => $attachments,
            'closure' => $closure,
            'assignments' => $enrichedAssignments,
            'messages' => $enrichedMessages,
            'activeTab' => 'requests',
        ]);
    }

    /**
     * Client Portal: Retainer Subscriptions & Hours Meter
     */
    public function plans()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $client = $this->getClientForUser();
        if (!$client) {
            $this->render('clients.portal_empty', ['user' => $user]);
            return;
        }

        $clientId = is_object($client) ? $client->iD : $client['iD'];
        $plans = Clientserviceplan::where('clientorganization', $clientId);
        $enrichedPlans = [];
        foreach ($plans as $p) {
            $pId = is_object($p) ? $p->iD : $p['iD'];
            $term = Clientserviceplantermination::where('clientserviceplan', $pId);
            $offering = Serviceoffering::find(is_object($p) ? $p->serviceoffering : $p['serviceoffering']);
            $manager = User::find(is_object($p) ? $p->service_manager : $p['service_manager']);
            $billingOwner = User::find(is_object($p) ? $p->billing_owner : $p['billing_owner']);
            $excess = Excesspolicy::find(is_object($p) ? $p->excesspolicy : $p['excesspolicy']);

            $enrichedPlans[] = [
                'plan' => $p,
                'offering' => $offering,
                'manager' => $manager,
                'billing_owner' => $billingOwner,
                'excess_policy' => $excess,
                'is_active' => empty($term),
                'termination' => !empty($term) ? $term[0] : null,
            ];
        }

        $this->render('clients.plans', [
            'user' => $user,
            'client' => $client,
            'plans' => $enrichedPlans,
            'activeTab' => 'plans',
        ]);
    }

    /**
     * Client Portal: Organization Team & Authorized Requesters
     */
    public function team()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $client = $this->getClientForUser();
        if (!$client) {
            $this->render('clients.portal_empty', ['user' => $user]);
            return;
        }

        $clientId = is_object($client) ? $client->iD : $client['iD'];
        $members = Clientmembership::where('clientorganization', $clientId);
        $enrichedMembers = [];
        foreach ($members as $m) {
            $u = User::find(is_object($m) ? $m->user : $m['user']);
            $r = Clientmemberrole::find(is_object($m) ? $m->clientmemberrole : $m['clientmemberrole']);
            $enrichedMembers[] = [
                'membership' => $m,
                'user' => $u,
                'role' => $r,
            ];
        }

        $roles = Clientmemberrole::all();

        $this->render('clients.team', [
            'user' => $user,
            'client' => $client,
            'members' => $enrichedMembers,
            'roles' => $roles,
            'activeTab' => 'team',
        ]);
    }
}
