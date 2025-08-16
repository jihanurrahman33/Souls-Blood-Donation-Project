<?php $pageTitle = 'Request Details'; ?>
<?php include 'views/layout/header.php'; ?>

<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>request">Blood Requests</a></li>
            <li class="breadcrumb-item active" aria-current="page">Request Details</li>
        </ol>
    </nav>

    <!-- Request Details Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-search me-2"></i>Blood Request Details
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-1"></i>Requester Name
                            </label>
                            <p class="mb-0"><?= htmlspecialchars($request['requester_name']) ?></p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-tint me-1"></i>Required Blood Group
                            </label>
                            <p class="mb-0">
                                <span class="badge bg-danger fs-6"><?= htmlspecialchars($request['blood_group']) ?></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt me-1"></i>Location
                            </label>
                            <p class="mb-0"><?= htmlspecialchars($request['location']) ?></p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-exclamation-triangle me-1"></i>Urgency Level
                            </label>
                            <p class="mb-0">
                                <span class="badge bg-<?= $request['urgency'] === 'critical' ? 'danger' : ($request['urgency'] === 'high' ? 'warning' : ($request['urgency'] === 'medium' ? 'info' : 'secondary')) ?> fs-6">
                                    <?= ucfirst($request['urgency']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar me-1"></i>Required Date
                            </label>
                            <p class="mb-0"><?= date('F j, Y', strtotime($request['request_date'])) ?></p>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-clock me-1"></i>Posted On
                            </label>
                            <p class="mb-0"><?= date('F j, Y \a\t g:i A', strtotime($request['created_at'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-phone me-1"></i>Contact Information
                            </label>
                            <p class="mb-0"><?= htmlspecialchars($request['contact_info']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Requester Information Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>Requester Information
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($requester): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-user me-1"></i>Name
                            </label>
                            <p class="mb-0"><?= htmlspecialchars($requester['first_name'] . ' ' . $requester['last_name']) ?></p>
                        </div>
                        
                        <?php if ($requester['phone']): ?>
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-phone me-1"></i>Phone
                                </label>
                                <p class="mb-0"><?= htmlspecialchars($requester['phone']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-envelope me-1"></i>Email
                            </label>
                            <p class="mb-0"><?= htmlspecialchars($requester['email']) ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-alt me-1"></i>Member Since
                            </label>
                            <p class="mb-0"><?= date('F Y', strtotime($requester['created_at'])) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Anonymous requester</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Donation Action Card -->
            <div class="card border-0 shadow-sm">
                <?php if ($request['status'] === 'pending'): ?>
                    <?php if ($request['user_id'] === null): ?>
                        <!-- Anonymous Request -->
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-user-slash me-2"></i>Anonymous Request
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Anonymous Request:</strong> This request was posted anonymously and cannot use the automated donation confirmation system.
                            </div>
                            
                            <p class="text-muted mb-3">
                                If you have <?= htmlspecialchars($request['blood_group']) ?> blood type and can help, 
                                please contact the requester directly using the contact information provided above.
                            </p>
                            
                            <div class="d-grid">
                                <a href="tel:<?= htmlspecialchars($request['contact_info']) ?>" class="btn btn-primary btn-lg">
                                    <i class="fas fa-phone me-2"></i>Call Requester
                                </a>
                            </div>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    For anonymous requests, please coordinate directly with the requester.
                                </small>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Registered User Request -->
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-heart me-2"></i>Help Save a Life
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                If you have <?= htmlspecialchars($request['blood_group']) ?> blood type and can help, 
                                please click the button below to confirm your donation.
                            </p>
                            
                            <form method="POST" action="<?= APP_URL ?>request/confirm/<?= $request['id'] ?>" id="donationForm">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-heart me-2"></i>I will Donate
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    By clicking "I will Donate", you confirm that you are willing to donate blood for this request.
                                </small>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php elseif ($request['status'] === 'in_progress'): ?>
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Donation in Progress
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            A donor has confirmed their willingness to donate for this request. 
                            The donation process is now in progress.
                        </p>
                        
                        <?php if (isLoggedIn() && $_SESSION['user_id'] == $request['user_id']): ?>
                            <!-- Show "Confirm Received" button for requester -->
                            <form method="POST" action="<?= APP_URL ?>request/confirm/<?= $request['id'] ?>" id="confirmReceivedForm">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check-circle me-2"></i>Confirm Received
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Click this button once you have received the blood donation.
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                The requester will confirm when they receive the donation.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($request['status'] === 'completed'): ?>
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>Donation Completed
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <i class="fas fa-heart fa-3x text-success mb-3"></i>
                            <h5 class="text-success">Donation Successfully Completed!</h5>
                            <p class="text-muted mb-0">
                                This blood request has been fulfilled and the donation process is complete.
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Additional Information -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Important Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-2"></i>Before Donating</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-arrow-right text-primary me-2"></i>Ensure you are in good health</li>
                                <li><i class="fas fa-arrow-right text-primary me-2"></i>Have not donated blood in the last 56 days</li>
                                <li><i class="fas fa-arrow-right text-primary me-2"></i>Weigh at least 50 kg (110 lbs)</li>
                                <li><i class="fas fa-arrow-right text-primary me-2"></i>Are between 18-65 years old</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-phone text-success me-2"></i>Contact the Requester</h6>
                            <p class="mb-2">Use the contact information provided above to coordinate the donation process.</p>
                            <p class="mb-0"><strong>Remember:</strong> Always meet in a safe, public location or at a recognized blood donation center.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const donationForm = document.getElementById('donationForm');
    
    donationForm.addEventListener('submit', function(e) {
        if (!confirm('Are you sure you want to confirm your donation for this request?')) {
            e.preventDefault();
            return;
        }
        
        // Show loading state
        const submitBtn = donationForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Confirming...';
        submitBtn.disabled = true;
        
        // Re-enable after a short delay (in case of error)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>
