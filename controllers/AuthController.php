<?php
require_once 'models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Validation
            $errors = [];
            if (empty($email)) $errors[] = 'Email is required';
            if (empty($password)) $errors[] = 'Password is required';
            
            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);
                
                if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
                    // Set session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    
                    redirect('');
                } else {
                    $errors[] = 'Invalid email or password';
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                redirect('auth/login');
            }
        }
        
        include 'views/auth/login.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => sanitize($_POST['username'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'confirm_password' => $_POST['confirm_password'] ?? '',
                'first_name' => sanitize($_POST['first_name'] ?? ''),
                'last_name' => sanitize($_POST['last_name'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'blood_group' => sanitize($_POST['blood_group'] ?? '')
            ];
            
            // Validation
            $errors = [];
            
            if (empty($data['username'])) $errors[] = 'Username is required';
            if (empty($data['email'])) $errors[] = 'Email is required';
            if (empty($data['password'])) $errors[] = 'Password is required';
            if (empty($data['first_name'])) $errors[] = 'First name is required';
            if (empty($data['last_name'])) $errors[] = 'Last name is required';
            
            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
            
            if (strlen($data['password']) < 6) {
                $errors[] = 'Password must be at least 6 characters long';
            }
            
            if ($data['password'] !== $data['confirm_password']) {
                $errors[] = 'Passwords do not match';
            }
            
            // Check if username or email already exists
            if (!empty($data['username'])) {
                $existingUser = $this->userModel->findByUsername($data['username']);
                if ($existingUser) {
                    $errors[] = 'Username already exists';
                }
            }
            
            if (!empty($data['email'])) {
                $existingUser = $this->userModel->findByEmail($data['email']);
                if ($existingUser) {
                    $errors[] = 'Email already exists';
                }
            }
            
            if (empty($errors)) {
                try {
                    $this->userModel->create($data);
                    $_SESSION['success'] = 'Registration successful! Please login.';
                    redirect('auth/login');
                } catch (Exception $e) {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['form_data'] = $data;
                redirect('auth/register');
            }
        }
        
        include 'views/auth/register.php';
    }
    
    public function logout() {
        session_destroy();
        redirect('');
    }
} 