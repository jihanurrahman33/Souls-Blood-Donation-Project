<?php
require_once 'models/ChatMessage.php';
require_once 'models/User.php';

class ChatController {
    private $chatModel;
    private $userModel;
    
    public function __construct() {
        $this->chatModel = new ChatMessage();
        $this->userModel = new User();
    }
    
    /**
     * Show chat interface
     */
    public function index() {
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Please log in to access chat.';
            redirect('auth/login');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Update user's last activity
        $this->chatModel->updateLastActivity($userId);
        
        // Get recent conversations
        $conversations = $this->chatModel->getRecentConversations($userId);
        
        // Get online users
        $onlineUsers = $this->chatModel->getOnlineUsers($userId);
        
        // Get unread count
        $unreadCount = $this->chatModel->getUnreadCount($userId);
        
        include 'views/chat/index.php';
    }
    
    /**
     * Show conversation with specific user
     */
    public function conversation($otherUserId) {
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Please log in to access chat.';
            redirect('auth/login');
        }
        
        $currentUserId = $_SESSION['user_id'];
        
        // Validate other user exists
        $otherUser = $this->userModel->findById($otherUserId);
        if (!$otherUser) {
            $_SESSION['error'] = 'User not found.';
            redirect('chat');
        }
        
        // Update user's last activity
        $this->chatModel->updateLastActivity($currentUserId);
        
        // Mark messages as read
        $this->chatModel->markAsRead($otherUserId, $currentUserId);
        
        // Get conversation messages
        $messages = $this->chatModel->getConversation($currentUserId, $otherUserId);
        
        // Get recent conversations for sidebar
        $conversations = $this->chatModel->getRecentConversations($currentUserId);
        
        include 'views/chat/conversation.php';
    }
    
    /**
     * Send a message (AJAX endpoint)
     */
    public function sendMessage() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Authentication required'], 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $data = [
            'sender_id' => $_SESSION['user_id'],
            'receiver_id' => (int)($input['receiver_id'] ?? 0),
            'message' => sanitize($input['message'] ?? ''),
            'message_type' => sanitize($input['message_type'] ?? 'text')
        ];
        
        // Validation
        if (empty($data['message'])) {
            $this->jsonResponse(['error' => 'Message cannot be empty'], 400);
        }
        
        if (empty($data['receiver_id'])) {
            $this->jsonResponse(['error' => 'Receiver ID is required'], 400);
        }
        
        // Check if receiver exists
        $receiver = $this->userModel->findById($data['receiver_id']);
        if (!$receiver) {
            $this->jsonResponse(['error' => 'Receiver not found'], 404);
        }
        
        try {
            $this->chatModel->create($data);
            $this->jsonResponse(['success' => true, 'message' => 'Message sent successfully']);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to send message'], 500);
        }
    }
    
    /**
     * Get new messages (AJAX endpoint for real-time updates)
     */
    public function getNewMessages() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Authentication required'], 401);
        }
        
        $currentUserId = $_SESSION['user_id'];
        $otherUserId = (int)($_GET['other_user_id'] ?? 0);
        $lastMessageId = (int)($_GET['last_message_id'] ?? 0);
        
        if (empty($otherUserId)) {
            $this->jsonResponse(['error' => 'Other user ID is required'], 400);
        }
        
        try {
            // Get messages newer than last_message_id
            $messages = $this->chatModel->getNewMessages($currentUserId, $otherUserId, $lastMessageId);
            
            // Mark messages as read
            if (!empty($messages)) {
                $this->chatModel->markAsRead($otherUserId, $currentUserId);
            }
            
            $this->jsonResponse(['messages' => $messages]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to get messages'], 500);
        }
    }
    
    /**
     * Get unread count (AJAX endpoint)
     */
    public function getUnreadCount() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Authentication required'], 401);
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $count = $this->chatModel->getUnreadCount($userId);
            $this->jsonResponse(['unread_count' => $count]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to get unread count'], 500);
        }
    }
    
    /**
     * Get online users (AJAX endpoint)
     */
    public function getOnlineUsers() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Authentication required'], 401);
        }
        
        $currentUserId = $_SESSION['user_id'];
        
        try {
            $onlineUsers = $this->chatModel->getOnlineUsers($currentUserId);
            $this->jsonResponse(['online_users' => $onlineUsers]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to get online users'], 500);
        }
    }
    
    /**
     * Delete a message (AJAX endpoint)
     */
    public function deleteMessage() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Authentication required'], 401);
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $messageId = (int)($input['message_id'] ?? 0);
        $senderId = $_SESSION['user_id'];
        
        if (empty($messageId)) {
            $this->jsonResponse(['error' => 'Message ID is required'], 400);
        }
        
        try {
            $result = $this->chatModel->delete($messageId, $senderId);
            if ($result) {
                $this->jsonResponse(['success' => true, 'message' => 'Message deleted successfully']);
            } else {
                $this->jsonResponse(['error' => 'Message not found or cannot be deleted'], 404);
            }
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to delete message'], 500);
        }
    }
    
    /**
     * Update user activity (AJAX endpoint for keeping user online)
     */
    public function updateActivity() {
        if (!isLoggedIn()) {
            $this->jsonResponse(['error' => 'Authentication required'], 401);
        }
        
        $userId = $_SESSION['user_id'];
        
        try {
            $this->chatModel->updateLastActivity($userId);
            $this->jsonResponse(['success' => true]);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => 'Failed to update activity'], 500);
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
