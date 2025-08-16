<?php $pageTitle = 'Donate Blood'; ?>
<?php include 'views/layout/header.php'; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-hand-holding-heart me-2"></i>Blood Donation Form
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= APP_URL ?>donate" id="donateForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="donor_name" class="form-label">
                                <i class="fas fa-user me-1"></i>Donor Name
                            </label>
                            <input type="text" class="form-control" id="donor_name" name="donor_name" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['donor_name'] ?? '') ?>" 
                                   required>
                            <div class="invalid-feedback" id="donorNameError"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="blood_group" class="form-label">
                                <i class="fas fa-tint me-1"></i>Blood Group
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
                    
                    <div class="mb-3">
                        <label for="donation_date" class="form-label">
                            <i class="fas fa-calendar me-1"></i>Preferred Donation Date
                        </label>
                        <input type="date" class="form-control" id="donation_date" name="donation_date" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['donation_date'] ?? '') ?>" 
                               min="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback" id="donationDateError"></div>
                        <small class="form-text text-muted">Please select a future date</small>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-hand-holding-heart me-2"></i>Submit Donation Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Donation Guidelines
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Age 18-65 years
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Weight at least 50kg
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Good health condition
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>No recent surgeries
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>Not pregnant or nursing
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success me-2"></i>No recent tattoos/piercings
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- User Donation History -->
        <?php if (isLoggedIn() && !empty($userDonations)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Your Donation History
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach ($userDonations as $donation): ?>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($donation['donor_name']) ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-tint me-1"></i><?= htmlspecialchars($donation['blood_group']) ?>
                                        <i class="fas fa-calendar ms-2 me-1"></i><?= date('M j, Y', strtotime($donation['donation_date'])) ?>
                                    </small>
                                </div>
                                <span class="badge bg-<?= $donation['status'] === 'completed' ? 'success' : ($donation['status'] === 'approved' ? 'primary' : 'warning') ?>">
                                    <?= ucfirst($donation['status']) ?>
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
    const form = document.getElementById('donateForm');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Donor name validation
        const donorName = document.getElementById('donor_name').value.trim();
        if (!donorName) {
            showError('donor_name', 'Donor name is required');
            isValid = false;
        } else {
            clearError('donor_name');
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
        
        // Date validation
        const donationDate = document.getElementById('donation_date').value;
        if (!donationDate) {
            showError('donation_date', 'Please select a donation date');
            isValid = false;
        } else {
            const selectedDate = new Date(donationDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                showError('donation_date', 'Please select a future date');
                isValid = false;
            } else {
                clearError('donation_date');
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