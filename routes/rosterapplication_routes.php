<?php
use App\Controllers\AccountController;
use App\Controllers\RosterapplicationsController;
use App\Helpers\ViewAccess;
use App\Models\Rosterapplication;

$uri = 'rosterapplications';
$table = 'rosterapplication';
$options['view'] = 'rosterapplications';
$options['title'] = 'Rosterapplication';
$options['MainController'] = new RosterapplicationsController();

/** Fetch a record by id or render the 404 page (used by view/edit routes). */
$options['findOr404'] = function ($recordiD) use ($options) {
    $records = Rosterapplication::where('iD', $recordiD);
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
         'user' => $record->user()->name,
         'applicationtrack' => $record->applicationtrack()->name,
         'applicationstatus' => $record->applicationstatus()->name,
         'primaryfunction' => $record->primaryfunction()->name,
         'legal_name' => $record->legal_name,
         'preferred_name' => $record->preferred_name,
         'email' => $record->email,
         'mobile_number' => $record->mobile_number,
         'whatsapp_number' => $record->whatsapp_number,
         'date_of_birth' => $record->date_of_birth,
         'gender' => $record->gender()->name,
         'city' => $record->city,
         'suburb' => $record->suburb,
         'zimprovince' => $record->zimprovince()->name,
         'country' => $record->country,
         'nationality' => $record->nationality,
         'workrightstatus' => $record->workrightstatus()->name,
         'work_permit_number' => $record->work_permit_number,
         'work_permit_expiry' => $record->work_permit_expiry,
         'has_disability_adjustment' => $record->has_disability_adjustment,
         'adjustment_details' => $record->adjustment_details,
         'how_heard' => $record->how_heard,
         'referred_by' => $record->referred_by,
         'consent_version' => $record->consent_version,
         'consent_timestamp' => $record->consent_timestamp,
         'consent_ip_address' => $record->consent_ip_address,
         'e_signature' => $record->e_signature
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
