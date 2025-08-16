<?php $pageTitle = 'Register'; ?>
<?php include 'views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-danger text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= APP_URL ?>auth/register" id="registerForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">
                                <i class="fas fa-user me-1"></i>First Name
                            </label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['first_name'] ?? '') ?>" 
                                   required>
                            <div class="invalid-feedback" id="firstNameError"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">
                                <i class="fas fa-user me-1"></i>Last Name
                            </label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['last_name'] ?? '') ?>" 
                                   required>
                            <div class="invalid-feedback" id="lastNameError"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-at me-1"></i>Username
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['username'] ?? '') ?>" 
                                   required>
                            <div class="invalid-feedback" id="usernameError"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email Address
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" 
                                   required>
                            <div class="invalid-feedback" id="emailError"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>Phone Number
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($_SESSION['form_data']['phone'] ?? '') ?>">
                            <div class="invalid-feedback" id="phoneError"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="blood_group" class="form-label">
                                <i class="fas fa-tint me-1"></i>Blood Group
                            </label>
                            <select class="form-select" id="blood_group" name="blood_group">
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
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Password
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="passwordError"></div>
                            <small class="form-text text-muted">Minimum 6 characters</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Confirm Password
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback" id="confirmPasswordError"></div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="mb-0">Already have an account?</p>
                    <a href="<?= APP_URL ?>auth/login" class="btn btn-outline-danger">
                        <i class="fas fa-sign-in-alt me-2"></i>Login Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    
    // Toggle password visibility
    function setupPasswordToggle(toggleBtn, inputId) {
        toggleBtn.addEventListener('click', function() {
            const input = document.getElementById(inputId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
    
    setupPasswordToggle(togglePassword, 'password');
    setupPasswordToggle(toggleConfirmPassword, 'confirm_password');
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // First name validation
        const firstName = document.getElementById('first_name').value.trim();
        if (!firstName) {
            showError('first_name', 'First name is required');
            isValid = false;
        } else {
            clearError('first_name');
        }
        
        // Last name validation
        const lastName = document.getElementById('last_name').value.trim();
        if (!lastName) {
            showError('last_name', 'Last name is required');
            isValid = false;
        } else {
            clearError('last_name');
        }
        
        // Username validation
        const username = document.getElementById('username').value.trim();
        if (!username) {
            showError('username', 'Username is required');
            isValid = false;
        } else if (username.length < 3) {
            showError('username', 'Username must be at least 3 characters');
            isValid = false;
        } else {
            clearError('username');
        }
        
        // Email validation
        const email = document.getElementById('email').value.trim();
        if (!email) {
            showError('email', 'Email is required');
            isValid = false;
        } else if (!isValidEmail(email)) {
            showError('email', 'Please enter a valid email address');
            isValid = false;
        } else {
            clearError('email');
        }
        
        // Phone validation (optional)
        const phone = document.getElementById('phone').value.trim();
        if (phone && !isValidPhone(phone)) {
            showError('phone', 'Please enter a valid phone number');
            isValid = false;
        } else {
            clearError('phone');
        }
        
        // Password validation
        const password = document.getElementById('password').value;
        if (!password) {
            showError('password', 'Password is required');
            isValid = false;
        } else if (password.length < 6) {
            showError('password', 'Password must be at least 6 characters');
            isValid = false;
        } else {
            clearError('password');
        }
        
        // Confirm password validation
        const confirmPassword = document.getElementById('confirm_password').value;
        if (!confirmPassword) {
            showError('confirm_password', 'Please confirm your password');
            isValid = false;
        } else if (password !== confirmPassword) {
            showError('confirm_password', 'Passwords do not match');
            isValid = false;
        } else {
            clearError('confirm_password');
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function isValidPhone(phone) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
    }
    
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