<?php

use App\Helpers\Auth;
use App\Helpers\Csrf;
use App\Helpers\Mailer;
use App\Helpers\NotificationHelper;
use App\Models\ClientOnboardingRequest;
use App\Models\Clientorganization;
use App\Models\Clientmembership;
use App\Models\Clientserviceplan;
use App\Models\Serviceoffering;
use App\Models\Sectortype;
use App\Models\Engagementmodel;
use App\Models\User;
use App\Models\Login;

global $router, $siteConfig;

// Public: Client Self-Onboarding Intake Form
$router->addRoute('GET', '/clients/onboard', function () use ($router, $siteConfig) {
    $offerings = Serviceoffering::all();
    $sectors = Sectortype::all();
    $models = Engagementmodel::all();

    $data = [
        'title' => 'Client Self-Onboarding & Partner Intake',
        'offerings' => $offerings,
        'sectors' => $sectors,
        'models' => $models,
        'user' => Auth::user()
    ];

    echo view('clients.onboard_request', compact('data'));
    exit;
});

// Public: Process Client Onboarding Form Submission
$router->addRoute('POST', '/clients/onboard', function () use ($router, $siteConfig) {
    header('Content-Type: application/json');

    $companyName = trim($_POST['company_name'] ?? '');
    $contactName = trim($_POST['contact_name'] ?? '');
    $contactEmail = strtolower(trim($_POST['contact_email'] ?? ''));
    $contactPhone = trim($_POST['contact_phone'] ?? '');

    if (empty($companyName) || empty($contactName) || empty($contactEmail) || empty($contactPhone)) {
        echo json_encode(['status' => 0, 'msg' => 'Please fill in all required company and primary representative contact fields.']);
        exit;
    }

    if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 0, 'msg' => 'Please provide a valid email address.']);
        exit;
    }

    $req = new ClientOnboardingRequest();
    $req->company_name = $companyName;
    $req->trading_name = trim($_POST['trading_name'] ?? '');
    $req->registration_number = trim($_POST['registration_number'] ?? '');
    $req->tax_number = trim($_POST['tax_number'] ?? '');
    $req->sectortype = !empty($_POST['sectortype']) ? (int)$_POST['sectortype'] : null;
    $req->website = trim($_POST['website'] ?? '');
    $req->street_address = trim($_POST['street_address'] ?? '');
    $req->city = trim($_POST['city'] ?? '');
    $req->country = trim($_POST['country'] ?? 'Zimbabwe');
    $req->contact_name = $contactName;
    $req->contact_email = $contactEmail;
    $req->contact_phone = $contactPhone;
    $req->contact_title = trim($_POST['contact_title'] ?? '');
    $req->serviceoffering = !empty($_POST['serviceoffering']) ? (int)$_POST['serviceoffering'] : null;
    $req->engagementmodel = !empty($_POST['engagementmodel']) ? (int)$_POST['engagementmodel'] : null;
    $req->estimated_monthly_hours = !empty($_POST['estimated_monthly_hours']) ? (int)$_POST['estimated_monthly_hours'] : 40;
    $req->currency_preference = trim($_POST['currency_preference'] ?? 'USD');
    $req->notes = trim($_POST['notes'] ?? '');
    $req->status = 'pending';
    $req->reg_by = Auth::id() ?? 1;
    $req->save();

    // 1. Dispatch confirmation email to client contact
    Mailer::sendClientOnboardingSubmitted($req);

    // 2. Dispatch in-app notification to all platform administrators
    NotificationHelper::notifyAdmins(
        'New Client Onboarding Request',
        "{$req->company_name} ({$req->contact_name}) requested corporate partner onboarding.",
        '/admin/clients/onboarding',
        'warning',
        'fa-building'
    );

    echo json_encode([
        'status' => 1,
        'msg' => 'Your client onboarding application has been successfully submitted. Our team will review your organization details and email login credentials upon approval.',
        'request_id' => (int)$req->iD
    ]);
    exit;
});

