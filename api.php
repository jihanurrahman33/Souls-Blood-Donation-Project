<?php
session_start();

// Check if configuration files exist
if (!file_exists('config/config.php') || !file_exists('config/database.php')) {
    header('Content-Type: application/json');
    http_response_code(503);
    echo json_encode([
        'error' => 'Configuration files missing',
        'message' => 'The application needs to be set up first. Run php setup.php to configure the application.',
        'setup_required' => true
    ]);
    exit;
}

// Load configuration files
require_once 'config/config.php';
require_once 'config/database.php';

// Set JSON content type for all API responses
header('Content-Type: application/json');

// Enable CORS for API access
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parse the API URL
$request = $_SERVER['REQUEST_URI'];

// Remove the /souls/ prefix if present (for XAMPP setup)
$request = preg_replace('/^\/souls/', '', $request);

// Remove the base path and api.php from request
$request = str_replace('/api.php', '', $request);
$request = str_replace('/api', '', $request);

// Parse the URL parts
$urlParts = explode('/', trim($request, '/'));
$resource = isset($urlParts[0]) ? $urlParts[0] : '';
$id = isset($urlParts[1]) ? $urlParts[1] : null;

// Load the API controller
require_once 'controllers/ApiController.php';
$apiController = new ApiController();

try {
    // Route the request based on HTTP method and resource
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($resource === 'health') {
                $apiController->health();
            } elseif ($resource === 'requests') {
                if ($id) {
                    $apiController->getRequest($id);
                } else {
                    $apiController->getRequests();
                }
            } elseif ($resource === 'donations') {
                $apiController->getDonations();
            } elseif ($resource === 'forum') {
                $apiController->getForumPosts();
            } elseif ($resource === 'users') {
                if ($id) {
                    $apiController->getUser($id);
                } else {
                    $apiController->getUsers();
                }
            } else {
                $apiController->notFound();
            }
            break;
            
        case 'POST':
            if ($resource === 'auth') {
                $action = $id; // login, register, logout
                if ($action === 'login') {
                    $apiController->login();
                } elseif ($action === 'register') {
                    $apiController->register();
                } elseif ($action === 'logout') {
                    $apiController->logout();
                } else {
                    $apiController->notFound();
                }
            } elseif ($resource === 'requests') {
                $apiController->createRequest();
            } elseif ($resource === 'donations') {
                $apiController->createDonation();
            } elseif ($resource === 'forum') {
                $apiController->createForumPost();
            } else {
                $apiController->notFound();
            }
            break;
            
        case 'PUT':
            if ($resource === 'requests' && $id) {
                $apiController->updateRequest($id);
            } elseif ($resource === 'donations' && $id) {
                $apiController->updateDonation($id);
            } elseif ($resource === 'forum' && $id) {
                $apiController->updateForumPost($id);
            } elseif ($resource === 'users' && $id) {
                $apiController->updateUser($id);
            } else {
                $apiController->notFound();
            }
            break;
            
        case 'DELETE':
            if ($resource === 'requests' && $id) {
                $apiController->deleteRequest($id);
            } elseif ($resource === 'donations' && $id) {
                $apiController->deleteDonation($id);
            } elseif ($resource === 'forum' && $id) {
                $apiController->deleteForumPost($id);
            } elseif ($resource === 'users' && $id) {
                $apiController->deleteUser($id);
            } else {
                $apiController->notFound();
            }
            break;
            
        default:
            $apiController->methodNotAllowed();
            break;
    }
    
} catch (Exception $e) {
    // Handle any unexpected errors
    $apiController->error($e->getMessage());
}
?>
