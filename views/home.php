<?php $pageTitle = 'Home'; ?>
<?php include 'views/layout/header.php'; ?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container-fluid">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-6 text-center text-lg-start">
                <h1 class="hero-title mb-4">
                    Save Lives Through 
                    <span class="text-danger">Blood Donation</span>
                </h1>
                <p class="hero-subtitle mb-5">
                    Join our community of heroes who make a difference every day. 
                    Your donation can save up to 3 lives and give hope to families in need.
                </p>
                <div class="hero-buttons">
                    <a href="<?= APP_URL ?>donate" class="btn btn-primary btn-lg me-3 mb-3">
                        <i class="fas fa-heartbeat me-2"></i>Donate Blood Now
                    </a>
                    <a href="<?= APP_URL ?>request" class="btn btn-outline-light btn-lg mb-3">
                        <i class="fas fa-search me-2"></i>Request Blood
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <div class="hero-image">
                    <i class="fas fa-heartbeat"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<section class="stats-section py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="section-title">Our Impact</h2>
                <p class="section-subtitle">Together, we're making a difference in people's lives</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="stat-number"><?= number_format($stats['total_users']) ?></h3>
                    <p class="stat-label">Registered Donors</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3 class="stat-number"><?= number_format($stats['total_donations']) ?></h3>
                    <p class="stat-label">Lives Saved</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="stat-number"><?= number_format($stats['total_requests']) ?></h3>
                    <p class="stat-label">Requests Fulfilled</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="stat-number"><?= number_format($stats['total_posts']) ?></h3>
                    <p class="stat-label">Community Posts</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Urgent Requests Section -->
<?php if (!empty($urgentRequests)): ?>
<section class="urgent-section py-5">
    <div class="container">
        <div class="urgent-alert">
            <div class="urgent-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Urgent Blood Requests</h3>
            </div>
            <p>There are urgent blood requests that need immediate attention:</p>
            <div class="urgent-grid">
                <?php foreach ($urgentRequests as $request): ?>
                    <div class="urgent-card">
                        <div class="urgent-card-header">
                            <span class="blood-type"><?= htmlspecialchars($request['blood_group']) ?></span>
                            <span class="urgency-badge"><?= ucfirst($request['urgency']) ?></span>
                        </div>
                        <h4><?= htmlspecialchars($request['location']) ?></h4>
                        <p>Contact: <?= htmlspecialchars($request['contact_info']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="urgent-action">
                <a href="<?= APP_URL ?>request" class="btn btn-danger">
                    <i class="fas fa-eye me-2"></i>View All Requests
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->
<section class="features-section py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="section-title">Why Choose Our Platform?</h2>
                <p class="section-subtitle">We provide comprehensive blood donation services and support</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h4>Life-Saving Impact</h4>
                    <p>Every blood donation can save up to 3 lives. Join our mission to help patients in critical need.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4>Quick Blood Matching</h4>
                    <p>Our advanced system quickly matches donors with recipients based on blood type and location.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <h4>Hospital Partnerships</h4>
                    <p>We work with hospitals and medical centers to ensure safe and reliable blood distribution.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Safe Donation Process</h4>
                    <p>All donations follow strict safety protocols and medical standards for donor and recipient safety.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>Donation Scheduling</h4>
                    <p>Schedule your blood donation at convenient times and locations near you.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Community Support</h4>
                    <p>Connect with fellow donors, share experiences, and support each other in our community.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Activities Section -->
<section class="activities-section py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="section-title">Recent Activities</h2>
                <p class="section-subtitle">Stay updated with the latest donations and requests</p>
            </div>
        </div>
        <div class="row g-4">
            <!-- Recent Donations -->
            <div class="col-lg-6">
                <div class="activity-card">
                    <div class="activity-header">
                        <i class="fas fa-hand-holding-heart"></i>
                        <h3>Recent Donations</h3>
                    </div>
                    <div class="activity-content">
                        <?php if (!empty($recentDonations)): ?>
                            <div class="activity-list">
                                <?php foreach ($recentDonations as $donation): ?>
                                    <div class="activity-item">
                                        <div class="activity-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="activity-details">
                                            <h5><?= htmlspecialchars($donation['donor_name']) ?></h5>
                                            <p>
                                                <i class="fas fa-tint"></i>
                                                <?= htmlspecialchars($donation['blood_group']) ?> • 
                                                <?= htmlspecialchars($donation['location']) ?>
                                            </p>
                                            <small>
                                                <i class="fas fa-calendar"></i>
                                                <?= date('M j, Y', strtotime($donation['donation_date'])) ?>
                                            </small>
                                        </div>
                                        <span class="status-badge success">Completed</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-hand-holding-heart"></i>
                                <p>No recent donations</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="col-lg-6">
                <div class="activity-card">
                    <div class="activity-header">
                        <i class="fas fa-search"></i>
                        <h3>Recent Requests</h3>
                    </div>
                    <div class="activity-content">
                        <?php if (!empty($recentRequests)): ?>
                            <div class="activity-list">
                                <?php foreach ($recentRequests as $request): ?>
                                    <div class="activity-item">
                                        <div class="activity-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="activity-details">
                                            <h5><?= htmlspecialchars($request['requester_name']) ?></h5>
                                            <p>
                                                <i class="fas fa-tint"></i>
                                                <?= htmlspecialchars($request['blood_group']) ?> • 
                                                <?= htmlspecialchars($request['location']) ?>
                                            </p>
                                            <small>
                                                <i class="fas fa-calendar"></i>
                                                <?= date('M j, Y', strtotime($request['request_date'])) ?>
                                            </small>
                                        </div>
                                        <span class="status-badge <?= $request['urgency'] === 'critical' ? 'danger' : 'warning' ?>">
                                            <?= ucfirst($request['urgency']) ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-search"></i>
                                <p>No recent requests</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section py-5">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-8 text-center text-lg-start">
                <h2 class="cta-title">Ready to Make a Difference?</h2>
                <p class="cta-subtitle">
                    Join thousands of donors who are saving lives every day. 
                    Your donation can give someone a second chance at life.
                </p>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <?php if (isLoggedIn()): ?>
                    <div class="cta-buttons">
                        <a href="<?= APP_URL ?>donate" class="btn btn-light btn-lg me-3 mb-3">
                            <i class="fas fa-heartbeat me-2"></i>Donate Now
                        </a>
                        <a href="<?= APP_URL ?>home/dashboard" class="btn btn-outline-light btn-lg mb-3">
                            <i class="fas fa-tachometer-alt me-2"></i>My Dashboard
                        </a>
                    </div>
                <?php else: ?>
                    <div class="cta-buttons">
                        <a href="<?= APP_URL ?>auth/register" class="btn btn-light btn-lg me-3 mb-3">
                            <i class="fas fa-user-plus me-2"></i>Join Now
                        </a>
                        <a href="<?= APP_URL ?>auth/login" class="btn btn-outline-light btn-lg mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Footer Section -->
<footer class="footer-section">
    <div class="container-fluid">
        <div class="row text-center">
            <div class="col-12">
                <p class="footer-text">
                    <i class="fas fa-heartbeat"></i>
                    <strong>Souls</strong> - Saving lives, one donation at a time
                </p>
                <small class="footer-copyright">
                    © <?= date('Y') ?> Souls. All rights reserved.
                </small>
            </div>
        </div>
    </div>
</footer>

<?php include 'views/layout/footer.php'; ?> 