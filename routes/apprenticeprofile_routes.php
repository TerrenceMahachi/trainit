<?php
use App\Controllers\AccountController;
use App\Controllers\ApprenticeprofilesController;
use App\Helpers\ViewAccess;
use App\Models\Apprenticeprofile;

$uri = 'apprenticeprofiles';
$table = 'apprenticeprofile';
$options['view'] = 'apprenticeprofiles';
$options['title'] = 'Apprenticeprofile';
$options['MainController'] = new ApprenticeprofilesController();

/** Fetch a record by id or render the 404 page (used by view/edit routes). */
$options['findOr404'] = function ($recordiD) use ($options) {
    $records = Apprenticeprofile::where('iD', $recordiD);
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
         'apprenticestatus' => $record->apprenticestatus()->name,
         'institution_name' => $record->institution_name,
         'degree_programme' => $record->degree_programme,
         'study_level' => $record->study_level,
         'student_reg_number' => $record->student_reg_number,
         'expected_completion_date' => $record->expected_completion_date,
         'is_wrl_attachment' => $record->is_wrl_attachment,
         'wrl_start_date' => $record->wrl_start_date,
         'wrl_end_date' => $record->wrl_end_date,
         'wrl_duration_months' => $record->wrl_duration_months,
         'wrl_coordinator_name' => $record->wrl_coordinator_name,
         'wrl_coordinator_email' => $record->wrl_coordinator_email,
         'wrl_coordinator_phone' => $record->wrl_coordinator_phone,
         'requires_placement_letter' => $record->requires_placement_letter,
         'requires_host_mou' => $record->requires_host_mou,
         'requires_logbook_visits' => $record->requires_logbook_visits,
         'requires_host_insurance' => $record->requires_host_insurance,
         'min_stipend_required' => $record->min_stipend_required,
         'engagementmodel' => $record->engagementmodel()->name,
         'worklocationpreference' => $record->worklocationpreference()->name,
         'proof_of_registration_doc' => $record->proof_of_registration_doc,
         'transcript_doc' => $record->transcript_doc,
         'current_average_grade' => $record->current_average_grade
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
