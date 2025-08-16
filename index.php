<?php
session_start();

// Check if configuration files exist
if (!file_exists('config/config.php') || !file_exists('config/database.php')) {
    // Show setup page if configuration doesn't exist
    if (basename($_SERVER['REQUEST_URI']) !== 'setup.php') {
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Souls - Setup Required</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .setup-card { background: rgba(255,255,255,0.95); border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="setup-card p-5 text-center">
            <h1 class="mb-4">🩸 Souls Blood Donation Website</h1>
            <p class="lead mb-4">Configuration files are missing. The application needs to be set up first.</p>
            <div class="alert alert-info">
                <strong>To set up the application:</strong><br>
                1. Start XAMPP (Apache + MySQL)<br>
                2. Run: <code>php setup.php</code><br>
                3. Or run: <code>php deploy.php</code> for deployment check
            </div>
            <a href="setup.php" class="btn btn-primary btn-lg">Run Setup Now</a>
        </div>
    </div>
</body>
</html>';
        exit;
    }
}

// Load configuration files if they exist
if (file_exists('config/config.php')) {
    require_once 'config/config.php';
}
if (file_exists('config/database.php')) {
    require_once 'config/database.php';
}

// Simple router
$request = $_SERVER['REQUEST_URI'];

// Remove index.php from request if present
$request = str_replace('/index.php', '', $request);

// Remove the /souls/ prefix if present (for XAMPP setup)
$request = preg_replace('/^\/souls/', '', $request);

// Parse the URL
$urlParts = explode('/', trim($request, '/'));
$controller = !empty($urlParts[0]) ? $urlParts[0] : 'home';
$action = isset($urlParts[1]) ? $urlParts[1] : 'index';
$id = isset($urlParts[2]) ? $urlParts[2] : null;



// Regular page routes
switch ($controller) {
    case 'home':
        require_once 'controllers/HomeController.php';
        $homeController = new HomeController();
        if ($action === 'dashboard') {
            $homeController->dashboard();
        } else {
            $homeController->index();
        }
        break;
    case 'auth':
        require_once 'controllers/AuthController.php';
        $authController = new AuthController();
        if ($action === 'login') {
            $authController->login();
        } elseif ($action === 'register') {
            $authController->register();
        } elseif ($action === 'logout') {
            $authController->logout();
        }
        break;
    case 'donate':
        require_once 'controllers/DonateController.php';
        $donateController = new DonateController();
        $donateController->index();
        break;
    case 'request':
        require_once 'controllers/RequestController.php';
        $requestController = new RequestController();
        if ($action === 'details' && $id) {
            $requestController->showDetails($id);
        } elseif ($action === 'confirm' && $id) {
            $requestController->confirmDonation($id);
        } else {
            $requestController->index();
        }
        break;
    case 'forum':
        require_once 'controllers/ForumController.php';
        $forumController = new ForumController();
        if ($action === 'create') {
            $forumController->create();
        } elseif ($action === 'edit' && $id) {
            $forumController->edit($id);
        } elseif ($action === 'delete' && $id) {
            $forumController->delete($id);
        } else {
            $forumController->index();
        }
        break;
    case 'admin':
        require_once 'controllers/AdminController.php';
        $adminController = new AdminController();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /souls/auth/login');
            exit;
        }
        $adminController->index();
        break;
        
    case 'chat':
        require_once 'controllers/ChatController.php';
        $chatController = new ChatController();
        if ($action === 'conversation' && $id) {
            $chatController->conversation($id);
        } elseif ($action === 'sendMessage') {
            $chatController->sendMessage();
        } elseif ($action === 'getNewMessages') {
            $chatController->getNewMessages();
        } elseif ($action === 'getUnreadCount') {
            $chatController->getUnreadCount();
        } elseif ($action === 'getOnlineUsers') {
            $chatController->getOnlineUsers();
        } elseif ($action === 'deleteMessage') {
            $chatController->deleteMessage();
        } elseif ($action === 'updateActivity') {
            $chatController->updateActivity();
        } else {
            $chatController->index();
        }
        break;
        
    case 'search':
        require_once 'controllers/SearchController.php';
        $searchController = new SearchController();
        if ($action === 'ajaxSearch') {
            $searchController->ajaxSearch();
        } elseif ($action === 'getSuggestions') {
            $searchController->getSuggestions();
        } else {
            $searchController->index();
        }
        break;
        
    default:
        require_once 'controllers/HomeController.php';
        $homeController = new HomeController();
        $homeController->index();
        break;
}
?> 