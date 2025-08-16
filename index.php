<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

// Simple router
$request = $_SERVER['REQUEST_URI'];

// Remove index.php from request if present
$request = str_replace('/index.php', '', $request);

// Parse the URL
$urlParts = explode('/', trim($request, '/'));
$controller = !empty($urlParts[0]) ? $urlParts[0] : 'home';
$action = isset($urlParts[1]) ? $urlParts[1] : 'index';
$id = isset($urlParts[2]) ? $urlParts[2] : null;

// API routes
if ($controller === 'api') {
    $apiAction = isset($urlParts[1]) ? $urlParts[1] : '';
    $apiResource = isset($urlParts[2]) ? $urlParts[2] : '';
    $apiId = isset($urlParts[3]) ? $urlParts[3] : null;
    
    require_once 'controllers/ApiController.php';
    $apiController = new ApiController();
    
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($apiResource === 'donations') {
                $apiController->getDonations();
            } elseif ($apiResource === 'requests') {
                $apiController->getRequests();
            } elseif ($apiResource === 'forum') {
                $apiController->getForumPosts();
            }
            break;
        case 'POST':
            if ($apiAction === 'register') {
                $apiController->register();
            } elseif ($apiAction === 'login') {
                $apiController->login();
            } elseif ($apiAction === 'donate') {
                $apiController->donate();
            } elseif ($apiAction === 'request') {
                $apiController->requestBlood();
            } elseif ($apiResource === 'forum') {
                $apiController->createForumPost();
            }
            break;
        case 'PUT':
            if ($apiResource === 'forum' && $apiId) {
                $apiController->updateForumPost($apiId);
            }
            break;
        case 'DELETE':
            if ($apiResource === 'forum' && $apiId) {
                $apiController->deleteForumPost($apiId);
            }
            break;
    }
    exit;
}

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
            header('Location: /souls-blood%20donation%20website/auth/login');
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