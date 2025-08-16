<?php include 'views/layout/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar with conversations and online users -->
        <div class="col-md-4 col-lg-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Chat
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger ms-2"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Online Users -->
                    <div class="p-3 border-bottom">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-circle text-success me-1"></i>Online Users
                        </h6>
                        <div id="onlineUsersList">
                            <?php if (empty($onlineUsers)): ?>
                                <p class="text-muted small mb-0">No users online</p>
                            <?php else: ?>
                                <?php foreach ($onlineUsers as $user): ?>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                                            <div class="text-muted small">@<?= htmlspecialchars($user['username']) ?></div>
                                        </div>
                                        <a href="<?= APP_URL ?>chat/conversation/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-comment"></i>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Recent Conversations -->
                    <div class="p-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-history me-1"></i>Recent Conversations
                        </h6>
                        <div id="conversationsList">
                            <?php if (empty($conversations)): ?>
                                <p class="text-muted small mb-0">No conversations yet</p>
                            <?php else: ?>
                                <?php foreach ($conversations as $conv): ?>
                                    <div class="conversation-item p-2 border-bottom" data-user-id="<?= $conv['other_user_id'] ?>">
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
                    <div class="text-center">
                        <h5 class="mb-0 text-muted">
                            <i class="fas fa-comments me-2"></i>Select a conversation to start chatting
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Welcome to Chat</h5>
                        <p class="text-muted">Choose a user from the sidebar to start a conversation</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle conversation item clicks
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const userId = this.dataset.userId;
            window.location.href = '<?= APP_URL ?>chat/conversation/' + userId;
        });
    });

    // Update activity every 30 seconds
    setInterval(updateActivity, 30000);

    // Update online users every 60 seconds
    setInterval(updateOnlineUsers, 60000);

    // Update conversations every 30 seconds
    setInterval(updateConversations, 30000);
});

function updateActivity() {
    fetch('<?= APP_URL ?>chat/updateActivity', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    });
}

function updateOnlineUsers() {
    fetch('<?= APP_URL ?>chat/getOnlineUsers')
        .then(response => response.json())
        .then(data => {
            if (data.online_users) {
                updateOnlineUsersList(data.online_users);
            }
        });
}

function updateConversations() {
    // This would typically fetch updated conversations
    // For now, we'll just update the unread count
    fetch('<?= APP_URL ?>chat/getUnreadCount')
        .then(response => response.json())
        .then(data => {
            updateUnreadBadge(data.unread_count);
        });
}

function updateOnlineUsersList(users) {
    const container = document.getElementById('onlineUsersList');
    if (users.length === 0) {
        container.innerHTML = '<p class="text-muted small mb-0">No users online</p>';
        return;
    }

    let html = '';
    users.forEach(user => {
        html += `
            <div class="d-flex align-items-center mb-2">
                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                    ${user.first_name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold small">${user.first_name} ${user.last_name}</div>
                    <div class="text-muted small">@${user.username}</div>
                </div>
                <a href="<?= APP_URL ?>chat/conversation/${user.id}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-comment"></i>
                </a>
            </div>
        `;
    });
    container.innerHTML = html;
}

function updateUnreadBadge(count) {
    const badge = document.querySelector('.card-header .badge');
    if (count > 0) {
        if (badge) {
            badge.textContent = count;
        } else {
            const header = document.querySelector('.card-header h5');
            header.innerHTML += `<span class="badge bg-danger ms-2">${count}</span>`;
        }
    } else if (badge) {
        badge.remove();
    }
}
</script>

<style>
.conversation-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.conversation-item:hover {
    background-color: #f8f9fa;
}

.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
}

@media (max-width: 768px) {
    .col-md-4 {
        margin-bottom: 1rem;
    }
}
</style>

<?php include 'views/layout/footer.php'; ?>
