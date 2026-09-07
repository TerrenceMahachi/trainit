<?php
use App\Controllers\AccountController;
use App\Controllers\RosteronboardingsController;
use App\Helpers\ViewAccess;
use App\Models\Rosteronboarding;

$uri = 'rosteronboardings';
$table = 'rosteronboarding';
$options['view'] = 'rosteronboardings';
$options['title'] = 'Rosteronboarding';
$options['MainController'] = new RosteronboardingsController();

/** Fetch a record by id or render the 404 page (used by view/edit routes). */
$options['findOr404'] = function ($recordiD) use ($options) {
    $records = Rosteronboarding::where('iD', $recordiD);
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
         'national_id_number' => $record->national_id_number,
         'national_id_doc' => $record->national_id_doc,
         'passport_number' => $record->passport_number,
         'passport_expiry' => $record->passport_expiry,
         'street_address' => $record->street_address,
         'city' => $record->city,
         'country' => $record->country,
         'bank_name' => $record->bank_name,
         'bank_branch' => $record->bank_branch,
         'account_name' => $record->account_name,
         'account_number' => $record->account_number,
         'bank_currency' => $record->bank_currency,
         'emergency_contact_name' => $record->emergency_contact_name,
         'emergency_contact_phone' => $record->emergency_contact_phone,
         'emergency_contact_relationship' => $record->emergency_contact_relationship,
         'nssa_number' => $record->nssa_number,
         'police_clearance_doc' => $record->police_clearance_doc,
         'police_clearance_date' => $record->police_clearance_date,
         'signed_contract_doc' => $record->signed_contract_doc,
         'signed_nda_doc' => $record->signed_nda_doc,
         'odoo_applicant_id' => $record->odoo_applicant_id,
         'odoo_employee_id' => $record->odoo_employee_id,
         'synced_to_odoo_at' => $record->synced_to_odoo_at
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
