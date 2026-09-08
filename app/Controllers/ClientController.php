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
use App\Models\Prioritylevel;
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
     * Client Portal: Self-Service Console for Client Users (Role = 3) or Admin testing
     */
    public function portal()
    {
        $user = Auth::user();
        if (!$user) {
            header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/login');
            exit;
        }

        $userId = is_object($user) ? $user->iD : $user['iD'];
        
        // Find client membership for this user
        $memberships = Clientmembership::where('user', $userId);
        $client = null;
        if (!empty($memberships)) {
            $clientId = is_object($memberships[0]) ? $memberships[0]->clientorganization : $memberships[0]['clientorganization'];
            $client = Clientorganization::find($clientId);
        } else if (Auth::isStaff()) {
            // For staff previewing client portal, default to the first client
            $clients = Clientorganization::all();
            $client = !empty($clients) ? $clients[0] : null;
        }

        if (!$client) {
            $this->render('clients.portal_empty', [
                'user' => $user,
            ]);
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
                $manager = User::find(is_object($p) ? $p->service_manager : $p['service_manager']);
                $activePlans[] = [
                    'plan' => $p,
                    'offering' => $offering,
                    'manager' => $manager,
                ];
            }
        }

        $requests = Servicerequest::where('clientorganization', $clientId);
        $priorities = Prioritylevel::all();

        $this->render('clients.portal', [
            'user' => $user,
            'client' => $client,
            'activePlans' => $activePlans,
            'requests' => $requests,
            'priorities' => $priorities,
        ]);
    }
}
