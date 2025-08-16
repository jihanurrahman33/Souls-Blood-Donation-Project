<?php
/**
 * Fix APP_URL Configuration Script
 * Run this script to automatically fix the APP_URL in your config file
 * This is useful when the routing is not working after cloning the project
 */

echo "🔧 Souls - Fix APP_URL Configuration\n";
echo "====================================\n\n";

// Check if config file exists
if (!file_exists('config/config.php')) {
    echo "❌ Configuration file not found. Please run setup.php first.\n";
    exit(1);
}

// Auto-detect the correct base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$port = $_SERVER['SERVER_PORT'] ?? 80;

// Get the current script path to determine the base directory
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = dirname($scriptPath);

// If running from command line, try to detect the project path
if (php_sapi_name() === 'cli') {
    // Get current working directory
    $cwd = getcwd();
    // Extract the project name from the path
    $pathParts = explode(DIRECTORY_SEPARATOR, $cwd);
    $projectName = end($pathParts);
    
    if ($port == 80 || $port == 443) {
        $appUrl = $protocol . "://" . $host . "/" . $projectName . "/";
    } else {
        $appUrl = $protocol . "://" . $host . ":" . $port . "/" . $projectName . "/";
    }
} else {
    // Build the base URL for web requests
    if ($port == 80 || $port == 443) {
        $appUrl = $protocol . "://" . $host . $basePath . "/";
    } else {
        $appUrl = $protocol . "://" . $host . ":" . $port . $basePath . "/";
    }
}

// Ensure we have a valid URL
if ($appUrl === "http://localhost//" || $appUrl === "https://localhost//") {
    $appUrl = "http://localhost/";
}

$currentUrl = $_SERVER['REQUEST_URI'] ?? 'CLI';
echo "📍 Current URL: $currentUrl\n";
echo "📍 Detected Base URL: $appUrl\n\n";

// Read the current config file
$configContent = file_get_contents('config/config.php');

// Replace the APP_URL line
$oldPattern = '/define\("APP_URL",\s*"[^"]*"\);/';
$newAppUrl = 'define("APP_URL", "' . $appUrl . '");';

if (preg_match($oldPattern, $configContent)) {
    $newConfigContent = preg_replace($oldPattern, $newAppUrl, $configContent);
    
    // Write the updated config file
    if (file_put_contents('config/config.php', $newConfigContent)) {
        echo "✅ APP_URL updated successfully!\n";
        echo "📍 New APP_URL: $appUrl\n\n";
        echo "🚀 Your routing should now work correctly!\n";
        echo "📍 Try accessing: $appUrl\n";
    } else {
        echo "❌ Failed to update configuration file.\n";
        echo "📍 Please check file permissions.\n";
    }
} else {
    echo "❌ Could not find APP_URL in configuration file.\n";
    echo "📍 Please check your config/config.php file.\n";
}

echo "\n✨ URL fix completed!\n";
?>
