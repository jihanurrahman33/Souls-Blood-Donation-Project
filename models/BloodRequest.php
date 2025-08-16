<?php
require_once 'config/database.php';

class BloodRequest {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO blood_requests 
                (user_id, requester_name, blood_group, location, contact_info, urgency, request_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        return $this->db->query($sql, [
            $data['user_id'] ?? null,
            $data['requester_name'],
            $data['blood_group'],
            $data['location'],
            $data['contact_info'],
            $data['urgency'] ?? 'medium',
            $data['request_date']
        ]);
    }

    public function findById($id) {
        return $this->db->fetch("SELECT * FROM blood_requests WHERE id = ?", [$id]);
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT br.*, u.username, u.first_name, u.last_name 
                FROM blood_requests br 
                LEFT JOIN users u ON br.user_id = u.id 
                ORDER BY br.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$limit, $offset]);
        }

        return $this->db->fetchAll($sql);
    }

    public function getByUserId($userId, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM blood_requests 
                WHERE user_id = ? 
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$userId, $limit, $offset]);
        }

        return $this->db->fetchAll($sql, [$userId]);
    }

    public function update($id, $data) {
        $sql = "UPDATE blood_requests SET 
                requester_name = ?, 
                blood_group = ?, 
                location = ?, 
                contact_info = ?, 
                urgency = ?, 
                request_date = ?, 
                status = ? 
                WHERE id = ?";

        return $this->db->query($sql, [
            $data['requester_name'],
            $data['blood_group'],
            $data['location'],
            $data['contact_info'],
            $data['urgency'] ?? 'medium',
            $data['request_date'],
            $data['status'] ?? 'pending',
            $id
        ]);
    }

    public function updateStatus($id, $status) {
        return $this->db->query("UPDATE blood_requests SET status = ? WHERE id = ?", [$status, $id]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM blood_requests WHERE id = ?", [$id]);
    }

    public function count() {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM blood_requests");
        return $result['count'];
    }

    public function countByStatus($status) {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM blood_requests WHERE status = ?", [$status]);
        return $result['count'];
    }

    public function countByUrgency($urgency) {
        $result = $this->db->fetch("SELECT COUNT(*) as count 
                                    FROM blood_requests 
                                    WHERE urgency = ? AND status = 'pending'", [$urgency]);
        return $result['count'];
    }

    public function getByBloodGroup($bloodGroup) {
        return $this->db->fetchAll("SELECT * FROM blood_requests 
                                    WHERE blood_group = ? AND status = 'pending'", [$bloodGroup]);
    }

    public function getUrgentRequests($limit = 5) {
        return $this->db->fetchAll("SELECT * FROM blood_requests 
                                    WHERE urgency IN ('high', 'critical') AND status = 'pending' 
                                    ORDER BY created_at DESC LIMIT ?", [$limit]);
    }

    public function getRecentRequests($limit = 5) {
        return $this->db->fetchAll("SELECT * FROM blood_requests 
                                    ORDER BY created_at DESC LIMIT ?", [$limit]);
    }

    /**
     * Get all pending blood requests
     */
    public function allPending() {
        return $this->db->fetchAll(
            "SELECT br.*, u.username, u.first_name, u.last_name 
             FROM blood_requests br 
             LEFT JOIN users u ON br.user_id = u.id 
             WHERE br.status = 'pending'
             ORDER BY br.created_at DESC"
        );
    }

    /**
     * Find matching requests for a specific blood group
     */
    public function findMatchingRequests($blood_group) {
        return $this->db->fetchAll(
            "SELECT br.*, u.username, u.first_name, u.last_name 
             FROM blood_requests br 
             LEFT JOIN users u ON br.user_id = u.id 
             WHERE br.blood_group = ? AND br.status = 'pending'
             ORDER BY br.urgency DESC, br.created_at DESC", 
            [$blood_group]
        );
    }
}
