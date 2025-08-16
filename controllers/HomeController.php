<?php
require_once 'models/User.php';
require_once 'models/Donation.php';
require_once 'models/BloodRequest.php';
require_once 'models/ForumPost.php';

class HomeController {
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
    
    public function index() {
        // Get statistics for dashboard
        $stats = [
            'total_users' => $this->userModel->count(),
            'total_donations' => $this->donationModel->count(),
            'total_requests' => $this->bloodRequestModel->count(),
            'total_posts' => $this->forumPostModel->count(),
            'pending_donations' => $this->donationModel->countByStatus('pending'),
            'pending_requests' => $this->bloodRequestModel->countByStatus('pending'),
            'urgent_requests' => $this->bloodRequestModel->countByUrgency('critical') + $this->bloodRequestModel->countByUrgency('high')
        ];
        
        // Get recent activities
        $recentDonations = $this->donationModel->getRecentDonations(5);
        $recentRequests = $this->bloodRequestModel->getRecentRequests(5);
        $recentPosts = $this->forumPostModel->getRecentPosts(5);
        $urgentRequests = $this->bloodRequestModel->getUrgentRequests(3);
        
        // Include the view
        include 'views/home.php';
    }
    
    public function dashboard() {
        // Start output buffering immediately to catch any output
        ob_start();
        
        // Require login - check before any output
        if (!isLoggedIn()) {
            ob_end_clean(); // Clear any output buffer
            $_SESSION['error'] = 'Please log in to access your dashboard.';
            redirect('auth/login');
            return; // Ensure no further execution
        }
        
        // Get logged-in user's information
        $user = $this->userModel->findById($_SESSION['user_id']);
        
        if (!$user) {
            ob_end_clean(); // Clear any output buffer
            $_SESSION['error'] = 'User not found.';
            redirect('auth/login');
            return; // Ensure no further execution
        }
        
        // Get user's blood group
        $bloodGroup = $user['blood_group'];
        
        if (!$bloodGroup) {
            ob_end_clean(); // Clear any output buffer
            $_SESSION['error'] = 'Please update your blood group in your profile to see matching requests.';
            redirect('profile');
            return; // Ensure no further execution
        }
        
        // Get matching requests for user's blood group
        $matchingRequests = $this->bloodRequestModel->findMatchingRequests($bloodGroup);
        
        // Get user's donation history
        $userDonations = $this->donationModel->getByUserId($_SESSION['user_id'], 5);
        
        // Get user's request history
        $userRequests = $this->bloodRequestModel->getByUserId($_SESSION['user_id'], 5);
        
        // End output buffering and include the dashboard view
        ob_end_clean();
        include 'views/dashboard.php';
    }
} 