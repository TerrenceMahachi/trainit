<?php
use App\Controllers\AccountController;
use App\Controllers\UserProfilesController;
use App\Models\UserProfile; 

$uri = 'userprofiles';
$table = 'userprofile';
$options['view'] = 'userprofiles';
$options['title'] = 'UserProfile';
$options['MainController'] = new UserProfilesController();

// GET all records
$router->addRoute('GET', "/$uri"  , function () use ($router, $options) {
    $router->authMiddleware();
    $data = ['title' =>  $options['title']];
    if(isset($_COOKIE['user'])){ $data['user'] = (new AccountController())->getUser($_COOKIE['user']); }
   // $data['records'] = $options['MainController']->index();
    echo view($options['view'] . '.index', compact('data'));
    exit;
});

$router->addRoute('POST', "/get-$table-records"  , function () use ($router, $options) {
    $router->postAuthMiddleware();
    $data = $options['MainController']->index();
    $ds = $data['records'];
   
    $data['records'] = array_map(function($record) {
        return [
            'iD' => $record->iD,
            'status' => [
                'id' => $record->status()->iD ?? null,
                'name' => $record->status()->name ?? null,
            ] ,
         'name' => $record->name,
         'user' => $record->user()->name
        ];
    }, $ds);
    echo json_encode($data);
    exit;
});
// GET view record
$router->addRoute('GET', "/view-$table/:recordiD", function ($recordiD) use ($router, $options) {
    $router->authMiddleware();
    $data = ['title' =>  $options['title']];
    if(isset($_COOKIE['user'])){ $data['user'] = (new AccountController())->getUser($_COOKIE['user']); }
    $data['record'] = (new UserProfile())->where('iD', $recordiD)[0];
    
    echo view($options['view'] . '.view', compact('data'));
    exit;
});

// GET edit form
$router->addRoute('GET', "/edit-{$table}/:recordiD", function ($recordiD) use ($router, $options) {
    $router->authMiddleware();
    $data = ['title' =>  $options['title']];
    if(isset($_COOKIE['user'])){ $data['user'] = (new AccountController())->getUser($_COOKIE['user']); }
    $data['record'] = (new UserProfile())->where('iD', $recordiD)[0];
   
    echo view($options['view'] . '.edit', compact('data'));
    exit;
});


// GET create form
$router->addRoute('GET', "/new-{$table}", function () use ($router, $options) {
    $router->authMiddleware();
    $data = ['title' =>  $options['title']];
    if(isset($_COOKIE['user'])){ $data['user'] = (new AccountController())->getUser($_COOKIE['user']); }
    echo view($options['view'] . '.create', compact('data'));
    exit;
});

// POST create new record
$router->addRoute('POST', "/create-$table", function () use ($router, $options) {
    $router->postAuthMiddleware();
    $result = $options['MainController']->create();

    $data = ['status' => $result['status'], 'message' => $result['msg']];
    echo json_encode($data);
    exit;
});

// POST update record
$router->addRoute('POST', "/update-$table", function () use ($router, $options) {
    $router->postAuthMiddleware();
    $result = $options['MainController']->edit($_POST['itemiD']);
    $data = ['status' => $result['status'], 'message' => $result['msg']];
    echo json_encode($data);
    exit;
});