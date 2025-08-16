<?php
/**
 * Cross-Platform Deployment Script for Souls Blood Donation Website
 * Compatible with macOS, Windows, and Linux
 */

require_once '../config/platform_config.php';

echo "🚀 Souls - Cross-Platform Deployment\n";
echo "====================================\n\n";

// Detect platform
$os = PlatformConfig::detectOS();
echo "📍 Detected Platform: " . strtoupper($os) . "\n\n";

// Check system requirements
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

// Check services
echo "🔍 Checking Required Services...\n";
$issues = PlatformConfig::checkServices();

if (empty($issues)) {
    echo "✅ All services are running correctly!\n\n";
} else {
    echo "⚠️  Some issues detected:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
    echo "\n";
    
    // Show troubleshooting tips
    $tips = PlatformConfig::getTroubleshootingTips();
    echo "💡 Troubleshooting Tips for " . $tips['title'] . ":\n";
    foreach ($tips['tips'] as $tip) {
        echo "  • $tip\n";
    }
    echo "\n";
}

// Check if database exists
echo "🗄️  Checking Database...\n";
try {
    $config = PlatformConfig::getDatabaseConfig();
    $dsn = PlatformConfig::getDatabaseDSN();
    $pdo = new PDO($dsn, $config['user'], $config['pass']);
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '{$config['name']}'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Database '{$config['name']}' exists\n";
        
        // Check if tables exist
        $pdo = new PDO(PlatformConfig::getDatabaseDSN($config['name']), $config['user'], $config['pass']);
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "✅ Database tables exist (" . count($tables) . " tables)\n";
        } else {
            echo "⚠️  Database exists but no tables found. Run setup.php to create tables.\n";
        }
    } else {
        echo "⚠️  Database '{$config['name']}' does not exist. Run setup.php to create it.\n";
    }
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Check file permissions
echo "📁 Checking File Permissions...\n";
$directories = ['logs', 'uploads', 'temp'];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created directory: $dir\n";
        } else {
            echo "❌ Failed to create directory: $dir\n";
        }
    } else {
        if (is_writable($dir)) {
            echo "✅ Directory writable: $dir\n";
        } else {
            echo "⚠️  Directory not writable: $dir\n";
        }
    }
}

echo "\n";

// Show deployment information
echo "📊 Deployment Information:\n";
echo "  Platform: " . strtoupper($os) . "\n";
echo "  PHP Version: " . PHP_VERSION . "\n";
echo "  Web Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "  Document Root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' . "\n";
echo "  Application URL: " . PlatformConfig::getAppUrl() . "\n";

echo "\n";

// Show next steps
echo "🚀 Next Steps:\n";
if ($os === 'macos') {
    echo "  1. Ensure XAMPP is running (Apache and MySQL)\n";
    echo "  2. Open " . PlatformConfig::getAppUrl() . "souls in your browser\n";
} elseif ($os === 'windows') {
    echo "  1. Ensure XAMPP is running (Apache and MySQL)\n";
    echo "  2. Open " . PlatformConfig::getAppUrl() . "souls in your browser\n";
} else {
    echo "  1. Ensure your web server and MySQL are running\n";
    echo "  2. Open " . PlatformConfig::getAppUrl() . "souls in your browser\n";
}

echo "  3. If database setup is needed, run: php setup.php\n";
echo "  4. Login with admin credentials: admin / admin123\n";

echo "\n✨ Deployment check completed!\n";
?>
