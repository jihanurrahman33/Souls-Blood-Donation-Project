<?php
require_once 'models/BloodRequest.php';
require_once 'models/User.php';

class RequestController {
    private $bloodRequestModel;

    public function __construct() {
        $this->bloodRequestModel = new BloodRequest();
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $_SESSION['user_id'] ?? null,
                'requester_name' => sanitize($_POST['requester_name'] ?? ''),
                'blood_group' => sanitize($_POST['blood_group'] ?? ''),
                'location' => sanitize($_POST['location'] ?? ''),
                'contact_info' => sanitize($_POST['contact_info'] ?? ''),
                'urgency' => sanitize($_POST['urgency'] ?? 'medium'),
                'request_date' => sanitize($_POST['request_date'] ?? '')
            ];

            $errors = [];

            // Basic validation
            if (empty($data['requester_name'])) $errors[] = 'Requester name is required.';
            if (empty($data['blood_group'])) $errors[] = 'Blood group is required.';
            if (empty($data['location'])) $errors[] = 'Location is required.';
            if (empty($data['contact_info'])) $errors[] = 'Contact info is required.';
            if (empty($data['request_date'])) $errors[] = 'Request date is required.';

            // Validate blood group
            $validGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            if (!in_array($data['blood_group'], $validGroups)) {
                $errors[] = 'Invalid blood group.';
            }

            // Validate date format
            $date = DateTime::createFromFormat('Y-m-d', $data['request_date']);
            if (!$date || $date->format('Y-m-d') !== $data['request_date']) {
                $errors[] = 'Invalid request date format.';
            }

            // If no errors, insert the request
            if (empty($errors)) {
                try {
                    $this->bloodRequestModel->create($data);
                    
                    // Send email notifications
                    $this->sendEmailNotifications($data);
                    
                    $_SESSION['success'] = 'Blood request submitted successfully.';
                    redirect('request'); // No output before this
                } catch (Exception $e) {
                    $errors[] = 'Could not submit blood request. Please try again.';
                }
            }

            // If errors, save to session
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = $data;
            }
        }

        // Fetch current user's requests if logged in
        $userRequests = [];
        if (isLoggedIn()) {
            $userRequests = $this->bloodRequestModel->getByUserId($_SESSION['user_id'], 10);
        }

        // Fetch all pending requests for public display
        $pendingRequests = $this->bloodRequestModel->allPending();

        // Load view (must not send output early)
        include 'views/request.php';
    }
    
    public function showDetails($request_id) {
        // Require login
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Please log in to view request details.';
            redirect('auth/login');
        }
        
        // Validate request ID
        if (!$request_id || !is_numeric($request_id)) {
            $_SESSION['error'] = 'Invalid request ID.';
            redirect('request');
        }
        
        // Fetch request details
        $request = $this->bloodRequestModel->findById($request_id);
        
        if (!$request) {
            $_SESSION['error'] = 'Request not found.';
            redirect('request');
        }
        
        // Check if request is still pending
        if ($request['status'] !== 'pending') {
            $_SESSION['error'] = 'This request is no longer available for donation.';
            redirect('request');
        }
        
        // Fetch requester information
        $userModel = new User();
        $requester = null;
        
        if ($request['user_id']) {
            $requester = $userModel->findById($request['user_id']);
        }
        
        // Include the details view
        include 'views/request/details.php';
    }
    
    public function confirmDonation($request_id) {
        // Require login
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Please log in to confirm donation.';
            redirect('auth/login');
        }
        
        // Validate request ID
        if (!$request_id || !is_numeric($request_id)) {
            $_SESSION['error'] = 'Invalid request ID.';
            redirect('request');
        }
        
        // Fetch request details
        $request = $this->bloodRequestModel->findById($request_id);
        
        if (!$request) {
            $_SESSION['error'] = 'Request not found.';
            redirect('request');
        }
        
        // Get current user ID
        $currentUserId = $_SESSION['user_id'];
        $requesterId = $request['user_id'];
        
        // Load DonationConfirmation model
        require_once 'models/DonationConfirmation.php';
        $confirmationModel = new DonationConfirmation();
        
        // Check if this is an anonymous request (no user_id)
        if ($requesterId === null) {
            $_SESSION['error'] = 'Cannot confirm donation for anonymous requests. Please contact the requester directly.';
            redirect('request/details/' . $request_id);
        }
        
        // Check if user is the requester (recipient)
        if ($currentUserId == $requesterId) {
            // Requester confirming they received the donation
            if ($request['status'] !== 'in_progress') {
                $_SESSION['error'] = 'This request is not in progress.';
                redirect('request/details/' . $request_id);
            }
            
            // Find the confirmation record for this request
            $confirmations = $confirmationModel->getByRequestId($request_id);
            
            if (empty($confirmations)) {
                $_SESSION['error'] = 'No donation confirmation found for this request.';
                redirect('request/details/' . $request_id);
            }
            
            // Mark recipient as confirmed
            $confirmationModel->confirmRecipient($confirmations[0]['id']);
            
            // Check if both donor and recipient have confirmed
            if ($confirmationModel->bothConfirmed($confirmations[0]['id'])) {
                // Update request status to 'completed'
                $this->bloodRequestModel->updateStatus($request_id, 'completed');
                $_SESSION['success'] = 'Donation completed successfully! Thank you for confirming.';
            } else {
                $_SESSION['success'] = 'Recipient confirmation recorded. Waiting for donor confirmation.';
            }
            
        } else {
            // Donor confirming they will donate
            if ($request['status'] !== 'pending') {
                $_SESSION['error'] = 'This request is no longer available for donation.';
                redirect('request/details/' . $request_id);
            }
            
            // Check if user is trying to donate to their own request
            if ($currentUserId == $requesterId) {
                $_SESSION['error'] = 'You cannot donate to your own request.';
                redirect('request/details/' . $request_id);
            }
            
            // Check if confirmation already exists
            $existingConfirmation = $confirmationModel->findByRequestAndDonor($request_id, $currentUserId);
            
            if ($existingConfirmation) {
                // Update existing confirmation
                $confirmationModel->confirmDonor($existingConfirmation['id']);
                $_SESSION['success'] = 'Donation confirmation updated successfully.';
            } else {
                // Create new confirmation
                $confirmationModel->create($request_id, $currentUserId, $requesterId);
                $_SESSION['success'] = 'Donation confirmed successfully.';
            }
            
            // Update request status to 'in_progress'
            $this->bloodRequestModel->updateStatus($request_id, 'in_progress');
        }
        
        // Redirect back to request details
        redirect('request/details/' . $request_id);
    }
    
    /**
     * Send email notifications for new blood request
     */
    private function sendEmailNotifications($requestData) {
        try {
            require_once 'services/EmailService.php';
            require_once 'models/User.php';
            
            $emailService = new EmailService();
            $userModel = new User();
            
            // Get the created request with ID
            $recentRequests = $this->bloodRequestModel->getAll(1, 0);
            if (empty($recentRequests)) {
                return;
            }
            
            $request = $recentRequests[0];
            
            // Find users with matching blood group
            $matchingDonors = $userModel->getUsersByBloodGroup($request['blood_group']);
            
            if (!empty($matchingDonors)) {
                // Send email notification with correct parameters
                $emailService->sendBloodRequestNotification(
                    $request['blood_group'],
                    $request['location'],
                    $request['urgency']
                );
            }
            
        } catch (Exception $e) {
            // Log error but don't break the request creation
            error_log("Email notification error: " . $e->getMessage());
        }
    }
}
