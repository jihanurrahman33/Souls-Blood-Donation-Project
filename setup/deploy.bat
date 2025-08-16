@echo off
echo ========================================
echo    Souls - Blood Donation Website
echo    Windows Deployment Script
echo ========================================
echo.

echo Checking PHP installation...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install XAMPP or add PHP to your PATH
    pause
    exit /b 1
)

echo PHP found. Running deployment check...
echo.

php deploy.php

echo.
echo ========================================
echo Deployment check completed!
echo.
echo If you see any issues above, please:
echo 1. Make sure XAMPP is installed and running
echo 2. Start Apache and MySQL from XAMPP Control Panel
echo 3. Check that the project is in C:\xampp\htdocs\souls\
echo.
echo To run the setup script, type: php setup.php
echo.
pause
