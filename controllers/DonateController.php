<?php
require_once 'models/Donation.php';

class DonateController {
    private $donationModel;

    public function __construct() {
        $this->donationModel = new Donation();
    }

    public function index() {
        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id' => $_SESSION['user_id'] ?? null,
                'donor_name' => sanitize($_POST['donor_name'] ?? ''),
                'blood_group' => sanitize($_POST['blood_group'] ?? ''),
                'location' => sanitize($_POST['location'] ?? ''),
                'contact_info' => sanitize($_POST['contact_info'] ?? ''),
                'donation_date' => sanitize($_POST['donation_date'] ?? '')
            ];

            $errors = [];

            // Validate input
            if (empty($data['donor_name'])) $errors[] = 'Donor name is required';
            if (empty($data['blood_group'])) $errors[] = 'Blood group is required';
            if (empty($data['location'])) $errors[] = 'Location is required';
            if (empty($data['contact_info'])) $errors[] = 'Contact information is required';
            if (empty($data['donation_date'])) $errors[] = 'Donation date is required';

            // Validate blood group
            $validBloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
            if (!empty($data['blood_group']) && !in_array($data['blood_group'], $validBloodGroups)) {
                $errors[] = 'Invalid blood group';
            }

            // Validate date format
            if (!empty($data['donation_date'])) {
                $date = DateTime::createFromFormat('Y-m-d', $data['donation_date']);
                if (!$date || $date->format('Y-m-d') !== $data['donation_date']) {
                    $errors[] = 'Invalid date format';
                }
            }

            if (empty($errors)) {
                try {
                    $this->donationModel->create($data);
                    $_SESSION['success'] = 'Blood donation request submitted successfully!';
                    redirect('donate'); // Safe redirect, no output before this
                } catch (Exception $e) {
                    $errors[] = 'Failed to submit donation request. Please try again.';
                }
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = $data;
            }
        }

        // Get user donations
        $userDonations = [];
        if (isLoggedIn()) {
            $userDonations = $this->donationModel->getByUserId($_SESSION['user_id'], 10);
        }

        // Show view (view file must not output anything before this controller completes)
        include 'views/donate.php';
    }
}
