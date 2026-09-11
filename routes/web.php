<?php

use App\Controllers\AccountController;
use App\Controllers\UserController;

global $router;
//use function App\Helpers\view;


$router->addRoute('GET', "/apk", function () {
    $apkDir = __DIR__ . "/../../apk"; // Correct directory
    $apkUrl = "/easiwrap/public/apk"; // Public URL path for downloading
    $files = [];

    if (is_dir($apkDir)) {
        foreach (scandir($apkDir) as $file) {
            if (is_file($apkDir . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'apk') {
                $files[] = $file;
            }
        }
    }

    // HTML output
    echo "<!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title>APK Downloads</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
        <style>
            body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; padding: 2rem; }
            .container { max-width: 600px; margin: auto; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h4 class='mb-4 text-center text-success'>📱 Available APK Files</h4>";

    if (empty($files)) {
        echo "<div class='alert alert-warning text-center'>No APK files available.</div>";
    } else {
        echo "<ul class='list-group'>";
        foreach ($files as $file) {
            $encodedFile = urlencode($file);
            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                    <div class='text-truncate me-2' style='max-width:70%;'>$file</div>
                    <a href='apk/download?file=$encodedFile' class='btn btn-sm btn-outline-success'>
                        <i class='fa fa-download me-1'></i> Download
                    </a>
                </li>";
        }
        echo "</ul>";
    }

    echo "</div>
    </body>
    </html>";
    exit;
});
$router->addRoute('GET', '/apk/download', function () {
    if (!isset($_GET['file'])) {
        http_response_code(400);
        echo "No file specified.";
        return;
    }

    $file = basename($_GET['file']);
    $filePath = __DIR__ . "/../../apk/" . $file;

    if (!file_exists($filePath)) {
        http_response_code(404);
        echo "File not found.";
        return;
    }

    // Set headers to force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
});

$router->addRoute('GET', '/dashboard', function () use ($router) {
    $router->authMiddleware();
    $us = (new AccountController())->getUser($_COOKIE['user']);
    if (in_array((int)$us->role, [1, 6, 7, 8], true)) {
        $roleObj = $us->role();
        $roleName = $roleObj ? $roleObj->name : 'Staff';
        $data = ['title' => $roleName . ' Dashboard', 'user' => $us];
        echo view('dashboard.admin', compact('data'));
    } elseif ((int)$us->role === 3) {
        header('Location: ' . $GLOBALS['siteConfig']->siteUrl . '/client/portal');
        exit;
    } else {
        $data = ['title' => 'Dashboard', 'user' => $us];
        echo view('dashboard.client', compact('data'));
    }
    exit;
});

$router->addRoute('GET', '/home', function () use ($router) {

    $data = ['title' => 'Home'];
    if (isset($_COOKIE['user'])) {
        $data['user'] = (new AccountController())->getUser($_COOKIE['user']);
    }
    echo view('home.index', compact('data'));

    exit;
});
$router->addRoute('GET', '/contact', function () use ($router) {
    $data = ['title' => 'Contact Us'];
    if (isset($_COOKIE['user'])) {
        $data['user'] = (new AccountController())->getUser($_COOKIE['user']);
    }
    echo view('home.contact', compact('data'));
    exit;
});

$router->addRoute('POST', '/contact', function () use ($router) {
    header('Content-Type: application/json');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? 'Website Contact Inquiry');
    $message = trim($_POST['message'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['status' => 0, 'msg' => 'Please fill in your name, email, and message.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 0, 'msg' => 'Please provide a valid email address.']);
        exit;
    }

    \App\Helpers\Mailer::sendContactInquiry($name, $email, $subject, $message, $phone);

    echo json_encode(['status' => 1, 'msg' => 'Thank you for reaching out! We have received your message and sent a confirmation to your email.']);
    exit;
});

$router->addRoute('GET', '/services', function () use ($router) {
    $data = ['title' => 'Services'];
    if (isset($_COOKIE['user'])) {
        $data['user'] = (new AccountController())->getUser($_COOKIE['user']);
    }
    echo view('home.services', compact('data'));
    exit;
});

$router->addRoute('GET', '/cloud', function () use ($router) {
    $data = ['title' => 'Cloud'];
    if (isset($_COOKIE['user'])) {
        $data['user'] = (new AccountController())->getUser($_COOKIE['user']);
    }
    echo view('home.cloud', compact('data'));
    exit;
});

$router->addRoute('GET', '/opportunities', function () use ($router) {
    $data = ['title' => 'Apprentices and Associates'];
    $loggedInUser = null;
    if (App\Helpers\Auth::check()) {
        $userId = App\Helpers\Auth::id();
        if ($userId) {
            $loggedInUser = (new AccountController())->getUser($userId);
            $data['user'] = $loggedInUser;
        }
    } elseif (isset($_COOKIE['user'])) {
        $loggedInUser = (new AccountController())->getUser($_COOKIE['user']);
        $data['user'] = $loggedInUser;
    }

    $isAdmin = App\Helpers\Auth::check() && (
        App\Helpers\Auth::isAdmin() || 
        App\Helpers\Auth::isVettingOfficer() || 
        App\Helpers\Auth::isServiceManager() || 
        ($loggedInUser && in_array((int)$loggedInUser->role, [1, 6, 8]))
    );
    $data['isAdmin'] = $isAdmin;

    if ($isAdmin) {
        $data['statuses'] = \App\Models\Applicationstatus::all();
        $data['tracks'] = \App\Models\Applicationtrack::all();

        $pdo = \App\Models\Database::sharedPdo();
        $data['totalApplicants'] = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication")->fetchColumn();
        $data['apprenticeCount'] = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationtrack = 1")->fetchColumn();
        $data['associateCount'] = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationtrack = 2")->fetchColumn();
        $data['submittedCount'] = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationstatus = 2")->fetchColumn();
        $data['shortlistedCount'] = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationstatus = 3")->fetchColumn();
        $data['interviewCount'] = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationstatus = 4")->fetchColumn();
        $data['onRosterCount'] = (int)$pdo->query("SELECT COUNT(*) FROM rosterapplication WHERE applicationstatus = 5")->fetchColumn();

        // Preload initial applications for instant SSR
        $data['initialApplications'] = \App\Models\Rosterapplication::findByQuery(
            "SELECT * FROM rosterapplication ORDER BY iD DESC LIMIT 20"
        );
    }

    // Load published vacancies for public display
    $data['publishedVacancies'] = \App\Models\Vacancy::findByQuery(
        "SELECT * FROM vacancy WHERE vacancystatus = 2 AND status = 1 ORDER BY is_featured DESC, publish_date DESC"
    );

    echo view('home.opportunities', compact('data'));
    exit;
});

$router->addRoute('GET', '/about', function () use ($router) {
    $data = ['title' => 'About Us'];
    if (isset($_COOKIE['user'])) {
        $data['user'] = (new AccountController())->getUser($_COOKIE['user']);
    }
    echo view('home.about', compact('data'));
    exit;
});
$router->addRoute('GET', '', function () use ($router) {
    $data = ['title' => 'Home'];
    if (isset($_COOKIE['user'])) {
        $data['user'] = (new AccountController())->getUser($_COOKIE['user']);
    }
    echo view('home.index', compact('data'));

    exit;
});
