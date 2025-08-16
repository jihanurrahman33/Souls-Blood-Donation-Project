<?php
require_once 'models/User.php';
require_once 'models/Donation.php';
require_once 'models/BloodRequest.php';
require_once 'models/ForumPost.php';

class AdminController {
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
        // Get statistics
        $stats = [
            'total_users' => $this->userModel->count(),
            'total_donations' => $this->donationModel->count(),
            'total_requests' => $this->bloodRequestModel->count(),
            'total_posts' => $this->forumPostModel->count(),
            'pending_donations' => $this->donationModel->countByStatus('pending'),
            'pending_requests' => $this->bloodRequestModel->countByStatus('pending'),
            'urgent_requests' => $this->bloodRequestModel->countByUrgency('critical') + $this->bloodRequestModel->countByUrgency('high')
        ];
        
        // Get recent data
        $recentUsers = $this->userModel->getAll(5);
        $recentDonations = $this->donationModel->getAll(5);
        $recentRequests = $this->bloodRequestModel->getAll(5);
        $recentPosts = $this->forumPostModel->getAll(5);
        
        include 'views/admin/dashboard.php';
    }
    
    public function users() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $users = $this->userModel->getAll($limit, $offset);
        $totalUsers = $this->userModel->count();
        $totalPages = ceil($totalUsers / $limit);
        
        include 'views/admin/users.php';
    }
    
    public function donations() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $donations = $this->donationModel->getAll($limit, $offset);
        $totalDonations = $this->donationModel->count();
        $totalPages = ceil($totalDonations / $limit);
        
        include 'views/admin/donations.php';
    }
    
    public function requests() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $requests = $this->bloodRequestModel->getAll($limit, $offset);
        $totalRequests = $this->bloodRequestModel->count();
        $totalPages = ceil($totalRequests / $limit);
        
        include 'views/admin/requests.php';
    }
    
    public function updateDonationStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $status = sanitize($_POST['status']);
            
            $validStatuses = ['pending', 'approved', 'completed', 'cancelled'];
            
            if (in_array($status, $validStatuses)) {
                try {
                    $this->donationModel->updateStatus($id, $status);
                    $_SESSION['success'] = 'Donation status updated successfully!';
                } catch (Exception $e) {
                    $_SESSION['errors'] = ['Failed to update donation status'];
                }
            } else {
                $_SESSION['errors'] = ['Invalid status'];
            }
        }
        
        redirect('admin/donations');
    }
    
    public function updateRequestStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $status = sanitize($_POST['status']);
            
            $validStatuses = ['pending', 'fulfilled', 'cancelled'];
            
            if (in_array($status, $validStatuses)) {
                try {
                    $this->bloodRequestModel->updateStatus($id, $status);
                    $_SESSION['success'] = 'Request status updated successfully!';
                } catch (Exception $e) {
                    $_SESSION['errors'] = ['Failed to update request status'];
                }
            } else {
                $_SESSION['errors'] = ['Invalid status'];
            }
        }
        
        redirect('admin/requests');
    }
    
    public function deleteUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            
            try {
                $this->userModel->delete($id);
                $_SESSION['success'] = 'User deleted successfully!';
            } catch (Exception $e) {
                $_SESSION['errors'] = ['Failed to delete user'];
            }
        }
        
        redirect('admin/users');
    }
}
?> 