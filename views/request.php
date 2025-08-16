<?php $pageTitle = 'Request Blood'; ?>
<?php include 'views/layout/header.php'; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="fas fa-search me-2"></i>Blood Request Form
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= APP_URL ?>request" id="requestForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="requester_name" class="form-label">
                                <i class="fas fa-user me-1"></i>Requester Name
                            </label>
                            <input type="text" class="form-control" id="requester_name" name="requester_name" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['requester_name'] ?? '') ?>" 
                                   required>
                            <div class="invalid-feedback" id="requesterNameError"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="blood_group" class="form-label">
                                <i class="fas fa-tint me-1"></i>Required Blood Group
                            </label>
                            <select class="form-select" id="blood_group" name="blood_group" required>
                                <option value="">Select Blood Group</option>
                                <option value="A+" <?= ($_SESSION['form_data']['blood_group'] ?? '') === 'A+' ? 'selected' : '' ?>>A+</option>
                                <option value="A-" <?= ($_SESSION['form_data']['blood_group'] ?? '') === 'A-' ? 'selected' : '' ?>>A-</option>
                                <option value="B+" <?= ($_SESSION['form_data']['blood_group'] ?? '') === 'B+' ? 'selected' : '' ?>>B+</option>
                                <option value="B-" <?= ($_SESSION['form_data']['blood_group'] ?? '') === 'B-' ? 'selected' : '' ?>>B-</option>
                                <option value="AB+" <?= ($_SESSION['form_data']['blood_group'] ?? '') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                                <option value="AB-" <?= ($_SESSION['form_data']['blood_group'] ?? '') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                                <option value="O+" <?= ($_SESSION['form_data']['blood_group'] ?? '') === 'O+' ? 'selected' : '' ?>>O+</option>
                                <option value="O-" <?= ($_SESSION['form_data']['blood_group'] ?? '') === 'O-' ? 'selected' : '' ?>>O-</option>
                            </select>
                            <div class="invalid-feedback" id="bloodGroupError"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Location
                            </label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['location'] ?? '') ?>" 
                                   placeholder="City, Hospital, or Blood Bank" required>
                            <div class="invalid-feedback" id="locationError"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contact_info" class="form-label">
                                <i class="fas fa-phone me-1"></i>Contact Information
                            </label>
                            <input type="text" class="form-control" id="contact_info" name="contact_info" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['contact_info'] ?? '') ?>" 
                                   placeholder="Phone number or email" required>
                            <div class="invalid-feedback" id="contactInfoError"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="urgency" class="form-label">
                                <i class="fas fa-exclamation-triangle me-1"></i>Urgency Level
                            </label>
                            <select class="form-select" id="urgency" name="urgency" required>
                                <option value="">Select Urgency</option>
                                <option value="low" <?= ($_SESSION['form_data']['urgency'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                                <option value="medium" <?= ($_SESSION['form_data']['urgency'] ?? '') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="high" <?= ($_SESSION['form_data']['urgency'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                                <option value="critical" <?= ($_SESSION['form_data']['urgency'] ?? '') === 'critical' ? 'selected' : '' ?>>Critical</option>
                            </select>
                            <div class="invalid-feedback" id="urgencyError"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="request_date" class="form-label">
                                <i class="fas fa-calendar me-1"></i>Required Date
                            </label>
                            <input type="date" class="form-control" id="request_date" name="request_date" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['request_date'] ?? '') ?>" 
                                   min="<?= date('Y-m-d') ?>" required>
                            <div class="invalid-feedback" id="requestDateError"></div>
                            <small class="form-text text-muted">When do you need the blood?</small>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-search me-2"></i>Submit Blood Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Active Blood Requests Section -->
<div class="row mt-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-heartbeat me-2"></i>Active Blood Requests
                </h4>
                <p class="mb-0 mt-2">Find blood requests that match your blood type and help save lives</p>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($pendingRequests)): ?>
                    <!-- Desktop Table View (≥992px) -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-user me-1"></i>Requester</th>
                                        <th><i class="fas fa-tint me-1"></i>Blood Group</th>
                                        <th><i class="fas fa-map-marker-alt me-1"></i>Location</th>
                                        <th><i class="fas fa-exclamation-triangle me-1"></i>Urgency</th>
                                        <th><i class="fas fa-calendar me-1"></i>Required Date</th>
                                        <th><i class="fas fa-clock me-1"></i>Posted</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingRequests as $request): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($request['requester_name']) ?></strong>
                                                <?php if ($request['first_name'] && $request['last_name']): ?>
                                                    <br><small class="text-muted">by <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger"><?= htmlspecialchars($request['blood_group']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($request['location']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $request['urgency'] === 'critical' ? 'danger' : ($request['urgency'] === 'high' ? 'warning' : ($request['urgency'] === 'medium' ? 'info' : 'secondary')) ?>">
                                                    <?= ucfirst($request['urgency']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M j, Y', strtotime($request['request_date'])) ?></td>
                                            <td><?= date('M j', strtotime($request['created_at'])) ?></td>
                                            <td class="text-center">
                                                <a href="<?= APP_URL ?>request/details/<?= $request['id'] ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View Details & Donate
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View (≤600px) -->
                    <div class="d-lg-none">
                        <div class="row g-3 p-3">
                            <?php foreach ($pendingRequests as $request): ?>
                                <div class="col-12">
                                    <div class="card border shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0"><?= htmlspecialchars($request['requester_name']) ?></h6>
                                                <span class="badge bg-<?= $request['urgency'] === 'critical' ? 'danger' : ($request['urgency'] === 'high' ? 'warning' : ($request['urgency'] === 'medium' ? 'info' : 'secondary')) ?>">
                                                    <?= ucfirst($request['urgency']) ?>
                                                </span>
                                            </div>
                                            
                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-tint me-1"></i>Blood Group
                                                    </small>
                                                    <br>
                                                    <span class="badge bg-danger"><?= htmlspecialchars($request['blood_group']) ?></span>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>Required Date
                                                    </small>
                                                    <br>
                                                    <strong><?= date('M j, Y', strtotime($request['request_date'])) ?></strong>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>Location
                                                </small>
                                                <br>
                                                <strong><?= htmlspecialchars($request['location']) ?></strong>
                                            </div>
                                            
                                            <?php if ($request['first_name'] && $request['last_name']): ?>
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-user me-1"></i>Posted by
                                                    </small>
                                                    <br>
                                                    <strong><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></strong>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="d-grid">
                                                <a href="<?= APP_URL ?>request/details/<?= $request['id'] ?>" class="btn btn-primary">
                                                    <i class="fas fa-eye me-1"></i>View Details & Donate
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-heartbeat fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Active Blood Requests</h5>
                        <p class="text-muted">There are currently no pending blood requests. Check back later!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Active Blood Requests -->
        <?php if (!empty($pendingRequests)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-heartbeat me-2"></i>Active Blood Requests
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach ($pendingRequests as $request): ?>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($request['requester_name']) ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-tint me-1"></i><?= htmlspecialchars($request['blood_group']) ?>
                                        <i class="fas fa-map-marker-alt ms-2 me-1"></i><?= htmlspecialchars($request['location']) ?>
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-<?= $request['urgency'] === 'critical' ? 'danger' : ($request['urgency'] === 'high' ? 'warning' : 'info') ?> mb-1">
                                        <?= ucfirst($request['urgency']) ?>
                                    </span>
                                    <br>
                                    <a href="<?= APP_URL ?>request/details/<?= $request['id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View Details & Donate
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Request Guidelines
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Provide accurate information
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Include valid contact details
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Specify urgency level correctly
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Be available for contact
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>Update status when fulfilled
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- User Request History -->
        <?php if (isLoggedIn() && !empty($userRequests)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Your Request History
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach ($userRequests as $request): ?>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($request['requester_name']) ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-tint me-1"></i><?= htmlspecialchars($request['blood_group']) ?>
                                        <i class="fas fa-calendar ms-2 me-1"></i><?= date('M j, Y', strtotime($request['request_date'])) ?>
                                    </small>
                                </div>
                                <span class="badge bg-<?= $request['status'] === 'fulfilled' ? 'success' : ($request['status'] === 'pending' ? 'warning' : 'secondary') ?>">
                                    <?= ucfirst($request['status']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('requestForm');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Requester name validation
        const requesterName = document.getElementById('requester_name').value.trim();
        if (!requesterName) {
            showError('requester_name', 'Requester name is required');
            isValid = false;
        } else {
            clearError('requester_name');
        }
        
        // Blood group validation
        const bloodGroup = document.getElementById('blood_group').value;
        if (!bloodGroup) {
            showError('blood_group', 'Please select a blood group');
            isValid = false;
        } else {
            clearError('blood_group');
        }
        
        // Location validation
        const location = document.getElementById('location').value.trim();
        if (!location) {
            showError('location', 'Location is required');
            isValid = false;
        } else {
            clearError('location');
        }
        
        // Contact info validation
        const contactInfo = document.getElementById('contact_info').value.trim();
        if (!contactInfo) {
            showError('contact_info', 'Contact information is required');
            isValid = false;
        } else {
            clearError('contact_info');
        }
        
        // Urgency validation
        const urgency = document.getElementById('urgency').value;
        if (!urgency) {
            showError('urgency', 'Please select urgency level');
            isValid = false;
        } else {
            clearError('urgency');
        }
        
        // Date validation
        const requestDate = document.getElementById('request_date').value;
        if (!requestDate) {
            showError('request_date', 'Please select a required date');
            isValid = false;
        } else {
            const selectedDate = new Date(requestDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                showError('request_date', 'Please select a future date');
                isValid = false;
            } else {
                clearError('request_date');
            }
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
});
</script>

<?php include 'views/layout/footer.php'; ?> 