<?php
/**
 * Souls Blood Donation Website - Complete Setup Script
 * This script handles all setup tasks automatically
 */

// Include configuration
require_once 'config/config.php';

// Start output buffering
ob_start();

echo "🚀 Souls - Complete Setup\n";
echo "========================\n\n";

// Check system requirements
function checkRequirements() {
    echo "📋 Checking System Requirements...\n";
    
    $requirements = [
        'PHP Version (>= 7.4)' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'PDO Extension' => extension_loaded('pdo'),
        'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
        'JSON Extension' => extension_loaded('json'),
        'cURL Extension' => extension_loaded('curl'),
        'OpenSSL Extension' => extension_loaded('openssl'),
        'Fileinfo Extension' => extension_loaded('fileinfo')
    ];
    
    $allPassed = true;
    foreach ($requirements as $requirement => $passed) {
        $status = $passed ? "✅" : "❌";
        echo "  $status $requirement\n";
        if (!$passed) $allPassed = false;
    }
    
    if (!$allPassed) {
        echo "\n❌ Some requirements are not met. Please install the missing extensions.\n";
        exit(1);
    }
    
    echo "✅ All requirements met!\n\n";
    return true;
}

// Create database
function createDatabase() {
    echo "🗄️  Creating Database...\n";
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ Database '" . DB_NAME . "' created successfully!\n\n";
        
        return true;
    } catch (PDOException $e) {
        echo "❌ Database creation failed: " . $e->getMessage() . "\n";
        return false;
    }
}

// Create tables
function createTables() {
    echo "📊 Creating Tables...\n";
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Read and execute SQL file
        $sql = file_get_contents('setup_database.sql');
        $pdo->exec($sql);
        
        echo "✅ All tables created successfully!\n\n";
        return true;
    } catch (PDOException $e) {
        echo "❌ Table creation failed: " . $e->getMessage() . "\n";
        return false;
    }
}

// Create configuration
function createConfig() {
    echo "⚙️  Creating Configuration...\n";
    
    $configContent = '<?php
// Database Configuration
define("DB_HOST", "localhost");
define("DB_NAME", "blood_donation");
define("DB_USER", "root");
define("DB_PASS", "");

// Application Configuration
define("APP_NAME", "Souls");
define("APP_URL", "http://localhost:8000/");
define("APP_VERSION", "2.0.0");

// Email Configuration (for notifications)
define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_PORT", 587);
define("SMTP_USERNAME", "your-email@gmail.com");
define("SMTP_PASSWORD", "your-app-password");
define("SMTP_FROM_EMAIL", "noreply@blooddonation.com");
define("SMTP_FROM_NAME", "Souls");

// Security
define("CSRF_TOKEN_SECRET", "' . bin2hex(random_bytes(32)) . '");

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
?>';
    
    if (file_put_contents('config/config.php', $configContent)) {
        echo "✅ Configuration file created successfully!\n\n";
        return true;
    } else {
        echo "❌ Failed to create configuration file.\n";
        return false;
    }
}

// Insert sample data
function insertSampleData() {
    echo "📝 Inserting Sample Data...\n";
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if sample data already exists
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'john_doe'");
        if ($stmt->fetchColumn() > 0) {
            echo "ℹ️  Sample data already exists, skipping...\n\n";
            return true;
        }
        
        // Insert sample users
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name, blood_group, phone, location, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $users = [
            ['john_doe', 'john@example.com', password_hash('password123', PASSWORD_DEFAULT), 'John', 'Doe', 'A+', '1234567890', 'New York', 'user'],
            ['jane_smith', 'jane@example.com', password_hash('password123', PASSWORD_DEFAULT), 'Jane', 'Smith', 'O-', '0987654321', 'Los Angeles', 'user'],
            ['mike_wilson', 'mike@example.com', password_hash('password123', PASSWORD_DEFAULT), 'Mike', 'Wilson', 'B+', '1122334455', 'Chicago', 'user']
        ];
        
        foreach ($users as $user) {
            $stmt->execute($user);
        }
        
        // Get user IDs for foreign key relationships
        $johnId = $pdo->lastInsertId();
        $janeId = $johnId + 1;
        $mikeId = $johnId + 2;
        
        // Insert sample blood requests
        $stmt = $pdo->prepare("INSERT INTO blood_requests (user_id, requester_name, blood_group, location, urgency, contact_info, request_date, required_date, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, 'pending', NOW())");
        
        $requests = [
            [$johnId, 'John Doe', 'A+', 'New York', 'critical', 'john@example.com', date('Y-m-d', strtotime('+3 days'))],
            [null, 'Anonymous', 'O-', 'Los Angeles', 'urgent', 'emergency@hospital.com', date('Y-m-d', strtotime('+1 day'))],
            [$mikeId, 'Mike Wilson', 'B+', 'Chicago', 'normal', 'mike@example.com', date('Y-m-d', strtotime('+5 days'))]
        ];
        
        foreach ($requests as $request) {
            $stmt->execute($request);
        }
        
        // Insert sample donations
        $stmt = $pdo->prepare("INSERT INTO donations (user_id, donor_name, blood_group, location, donation_date, contact_info, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        
        $donations = [
            [$janeId, 'Jane Smith', 'O-', 'Los Angeles', date('Y-m-d', strtotime('-2 days')), 'jane@example.com'],
            [$mikeId, 'Mike Wilson', 'B+', 'Chicago', date('Y-m-d', strtotime('-1 day')), 'mike@example.com']
        ];
        
        foreach ($donations as $donation) {
            $stmt->execute($donation);
        }
        
        // Insert sample forum posts
        $stmt = $pdo->prepare("INSERT INTO forum_posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())");
        
        $posts = [
            [$johnId, 'Blood Donation Experience', 'I recently donated blood and it was a great experience. The staff was very professional and caring.'],
            [$janeId, 'Importance of Regular Donations', 'Regular blood donations are crucial for maintaining adequate blood supply. Let\'s all do our part!'],
            [$mikeId, 'Tips for First-Time Donors', 'For first-time donors, make sure to eat well and stay hydrated before your donation.']
        ];
        
        foreach ($posts as $post) {
            $stmt->execute($post);
        }
        
        echo "✅ Sample data inserted successfully!\n\n";
        return true;
    } catch (PDOException $e) {
        echo "❌ Sample data insertion failed: " . $e->getMessage() . "\n";
        return false;
    }
}

