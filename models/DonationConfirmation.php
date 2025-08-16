<?php
require_once 'config/database.php';

class DonationConfirmation {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find confirmation record by request ID and donor ID
     */
    public function findByRequestAndDonor($requestId, $donorId) {
        return $this->db->fetch(
            "SELECT * FROM donation_confirmations 
             WHERE request_id = ? AND donor_id = ?", 
            [$requestId, $donorId]
        );
    }

    /**
     * Create a new donation confirmation record
     */
    public function create($requestId, $donorId, $recipientId) {
        $sql = "INSERT INTO donation_confirmations 
                (request_id, donor_id, recipient_id) 
                VALUES (?, ?, ?)";
        
        return $this->db->query($sql, [$requestId, $donorId, $recipientId]);
    }

    /**
     * Mark donor as confirmed
     */
    public function confirmDonor($id) {
        return $this->db->query(
            "UPDATE donation_confirmations 
             SET donor_confirmed = 1 
             WHERE id = ?", 
            [$id]
        );
    }

    /**
     * Mark recipient as confirmed
     */
    public function confirmRecipient($id) {
        return $this->db->query(
            "UPDATE donation_confirmations 
             SET recipient_confirmed = 1 
             WHERE id = ?", 
            [$id]
        );
    }

    /**
     * Check if both donor and recipient have confirmed
     */
    public function bothConfirmed($id) {
        $result = $this->db->fetch(
            "SELECT donor_confirmed, recipient_confirmed 
             FROM donation_confirmations 
             WHERE id = ?", 
            [$id]
        );
        
        return $result && $result['donor_confirmed'] == 1 && $result['recipient_confirmed'] == 1;
    }

    /**
     * Get confirmation by ID
     */
    public function findById($id) {
        return $this->db->fetch(
            "SELECT * FROM donation_confirmations WHERE id = ?", 
            [$id]
        );
    }

    /**
     * Get all confirmations for a specific request
     */
    public function getByRequestId($requestId) {
        return $this->db->fetchAll(
            "SELECT dc.*, 
                    d.username as donor_username, d.first_name as donor_first_name, d.last_name as donor_last_name,
                    r.username as recipient_username, r.first_name as recipient_first_name, r.last_name as recipient_last_name
             FROM donation_confirmations dc
             LEFT JOIN users d ON dc.donor_id = d.id
             LEFT JOIN users r ON dc.recipient_id = r.id
             WHERE dc.request_id = ?
             ORDER BY dc.created_at DESC", 
            [$requestId]
        );
    }

    /**
     * Get all confirmations for a specific donor
     */
    public function getByDonorId($donorId) {
        return $this->db->fetchAll(
            "SELECT dc.*, 
                    br.requester_name, br.blood_group, br.urgency, br.status as request_status,
                    r.username as recipient_username, r.first_name as recipient_first_name, r.last_name as recipient_last_name
             FROM donation_confirmations dc
             LEFT JOIN blood_requests br ON dc.request_id = br.id
             LEFT JOIN users r ON dc.recipient_id = r.id
             WHERE dc.donor_id = ?
             ORDER BY dc.created_at DESC", 
            [$donorId]
        );
    }

    /**
     * Get all confirmations for a specific recipient
     */
    public function getByRecipientId($recipientId) {
        return $this->db->fetchAll(
            "SELECT dc.*, 
                    br.requester_name, br.blood_group, br.urgency, br.status as request_status,
                    d.username as donor_username, d.first_name as donor_first_name, d.last_name as donor_last_name
             FROM donation_confirmations dc
             LEFT JOIN blood_requests br ON dc.request_id = br.id
             LEFT JOIN users d ON dc.donor_id = d.id
             WHERE dc.recipient_id = ?
             ORDER BY dc.created_at DESC", 
            [$recipientId]
        );
    }

    /**
     * Delete confirmation record
     */
    public function delete($id) {
        return $this->db->query(
            "DELETE FROM donation_confirmations WHERE id = ?", 
            [$id]
        );
    }

    /**
     * Count total confirmations
     */
    public function count() {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM donation_confirmations");
        return $result['count'];
    }

    /**
     * Count completed confirmations (both confirmed)
     */
    public function countCompleted() {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count 
             FROM donation_confirmations 
             WHERE donor_confirmed = 1 AND recipient_confirmed = 1"
        );
        return $result['count'];
    }
}
