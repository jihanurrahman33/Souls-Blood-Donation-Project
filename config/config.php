<?php
// Database Configuration
define("DB_HOST", "localhost");
define("DB_NAME", "blood_donation");
define("DB_USER", "root");
define("DB_PASS", "");

// Application Configuration
define("APP_NAME", "Souls");
define("APP_URL", "http://localhost/souls/");
define("APP_VERSION", "2.0.0");

// Email Configuration (for notifications)
define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_PORT", 587);
define("SMTP_USERNAME", "your-email@gmail.com");
define("SMTP_PASSWORD", "your-app-password");
define("SMTP_FROM_EMAIL", "noreply@blooddonation.com");
define("SMTP_FROM_NAME", "Souls");

// Security
define("HASH_COST", 12); // Password hashing cost (higher = more secure but slower)
define("CSRF_TOKEN_SECRET", "12358392af6bcd1a2427f7e31e585c9ea5eb423845a5e70b4ab5f41c0c0712ec");

// Error Reporting (set to 0 for production)
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Session Configuration (only set if session not already started)
if (session_status() === PHP_SESSION_NONE) {
    ini_set("session.cookie_httponly", 1);
    ini_set("session.use_only_cookies", 1);
    ini_set("session.cookie_secure", 0); // Set to 1 for HTTPS
}

// Helper Functions
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, "UTF-8");
}

function redirect($path) {
    header("Location: " . APP_URL . $path);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION["user_id"]);
}

function generateCSRFToken() {
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function verifyCSRFToken($token) {
    return isset($_SESSION["csrf_token"]) && hash_equals($_SESSION["csrf_token"], $token);
}

// Flash Messages
function setFlashMessage($type, $message) {
    $_SESSION[$type] = $message;
}

function getFlashMessage($type) {
    if (isset($_SESSION[$type])) {
        $message = $_SESSION[$type];
        unset($_SESSION[$type]);
        return $message;
    }
    return null;
}

function isAdmin() {
    return isset($_SESSION["user_role"]) && $_SESSION["user_role"] === "admin";
}
?>