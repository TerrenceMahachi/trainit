<?php
use App\Controllers\AccountController;
use App\Controllers\UserprofilesController;
use App\Helpers\ViewAccess;
use App\Models\Userprofile;

$uri = 'userprofiles';
$table = 'userprofile';
$options['view'] = 'userprofiles';
$options['title'] = 'Userprofile';
$options['MainController'] = new UserprofilesController();

/** Fetch a record by id or render the 404 page (used by view/edit routes). */
$options['findOr404'] = function ($recordiD) use ($options) {
    $records = Userprofile::where('iD', $recordiD);
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
         'profiletype' => $record->profiletype()->name,
         'profilestatus' => $record->profilestatus()->name,
         'display_title' => $record->display_title,
         'is_default' => $record->is_default,
         'request_notes' => $record->request_notes,
         'reviewer_notes' => $record->reviewer_notes,
         'reviewed_by' => $record->reviewed_by()->name,
         'reviewed_at' => $record->reviewed_at
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

// POST review profile application (Approve / Reject)
$router->addRoute('POST', "/$uri/review/:recordiD", function ($recordiD) use ($router, $options) {
    $router->postAuthMiddleware();
    ViewAccess::enforceForView($options['view']);

    $profile = $options['findOr404']($recordiD);
    $action = $_POST['action'] ?? '';
    $notes = trim($_POST['reviewer_notes'] ?? '');

    $reviewerId = \App\Helpers\Auth::id();
    $now = date('Y-m-d H:i:s');
    $db = \App\Models\Database::sharedPdo();

    if ($action === 'approve') {
        $stmt = $db->prepare("UPDATE userprofile SET profilestatus = 3, reviewer_notes = ?, reviewed_by = ?, reviewed_at = ? WHERE iD = ?");
        $stmt->execute([$notes, $reviewerId, $now, $recordiD]);

        // If this profile was a staff/client profile, approve associated pending clientmembership
        if ($profile->profiletype == 4 || $profile->profiletype == 5) {
            $db->prepare("UPDATE clientmembership SET status = 1 WHERE user = ? AND status = 2")->execute([$profile->user]);
        }

        // Sync main user role if candidate
        if ($profile->profiletype == 2) { // apprentice
            $db->prepare("UPDATE user SET role = 5 WHERE iD = ? AND role = 2")->execute([$profile->user]);
        } elseif ($profile->profiletype == 3) { // associate
            $db->prepare("UPDATE user SET role = 4 WHERE iD = ? AND role = 2")->execute([$profile->user]);
        }

        $data = ['status' => 'success', 'message' => 'Profile application approved successfully!'];
    } elseif ($action === 'reject') {
        $stmt = $db->prepare("UPDATE userprofile SET profilestatus = 4, reviewer_notes = ?, reviewed_by = ?, reviewed_at = ? WHERE iD = ?");
        $stmt->execute([$notes, $reviewerId, $now, $recordiD]);
        $data = ['status' => 'success', 'message' => 'Profile application rejected.'];
    } else {
        $data = ['status' => 'failed', 'message' => 'Invalid action'];
    }

    echo json_encode($data);
    exit;
});

// POST delete / withdraw profile application
$router->addRoute('POST', "/delete-$table", function () use ($router, $options, $table) {
    $router->postAuthMiddleware();
    ViewAccess::enforceForView($options['view']);
    $recordiD = (int)($_POST['itemiD'] ?? 0);
    $db = \App\Models\Database::sharedPdo();
    $db->prepare("DELETE FROM profilerequestaudit WHERE userprofile = ?")->execute([$recordiD]);
    $db->prepare("DELETE FROM userprofile WHERE iD = ?")->execute([$recordiD]);
    echo json_encode(['status' => 1, 'message' => 'Profile application removed successfully.']);
    exit;
});

