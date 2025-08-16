<?php
require_once 'models/User.php';
require_once 'models/Donation.php';
require_once 'models/BloodRequest.php';
require_once 'models/ForumPost.php';

class ApiController {
    private $userModel;
    private $donationModel;
    private $bloodRequestModel;
    private $forumPostModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->donationModel = new Donation();
        $this->bloodRequestModel = new BloodRequest();
        $this->forumPostModel = new ForumPost();
    }
    
    private function sendResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    private function sendError($message, $statusCode = 400) {
        $this->sendResponse(['error' => $message], $statusCode);
    }
    
    /**
     * Helper method to send JSON response
     */
    public function json($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Get authorization token from headers
     */
    private function getAuthToken() {
        $headers = getallheaders();
        
        // Check for Authorization header
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
            if (strpos($auth, 'Bearer ') === 0) {
                return substr($auth, 7); // Remove 'Bearer ' prefix
            }
        }
        
        // Check for X-API-Key header (alternative)
        if (isset($headers['X-API-Key'])) {
            return $headers['X-API-Key'];
        }
        
        return null;
    }
    
    /**
     * Validate API token
     */
    private function validateToken($token) {
        if (!$token) {
            return false;
        }
        
        // For now, we'll use session-based authentication
        // In a production environment, you'd implement JWT or other token-based auth
        return isLoggedIn();
    }
    
    /**
     * Require authentication for protected endpoints
     */
    private function requireAuth() {
        $token = $this->getAuthToken();
        
        if (!$this->validateToken($token)) {
            $this->json(['error' => 'Authentication required'], 401);
        }
    }
    
    /**
     * Require admin privileges
     */
    private function requireAdmin() {
        $this->requireAuth();
        
        if (!isAdmin()) {
            $this->json(['error' => 'Admin privileges required'], 403);
        }
    }
    
    /**
     * Get JSON input from request body
     */
    private function getJsonInput() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->json(['error' => 'Invalid JSON'], 400);
        }
        
        return $input;
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendError('Method not allowed', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $data = [
            'username' => sanitize($input['username'] ?? ''),
            'email' => sanitize($input['email'] ?? ''),
            'password' => $input['password'] ?? '',
            'first_name' => sanitize($input['first_name'] ?? ''),
            'last_name' => sanitize($input['last_name'] ?? ''),
            'phone' => sanitize($input['phone'] ?? ''),
            'blood_group' => sanitize($input['blood_group'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['username'])) $errors[] = 'Username is required';
        if (empty($data['email'])) $errors[] = 'Email is required';
        if (empty($data['password'])) $errors[] = 'Password is required';
        if (empty($data['first_name'])) $errors[] = 'First name is required';
        if (empty($data['last_name'])) $errors[] = 'Last name is required';
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters long';
        }
        
        // Check if username or email already exists
        if (!empty($data['username'])) {
            $existingUser = $this->userModel->findByUsername($data['username']);
            if ($existingUser) {
                $errors[] = 'Username already exists';
            }
        }
        
        if (!empty($data['email'])) {
            $existingUser = $this->userModel->findByEmail($data['email']);
            if ($existingUser) {
                $errors[] = 'Email already exists';
            }
        }
        
        if (!empty($errors)) {
            $this->sendError(implode(', ', $errors));
        }
        
        try {
            $this->userModel->create($data);
            $this->sendResponse(['message' => 'Registration successful'], 201);
        } catch (Exception $e) {
            $this->sendError('Registration failed', 500);
        }
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendError('Method not allowed', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $email = sanitize($input['email'] ?? '');
        $password = $input['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $this->sendError('Email and password are required');
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $this->sendError('Invalid email or password', 401);
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        
        $this->sendResponse([
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => $user['role']
            ]
        ]);
    }
    
    public function donate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendError('Method not allowed', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $data = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'donor_name' => sanitize($input['donor_name'] ?? ''),
            'blood_group' => sanitize($input['blood_group'] ?? ''),
            'location' => sanitize($input['location'] ?? ''),
            'contact_info' => sanitize($input['contact_info'] ?? ''),
            'donation_date' => sanitize($input['donation_date'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['donor_name'])) $errors[] = 'Donor name is required';
        if (empty($data['blood_group'])) $errors[] = 'Blood group is required';
        if (empty($data['location'])) $errors[] = 'Location is required';
        if (empty($data['contact_info'])) $errors[] = 'Contact information is required';
        if (empty($data['donation_date'])) $errors[] = 'Donation date is required';
        
        if (!empty($errors)) {
            $this->sendError(implode(', ', $errors));
        }
        
        try {
            $this->donationModel->create($data);
            $this->sendResponse(['message' => 'Blood donation request submitted successfully'], 201);
        } catch (Exception $e) {
            $this->sendError('Failed to submit donation request', 500);
        }
    }
    
    public function requestBlood() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendError('Method not allowed', 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $data = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'requester_name' => sanitize($input['requester_name'] ?? ''),
            'blood_group' => sanitize($input['blood_group'] ?? ''),
            'location' => sanitize($input['location'] ?? ''),
            'contact_info' => sanitize($input['contact_info'] ?? ''),
            'urgency' => sanitize($input['urgency'] ?? 'medium'),
            'request_date' => sanitize($input['request_date'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['requester_name'])) $errors[] = 'Requester name is required';
        if (empty($data['blood_group'])) $errors[] = 'Blood group is required';
        if (empty($data['location'])) $errors[] = 'Location is required';
        if (empty($data['contact_info'])) $errors[] = 'Contact information is required';
        if (empty($data['request_date'])) $errors[] = 'Request date is required';
        
        if (!empty($errors)) {
            $this->sendError(implode(', ', $errors));
        }
        
        try {
            $this->bloodRequestModel->create($data);
            $this->sendResponse(['message' => 'Blood request submitted successfully'], 201);
        } catch (Exception $e) {
            $this->sendError('Failed to submit blood request', 500);
        }
    }
    
    public function getDonations() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendError('Method not allowed', 405);
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        try {
            $donations = $this->donationModel->getAll($limit, $offset);
            $total = $this->donationModel->count();
            
            $this->sendResponse([
                'donations' => $donations,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]);
        } catch (Exception $e) {
            $this->sendError('Failed to fetch donations', 500);
        }
    }
    
    public function getRequests() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendError('Method not allowed', 405);
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        try {
            $requests = $this->bloodRequestModel->getAll($limit, $offset);
            $total = $this->bloodRequestModel->count();
            
            $this->sendResponse([
                'requests' => $requests,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]);
        } catch (Exception $e) {
            $this->sendError('Failed to fetch requests', 500);
        }
    }
    
    public function getForumPosts() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->sendError('Method not allowed', 405);
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        try {
            $posts = $this->forumPostModel->getAll($limit, $offset);
            $total = $this->forumPostModel->count();
            
            $this->sendResponse([
                'posts' => $posts,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]);
        } catch (Exception $e) {
            $this->sendError('Failed to fetch forum posts', 500);
        }
    }
    
    public function createForumPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendError('Method not allowed', 405);
        }
        
        if (!isLoggedIn()) {
            $this->sendError('Authentication required', 401);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'title' => sanitize($input['title'] ?? ''),
            'content' => sanitize($input['content'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['title'])) $errors[] = 'Title is required';
        if (empty($data['content'])) $errors[] = 'Content is required';
        
        if (strlen($data['title']) > 255) $errors[] = 'Title is too long';
        if (strlen($data['content']) > 5000) $errors[] = 'Content is too long';
        
        if (!empty($errors)) {
            $this->sendError(implode(', ', $errors));
        }
        
        try {
            $this->forumPostModel->create($data);
            $this->sendResponse(['message' => 'Post created successfully'], 201);
        } catch (Exception $e) {
            $this->sendError('Failed to create post', 500);
        }
    }
    
    public function updateForumPost($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->sendError('Method not allowed', 405);
        }
        
        if (!isLoggedIn()) {
            $this->sendError('Authentication required', 401);
        }
        
        $post = $this->forumPostModel->findById($id);
        
        if (!$post) {
            $this->sendError('Post not found', 404);
        }
        
        if ($post['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $this->sendError('You can only edit your own posts', 403);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $data = [
            'title' => sanitize($input['title'] ?? ''),
            'content' => sanitize($input['content'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['title'])) $errors[] = 'Title is required';
        if (empty($data['content'])) $errors[] = 'Content is required';
        
        if (strlen($data['title']) > 255) $errors[] = 'Title is too long';
        if (strlen($data['content']) > 5000) $errors[] = 'Content is too long';
        
        if (!empty($errors)) {
            $this->sendError(implode(', ', $errors));
        }
        
        try {
            $this->forumPostModel->update($id, $data);
            $this->sendResponse(['message' => 'Post updated successfully']);
        } catch (Exception $e) {
            $this->sendError('Failed to update post', 500);
        }
    }
    
    public function deleteForumPost($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->sendError('Method not allowed', 405);
        }
        
        if (!isLoggedIn()) {
            $this->sendError('Authentication required', 401);
        }
        
        $post = $this->forumPostModel->findById($id);
        
        if (!$post) {
            $this->sendError('Post not found', 404);
        }
        
        if ($post['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $this->sendError('You can only delete your own posts', 403);
        }
        
        try {
            $this->forumPostModel->delete($id);
            $this->sendResponse(['message' => 'Post deleted successfully']);
        } catch (Exception $e) {
            $this->sendError('Failed to delete post', 500);
        }
    }
    
    /**
     * Health check endpoint
     */
    public function health() {
        $this->json([
            'status' => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'environment' => 'development'
        ]);
    }
    
    /**
     * Get single request by ID
     */
    public function getRequest($id) {
        if (!is_numeric($id)) {
            $this->json(['error' => 'Invalid request ID'], 400);
        }
        
        try {
            $request = $this->bloodRequestModel->findById($id);
            
            if (!$request) {
                $this->json(['error' => 'Request not found'], 404);
            }
            
            $this->json(['request' => $request]);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to fetch request'], 500);
        }
    }
    
    /**
     * Create new blood request
     */
    public function createRequest() {
        $this->requireAuth();
        
        $input = $this->getJsonInput();
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'requester_name' => sanitize($input['requester_name'] ?? ''),
            'blood_group' => sanitize($input['blood_group'] ?? ''),
            'location' => sanitize($input['location'] ?? ''),
            'contact_info' => sanitize($input['contact_info'] ?? ''),
            'urgency' => sanitize($input['urgency'] ?? 'medium'),
            'request_date' => sanitize($input['request_date'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['requester_name'])) $errors[] = 'Requester name is required';
        if (empty($data['blood_group'])) $errors[] = 'Blood group is required';
        if (empty($data['location'])) $errors[] = 'Location is required';
        if (empty($data['contact_info'])) $errors[] = 'Contact information is required';
        if (empty($data['request_date'])) $errors[] = 'Request date is required';
        
        if (!empty($errors)) {
            $this->json(['error' => implode(', ', $errors)], 400);
        }
        
        try {
            $this->bloodRequestModel->create($data);
            $this->json(['message' => 'Blood request created successfully'], 201);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to create request'], 500);
        }
    }
    
    /**
     * Update blood request
     */
    public function updateRequest($id) {
        $this->requireAuth();
        
        if (!is_numeric($id)) {
            $this->json(['error' => 'Invalid request ID'], 400);
        }
        
        $request = $this->bloodRequestModel->findById($id);
        
        if (!$request) {
            $this->json(['error' => 'Request not found'], 404);
        }
        
        // Check ownership or admin privileges
        if ($request['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $this->json(['error' => 'You can only edit your own requests'], 403);
        }
        
        $input = $this->getJsonInput();
        
        $data = [
            'requester_name' => sanitize($input['requester_name'] ?? $request['requester_name']),
            'blood_group' => sanitize($input['blood_group'] ?? $request['blood_group']),
            'location' => sanitize($input['location'] ?? $request['location']),
            'contact_info' => sanitize($input['contact_info'] ?? $request['contact_info']),
            'urgency' => sanitize($input['urgency'] ?? $request['urgency']),
            'request_date' => sanitize($input['request_date'] ?? $request['request_date'])
        ];
        
        try {
            $this->bloodRequestModel->update($id, $data);
            $this->json(['message' => 'Request updated successfully']);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to update request'], 500);
        }
    }
    
    /**
     * Delete blood request
     */
    public function deleteRequest($id) {
        $this->requireAuth();
        
        if (!is_numeric($id)) {
            $this->json(['error' => 'Invalid request ID'], 400);
        }
        
        $request = $this->bloodRequestModel->findById($id);
        
        if (!$request) {
            $this->json(['error' => 'Request not found'], 404);
        }
        
        // Check ownership or admin privileges
        if ($request['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $this->json(['error' => 'You can only delete your own requests'], 403);
        }
        
        try {
            $this->bloodRequestModel->delete($id);
            $this->json(['message' => 'Request deleted successfully']);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to delete request'], 500);
        }
    }
    
    /**
     * Create new donation
     */
    public function createDonation() {
        $this->requireAuth();
        
        $input = $this->getJsonInput();
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'donor_name' => sanitize($input['donor_name'] ?? ''),
            'blood_group' => sanitize($input['blood_group'] ?? ''),
            'location' => sanitize($input['location'] ?? ''),
            'contact_info' => sanitize($input['contact_info'] ?? ''),
            'donation_date' => sanitize($input['donation_date'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['donor_name'])) $errors[] = 'Donor name is required';
        if (empty($data['blood_group'])) $errors[] = 'Blood group is required';
        if (empty($data['location'])) $errors[] = 'Location is required';
        if (empty($data['contact_info'])) $errors[] = 'Contact information is required';
        if (empty($data['donation_date'])) $errors[] = 'Donation date is required';
        
        if (!empty($errors)) {
            $this->json(['error' => implode(', ', $errors)], 400);
        }
        
        try {
            $this->donationModel->create($data);
            $this->json(['message' => 'Donation created successfully'], 201);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to create donation'], 500);
        }
    }
    
    /**
     * Update donation
     */
    public function updateDonation($id) {
        $this->requireAuth();
        
        if (!is_numeric($id)) {
            $this->json(['error' => 'Invalid donation ID'], 400);
        }
        
        $donation = $this->donationModel->findById($id);
        
        if (!$donation) {
            $this->json(['error' => 'Donation not found'], 404);
        }
        
        // Check ownership or admin privileges
        if ($donation['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $this->json(['error' => 'You can only edit your own donations'], 403);
        }
        
        $input = $this->getJsonInput();
        
        $data = [
            'donor_name' => sanitize($input['donor_name'] ?? $donation['donor_name']),
            'blood_group' => sanitize($input['blood_group'] ?? $donation['blood_group']),
            'location' => sanitize($input['location'] ?? $donation['location']),
            'contact_info' => sanitize($input['contact_info'] ?? $donation['contact_info']),
            'donation_date' => sanitize($input['donation_date'] ?? $donation['donation_date'])
        ];
        
        try {
            $this->donationModel->update($id, $data);
            $this->json(['message' => 'Donation updated successfully']);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to update donation'], 500);
        }
    }
    
    /**
     * Delete donation
     */
    public function deleteDonation($id) {
        $this->requireAuth();
        
        if (!is_numeric($id)) {
            $this->json(['error' => 'Invalid donation ID'], 400);
        }
        
        $donation = $this->donationModel->findById($id);
        
        if (!$donation) {
            $this->json(['error' => 'Donation not found'], 404);
        }
        
        // Check ownership or admin privileges
        if ($donation['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
            $this->json(['error' => 'You can only delete your own donations'], 403);
        }
        
        try {
            $this->donationModel->delete($id);
            $this->json(['message' => 'Donation deleted successfully']);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to delete donation'], 500);
        }
    }
    
    /**
     * Get users (admin only)
     */
    public function getUsers() {
        $this->requireAdmin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        try {
            $users = $this->userModel->getAll($limit, $offset);
            $total = $this->userModel->count();
            
            $this->json([
                'users' => $users,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to fetch users'], 500);
        }
    }
    
    /**
     * Get single user
     */
    public function getUser($id) {
        $this->requireAuth();
        
        if (!is_numeric($id)) {
            $this->json(['error' => 'Invalid user ID'], 400);
        }
        
        // Users can only view their own profile unless admin
        if ($id != $_SESSION['user_id'] && !isAdmin()) {
            $this->json(['error' => 'Access denied'], 403);
        }
        
        try {
            $user = $this->userModel->findById($id);
            
            if (!$user) {
                $this->json(['error' => 'User not found'], 404);
            }
            
            // Remove sensitive information
            unset($user['password']);
            
            $this->json(['user' => $user]);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to fetch user'], 500);
        }
    }
    
    /**
     * Update user
     */
    public function updateUser($id) {
        $this->requireAuth();
        
        if (!is_numeric($id)) {
            $this->json(['error' => 'Invalid user ID'], 400);
        }
        
        // Users can only update their own profile unless admin
        if ($id != $_SESSION['user_id'] && !isAdmin()) {
            $this->json(['error' => 'Access denied'], 403);
        }
        
        $input = $this->getJsonInput();
        
        $data = [
            'first_name' => sanitize($input['first_name'] ?? ''),
            'last_name' => sanitize($input['last_name'] ?? ''),
            'phone' => sanitize($input['phone'] ?? ''),
            'blood_group' => sanitize($input['blood_group'] ?? '')
        ];
        
        // Validation
        $errors = [];
        
        if (empty($data['first_name'])) $errors[] = 'First name is required';
        if (empty($data['last_name'])) $errors[] = 'Last name is required';
        
        if (!empty($errors)) {
            $this->json(['error' => implode(', ', $errors)], 400);
        }
        
        try {
            $this->userModel->update($id, $data);
            $this->json(['message' => 'User updated successfully']);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to update user'], 500);
        }
    }
    
    /**
     * Delete user (admin only)
     */
    public function deleteUser($id) {
        $this->requireAdmin();
        
        if (!is_numeric($id)) {
            $this->json(['error' => 'Invalid user ID'], 400);
        }
        
        try {
            $this->userModel->delete($id);
            $this->json(['message' => 'User deleted successfully']);
        } catch (Exception $e) {
            $this->json(['error' => 'Failed to delete user'], 500);
        }
    }
    
    /**
     * Logout endpoint
     */
    public function logout() {
        session_destroy();
        $this->json(['message' => 'Logged out successfully']);
    }
    
    /**
     * 404 Not Found
     */
    public function notFound() {
        $this->json(['error' => 'Endpoint not found'], 404);
    }
    
    /**
     * 405 Method Not Allowed
     */
    public function methodNotAllowed() {
        $this->json(['error' => 'Method not allowed'], 405);
    }
    
    /**
     * Generic error handler
     */
    public function error($message) {
        $this->json(['error' => $message], 500);
    }
}
?> 