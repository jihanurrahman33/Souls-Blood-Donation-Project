<?php
require_once 'config/database.php';

class Donation {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO donations (user_id, donor_name, blood_group, location, contact_info, donation_date) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->query($sql, [
            $data['user_id'] ?? null,
            $data['donor_name'],
            $data['blood_group'],
            $data['location'],
            $data['contact_info'],
            $data['donation_date']
        ]);
    }
    
    public function findById($id) {
        return $this->db->fetch("SELECT * FROM donations WHERE id = ?", [$id]);
    }
    
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT d.*, u.username, u.first_name, u.last_name 
                FROM donations d 
                LEFT JOIN users u ON d.user_id = u.id 
                ORDER BY d.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$limit, $offset]);
        }
        
        return $this->db->fetchAll($sql);
    }
    
    public function getByUserId($userId, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM donations WHERE user_id = ? ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$userId, $limit, $offset]);
        }
        
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE donations SET 
                donor_name = ?, 
                blood_group = ?, 
                location = ?, 
                contact_info = ?, 
                donation_date = ?, 
                status = ? 
                WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['donor_name'],
            $data['blood_group'],
            $data['location'],
            $data['contact_info'],
            $data['donation_date'],
            $data['status'] ?? 'pending',
            $id
        ]);
    }
    
    public function updateStatus($id, $status) {
        return $this->db->query("UPDATE donations SET status = ? WHERE id = ?", [$status, $id]);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM donations WHERE id = ?", [$id]);
    }
    
    public function count() {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM donations");
        return $result['count'];
    }
    
    public function countByStatus($status) {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM donations WHERE status = ?", [$status]);
        return $result['count'];
    }
    
    public function getByBloodGroup($bloodGroup) {
        return $this->db->fetchAll("SELECT * FROM donations WHERE blood_group = ? AND status = 'approved'", [$bloodGroup]);
    }
    
    public function getRecentDonations($limit = 5) {
        return $this->db->fetchAll("SELECT * FROM donations ORDER BY created_at DESC LIMIT ?", [$limit]);
    }
} 