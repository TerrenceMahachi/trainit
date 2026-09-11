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
use App\Models\Clientinvoice;
use App\Models\Clientinvoiceitem;
use App\Models\Clientinvoicepayment;

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

    /**
     * Client Portal: Monthly Invoices & Billing Statements Ledger
     */
    public function invoices()
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
        $rawInvoices = Clientinvoice::findByQuery(
            "SELECT * FROM clientinvoice WHERE clientorganization = ? AND status = 1 ORDER BY billing_period_start DESC",
            [$clientId]
        );

        $enrichedInvoices = [];
        $totalBilled = 0;
        $totalPaid = 0;
        $totalPending = 0;

        foreach ($rawInvoices as $inv) {
            $plan = $inv->clientserviceplan ? Clientserviceplan::find($inv->clientserviceplan) : null;
            $items = Clientinvoiceitem::where('clientinvoice', $inv->iD);
            $payments = Clientinvoicepayment::where('clientinvoice', $inv->iD);

            $amt = (float)$inv->total_amount;
            $totalBilled += $amt;
            if ((int)$inv->payment_status === 2) {
                $totalPaid += $amt;
            } else {
                $totalPending += $amt;
            }

            $enrichedInvoices[] = [
                'iD' => $inv->iD,
                'invoice_number' => $inv->invoice_number,
                'billing_period_start' => $inv->billing_period_start,
                'billing_period_end' => $inv->billing_period_end,
                'currency' => $inv->currency,
                'subtotal' => $inv->subtotal,
                'vat_rate' => $inv->vat_rate,
                'vat_amount' => $inv->vat_amount,
                'total_amount' => $inv->total_amount,
                'payment_status' => $inv->payment_status,
                'issue_date' => $inv->issue_date,
                'due_date' => $inv->due_date,
                'paid_date' => $inv->paid_date,
                'plan_name' => $plan ? $plan->plan_name : 'Enterprise Retainer',
                'items_count' => count($items),
                'payments_count' => count($payments),
            ];
        }

        $this->render('clients.invoices', [
            'user' => $user,
            'client' => $client,
            'invoices' => $enrichedInvoices,
            'stats' => [
                'total_billed' => $totalBilled,
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending,
                'invoices_count' => count($enrichedInvoices),
            ],
            'activeTab' => 'invoices',
        ]);
    }

    /**
     * Client Portal: View Individual Detailed Tax Invoice Statement
     */
    public function viewInvoice(int $id)
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

        $invoice = Clientinvoice::find($id);
        if (!$invoice) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices?error=not_found');
            exit;
        }

        $reqClientId = (int)$invoice->clientorganization;
        $clientId = (int)(is_object($client) ? $client->iD : $client['iD']);

        if ($reqClientId !== $clientId && !Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices?error=unauthorized');
            exit;
        }

        $items = Clientinvoiceitem::where('clientinvoice', $id);
        $payments = Clientinvoicepayment::where('clientinvoice', $id);
        $plan = $invoice->clientserviceplan ? Clientserviceplan::find($invoice->clientserviceplan) : null;

        $this->render('clients.invoice_view', [
            'user' => $user,
            'client' => $client,
            'invoice' => $invoice,
            'items' => $items,
            'payments' => $payments,
            'plan' => $plan,
        ]);
    }

    /**
     * Download or stream Tax Invoice as PDF
     */
    public function downloadInvoicePdf(int $id)
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $invoice = Clientinvoice::find($id);
        if (!$invoice) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices?error=not_found');
            exit;
        }

        $client = Clientorganization::find((int)$invoice->clientorganization);
        $userClient = $this->getClientForUser();
        $userClientId = $userClient ? (int)(is_object($userClient) ? $userClient->iD : $userClient['iD']) : 0;

        if ($userClientId !== (int)$invoice->clientorganization && !Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices?error=unauthorized');
            exit;
        }

        $items = Clientinvoiceitem::where('clientinvoice', $id);
        $payments = Clientinvoicepayment::where('clientinvoice', $id);
        $plan = $invoice->clientserviceplan ? Clientserviceplan::find($invoice->clientserviceplan) : null;

        $html = $this->buildInvoicePrintHtml($invoice, $client, $items, $payments, $plan);

        // Check for Edge or Chrome executable
        $possibleBins = [
            '/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge',
            '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
            '/usr/bin/google-chrome',
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium'
        ];

        $edgeBin = null;
        foreach ($possibleBins as $bin) {
            if (file_exists($bin)) {
                $edgeBin = $bin;
                break;
            }
        }

        $cleanInvNum = preg_replace('/[^A-Za-z0-9_-]/', '', $invoice->invoice_number);
        $filename = 'TSG-INV-' . $cleanInvNum . '.pdf';

        if ($edgeBin && (!isset($_GET['format']) || $_GET['format'] !== 'html')) {
            $tmpHtml = sys_get_temp_dir() . '/inv_' . $id . '_' . time() . '.html';
            $tmpPdf = sys_get_temp_dir() . '/inv_' . $id . '_' . time() . '.pdf';
            file_put_contents($tmpHtml, $html);

            $cmd = escapeshellarg($edgeBin) . ' --headless --disable-gpu --user-data-dir=' . escapeshellarg(sys_get_temp_dir() . '/edge_tmp_' . $id) . ' --no-sandbox --no-pdf-header-footer --print-to-pdf=' . escapeshellarg($tmpPdf) . ' ' . escapeshellarg('file://' . $tmpHtml);
            
            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w']
            ];
            $process = @proc_open($cmd, $descriptors, $pipes);
            if (is_resource($process)) {
                @fclose($pipes[0]);
                $start = microtime(true);
                while (microtime(true) - $start < 3.0) {
                    if (file_exists($tmpPdf) && filesize($tmpPdf) > 1000) {
                        break;
                    }
                    usleep(100000);
                }
                @fclose($pipes[1]);
                @fclose($pipes[2]);
                @proc_terminate($process);
                @proc_close($process);
            }

            if (file_exists($tmpPdf) && filesize($tmpPdf) > 1000) {
                @unlink($tmpHtml);
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . $filename . '"');
                header('Content-Length: ' . filesize($tmpPdf));
                readfile($tmpPdf);
                @unlink($tmpPdf);
                exit;
            }
            @unlink($tmpHtml);
            if (file_exists($tmpPdf)) @unlink($tmpPdf);
        }

        // Fallback: output clean print-ready HTML with auto-print
        header('Content-Type: text/html; charset=UTF-8');
        echo $html;
        exit;
    }

    /**
     * Build standalone, print-optimized HTML for Tax Invoice
     */
    protected function buildInvoicePrintHtml($invoice, $client, $items, $payments, $plan): string
    {
        $invNumber = htmlspecialchars($invoice->invoice_number);
        $clientName = htmlspecialchars($client->legal_name ?? 'Client Organization');
        $regNumber = htmlspecialchars($client->registration_number ?? 'N/A');
        $taxNumber = htmlspecialchars($client->tax_number ?? 'N/A');
        $address = htmlspecialchars($client->address ?? '');
        $city = htmlspecialchars($client->city ?? 'Harare');
        $country = htmlspecialchars($client->country ?? 'Zimbabwe');
        $billingEmail = htmlspecialchars($client->billing_email ?? 'billing@client.co.zw');
        $planName = htmlspecialchars($plan->plan_name ?? 'Enterprise Service Retainer');
        $periodStart = date('d M Y', strtotime($invoice->billing_period_start));
        $periodEnd = date('d M Y', strtotime($invoice->billing_period_end));
        $issueDate = date('d M Y', strtotime($invoice->issue_date));
        $dueDate = date('d M Y', strtotime($invoice->due_date));
        $isPaid = (int)$invoice->payment_status === 2;
        $subtotal = number_format((float)$invoice->subtotal, 2);
        $vatRate = number_format((float)$invoice->vat_rate, 1);
        $vatAmount = number_format((float)$invoice->vat_amount, 2);
        $totalAmount = number_format((float)$invoice->total_amount, 2);

        $itemsRows = '';
        $idx = 1;
        foreach ($items as $it) {
            $desc = htmlspecialchars($it->description);
            $type = htmlspecialchars($it->item_type);
            $qty = number_format((float)$it->quantity, 1);
            $unit = number_format((float)$it->unit_price, 2);
            $tot = number_format((float)$it->total_price, 2);
            $itemsRows .= "
            <tr>
                <td style='text-align: center; color: #64748b;'>{$idx}</td>
                <td>
                    <div style='font-weight: 700; color: #0f172a;'>{$desc}</div>
                    <div style='font-size: 7pt; color: #64748b;'>Type: {$type}</div>
                </td>
                <td style='text-align: center; font-weight: 600;'>{$qty}</td>
                <td style='text-align: right;'>\${$unit}</td>
                <td style='text-align: right; font-weight: 700; color: #0f172a;'>\${$tot}</td>
            </tr>";
            $idx++;
        }

        $paymentsHtml = '';
        if (!empty($payments)) {
            $paymentsHtml .= "<div style='background: #dcfce7; border: 1px solid #86efac; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px;'>
                <div style='color: #15803d; font-weight: 700; font-size: 8.5pt; margin-bottom: 4px;'>✓ Electronic Payment Settlement Confirmation</div>";
            foreach ($payments as $p) {
                $pMethod = htmlspecialchars($p->payment_method);
                $pRef = htmlspecialchars($p->transaction_reference);
                $pAmt = number_format((float)$p->amount, 2);
                $pDate = date('d M Y, H:i', strtotime($p->paid_at));
                $paymentsHtml .= "<div style='font-size: 7.8pt; color: #166534;'>
                    Settled via: <strong>{$pMethod}</strong> &bull; Ref: <code style='background: #bbf7d0; padding: 1px 4px; border-radius: 3px;'>{$pRef}</code> &bull; Amount: <strong>\${$pAmt}</strong> &bull; Date: {$pDate}
                </div>";
            }
            $paymentsHtml .= "</div>";
        }

        $statusBadge = $isPaid 
            ? "<span style='background: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 3px 10px; border-radius: 12px; font-weight: 700; font-size: 8pt;'>PAID &amp; SETTLED</span>"
            : "<span style='background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 3px 10px; border-radius: 12px; font-weight: 700; font-size: 8pt;'>PAYMENT DUE</span>";

        return "<!DOCTYPE html>
<html lang='en'>
<head>
<meta charset='UTF-8'>
<title>Tax Invoice - {$invNumber}</title>
<style>
  @page {
    size: A4 portrait;
    margin: 12mm 15mm 15mm 15mm;
    @bottom-center {
      content: 'Tsigiro Tax Invoice {$invNumber} • Official ZIMRA VAT Statement';
      font-size: 7pt;
      color: #94a3b8;
      font-family: -apple-system, BlinkMacSystemFont, sans-serif;
    }
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    color: #1e293b;
    background: #ffffff;
    font-size: 8.5pt;
    line-height: 1.45;
  }
  .header-table { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #0f2b23; padding-bottom: 14px; }
  .logo-box { width: 42px; height: 42px; background: #090b0b; border-radius: 10px; display: inline-block; vertical-align: middle; margin-right: 10px; text-align: center; }
  .grid-2 { display: table; width: 100%; margin-bottom: 18px; }
  .col-2 { display: table-cell; width: 50%; vertical-align: top; }
  .card-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; margin-right: 6px; }
  .card-box-right { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; margin-left: 6px; }
  table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
  table.items thead th { background: #0f2b23; color: #ffffff; font-weight: 600; text-align: left; padding: 6px 8px; font-size: 7.8pt; }
  table.items td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; font-size: 8pt; vertical-align: middle; }
  table.items tfoot th { padding: 6px 8px; border-top: 1px solid #e2e8f0; font-size: 8pt; background: #ffffff; }
  table.items tfoot tr.total-row th { background: #0f2b23 !important; color: #ffffff !important; }
  .bank-box { border-top: 1px solid #e2e8f0; padding-top: 12px; font-size: 7.5pt; color: #64748b; }
  @media print {
    .no-print { display: none !important; }
    body { padding: 0 !important; }
  }
</style>
</head>
<body style='padding: 20px; max-width: 820px; margin: 0 auto;'>
  <div class='no-print' style='background: #0f2b23; color: #ffffff; padding: 10px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.12);'>
    <div style='display: flex; align-items: center; gap: 8px;'>
      <span style='font-size: 13px; font-weight: 700; color: #32c99a;'>TSIGIRO OFFICIAL INVOICE</span>
      <span style='font-size: 11px; color: #94a3b8;'>| {$invNumber}</span>
    </div>
    <div style='display: flex; gap: 8px;'>
      <button onclick='window.print()' style='background: #32c99a; color: #090b0b; border: none; padding: 6px 14px; border-radius: 6px; font-weight: 700; font-size: 11px; cursor: pointer;'>🖨️ Print / Save as PDF</button>
      <button onclick='window.close()' style='background: rgba(255,255,255,0.12); color: #ffffff; border: 1px solid rgba(255,255,255,0.25); padding: 6px 12px; border-radius: 6px; font-size: 11px; cursor: pointer;'>✕ Close</button>
    </div>
  </div>

  <table class='header-table'>
    <tr>
      <td style='vertical-align: middle;'>
        <div style='display: flex; align-items: center;'>
          <svg style='width: 44px; height: 44px; vertical-align: middle; margin-right: 10px;' viewBox='0 0 64 64' fill='none' xmlns='http://www.w3.org/2000/svg'>
            <rect width='64' height='64' rx='14' fill='#090b0b'/>
            <path d='M15 22h34v9H37v21H27V31H15z' fill='#ffffff'/>
            <rect x='37' y='10' width='10' height='10' rx='4' fill='#32c99a' transform='rotate(8 42 15)'/>
          </svg>
          <div style='display: inline-block; vertical-align: middle;'>
            <div style='font-size: 15pt; font-weight: 800; color: #090b0b; letter-spacing: -0.5px;'>TSIGIRO SERVICES</div>
            <div style='font-size: 7.5pt; text-transform: uppercase; color: #059669; font-weight: 700; letter-spacing: 1px;'>Verified Talent &amp; Managed Services</div>
          </div>
        </div>
        <div style='font-size: 7.5pt; color: #64748b; margin-top: 6px; line-height: 1.35;'>
          <strong>Tsigiro Operations (Pvt) Ltd</strong> &bull; Harare Technology Park, Borrowdale, Harare<br>
          ZIMRA Tax BP: <strong>BP20088921</strong> &bull; VAT Registration: <strong>10049281</strong> &bull; billing@tsigiro.co.zw
        </div>
      </td>
      <td style='text-align: right; vertical-align: top;'>
        <div style='font-size: 14pt; font-weight: 800; color: #0f2b23;'>TAX INVOICE</div>
        <div style='font-size: 9.5pt; font-weight: 700; font-family: monospace; color: #0f172a; margin: 2px 0 6px 0;'>{$invNumber}</div>
        <div style='margin-bottom: 6px;'>{$statusBadge}</div>
        <div style='font-size: 7.5pt; color: #64748b;'>
          Issue Date: <strong>{$issueDate}</strong><br>
          Due Date: <strong>{$dueDate}</strong>
        </div>
      </td>
    </tr>
  </table>

  <div class='grid-2'>
    <div class='col-2'>
      <div class='card-box'>
        <div style='font-size: 6.8pt; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.8px; margin-bottom: 2px;'>Invoiced To (Corporate Client):</div>
        <div style='font-size: 9.5pt; font-weight: 800; color: #0f2b23; margin-bottom: 2px;'>{$clientName}</div>
        <div style='font-size: 7.5pt; color: #475569; line-height: 1.35;'>
          {$address}<br>
          {$city}, {$country}<br>
          Company Reg: <strong>{$regNumber}</strong> &bull; ZIMRA BP: <strong>{$taxNumber}</strong><br>
          Billing Contact: {$billingEmail}
        </div>
      </div>
    </div>
    <div class='col-2'>
      <div class='card-box-right'>
        <div style='font-size: 6.8pt; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.8px; margin-bottom: 2px;'>Service Retainer Coverage:</div>
        <div style='font-size: 9.5pt; font-weight: 800; color: #0369a1; margin-bottom: 2px;'>{$planName}</div>
        <div style='font-size: 7.5pt; color: #475569; line-height: 1.35;'>
          Billing Period: <strong>{$periodStart} &ndash; {$periodEnd}</strong><br>
          Currency: <strong>USD (Nostro Electronic Settlement)</strong><br>
          Statutory Framework: Zimbabwe VAT Act [Chapter 23:12]
        </div>
      </div>
    </div>
  </div>

  <table class='items'>
    <thead>
      <tr>
        <th style='width: 5%; text-align: center;'>#</th>
        <th style='width: 55%;'>Description / Service Deliverable</th>
        <th style='width: 12%; text-align: center;'>Units / Hrs</th>
        <th style='width: 14%; text-align: right;'>Rate (USD)</th>
        <th style='width: 14%; text-align: right;'>Total (USD)</th>
      </tr>
    </thead>
    <tbody>
      {$itemsRows}
    </tbody>
    <tfoot>
      <tr>
        <th colspan='4' style='text-align: right; color: #475569; font-weight: 600;'>Net Subtotal:</th>
        <th style='text-align: right; font-weight: 700; color: #0f172a;'>\${$subtotal}</th>
      </tr>
      <tr>
        <th colspan='4' style='text-align: right; color: #475569; font-weight: 600;'>ZIMRA VAT ({$vatRate}%):</th>
        <th style='text-align: right; font-weight: 700; color: #0f172a;'>\${$vatAmount}</th>
      </tr>
      <tr class='total-row'>
        <th colspan='4' style='text-align: right; font-size: 9.5pt; font-weight: 800;'>Total Payable (USD):</th>
        <th style='text-align: right; font-size: 9.5pt; font-weight: 800;'>\${$totalAmount}</th>
      </tr>
    </tfoot>
  </table>

  {$paymentsHtml}

  <div class='bank-box'>
    <table style='width: 100%; border: none;'>
      <tr>
        <td style='width: 50%; vertical-align: top; border: none; font-size: 7.2pt;'>
          <strong style='color: #0f2b23; text-transform: uppercase;'>Settlement Account (Stanbic Bank Zimbabwe):</strong><br>
          Account Name: Tsigiro Operations (Pvt) Ltd<br>
          Account No (Nostro USD): <strong>9140003882910</strong> &bull; Swift: <strong>SBICZWHX</strong> &bull; Borrowdale Branch
        </td>
        <td style='width: 50%; text-align: right; vertical-align: top; border: none; font-size: 7.2pt;'>
          <strong>Official Tax Invoice Verification:</strong><br>
          Issued in terms of Zimbabwe Value Added Tax Act [Chapter 23:12].<br>
          Verification Portal: <a href='https://portal.tsigiro.co.zw' style='color: #0369a1;'>portal.tsigiro.co.zw</a>
        </td>
      </tr>
    </table>
  </div>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        window.print();
      }, 500);
    });
  </script>
</body>
</html>";
    }

    /**
     * Client: Submit Proof of Payment (POP) for Invoice
     */
    public function submitPaymentAction()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
        $invoice = Clientinvoice::find($invoiceId);
        if (!$invoice) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices?error=not_found');
            exit;
        }

        $userClient = $this->getClientForUser();
        $userClientId = $userClient ? (int)(is_object($userClient) ? $userClient->iD : $userClient['iD']) : 0;

        if ($userClientId !== (int)$invoice->clientorganization && !Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices?error=unauthorized');
            exit;
        }

        $paymentMethod = trim($_POST['payment_method'] ?? 'Bank Transfer');
        $transactionReference = trim($_POST['transaction_reference'] ?? '');
        $amount = (float)($_POST['amount'] ?? $invoice->total_amount);
        $paidAt = !empty($_POST['paid_at']) ? $_POST['paid_at'] : date('Y-m-d H:i:s');
        $notes = trim($_POST['notes'] ?? '');

        if (!$transactionReference) {
            $transactionReference = 'TXN-POP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        }

        // Handle uploaded POP receipt file
        if (!empty($_FILES['receipt_file']['name']) && $_FILES['receipt_file']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['receipt_file']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                $dir = _BASE_PATH . '/uploads/receipts';
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
                $destName = 'pop_' . $invoiceId . '_' . time() . '.' . $ext;
                $destPath = $dir . '/' . $destName;
                if (move_uploaded_file($_FILES['receipt_file']['tmp_name'], $destPath)) {
                    $pubDir = _BASE_PATH . '/public/uploads/receipts';
                    if (!is_dir($pubDir)) @mkdir($pubDir, 0755, true);
                    @copy($destPath, $pubDir . '/' . $destName);
                    $notes .= ($notes ? ' | ' : '') . 'Receipt: uploads/receipts/' . $destName;
                }
            }
        }

        // Insert payment record
        Clientinvoicepayment::create([
            'clientinvoice' => $invoiceId,
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transactionReference,
            'amount' => $amount,
            'paid_at' => date('Y-m-d H:i:s', strtotime($paidAt)),
            'notes' => $notes ?: 'Submitted via Client Portal POP form',
            'reg_by' => Auth::id() ?? 1,
            'reg_date' => date('Y-m-d H:i:s'),
            'status' => 1,
        ]);

        // Update invoice payment status to settled (2)
        $invoice->payment_status = 2;
        $invoice->paid_date = date('Y-m-d', strtotime($paidAt));
        $invoice->update();

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices/view/' . $invoiceId . '?msg=payment_recorded');
        exit;
    }

    /**
     * Admin/Finance: Reconcile or Reopen Invoice Settlement
     */
    public function reconcilePaymentAction()
    {
        if (!Auth::isStaff()) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
        $invoice = Clientinvoice::find($invoiceId);
        if (!$invoice) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/admin/invoices?error=not_found');
            exit;
        }

        $action = $_POST['action'] ?? 'settle';

        if ($action === 'settle') {
            $paymentMethod = trim($_POST['payment_method'] ?? 'Stanbic Nostro Bank Transfer');
            $ref = trim($_POST['transaction_reference'] ?? ('TXN-REC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4))));
            $amount = (float)($_POST['amount'] ?? $invoice->total_amount);
            $paidAt = !empty($_POST['paid_at']) ? $_POST['paid_at'] : date('Y-m-d H:i:s');
            $notes = trim($_POST['notes'] ?? 'Reconciled and certified by Billing Desk');

            Clientinvoicepayment::create([
                'clientinvoice' => $invoiceId,
                'payment_method' => $paymentMethod,
                'transaction_reference' => $ref,
                'amount' => $amount,
                'paid_at' => date('Y-m-d H:i:s', strtotime($paidAt)),
                'notes' => $notes,
                'reg_by' => Auth::id() ?? 1,
                'reg_date' => date('Y-m-d H:i:s'),
                'status' => 1,
            ]);

            $invoice->payment_status = 2;
            $invoice->paid_date = date('Y-m-d', strtotime($paidAt));
            $invoice->update();

            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices/view/' . $invoiceId . '?msg=invoice_settled');
            exit;
        } elseif ($action === 'reopen') {
            $invoice->payment_status = 1; // payment due
            $invoice->paid_date = null;
            $invoice->update();

            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices/view/' . $invoiceId . '?msg=invoice_reopened');
            exit;
        }

        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/invoices/view/' . $invoiceId);
        exit;
    }
}
