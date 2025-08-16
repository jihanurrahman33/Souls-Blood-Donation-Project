<?php
require_once 'config/database.php';

class ForumPost {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO forum_posts (user_id, title, content, created_at) 
                VALUES (?, ?, ?, NOW())";
        
        return $this->db->query($sql, [
            $data['user_id'],
            $data['title'],
            $data['content']
        ]);
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT fp.*, u.username 
                FROM forum_posts fp 
                JOIN users u ON fp.user_id = u.id 
                ORDER BY fp.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$limit, $offset]);
        }

        return $this->db->fetchAll($sql);
    }

    public function getRecentPosts($limit = 5) {
        $sql = "SELECT fp.*, u.username 
                FROM forum_posts fp 
                LEFT JOIN users u ON fp.user_id = u.id 
                ORDER BY fp.created_at DESC 
                LIMIT ?";
        return $this->db->fetchAll($sql, [$limit]);
    }
    

    public function findById($id) {
        return $this->db->fetch("SELECT * FROM forum_posts WHERE id = ?", [$id]);
    }

    public function update($id, $data) {
        $sql = "UPDATE forum_posts SET title = ?, content = ? WHERE id = ?";
        return $this->db->query($sql, [$data['title'], $data['content'], $id]);
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM forum_posts WHERE id = ?", [$id]);
    }

    public function count() {
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM forum_posts");
        return $result['count'];
    }

    public function getByUserId($userId) {
        return $this->db->fetchAll("SELECT * FROM forum_posts WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
    }

    public function search($keyword) {
        $like = '%' . $keyword . '%';
        return $this->db->fetchAll("SELECT * FROM forum_posts WHERE title LIKE ? OR content LIKE ?", [$like, $like]);
    }
}