// Admin: Review Queue of Client Onboarding Requests
$router->addRoute('GET', '/admin/clients/onboarding', function () use ($router, $siteConfig) {
    if (!Auth::check() || !in_array((int)Auth::role(), [1, 6, 7], true)) {
        header("Location: " . $siteConfig->siteUrl . "/login");
        exit;
    }

    $currentUser = Auth::user();
    $filter = $_GET['filter'] ?? 'pending';

    $where = "status = ?";
    $params = [$filter];
    if ($filter === 'all') {
        $where = "1 = 1";
        $params = [];
    }

    $requests = ClientOnboardingRequest::findByQuery(
        "SELECT * FROM client_onboarding_request WHERE {$where} ORDER BY iD DESC",
        $params
    );

    $pendingCount = count(ClientOnboardingRequest::where('status', 'pending'));
    $approvedCount = count(ClientOnboardingRequest::where('status', 'approved'));
    $rejectedCount = count(ClientOnboardingRequest::where('status', 'rejected'));

    $data = [
        'title' => 'Client Onboarding Requests Queue',
        'requests' => $requests,
        'currentFilter' => $filter,
        'pendingCount' => $pendingCount,
        'approvedCount' => $approvedCount,
        'rejectedCount' => $rejectedCount,
        'user' => $currentUser
    ];

    echo view('clients.onboarding_queue', compact('data'));
    exit;
});

