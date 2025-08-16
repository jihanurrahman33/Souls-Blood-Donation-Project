<?php
require_once 'config/database.php';

class ChatMessage {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new chat message
     */
    public function create($data) {
        $sql = "INSERT INTO chat_messages (sender_id, receiver_id, message, message_type) 
                VALUES (?, ?, ?, ?)";
        
        return $this->db->query($sql, [
            $data['sender_id'],
            $data['receiver_id'],
            $data['message'],
            $data['message_type'] ?? 'text'
        ]);
    }
    
    /**
     * Get messages between two users
     */
    public function getConversation($user1Id, $user2Id, $limit = 50, $offset = 0) {
        $sql = "SELECT cm.*, u.username, u.first_name, u.last_name 
                FROM chat_messages cm 
                JOIN users u ON cm.sender_id = u.id 
                WHERE (cm.sender_id = ? AND cm.receiver_id = ?) 
                   OR (cm.sender_id = ? AND cm.receiver_id = ?) 
                ORDER BY cm.created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$user1Id, $user2Id, $user2Id, $user1Id, $limit, $offset]);
    }
    
    /**
     * Get new messages (for real-time updates)
     */
    public function getNewMessages($user1Id, $user2Id, $lastMessageId) {
        $sql = "SELECT cm.*, u.username, u.first_name, u.last_name 
                FROM chat_messages cm 
                JOIN users u ON cm.sender_id = u.id 
                WHERE ((cm.sender_id = ? AND cm.receiver_id = ?) 
                   OR (cm.sender_id = ? AND cm.receiver_id = ?)) 
                   AND cm.id > ? 
                ORDER BY cm.created_at ASC";
        
        return $this->db->fetchAll($sql, [$user1Id, $user2Id, $user2Id, $user1Id, $lastMessageId]);
    }
    
    /**
     * Get recent conversations for a user
     */
    public function getRecentConversations($userId, $limit = 10) {
        $sql = "SELECT DISTINCT 
                    CASE 
                        WHEN cm.sender_id = ? THEN cm.receiver_id 
                        ELSE cm.sender_id 
                    END as other_user_id,
                    u.username, u.first_name, u.last_name,
                    (SELECT message FROM chat_messages 
                     WHERE ((sender_id = ? AND receiver_id = u.id) 
                            OR (sender_id = u.id AND receiver_id = ?)) 
                     ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT created_at FROM chat_messages 
                     WHERE ((sender_id = ? AND receiver_id = u.id) 
                            OR (sender_id = u.id AND receiver_id = ?)) 
                     ORDER BY created_at DESC LIMIT 1) as last_message_time
                FROM chat_messages cm 
                JOIN users u ON (cm.sender_id = u.id OR cm.receiver_id = u.id) 
                WHERE (cm.sender_id = ? OR cm.receiver_id = ?) 
                  AND u.id != ? 
                ORDER BY last_message_time DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [
            $userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId, $limit
        ]);
    }
    
    /**
     * Get unread message count for a user
     */
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count 
                FROM chat_messages 
                WHERE receiver_id = ? AND is_read = 0";
        
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'];
    }
    
    /**
     * Mark messages as read
     */
    public function markAsRead($senderId, $receiverId) {
        $sql = "UPDATE chat_messages 
                SET is_read = 1 
                WHERE sender_id = ? AND receiver_id = ? AND is_read = 0";
        
        return $this->db->query($sql, [$senderId, $receiverId]);
    }
    
    /**
     * Get message by ID
     */
    public function findById($id) {
        return $this->db->fetch("SELECT * FROM chat_messages WHERE id = ?", [$id]);
    }
    
    /**
     * Delete a message (only by sender)
     */
    public function delete($id, $senderId) {
        return $this->db->query(
            "DELETE FROM chat_messages WHERE id = ? AND sender_id = ?", 
            [$id, $senderId]
        );
    }
    
    /**
     * Get online users (users active in last 5 minutes)
     */
    public function getOnlineUsers($currentUserId) {
        $sql = "SELECT id, username, first_name, last_name, last_activity 
                FROM users 
                WHERE last_activity > DATE_SUB(NOW(), INTERVAL 5 MINUTE) 
                  AND id != ? 
                ORDER BY last_activity DESC";
        
        return $this->db->fetchAll($sql, [$currentUserId]);
    }
    
    /**
     * Update user's last activity
     */
    public function updateLastActivity($userId) {
        return $this->db->query(
            "UPDATE users SET last_activity = NOW() WHERE id = ?", 
            [$userId]
        );
    }
}
?>
