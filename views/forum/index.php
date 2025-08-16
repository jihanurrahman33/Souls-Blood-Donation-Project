<?php $pageTitle = 'Forum'; ?>
<?php include 'views/layout/header.php'; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="fas fa-comments me-2"></i>Community Forum
            </h2>
            <?php if (isLoggedIn()): ?>
                <a href="<?= APP_URL ?>forum/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Post
                </a>
            <?php else: ?>
                <a href="<?= APP_URL ?>auth/login" class="btn btn-outline-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Login to Post
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($posts)): ?>
            <div class="card border-0 shadow-sm">
                <div class="list-group list-group-flush">
                    <?php foreach ($posts as $post): ?>
                        <div class="list-group-item border-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">
                                        <a href="<?= APP_URL ?>forum/view/<?= $post['id'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($post['title']) ?>
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-2">
                                        <?= htmlspecialchars(substr($post['content'], 0, 150)) ?>
                                        <?= strlen($post['content']) > 150 ? '...' : '' ?>
                                    </p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <span>
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($post['username'] ?? 'Anonymous') ?>
                                        </span>
                                        <span class="mx-2">•</span>
                                        <span>
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('M j, Y', strtotime($post['created_at'])) ?>
                                        </span>
                                        <span class="mx-2">•</span>
                                        <span>
                                            <i class="fas fa-comments me-1"></i>
                                            <?= htmlspecialchars($post['reply_count'] ?? 0) ?> replies
                                        </span>
                                    </div>
                                </div>

                                <?php if (isLoggedIn() && ($post['user_id'] == ($_SESSION['user_id'] ?? null) || isAdmin())): ?>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
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
                                                   onclick="deletePost(<?= $post['id'] ?>, '<?= htmlspecialchars(addslashes($post['title'])) ?>')">
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
            </div>

            <!-- Pagination -->
            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <nav aria-label="Forum pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if (isset($page) && $page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php
                        $start = max(1, ($page ?? 1) - 2);
                        $end = min($totalPages, ($page ?? 1) + 2);
                        for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= ($i == ($page ?? 1)) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if (($page ?? 1) < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Posts Yet</h4>
                    <p class="text-muted">Be the first to start a discussion!</p>
                    <?php if (isLoggedIn()): ?>
                        <a href="<?= APP_URL ?>forum/create" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Post
                        </a>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>auth/login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Login to Post
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-4">
        <!-- Forum Guidelines -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Forum Guidelines
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Be respectful to others</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Stay on topic</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>No spam or advertising</li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Share helpful information</li>
                    <li class="mb-0"><i class="fas fa-check text-success me-2"></i>Report inappropriate content</li>
                </ul>
            </div>
        </div>

        <!-- Quick Actions -->
        <?php if (isLoggedIn()): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
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
        <?php endif; ?>
    </div>
</div>

<script>
function deletePost(postId, postTitle) {
    if (confirm(`Are you sure you want to delete the post "${postTitle}"?`)) {
        window.location.href = '<?= APP_URL ?>forum/delete/' + postId;
    }
}
</script>

<?php include 'views/layout/footer.php'; ?>
