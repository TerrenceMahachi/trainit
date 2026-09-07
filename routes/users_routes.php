<?php


use App\Controllers\AccountController;
use App\Controllers\UserController;
use App\Models\Role;
use App\Models\User;
use function App\Helpers\generate_string;

$router->addRoute('GET', '/user-accounts', function () {
    if (isset($_COOKIE['user'])) {
        $us = (new AccountController())->getUser($_COOKIE['user']);
        $data = ['title' => 'Accounts', 'role' => $us->role, 'name' => $us->name, 'role_name' => $us->role()->name];
        echo view('admin.user-accounts', compact('data'));
    } else {
        $data = ['title' => 'Login'];
        echo view('account.login', compact('data'));
    }
    exit;
});
// NOTE: public self-registration lives at POST /register (account_routes.php).
// The admin-only /create-user route is defined further below. A second, public
// /create-user route used to exist here but was shadowed by the admin one and
// broke registration — removed.

$router->addRoute('GET', '/users', function () use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $users = (new UserController())->index();
    $data = ['title' => 'Users', 'role' => '0', 'user' => $us, 'records' => $users];
    echo view('users.index', compact('data'));
    exit;
});
$router->addRoute('GET', '/users/create', function () use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $roles = (new Role())->all();
    $password = generate_string(5);
    $data = ['title' => 'Users', 'response_code' => '002', 'status' => '', 'role' => '0', 'password' => $password, 'user' => $us, 'roles' => $roles];
    echo view('users.create', compact('data'));
    exit;
});
$router->addRoute('POST', '/users/create', function () use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $result = (new UserController())->create();
    $roles = (new Role())->all();
    $password = generate_string(5);
    $data = [
        'title' => 'Users',
        'response_code' => '002',
        'result' => $result,
        'status' => '',
        'role' => '0',
        'password' => $password,
        'user' => $us,
        'roles' => $roles
    ];
    echo view('users.create', compact('data'));
    exit;
});

$router->addRoute('POST', '/create-user', function () use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $result = (new UserController())->create();
    $data = ['status' => $result['status'], 'message' => $result['msg']];
    echo json_encode($data);
    exit;
});
$router->addRoute('POST', '/users/create_', function () use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $result = (new UserController())->create();
    $roles = (new Role())->all();
    $password = generate_string(5);
    $data = ['title' => 'Users', 'response_code' => '002', 'result' => $result, 'status' => '', 'role' => '0', 'password' => $password, 'user' => $us, 'roles' => $roles];


    //  echo $obj->list_document($request);
    exit;
});
$router->addRoute('GET', '/users/view/:useriD', function ($useriD) use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $selected = (new AccountController())->getUser($useriD);

    $data = ['title' => 'Users', 'role' => '0', 'user' => $us, 'record' => $selected];
    echo view('users.view', compact('data'));
    exit;
});

$router->addRoute('GET', '/users/edit/:useriD', function ($useriD) use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $selected = (new AccountController())->getUser($useriD);
    $roles = (new Role())->all();

    $data = ['title' => 'Users', 'role' => '0', 'user' => $us, 'record' => $selected, 'roles' => $roles];
    echo view('users.edit', compact('data'));
    exit;
});

$router->addRoute('GET', '/users/login/:useriD', function ($useriD) use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $selected = (new AccountController())->getUser($useriD);
    $roles = (new Role())->all();

    $status = $selected->login()[0]->status;
    $allow = '1';
    if($status>2){ $allow = '2'; }

    $data = ['title' => 'Users', 'allow' => $allow, 'user' => $us, 'record' => $selected, 'roles' => $roles];
    echo view('users.login', compact('data'));
    exit;
});
$router->addRoute('POST', '/update-user', function () use ($router) {
    $router->postAuthMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    $record = User::where('iD', $_POST['user'])[0];
    $record->name = $_POST['name'];
    $record->email = $_POST['email'];
    $record->role = $_POST['role'];
    $updated = $record->update();

    $data = ['status' => '001', 'message' => $updated, 'user' => $us];


    echo json_encode(value: $data);
    exit;
});