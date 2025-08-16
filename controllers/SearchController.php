<?php
require_once 'models/BloodRequest.php';
require_once 'models/User.php';
require_once 'models/Donation.php';

class SearchController {
    private $bloodRequestModel;
    private $userModel;
    private $donationModel;
    
    public function __construct() {
        $this->bloodRequestModel = new BloodRequest();
        $this->userModel = new User();
        $this->donationModel = new Donation();
    }
    
    /**
     * Show search interface
     */
    public function index() {
        $searchTerm = $_GET['q'] ?? '';
        $filters = $this->getFilters();
        
        $results = [];
        $totalResults = 0;
        
        if (!empty($searchTerm)) {
            $results = $this->performSearch($searchTerm, $filters);
            $totalResults = count($results);
            
            // Log search for analytics
            $this->logSearch($searchTerm, $filters, $totalResults);
        }
        
        include 'views/search/index.php';
    }
    
    /**
     * AJAX search endpoint
     */
    public function ajaxSearch() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $searchTerm = $_GET['q'] ?? '';
        $filters = $this->getFilters();
        
        if (empty($searchTerm)) {
            $this->jsonResponse(['results' => [], 'total' => 0]);
        }
        
        $results = $this->performSearch($searchTerm, $filters);
        $totalResults = count($results);
        
        // Log search for analytics
        $this->logSearch($searchTerm, $filters, $totalResults);
        
