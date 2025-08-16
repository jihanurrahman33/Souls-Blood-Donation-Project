<?php
/**
 * Platform-specific configuration for Souls Blood Donation Website
 * Handles different operating systems and server setups
 */

class PlatformConfig {
    
    /**
     * Detect the current operating system
     */
    public static function detectOS() {
        $os = strtolower(PHP_OS);
        if (strpos($os, 'darwin') !== false) {
            return 'macos';
        } elseif (strpos($os, 'win') !== false) {
            return 'windows';
        } else {
            return 'linux';
        }
    }
    
    /**
     * Get database configuration based on platform
     */
    public static function getDatabaseConfig() {
        $os = self::detectOS();
        
        switch ($os) {
            case 'macos':
                return [
                    'host' => 'localhost',
                    'name' => 'blood_donation',
                    'user' => 'root',
                    'pass' => '', // XAMPP on macOS usually has no password
                    'port' => 3306,
                    'socket' => '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock',
                    'use_socket' => true
                ];
            case 'windows':
                return [
                    'host' => 'localhost',
                    'name' => 'blood_donation',
                    'user' => 'root',
                    'pass' => '', // XAMPP on Windows usually has no password
                    'port' => 3306,
                    'socket' => null,
                    'use_socket' => false
                ];
            default: // Linux
                return [
                    'host' => 'localhost',
                    'name' => 'blood_donation',
                    'user' => 'root',
                    'pass' => '',
                    'port' => 3306,
                    'socket' => null,
                    'use_socket' => false
                ];
        }
    }
    
    /**
     * Get application URL based on platform
     */
    public static function getAppUrl() {
        // Auto-detect the base URL from the current request
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $port = $_SERVER['SERVER_PORT'] ?? 80;
        
        // Get the current script path to determine the base directory
        $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = dirname(dirname($scriptPath)); // Go up two levels from setup/deploy.php
        
        // Build the base URL
        if ($port == 80 || $port == 443) {
            $appUrl = $protocol . "://" . $host . $basePath . "/";
        } else {
            $appUrl = $protocol . "://" . $host . ":" . $port . $basePath . "/";
        }
        
        // Ensure we have a valid URL
        if ($appUrl === "http://localhost//" || $appUrl === "https://localhost//") {
            $appUrl = "http://localhost/";
        }
        
        return $appUrl;
    }
    
    /**
     * Get database DSN string based on platform
     */
    public static function getDatabaseDSN($dbName = null) {
        $config = self::getDatabaseConfig();
        
        if ($config['use_socket'] && $config['socket']) {
            $dsn = "mysql:unix_socket={$config['socket']}";
        } else {
            $dsn = "mysql:host={$config['host']};port={$config['port']}";
        }
        
        if ($dbName) {
            $dsn .= ";dbname={$dbName}";
        }
        
        $dsn .= ";charset=utf8mb4";
        
        return $dsn;
    }
    
    /**
     * Get platform-specific troubleshooting tips
     */
    public static function getTroubleshootingTips() {
        $os = self::detectOS();
        
        switch ($os) {
            case 'macos':
                return [
                    'title' => 'macOS with XAMPP',
                    'tips' => [
                        'Make sure XAMPP is installed and running',
                        'Start MySQL from XAMPP Control Panel',
                        'Check if MySQL socket exists: /Applications/XAMPP/xamppfiles/var/mysql/mysql.sock',
                        'Ensure Apache is running on port 80',
                        'Check XAMPP logs for any errors'
                    ]
                ];
            case 'windows':
                return [
                    'title' => 'Windows with XAMPP',
                    'tips' => [
                        'Make sure XAMPP is installed and running',
                        'Start MySQL from XAMPP Control Panel',
                        'Check if MySQL service is running on port 3306',
                        'Ensure Apache is running on port 80',
                        'Check Windows Services for MySQL and Apache',
                        'Verify firewall settings'
                    ]
                ];
            default:
                return [
                    'title' => 'Linux',
                    'tips' => [
                        'Make sure MySQL/MariaDB is installed and running',
                        'Check if MySQL service is running: sudo systemctl status mysql',
                        'Start MySQL if needed: sudo systemctl start mysql',
                        'Ensure Apache/Nginx is running',
                        'Check MySQL socket: /var/run/mysqld/mysqld.sock'
                    ]
                ];
        }
    }
    
    /**
     * Check if required services are running
     */
    public static function checkServices() {
        $os = self::detectOS();
        $issues = [];
        
        // Check MySQL connection
        try {
            $config = self::getDatabaseConfig();
            $dsn = self::getDatabaseDSN();
            $pdo = new PDO($dsn, $config['user'], $config['pass']);
            $pdo->query("SELECT 1");
        } catch (Exception $e) {
            $issues[] = "MySQL: " . $e->getMessage();
        }
        
        // Check web server
        if (!isset($_SERVER['SERVER_SOFTWARE'])) {
            $issues[] = "Web server not detected";
        }
        
        return $issues;
    }
}
?>
