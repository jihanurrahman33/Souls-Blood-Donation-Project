#!/bin/bash

echo "========================================"
echo "   Souls - Blood Donation Website"
echo "   macOS/Linux Deployment Script"
echo "========================================"
echo

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP is not installed or not in PATH"
    echo "Please install PHP or XAMPP"
    exit 1
fi

echo "PHP found. Running deployment check..."
echo

# Run the deployment script
php deploy.php

echo
echo "========================================"
echo "Deployment check completed!"
echo
echo "If you see any issues above, please:"
echo "1. Make sure XAMPP is installed and running (macOS)"
echo "2. Start Apache and MySQL from XAMPP Control Panel"
echo "3. Check that the project is in the correct directory"
echo "   - macOS: /Applications/XAMPP/xamppfiles/htdocs/souls/"
echo "   - Linux: /var/www/html/souls/"
echo
echo "To run the setup script, type: php setup.php"
echo
