<?php
use App\Controllers\AccountController;
use App\Controllers\AssociateprofilesController;
use App\Helpers\ViewAccess;
use App\Models\Associateprofile;

$uri = 'associateprofiles';
$table = 'associateprofile';
$options['view'] = 'associateprofiles';
$options['title'] = 'Associateprofile';
$options['MainController'] = new AssociateprofilesController();

/** Fetch a record by id or render the 404 page (used by view/edit routes). */
$options['findOr404'] = function ($recordiD) use ($options) {
    $records = Associateprofile::where('iD', $recordiD);
    if (count($records) === 0) {
        http_response_code(404);
        $data = ['title' => 'Not Found', 'status' => 'failed', 'message' => 'Record not found'];
        echo view('errors.404', compact('data'));
        exit;
    }
    return $records[0];
};

// GET all records
$router->addRoute('GET', "/$uri", function () use ($router, $options) {
    $router->authMiddleware();
    ViewAccess::enforceForView($options['view']);
    $data = ['title' => $options['title']];
    $data['user'] = (new AccountController())->getUser(\App\Helpers\Auth::id());
    echo view($options['view'] . '.index', compact('data'));
    exit;
});

$router->addRoute('POST', "/get-$table-records", function () use ($router, $options) {
    $router->postAuthMiddleware();
    ViewAccess::enforceForView($options['view']);
    $data = $options['MainController']->index();
    $ds = $data['records'];

    $data['records'] = array_map(function ($record) {
        return [
            'iD' => $record->iD,
            'status' => [
                'id' => $record->status()->iD ?? null,
                'name' => $record->status()->name ?? null,
            ] ,
         'rosterapplication' => $record->rosterapplication()->name,
         'employmentstatus' => $record->employmentstatus()->name,
         'years_experience' => $record->years_experience,
         'donor_experience_years' => $record->donor_experience_years,
         'donors_worked_with' => $record->donors_worked_with,
         'largest_budget_handled' => $record->largest_budget_handled,
         'largest_team_supervised' => $record->largest_team_supervised,
         'largest_endpoints_supported' => $record->largest_endpoints_supported,
         'largest_dataset_managed' => $record->largest_dataset_managed,
         'supervised_juniors_before' => $record->supervised_juniors_before,
         'led_audits_or_evaluations' => $record->led_audits_or_evaluations,
         'rejected_work_experience' => $record->rejected_work_experience,
         'day_rate_expectation' => $record->day_rate_expectation,
         'capacity_days_per_month' => $record->capacity_days_per_month,
         'notice_period' => $record->notice_period,
         'invoiceentitytype' => $record->invoiceentitytype()->name,
         'has_tax_clearance_itf263' => $record->has_tax_clearance_itf263,
         'zimra_bp_number' => $record->zimra_bp_number,
         'tax_clearance_doc' => $record->tax_clearance_doc,
         'is_vat_registered' => $record->is_vat_registered,
         'vat_number' => $record->vat_number,
         'has_indemnity_insurance' => $record->has_indemnity_insurance,
         'insurance_cover_amount' => $record->insurance_cover_amount,
         'conflict_of_interest' => $record->conflict_of_interest,
         'moonlighting_restrictions' => $record->moonlighting_restrictions,
         'cv_bid_consent' => $record->cv_bid_consent,
         'restricted_sectors_or_donors' => $record->restricted_sectors_or_donors,
         'public_website_listing_consent' => $record->public_website_listing_consent
        ];
    }, $ds);
    echo json_encode($data);
    exit;
});

// GET view record
$router->addRoute('GET', "/view-$table/:recordiD", function ($recordiD) use ($router, $options) {
    $router->authMiddleware();
    ViewAccess::enforceForView($options['view']);
    $data = ['title' => $options['title']];
    $data['user'] = (new AccountController())->getUser(\App\Helpers\Auth::id());
    $data['record'] = $options['findOr404']($recordiD);

    echo view($options['view'] . '.view', compact('data'));
    exit;
});

// GET edit form
$router->addRoute('GET', "/edit-{$table}/:recordiD", function ($recordiD) use ($router, $options) {
    $router->authMiddleware();
    ViewAccess::enforceForView($options['view']);
    $data = ['title' => $options['title']];
    $data['user'] = (new AccountController())->getUser(\App\Helpers\Auth::id());
    $data['record'] = $options['findOr404']($recordiD);

    echo view($options['view'] . '.edit', compact('data'));
    exit;
});

// GET create form
$router->addRoute('GET', "/new-{$table}", function () use ($router, $options) {
    $router->authMiddleware();
    ViewAccess::enforceForView($options['view']);
    $data = ['title' => $options['title']];
    $data['user'] = (new AccountController())->getUser(\App\Helpers\Auth::id());
    echo view($options['view'] . '.create', compact('data'));
    exit;
});

// POST create new record
$router->addRoute('POST', "/create-$table", function () use ($router, $options) {
    $router->postAuthMiddleware();
    ViewAccess::enforceForView($options['view']);
    $result = $options['MainController']->create();

    $data = ['status' => $result['status'], 'message' => $result['msg']];
    echo json_encode($data);
    exit;
});

// POST update record
$router->addRoute('POST', "/update-$table", function () use ($router, $options) {
    $router->postAuthMiddleware();
    ViewAccess::enforceForView($options['view']);
    $result = $options['MainController']->edit($_POST['itemiD']);
    $data = ['status' => $result['status'], 'message' => $result['msg']];
    echo json_encode($data);
    exit;
});
