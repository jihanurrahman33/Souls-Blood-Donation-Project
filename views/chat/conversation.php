<?php include 'views/layout/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar with conversations -->
        <div class="col-md-4 col-lg-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Chat
                        <a href="<?= APP_URL ?>chat" class="btn btn-sm btn-outline-light float-end">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="p-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-history me-1"></i>Recent Conversations
                        </h6>
                        <div id="conversationsList">
                            <?php if (empty($conversations)): ?>
                                <p class="text-muted small mb-0">No conversations yet</p>
                            <?php else: ?>
                                <?php foreach ($conversations as $conv): ?>
                                    <div class="conversation-item p-2 border-bottom <?= $conv['other_user_id'] == $otherUser['id'] ? 'bg-light' : '' ?>" data-user-id="<?= $conv['other_user_id'] ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                <?= strtoupper(substr($conv['first_name'], 0, 1)) ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold small"><?= htmlspecialchars($conv['first_name'] . ' ' . $conv['last_name']) ?></div>
                                                <div class="text-muted small text-truncate">
                                                    <?= htmlspecialchars($conv['last_message'] ?? 'No messages yet') ?>
                                                </div>
                                                <div class="text-muted small">
                                                    <?= $conv['last_message_time'] ? date('M j, g:i A', strtotime($conv['last_message_time'])) : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="col-md-8 col-lg-9">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                            <?= strtoupper(substr($otherUser['first_name'], 0, 1)) ?>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-0"><?= htmlspecialchars($otherUser['first_name'] . ' ' . $otherUser['last_name']) ?></h5>
                            <small class="text-muted">@<?= htmlspecialchars($otherUser['username']) ?></small>
                        </div>
                        <div class="text-muted">
                            <small id="userStatus">
                                <i class="fas fa-circle text-success me-1"></i>Online
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Messages Container -->
                <div class="card-body p-0" style="height: 400px; overflow-y: auto;" id="messagesContainer">
                    <div class="p-3" id="messagesList">
                        <?php if (empty($messages)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                                <p class="text-muted">No messages yet. Start the conversation!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach (array_reverse($messages) as $message): ?>
                                <div class="message-item mb-3 <?= $message['sender_id'] == $_SESSION['user_id'] ? 'text-end' : 'text-start' ?>">
                                    <div class="d-inline-block">
                                        <div class="message-bubble <?= $message['sender_id'] == $_SESSION['user_id'] ? 'bg-primary text-white' : 'bg-light' ?> p-2 rounded">
                                            <div class="message-text"><?= htmlspecialchars($message['message']) ?></div>
                                            <small class="message-time <?= $message['sender_id'] == $_SESSION['user_id'] ? 'text-white-50' : 'text-muted' ?>">
                                                <?= date('g:i A', strtotime($message['created_at'])) ?>
                                            </small>
                                        </div>
                                        <?php if ($message['sender_id'] != $_SESSION['user_id']): ?>
                                            <div class="message-sender small text-muted mt-1">
                                                <?= htmlspecialchars($message['first_name'] . ' ' . $message['last_name']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Message Input -->
                <div class="card-footer">
                    <form id="messageForm" class="d-flex">
                        <input type="text" class="form-control me-2" id="messageInput" placeholder="Type your message..." maxlength="500">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat JavaScript -->
<script>
let lastMessageId = <?= empty($messages) ? 0 : max(array_column($messages, 'id')) ?>;
let otherUserId = <?= $otherUser['id'] ?>;
let currentUserId = <?= $_SESSION['user_id'] ?>;

document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');

    // Scroll to bottom of messages
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Handle conversation item clicks
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const userId = this.dataset.userId;
            if (userId != otherUserId) {
                window.location.href = '<?= APP_URL ?>chat/conversation/' + userId;
            }
        });
    });

    // Handle message form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    // Handle Enter key in message input
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Start real-time updates
    setInterval(checkNewMessages, 3000);
    setInterval(updateActivity, 30000);
});

function sendMessage() {
    const messageInput = document.getElementById('messageInput');
    const message = messageInput.value.trim();
    
    if (!message) return;

    // Disable input and show loading
    messageInput.disabled = true;
    
    fetch('<?= APP_URL ?>chat/sendMessage', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            receiver_id: otherUserId,
            message: message,
            message_type: 'text'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            // Add message to UI immediately
            addMessageToUI({
                id: Date.now(), // Temporary ID
                sender_id: currentUserId,
                message: message,
                created_at: new Date().toISOString(),
                first_name: '<?= $_SESSION['first_name'] ?>',
                last_name: '<?= $_SESSION['last_name'] ?>'
            });
        } else {
            alert('Failed to send message: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    })
    .finally(() => {
        messageInput.disabled = false;
        messageInput.focus();
    });
}

function checkNewMessages() {
    fetch(`<?= APP_URL ?>chat/getNewMessages?other_user_id=${otherUserId}&last_message_id=${lastMessageId}`)
        .then(response => response.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(message => {
                    addMessageToUI(message);
                    lastMessageId = Math.max(lastMessageId, message.id);
                });
            }
        })
        .catch(error => {
            console.error('Error checking new messages:', error);
        });
}

function addMessageToUI(message) {
    const messagesList = document.getElementById('messagesList');
    const messagesContainer = document.getElementById('messagesContainer');
    const isOwnMessage = message.sender_id == currentUserId;
    
    const messageHtml = `
        <div class="message-item mb-3 ${isOwnMessage ? 'text-end' : 'text-start'}">
            <div class="d-inline-block">
                <div class="message-bubble ${isOwnMessage ? 'bg-primary text-white' : 'bg-light'} p-2 rounded">
                    <div class="message-text">${escapeHtml(message.message)}</div>
                    <small class="message-time ${isOwnMessage ? 'text-white-50' : 'text-muted'}">
                        ${formatTime(message.created_at)}
                    </small>
                </div>
                ${!isOwnMessage ? `<div class="message-sender small text-muted mt-1">${escapeHtml(message.first_name + ' ' + message.last_name)}</div>` : ''}
            </div>
        </div>
    `;
    
    messagesList.insertAdjacentHTML('beforeend', messageHtml);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function updateActivity() {
    fetch('<?= APP_URL ?>chat/updateActivity', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString('en-US', { 
        hour: 'numeric', 
        minute: '2-digit',
        hour12: true 
    });
}
</script>

<style>
.message-bubble {
    max-width: 70%;
    word-wrap: break-word;
}

.message-item {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.avatar-md {
    width: 48px;
    height: 48px;
    font-size: 18px;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}

.conversation-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.conversation-item:hover {
    background-color: #f8f9fa;
}

.conversation-item.active {
    background-color: #e9ecef;
}

@media (max-width: 768px) {
    .col-md-4 {
        margin-bottom: 1rem;
    }
    
    .message-bubble {
        max-width: 85%;
    }
}
</style>

<?php include 'views/layout/footer.php'; ?>
