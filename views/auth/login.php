<?php $pageTitle = 'Login'; ?>
<?php include 'views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-danger text-white text-center">
                <h4 class="mb-0">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= APP_URL ?>auth/login" id="loginForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Email Address
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" 
                               required>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>
                    
                    <div class="mb-3">
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
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="mb-0">Don't have an account?</p>
                    <a href="<?= APP_URL ?>auth/register" class="btn btn-outline-danger">
                        <i class="fas fa-user-plus me-2"></i>Register Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    
    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Email validation
        const email = emailInput.value.trim();
        if (!email) {
            showError(emailInput, 'Email is required');
            isValid = false;
        } else if (!isValidEmail(email)) {
            showError(emailInput, 'Please enter a valid email address');
            isValid = false;
        } else {
            clearError(emailInput);
        }
        
        // Password validation
        const password = passwordInput.value;
        if (!password) {
            showError(passwordInput, 'Password is required');
            isValid = false;
        } else {
            clearError(passwordInput);
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function showError(input, message) {
        input.classList.add('is-invalid');
        const errorElement = input.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.textContent = message;
        }
    }
    
    function clearError(input) {
        input.classList.remove('is-invalid');
        const errorElement = input.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }
});
</script>

<?php include 'views/layout/footer.php'; ?> 