        $this->jsonResponse([
            'results' => $results,
            'total' => $totalResults,
            'search_term' => $searchTerm,
            'filters' => $filters
        ]);
    }
    
    /**
     * Get search filters from request
     */
    private function getFilters() {
        return [
            'type' => $_GET['type'] ?? 'all', // all, requests, users, donations
            'blood_group' => $_GET['blood_group'] ?? '',
            'location' => $_GET['location'] ?? '',
            'urgency' => $_GET['urgency'] ?? '',
            'status' => $_GET['status'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? '',
            'sort_by' => $_GET['sort_by'] ?? 'relevance', // relevance, date, urgency
            'sort_order' => $_GET['sort_order'] ?? 'desc'
        ];
    }
    
    /**
     * Perform search across different entities
     */
    private function performSearch($searchTerm, $filters) {
        $results = [];
        
        if ($filters['type'] === 'all' || $filters['type'] === 'requests') {
            $requests = $this->searchBloodRequests($searchTerm, $filters);
            $results = array_merge($results, $requests);
        }
        
        if ($filters['type'] === 'all' || $filters['type'] === 'users') {
            $users = $this->searchUsers($searchTerm, $filters);
            $results = array_merge($results, $users);
        }
        
        if ($filters['type'] === 'all' || $filters['type'] === 'donations') {
            $donations = $this->searchDonations($searchTerm, $filters);
            $results = array_merge($results, $donations);
        }
        
        // Sort results
        $results = $this->sortResults($results, $filters['sort_by'], $filters['sort_order']);
        
        return $results;
    }
    
    /**
     * Search blood requests
     */
    private function searchBloodRequests($searchTerm, $filters) {
        // Use the model's public methods instead of accessing private properties
        $allRequests = $this->bloodRequestModel->getAll();
        
        // Filter results based on search term and filters
        $filteredRequests = [];
        foreach ($allRequests as $request) {
            $matches = false;
            
            // Check if search term matches
            if (stripos($request['requester_name'], $searchTerm) !== false ||
                stripos($request['blood_group'], $searchTerm) !== false ||
                stripos($request['location'], $searchTerm) !== false ||
                stripos($request['contact_info'], $searchTerm) !== false) {
                $matches = true;
            }
            
            // Apply filters
            if ($matches && !empty($filters['blood_group']) && $request['blood_group'] !== $filters['blood_group']) {
                $matches = false;
            }
            
            if ($matches && !empty($filters['location']) && stripos($request['location'], $filters['location']) === false) {
                $matches = false;
            }
            
            if ($matches && !empty($filters['urgency']) && $request['urgency'] !== $filters['urgency']) {
                $matches = false;
            }
            
            if ($matches && !empty($filters['status']) && $request['status'] !== $filters['status']) {
                $matches = false;
            }
            
            if ($matches) {
                $request['result_type'] = 'blood_request';
                $request['title'] = "Blood Request: {$request['blood_group']} - {$request['requester_name']}";
                $request['description'] = "Location: {$request['location']} | Urgency: " . ucfirst($request['urgency']);
                $request['url'] = APP_URL . "request/details/{$request['id']}";
                $filteredRequests[] = $request;
            }
        }
        
        return $filteredRequests;
    }
    
    /**
     * Search users
     */
    private function searchUsers($searchTerm, $filters) {
        // Use the model's public methods instead of accessing private properties
        $allUsers = $this->userModel->getAll();
        
        // Filter results based on search term and filters
        $filteredUsers = [];
        foreach ($allUsers as $user) {
            $matches = false;
            
            // Check if search term matches
            if (stripos($user['username'], $searchTerm) !== false ||
                stripos($user['email'], $searchTerm) !== false ||
                stripos($user['first_name'], $searchTerm) !== false ||
                stripos($user['last_name'], $searchTerm) !== false) {
                $matches = true;
            }
            
            // Apply filters
            if ($matches && !empty($filters['blood_group']) && $user['blood_group'] !== $filters['blood_group']) {
                $matches = false;
            }
            
            if ($matches) {
                $user['result_type'] = 'user';
                $user['title'] = "User: {$user['first_name']} {$user['last_name']} (@{$user['username']})";
                $user['description'] = "Blood Group: {$user['blood_group']} | Role: " . ucfirst($user['role']);
                $user['url'] = APP_URL . "profile/{$user['id']}";
                $filteredUsers[] = $user;
            }
        }
        
        return $filteredUsers;
    }
    
    /**
     * Search donations
     */
    private function searchDonations($searchTerm, $filters) {
        // Use the model's public methods instead of accessing private properties
        $allDonations = $this->donationModel->getAll();
        
        // Filter results based on search term and filters
        $filteredDonations = [];
        foreach ($allDonations as $donation) {
            $matches = false;
            
            // Check if search term matches
            if (stripos($donation['donor_name'], $searchTerm) !== false ||
                stripos($donation['location'], $searchTerm) !== false ||
                stripos($donation['contact_info'], $searchTerm) !== false) {
                $matches = true;
            }
            
            // Apply filters
            if ($matches && !empty($filters['blood_group']) && $donation['blood_group'] !== $filters['blood_group']) {
                $matches = false;
            }
            
            if ($matches && !empty($filters['location']) && stripos($donation['location'], $filters['location']) === false) {
                $matches = false;
            }
            
            if ($matches) {
                $donation['result_type'] = 'donation';
                $donation['title'] = "Donation: {$donation['blood_group']} - {$donation['donor_name']}";
                $donation['description'] = "Location: {$donation['location']} | Date: " . date('M j, Y', strtotime($donation['donation_date']));
                $donation['url'] = APP_URL . "donate";
                $filteredDonations[] = $donation;
            }
        }
        
        return $filteredDonations;
    }
    
    /**
     * Sort search results
     */
    private function sortResults($results, $sortBy, $sortOrder) {
        usort($results, function($a, $b) use ($sortBy, $sortOrder) {
            $order = $sortOrder === 'asc' ? 1 : -1;
            
            switch ($sortBy) {
                case 'date':
                    $aVal = strtotime($a['created_at']);
                    $bVal = strtotime($b['created_at']);
                    return ($aVal - $bVal) * $order;
                    
                case 'urgency':
                    // Only for blood requests
                    if ($a['result_type'] === 'blood_request' && $b['result_type'] === 'blood_request') {
                        $urgencyOrder = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
                        $aVal = $urgencyOrder[$a['urgency']] ?? 0;
                        $bVal = $urgencyOrder[$b['urgency']] ?? 0;
                        return ($aVal - $bVal) * $order;
                    }
                    return 0;
                    
                case 'relevance':
                default:
                    // Keep original order (most recent first)
                    return 0;
            }
        });
        
        return $results;
    }
    
    /**
     * Log search for analytics
     */
    private function logSearch($searchTerm, $filters, $resultsCount) {
        try {
            $sql = "INSERT INTO search_logs (user_id, search_term, filters, results_count, ip_address, user_agent) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
            $filtersJson = json_encode($filters);
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $this->bloodRequestModel->db->query($sql, [
                $userId, $searchTerm, $filtersJson, $resultsCount, $ipAddress, $userAgent
            ]);
        } catch (Exception $e) {
            // Log error but don't break search functionality
            error_log("Search logging error: " . $e->getMessage());
        }
    }
    
    /**
     * Get search suggestions (AJAX endpoint)
     */
    public function getSuggestions() {
        $searchTerm = $_GET['q'] ?? '';
        
        if (strlen($searchTerm) < 2) {
            $this->jsonResponse(['suggestions' => []]);
        }
        
        $suggestions = [];
        
        // Get blood group suggestions
        $bloodGroups = $this->bloodRequestModel->db->fetchAll(
            "SELECT DISTINCT blood_group FROM blood_requests WHERE blood_group LIKE ? LIMIT 5",
            ["%$searchTerm%"]
        );
        
        foreach ($bloodGroups as $bg) {
            $suggestions[] = [
                'type' => 'blood_group',
                'text' => $bg['blood_group'],
                'display' => "Blood Group: {$bg['blood_group']}"
            ];
        }
        
        // Get location suggestions
        $locations = $this->bloodRequestModel->db->fetchAll(
            "SELECT DISTINCT location FROM blood_requests WHERE location LIKE ? LIMIT 5",
            ["%$searchTerm%"]
        );
        
        foreach ($locations as $loc) {
            $suggestions[] = [
                'type' => 'location',
                'text' => $loc['location'],
                'display' => "Location: {$loc['location']}"
            ];
        }
        
        // Get user name suggestions
        $users = $this->userModel->db->fetchAll(
            "SELECT first_name, last_name FROM users WHERE first_name LIKE ? OR last_name LIKE ? LIMIT 5",
            ["%$searchTerm%", "%$searchTerm%"]
        );
        
        foreach ($users as $user) {
            $suggestions[] = [
                'type' => 'user',
                'text' => $user['first_name'] . ' ' . $user['last_name'],
                'display' => "User: {$user['first_name']} {$user['last_name']}"
            ];
        }
        
        $this->jsonResponse(['suggestions' => array_slice($suggestions, 0, 10)]);
    }
    
    /**
     * Get color for result type badge
     */
    public function getResultTypeColor($type) {
        switch ($type) {
            case 'blood_request': return 'danger';
            case 'user': return 'primary';
            case 'donation': return 'success';
            default: return 'secondary';
        }
    }
    
    /**
     * Helper method to send JSON response
     */
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