// Admin: Approve Client Onboarding & Auto-Provision Account
$router->addRoute('POST', '/admin/clients/onboarding/:id/approve', function ($id) use ($router, $siteConfig) {
    if (!Auth::check() || (int)Auth::role() !== 1) {
        http_response_code(403);
        echo json_encode(['status' => 0, 'msg' => 'Forbidden: Administrator privileges required.']);
        exit;
    }

    $req = ClientOnboardingRequest::find((int)$id);
    if (!$req) {
        echo json_encode(['status' => 0, 'msg' => 'Onboarding request not found.']);
        exit;
    }

    if ($req->status === 'approved') {
        echo json_encode(['status' => 0, 'msg' => 'This request has already been approved and provisioned.']);
        exit;
    }

    $adminUser = Auth::user();

    // 1. Create or Find Client Organization
    $org = new Clientorganization();
    $org->legal_name = $req->company_name;
    $org->trading_name = $req->trading_name ?: $req->company_name;
    $org->registration_number = $req->registration_number ?: 'TRN-CORP-' . date('Ymd') . '-' . $req->iD;
    $org->tax_number = $req->tax_number ?: 'BP-' . rand(10000000, 99999999);
    $org->country = $req->country ?: 'Zimbabwe';
    $org->city = $req->city ?: 'Harare';
    $org->address = $req->street_address ?: 'CBD';
    $org->billing_email = $req->contact_email;
    $org->primary_phone = $req->contact_phone;
    $org->reg_by = $adminUser->iD;
    $org->status = 1;
    $org->save();

    // 2. Create User Account for Contact Representative (Role 3 = Client User)
    $existingUser = User::where('email', $req->contact_email);
    $tempPass = 'Tsigiro' . date('Y') . '!' . rand(100, 999);

    if (!empty($existingUser)) {
        $clientUser = $existingUser[0];
        // Upgrade user role to client user if general user
        if ((int)$clientUser->role === 2) {
            $clientUser->role = 3;
            $clientUser->save();
        }
    } else {
        $clientUser = new User();
        $clientUser->name = $req->contact_name;
        $clientUser->email = $req->contact_email;
        $clientUser->role = 3; // Client User
        $clientUser->reg_by = $adminUser->iD;
        $clientUser->status = 1;
        $clientUser->save();

        $login = new Login();
        $login->user = $clientUser->iD;
        $login->password = password_hash($tempPass, PASSWORD_BCRYPT);
        $login->failed_login = 0;
        $login->status = 1;
        $login->reg_by = $adminUser->iD;
        $login->save();
    }

    // 3. Create Client Membership (Role 1 = Primary Admin / Managing Partner)
    $membership = new Clientmembership();
    $membership->clientorganization = $org->iD;
    $membership->user = $clientUser->iD;
    $membership->clientmemberrole = 1; // Primary Admin
    $membership->reg_by = $adminUser->iD;
    $membership->status = 1;
    $membership->save();

    // 4. Optionally Initialize Retainer Service Plan
    if (!empty($req->serviceoffering)) {
        $offering = Serviceoffering::find($req->serviceoffering);
        if ($offering) {
            $plan = new Clientserviceplan();
            $plan->clientorganization = $org->iD;
            $plan->serviceoffering = $offering->iD;
            $plan->plan_name = ($offering->name ?? 'Retainer') . ' Plan';
            $plan->currency = $req->currency_preference ?: 'USD';
            $plan->monthly_fee = 1500.00;
            $plan->included_hours = (float)($req->estimated_monthly_hours ?: 40);
            $plan->associate_rate = 45.00;
            $plan->apprentice_rate = 25.00;
            $plan->billing_cycle_day = 1;
            $plan->service_manager = $adminUser->iD;
            $plan->billing_owner = $adminUser->iD;
            $plan->excesspolicy = 1; // Bill at Standard Excess Rate
            $plan->start_date = date('Y-m-d');
            $plan->reg_by = $adminUser->iD;
            $plan->status = 1;
            $plan->save();
        }
    }

    // 5. Update Request Status & Reference
    $req->status = 'approved';
    $req->reviewed_by = $adminUser->iD;
    $req->reviewed_at = date('Y-m-d H:i:s');
    $req->review_notes = trim($_POST['review_notes'] ?? 'Approved and provisioned.');
    $req->provisioned_clientorganization = $org->iD;
    $req->provisioned_user = $clientUser->iD;
    $req->save();

    // 6. Send Approval Credentials Email
    Mailer::sendClientOnboardingApproved($req, $clientUser, $tempPass);

    // 7. Dispatch In-App Notification to Newly Provisioned Client User
    NotificationHelper::notify(
        (int)$clientUser->iD,
        'Welcome to Tsigiro Client Portal',
        "Your organization {$req->company_name} is now active. You can now submit service requests and view retainers.",
        '/client/portal',
        'success',
        'fa-building'
    );

    echo json_encode([
        'status' => 1,
        'msg' => "Client organization '{$org->name}' provisioned successfully! User {$clientUser->email} account activated.",
        'org_id' => (int)$org->iD,
        'user_id' => (int)$clientUser->iD
    ]);
    exit;
});

// Admin: Reject Client Onboarding Request
$router->addRoute('POST', '/admin/clients/onboarding/:id/reject', function ($id) use ($router, $siteConfig) {
    if (!Auth::check() || (int)Auth::role() !== 1) {
        http_response_code(403);
        echo json_encode(['status' => 0, 'msg' => 'Forbidden: Administrator privileges required.']);
        exit;
    }

    $req = ClientOnboardingRequest::find((int)$id);
    if (!$req) {
        echo json_encode(['status' => 0, 'msg' => 'Onboarding request not found.']);
        exit;
    }

    $adminUser = Auth::user();
    $reason = trim($_POST['review_notes'] ?? 'Organization details could not be verified.');

    $req->status = 'rejected';
    $req->reviewed_by = $adminUser->iD;
    $req->reviewed_at = date('Y-m-d H:i:s');
    $req->review_notes = $reason;
    $req->save();

    Mailer::sendClientOnboardingRejected($req, $reason);

    echo json_encode([
        'status' => 1,
        'msg' => 'Client onboarding request marked as rejected and notification email sent.'
    ]);
    exit;
});
