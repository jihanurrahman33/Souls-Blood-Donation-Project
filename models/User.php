<?php
require_once 'config/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        
        $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone, blood_group, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->query($sql, [
            $data['username'],
            $data['email'],
            $hashedPassword,
            $data['first_name'],
            $data['last_name'],
            $data['phone'] ?? null,
            $data['blood_group'] ?? null,
            $data['role'] ?? 'donor'
        ]);
    }
    
    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    public function findById($id) {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    public function findByUsername($username) {
        return $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE users SET 
                first_name = ?, 
                last_name = ?, 
                phone = ?, 
                blood_group = ?, 
                updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
        
        return $this->db->query($sql, [
            $data['first_name'],
            $data['last_name'],
            $data['phone'] ?? null,
            $data['blood_group'] ?? null,
            $id
        ]);
    }
    
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT id, username, email, first_name, last_name, phone, blood_group, role, created_at 
                FROM users ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$limit, $offset]);
        }
        
        return $this->db->fetchAll($sql);
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }
    
    public function count() {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM users");
        return $result['count'];
    }
    
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Get users by blood group for email notifications
     */
    public function getUsersByBloodGroup($bloodGroup) {
        return $this->db->fetchAll(
            "SELECT id, username, email, first_name, last_name, blood_group 
             FROM users 
             WHERE blood_group = ? AND email IS NOT NULL 
             ORDER BY created_at DESC", 
            [$bloodGroup]
        );
    }
}