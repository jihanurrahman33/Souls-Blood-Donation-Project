<?php $pageTitle = 'Admin Dashboard'; ?>
<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
        </h2>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-users fa-2x text-primary mb-3"></i>
                <h3 class="card-title text-primary"><?= number_format($stats['total_users']) ?></h3>
                <p class="card-text">Total Users</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-hand-holding-heart fa-2x text-success mb-3"></i>
                <h3 class="card-title text-success"><?= number_format($stats['total_donations']) ?></h3>
                <p class="card-text">Total Donations</p>
                <small class="text-muted"><?= $stats['pending_donations'] ?> pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-search fa-2x text-warning mb-3"></i>
                <h3 class="card-title text-warning"><?= number_format($stats['total_requests']) ?></h3>
                <p class="card-text">Total Requests</p>
                <small class="text-muted"><?= $stats['pending_requests'] ?> pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body">
                <i class="fas fa-comments fa-2x text-info mb-3"></i>
                <h3 class="card-title text-info"><?= number_format($stats['total_posts']) ?></h3>
                <p class="card-text">Forum Posts</p>
            </div>
        </div>
    </div>
</div>

<!-- Urgent Alerts -->
<?php if ($stats['urgent_requests'] > 0): ?>
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <h5 class="alert-heading">
        <i class="fas fa-exclamation-triangle me-2"></i>Urgent Attention Required
    </h5>
    <p class="mb-0">There are <?= $stats['urgent_requests'] ?> urgent blood requests that need immediate attention.</p>
    <a href="<?= APP_URL ?>admin/requests" class="btn btn-danger btn-sm mt-2">View Requests</a>
</div>
<?php endif; ?>

<!-- Recent Activities -->
<div class="row">
    <!-- Recent Users -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Recent Users
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentUsers)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentUsers as $user): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-at me-1"></i><?= htmlspecialchars($user['username']) ?>
                                            <i class="fas fa-envelope ms-2 me-1"></i><?= htmlspecialchars($user['email']) ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'secondary' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No recent users</p>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= APP_URL ?>admin/users" class="btn btn-primary btn-sm">View All Users</a>
            </div>
        </div>
    </div>

    <!-- Recent Donations -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-hand-holding-heart me-2"></i>Recent Donations
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentDonations)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentDonations as $donation): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($donation['donor_name']) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-tint me-1"></i><?= htmlspecialchars($donation['blood_group']) ?>
                                            <i class="fas fa-map-marker-alt ms-2 me-1"></i><?= htmlspecialchars($donation['location']) ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $donation['status'] === 'completed' ? 'success' : ($donation['status'] === 'approved' ? 'primary' : 'warning') ?>">
                                        <?= ucfirst($donation['status']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No recent donations</p>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= APP_URL ?>admin/donations" class="btn btn-success btn-sm">View All Donations</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Requests -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-search me-2"></i>Recent Requests
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentRequests)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentRequests as $request): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($request['requester_name']) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-tint me-1"></i><?= htmlspecialchars($request['blood_group']) ?>
                                            <i class="fas fa-map-marker-alt ms-2 me-1"></i><?= htmlspecialchars($request['location']) ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $request['urgency'] === 'critical' ? 'danger' : ($request['urgency'] === 'high' ? 'warning' : 'info') ?>">
                                        <?= ucfirst($request['urgency']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No recent requests</p>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= APP_URL ?>admin/requests" class="btn btn-warning btn-sm">View All Requests</a>
            </div>
        </div>
    </div>

    <!-- Recent Forum Posts -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-comments me-2"></i>Recent Forum Posts
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($recentPosts)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentPosts as $post): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($post['title']) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i><?= htmlspecialchars($post['username'] ?? 'Anonymous') ?>
                                            <i class="fas fa-clock ms-2 me-1"></i><?= date('M j, Y', strtotime($post['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No recent posts</p>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <a href="<?= APP_URL ?>forum" class="btn btn-info btn-sm">View Forum</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="<?= APP_URL ?>admin/users" class="btn btn-outline-primary w-100">
                            <i class="fas fa-users me-2"></i>Manage Users
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= APP_URL ?>admin/donations" class="btn btn-outline-success w-100">
                            <i class="fas fa-hand-holding-heart me-2"></i>Manage Donations
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= APP_URL ?>admin/requests" class="btn btn-outline-warning w-100">
                            <i class="fas fa-search me-2"></i>Manage Requests
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?= APP_URL ?>forum" class="btn btn-outline-info w-100">
                            <i class="fas fa-comments me-2"></i>View Forum
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?> 