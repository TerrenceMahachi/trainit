<?php
use App\Controllers\AccountController;
use App\Controllers\RosterassessmentsController;
use App\Helpers\ViewAccess;
use App\Models\Rosterassessment;

$uri = 'rosterassessments';
$table = 'rosterassessment';
$options['view'] = 'rosterassessments';
$options['title'] = 'Rosterassessment';
$options['MainController'] = new RosterassessmentsController();

/** Fetch a record by id or render the 404 page (used by view/edit routes). */
$options['findOr404'] = function ($recordiD) use ($options) {
    $records = Rosterassessment::where('iD', $recordiD);
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
         'reviewer' => $record->reviewer()->name,
         'vettingrecommendation' => $record->vettingrecommendation()->name,
         'eligibility_gate_passed' => $record->eligibility_gate_passed,
         'technical_fit_score' => $record->technical_fit_score,
         'evidence_score' => $record->evidence_score,
         'judgement_score' => $record->judgement_score,
         'availability_score' => $record->availability_score,
         'motivation_score' => $record->motivation_score,
         'total_score' => $record->total_score,
         'automated_red_flags' => $record->automated_red_flags,
         'interview_notes' => $record->interview_notes,
         'technical_test_result' => $record->technical_test_result,
         'vetted_at' => $record->vetted_at
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
