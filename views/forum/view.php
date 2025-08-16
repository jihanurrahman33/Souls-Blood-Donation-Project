<?php $pageTitle = htmlspecialchars($post['title']); ?>
<?php include 'views/layout/header.php'; ?>

<div class="row">
    <div class="col-lg-8">
        <!-- Post Content -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-1"><?= htmlspecialchars($post['title']) ?></h4>
                        <small>
                            <i class="fas fa-user me-1"></i>
                            <?= htmlspecialchars($post['username'] ?? 'Anonymous') ?>
                            <i class="fas fa-clock ms-2 me-1"></i>
                            <?= date('M j, Y \a\t g:i A', strtotime($post['created_at'])) ?>
                        </small>
                    </div>
                    <?php if (isLoggedIn() && ($post['user_id'] == $_SESSION['user_id'] || isAdmin())): ?>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="<?= APP_URL ?>forum/edit/<?= $post['id'] ?>">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" 
                                       onclick="deletePost(<?= $post['id'] ?>, '<?= htmlspecialchars($post['title']) ?>')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="post-content">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>
            </div>
        </div>
        
        <!-- Replies -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-comments me-2"></i>Replies (<?= count($replies) ?>)
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($replies)): ?>
                    <div class="replies-list">
                        <?php foreach ($replies as $reply): ?>
                            <div class="reply-item border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <strong class="me-2"><?= htmlspecialchars($reply['username'] ?? 'Anonymous') ?></strong>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('M j, Y \a\t g:i A', strtotime($reply['created_at'])) ?>
                                            </small>
                                        </div>
                                        <div class="reply-content">
                                            <?= nl2br(htmlspecialchars($reply['content'])) ?>
                                        </div>
                                    </div>
                                    <?php if (isLoggedIn() && ($reply['user_id'] == $_SESSION['user_id'] || isAdmin())): ?>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" 
                                                       onclick="deleteReply(<?= $reply['id'] ?>)">
                                                        <i class="fas fa-trash me-2"></i>Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-comments fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No replies yet. Be the first to respond!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Reply Form -->
        <?php if (isLoggedIn()): ?>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-reply me-2"></i>Add Reply
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= APP_URL ?>forum/view/<?= $post['id'] ?>" id="replyForm">
                        <div class="mb-3">
                            <label for="content" class="form-label">Your Reply</label>
                            <textarea class="form-control" id="content" name="content" rows="4" 
                                      placeholder="Share your thoughts..." maxlength="2000" required></textarea>
                            <div class="invalid-feedback" id="contentError"></div>
                            <small class="form-text text-muted">Maximum 2000 characters</small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="<?= APP_URL ?>forum" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Forum
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane me-2"></i>Post Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body text-center py-4">
                    <i class="fas fa-lock fa-2x text-muted mb-3"></i>
                    <h5>Login to Reply</h5>
                    <p class="text-muted">You need to be logged in to reply to this post.</p>
                    <a href="<?= APP_URL ?>auth/login" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Login Now
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="col-lg-4">
        <!-- Post Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Post Information
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <strong>Author:</strong> <?= htmlspecialchars($post['username'] ?? 'Anonymous') ?>
                    </li>
                    <li class="mb-2">
                        <strong>Posted:</strong> <?= date('M j, Y', strtotime($post['created_at'])) ?>
                    </li>
                    <li class="mb-2">
                        <strong>Replies:</strong> <?= count($replies) ?>
                    </li>
                    <?php if ($post['updated_at'] !== $post['created_at']): ?>
                        <li class="mb-0">
                            <strong>Updated:</strong> <?= date('M j, Y', strtotime($post['updated_at'])) ?>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= APP_URL ?>forum" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Forum
                    </a>
                    <a href="<?= APP_URL ?>forum/create" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Post
                    </a>
                    <a href="<?= APP_URL ?>donate" class="btn btn-outline-success">
                        <i class="fas fa-hand-holding-heart me-2"></i>Donate Blood
                    </a>
                    <a href="<?= APP_URL ?>request" class="btn btn-outline-warning">
                        <i class="fas fa-search me-2"></i>Request Blood
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const replyForm = document.getElementById('replyForm');
    const contentInput = document.getElementById('content');
    
    if (replyForm) {
        // Character counter
        function updateCharCount(input, maxLength) {
            const remaining = maxLength - input.value.length;
            const counter = input.parentNode.querySelector('.form-text');
            if (counter) {
                counter.textContent = `${remaining} characters remaining`;
                if (remaining < 100) {
                    counter.classList.add('text-warning');
                } else {
                    counter.classList.remove('text-warning');
                }
            }
        }
        
        contentInput.addEventListener('input', function() {
            updateCharCount(this, 2000);
        });
        
        // Form validation
        replyForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            const content = contentInput.value.trim();
            if (!content) {
                showError('content', 'Reply content is required');
                isValid = false;
            } else if (content.length < 5) {
                showError('content', 'Reply must be at least 5 characters long');
                isValid = false;
            } else if (content.length > 2000) {
                showError('content', 'Reply is too long');
                isValid = false;
            } else {
                clearError('content');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        function showError(fieldId, message) {
            const input = document.getElementById(fieldId);
            input.classList.add('is-invalid');
            const errorElement = input.parentNode.querySelector('.invalid-feedback');
            if (errorElement) {
                errorElement.textContent = message;
            }
        }
        
        function clearError(fieldId) {
            const input = document.getElementById(fieldId);
            input.classList.remove('is-invalid');
            const errorElement = input.parentNode.querySelector('.invalid-feedback');
            if (errorElement) {
                errorElement.textContent = '';
            }
        }
        
        // Initialize character counter
        updateCharCount(contentInput, 2000);
    }
});

function deletePost(postId, postTitle) {
    if (confirm(`Are you sure you want to delete the post "${postTitle}"?`)) {
        window.location.href = '<?= APP_URL ?>forum/delete/' + postId;
    }
}

function deleteReply(replyId) {
    if (confirm('Are you sure you want to delete this reply?')) {
        // You would need to implement a delete reply endpoint
        alert('Delete reply functionality would be implemented here');
    }
}
</script>

<?php include 'views/layout/footer.php'; ?> 