// Create admin user
function createAdminUser() {
    echo "👑 Creating Admin User...\n";
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if admin already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        $stmt->execute();
        if ($stmt->fetchColumn() > 0) {
            echo "ℹ️  Admin user already exists, skipping...\n\n";
            return true;
        }
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name, blood_group, phone, location, role, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            'admin',
            'admin@blooddonation.com',
            password_hash('admin123', PASSWORD_DEFAULT),
            'Admin',
            'User',
            'O+',
            '5551234567',
            'System',
            'admin'
        ]);
        
        echo "✅ Admin user created successfully!\n";
        echo "   Username: admin\n";
        echo "   Password: admin123\n\n";
        return true;
    } catch (PDOException $e) {
        echo "❌ Admin user creation failed: " . $e->getMessage() . "\n";
        return false;
    }
}

// Create directories
function createDirectories() {
    echo "📁 Creating Directories...\n";
    
    $directories = [
        'logs',
        'uploads',
        'temp'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (mkdir($dir, 0755, true)) {
                echo "✅ Created directory: $dir\n";
            } else {
                echo "❌ Failed to create directory: $dir\n";
            }
        } else {
            echo "ℹ️  Directory already exists: $dir\n";
        }
    }
    echo "\n";
}

// Create .htaccess
function createHtaccess() {
    echo "🔒 Creating .htaccess...\n";
    
    $htaccessContent = 'RewriteEngine On

# Redirect /api/* to api.php
RewriteRule ^api/(.*)$ api.php [QSA,L]

# Redirect all other requests to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security Headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# Prevent access to sensitive files
<Files "*.log">
    Order allow,deny
    Deny from all
</Files>

<Files "*.sql">
    Order allow,deny
    Deny from all
</Files>

<Files "config.php">
    Order allow,deny
    Deny from all
</Files>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>';
    
    if (file_put_contents('.htaccess', $htaccessContent)) {
        echo "✅ .htaccess file created successfully!\n\n";
        return true;
    } else {
        echo "❌ Failed to create .htaccess file.\n";
        return false;
    }
}

// Create health check file
function createHealthCheck() {
    echo "🏥 Creating Health Check...\n";
    
    $healthContent = '<?php
header("Content-Type: application/json");
echo json_encode([
    "status" => "healthy",
    "message" => "Souls is running",
    "timestamp" => date("Y-m-d H:i:s"),
    "version" => "2.0.0"
]);
?>';
    
    if (file_put_contents('health.php', $healthContent)) {
        echo "✅ Health check file created successfully!\n\n";
        return true;
    } else {
        echo "❌ Failed to create health check file.\n";
        return false;
    }
}

// Test database connection
function testDatabaseConnection() {
    echo "🔍 Testing Database Connection...\n";
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Test a simple query
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn();
        
        echo "✅ Database connection successful!\n";
        echo "   Total users in database: $userCount\n\n";
        return true;
    } catch (PDOException $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "\n";
        return false;
    }
}

// Main setup process
function main() {
    global $success;
    
    $steps = [
        'checkRequirements' => 'System Requirements Check',
        'createDatabase' => 'Database Creation',
        'createTables' => 'Table Creation',
        'createConfig' => 'Configuration Setup',
        'insertSampleData' => 'Sample Data Insertion',
        'createAdminUser' => 'Admin User Creation',
        'createDirectories' => 'Directory Creation',
        'createHtaccess' => 'Security Configuration',
        'createHealthCheck' => 'Health Check Setup',
        'testDatabaseConnection' => 'Database Connection Test'
    ];
    
    foreach ($steps as $function => $description) {
        echo "🔄 $description...\n";
        if (!$function()) {
            echo "❌ Setup failed at: $description\n";
            return false;
        }
    }
    
    return true;
}

// Run setup
if (main()) {
    echo "🎉 Setup completed successfully!\n\n";
    echo "✨ Thank you for using Souls!\n\n";
    echo "🚀 Next Steps:\n";
    echo "   1. Start your web server (e.g., php -S localhost:8000)\n";
    echo "   2. Open http://localhost:8000 in your browser\n";
    echo "   3. Login with admin credentials:\n";
    echo "      Username: admin\n";
    echo "      Password: admin123\n\n";
    echo "📚 For more information, check the README.md file.\n";
} else {
    echo "❌ Setup failed. Please check the error messages above.\n";
}

// Flush output buffer
ob_end_flush();
?>
