# 🩸 Souls Blood Donation Website

A comprehensive blood donation management system built with PHP, MySQL, and Bootstrap. This platform connects blood donors with recipients, featuring real-time chat, advanced search, REST API, and automated setup.

[![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-green.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.0+-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

## 📋 Table of Contents

- [Features](#-features)
- [Quick Start](#-quick-start)
- [Installation](#-installation)
- [Project Structure](#-project-structure)
- [API Documentation](#-api-documentation)
- [Security Features](#-security-features)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

## ✨ Features

### 🎯 Core Features
- **User Management** - Registration, authentication, and profile management
- **Blood Donation System** - Complete donation workflow with status tracking
- **Blood Request System** - Request blood with dual confirmation workflow
- **Forum Community** - Discussion platform for donors and recipients
- **Admin Dashboard** - Comprehensive statistics and management tools
- **Responsive Design** - Mobile-first design with Bootstrap 5

### 🚀 Advanced Features
- **Real-time Chat** - Private messaging with online status indicators
- **Advanced Search** - Multi-entity search with filters and analytics
- **REST API** - Complete API for mobile app development
- **Email Notifications** - Automated email system for various events
- **Anonymous Requests** - Support for anonymous blood requests
- **Dual Confirmation** - Secure donation confirmation workflow

### 🔒 Security Features
- **CSRF Protection** - All forms protected against CSRF attacks
- **XSS Prevention** - Input sanitization and output encoding
- **SQL Injection Protection** - PDO prepared statements
- **Session Security** - HttpOnly cookies and secure session handling
- **File Access Control** - Protected sensitive directories and files

## 🚀 Quick Start

### Prerequisites
- **XAMPP** (Apache + MySQL + PHP 7.4+)
- **PHP Extensions**: pdo, pdo_mysql, json, mbstring

### One-Command Installation

1. **Download & Extract**
   ```bash
   # Extract to XAMPP htdocs folder
   C:\xampp\htdocs\souls-blood-donation\
   ```

2. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL**
   - Verify both services are running (green status)

3. **Run Setup Script**
   ```bash
   cd C:\xampp\htdocs\souls-blood-donation
   php setup.php
   ```

4. **Access Website**
   - **URL**: http://localhost:8000
   - **Admin Login**: admin@example.com / admin123

## 📦 Installation

### Automated Setup Process

The setup script (`setup.php`) automatically performs:

1. **System Validation**
   - ✅ PHP version check (7.4+)
   - ✅ Required extensions verification
   - ✅ MySQL connection test
   - ✅ Write permissions validation

2. **Configuration Setup**
   - ✅ Creates `config/config.php` with secure settings
   - ✅ Creates `config/database.php` with PDO connection
   - ✅ Generates secure CSRF tokens
   - ✅ Sets up session security

3. **Database Setup**
   - ✅ Creates `blood_donation` database
   - ✅ Creates all 8 tables with proper relationships
   - ✅ Adds database indexes for performance
   - ✅ Inserts sample data for testing

4. **User Management**
   - ✅ Creates admin user (admin@example.com / admin123)
   - ✅ Creates sample donor accounts
   - ✅ Sets up proper user roles

5. **File Organization**
   - ✅ Creates necessary directories (logs, uploads, temp)
   - ✅ Sets proper file permissions
   - ✅ Protects sensitive files with .htaccess

6. **Cleanup**
   - ✅ Removes temporary setup files
   - ✅ Creates health check endpoint
   - ✅ Updates documentation

### Manual Installation (Alternative)

If you prefer manual setup:

1. **Database Setup**
   ```sql
   -- Create database
   CREATE DATABASE blood_donation CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   
   -- Import schema
   mysql -u root -p blood_donation < setup_database.sql
   ```

2. **Configuration**
   - Copy `config/config.example.php` to `config/config.php`
   - Update database credentials and settings

3. **Permissions**
   ```bash
   chmod 755 logs/ uploads/ temp/
   chmod 644 config/config.php
   ```

## 📁 Project Structure

```
souls-blood-donation/
├── 📁 assets/                    # Static assets
│   ├── 📁 css/                   # Stylesheets
│   │   └── style.css            # Main stylesheet
│   └── 📁 js/                    # JavaScript files
│       └── script.js            # Main JavaScript
│
├── 📁 config/                    # Configuration files
│   ├── config.php               # Main configuration
│   └── database.php             # Database connection class
│
├── 📁 controllers/               # MVC Controllers
│   ├── AdminController.php      # Admin functionality
│   ├── ApiController.php        # API endpoints
│   ├── AuthController.php       # Authentication
│   ├── ChatController.php       # Chat system
│   ├── DonateController.php     # Blood donation
│   ├── ForumController.php      # Forum management
│   ├── HomeController.php       # Home page & dashboard
│   ├── RequestController.php    # Blood requests
│   └── SearchController.php     # Search functionality
│
├── 📁 models/                    # MVC Models
│   ├── BloodRequest.php         # Blood request operations
│   ├── ChatMessage.php          # Chat message operations
│   ├── Donation.php             # Donation operations
│   ├── DonationConfirmation.php # Donation confirmation
│   ├── ForumPost.php            # Forum post operations
│   └── User.php                 # User operations
│
├── 📁 services/                  # Business logic services
│   └── EmailService.php         # Email notification service
│
├── 📁 views/                     # MVC Views
│   ├── 📁 admin/                # Admin views
│   ├── 📁 auth/                 # Authentication views
│   ├── 📁 chat/                 # Chat views
│   ├── 📁 forum/                # Forum views
│   ├── 📁 layout/               # Layout components
│   ├── 📁 request/              # Request views
│   ├── 📁 search/               # Search views
│   └── *.php                    # Main view files
│
├── 📁 logs/                      # Application logs (protected)
├── 📁 uploads/                   # File uploads
├── 📁 temp/                      # Temporary files
│
├── 📄 index.php                  # Main entry point
├── 📄 api.php                    # API entry point
├── 📄 setup.php                  # Complete setup script
├── 📄 health.php                 # Health check endpoint
├── 📄 .htaccess                  # Security & routing
├── 📄 setup_database.sql         # Database schema
└── 📄 README.md                  # This file
```

## 📊 Database Schema

### Core Tables
- **`users`** - User accounts, profiles, and preferences
- **`blood_requests`** - Blood donation requests with urgency levels
- **`donations`** - Blood donation records and status tracking
- **`donation_confirmations`** - Dual confirmation system for donations
- **`forum_posts`** - Community forum posts and discussions

### Advanced Features
- **`chat_messages`** - Real-time chat system messages
- **`notifications`** - System notifications and alerts
- **`search_logs`** - Search analytics and user behavior tracking

## 📱 API Documentation

### Base URL
- **Website**: `http://localhost:8000/`
- **API**: `http://localhost:8000/api/`

### Authentication
- **Web Interface**: Session-based authentication
- **API Access**: Token-based authentication

### Key Endpoints

#### Health Check
```http
GET /api/health
```
**Response:**
```json
{
  "status": "healthy",
  "message": "Souls Blood Donation Website is running",
  "timestamp": "2024-01-16 10:30:00",
  "version": "2.0.0"
}
```

#### Blood Requests
```http
GET /api/requests
POST /api/requests
GET /api/requests/{id}
```

#### Users
```http
GET /api/users
POST /api/users/register
POST /api/users/login
```

#### Chat
```http
GET /api/chat/messages
POST /api/chat/messages
GET /api/chat/conversations
```

### Complete API Reference
For detailed API documentation, see the inline comments in `controllers/ApiController.php` or test endpoints using tools like Postman.

## 🔒 Security Features

### File Protection
- **`.htaccess`** - Apache security headers and URL rewriting
- **`logs/.htaccess`** - Protects logs directory from web access
- **Configuration files** - Secured outside web root

### Application Security
- **CSRF Protection** - All forms include CSRF tokens
- **XSS Prevention** - `htmlspecialchars()` for all output
- **SQL Injection Protection** - PDO prepared statements
- **Session Security** - HttpOnly cookies, secure session handling
- **Input Validation** - Comprehensive input sanitization

### Security Headers
```apache
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

## 🔧 Troubleshooting

### Common Issues

#### "Cannot connect to MySQL"
- Ensure XAMPP MySQL is running
- Check if port 3306 is available
- Verify MySQL credentials in setup

#### "PHP extension missing"
- Open XAMPP Control Panel
- Click "Config" → "php.ini"
- Uncomment required extensions (remove semicolon)
- Restart Apache

#### "Permission denied"
- Run Command Prompt as Administrator
- Check folder write permissions
- Ensure XAMPP has proper access

#### "Setup already completed"
- This is normal for subsequent runs
- The script handles existing data gracefully
- No action needed

### Getting Help
1. Check error messages in setup output
2. Verify XAMPP is running correctly
3. Check PHP version: `php -v`
4. Test MySQL: `mysql -u root -p`
5. Review logs in `logs/` directory

## 🚀 Quick Start After Installation

### 1. Explore the Website
- Visit http://localhost:8000
- Login as admin: admin@example.com / admin123
- Browse all features

### 2. Test Core Features
- Create a new blood request
- Register a donor account
- Test the donation confirmation system
- Try the chat feature
- Use the search functionality

### 3. API Testing
- Visit http://localhost:8000/api/health
- Test API endpoints with tools like Postman
- Check API responses and error handling

### 4. Customization
- Edit `config/config.php` for your settings
- Modify email settings in `services/EmailService.php`
- Customize the UI in `views/`
- Add new features following MVC pattern

## 📈 Performance Optimization

### Database Optimizations
- **Indexes** - All frequently queried columns have indexes
- **Prepared Statements** - Security and performance benefits
- **Efficient Queries** - Optimized joins and WHERE clauses

### Application Optimizations
- **Minimal Dependencies** - No external JavaScript libraries
- **Optimized Assets** - Compressed CSS and JavaScript
- **Efficient Routing** - Fast URL parsing and routing
- **Session Management** - Optimized session handling

### Monitoring
- **Health Check** - `/health.php` endpoint for monitoring
- **Search Analytics** - Track user search behavior
- **Email Logging** - Monitor email delivery
- **Error Logging** - Comprehensive error tracking

## 🔄 Updates & Maintenance

### Regular Maintenance
- Monitor logs in `logs/` directory
- Backup database regularly
- Update PHP and MySQL versions
- Review security settings

### Adding Features
- Follow MVC pattern for new features
- Add database migrations to setup script
- Update documentation
- Test thoroughly

## 🤝 Contributing

### Development Guidelines
1. **Follow MVC Pattern** - Keep controllers, models, and views separate
2. **Use PDO** - Always use prepared statements for database queries
3. **Security First** - Implement CSRF protection and input validation
4. **Responsive Design** - Ensure mobile compatibility
5. **Documentation** - Update README and inline comments

### Code Style
- Use meaningful variable and function names
- Add comments for complex logic
- Follow PSR-4 autoloading standards
- Maintain consistent indentation

## 📞 Support

### Documentation
- **README.md** - This comprehensive guide
- **Inline Comments** - Detailed code documentation
- **API Comments** - Complete API reference in code

### Getting Help
1. Check the documentation files
2. Review error logs in `logs/` directory
3. Test with the health endpoint: `/health.php`
4. Verify system requirements

## 📄 License

This project is open source and available under the **MIT License**.

```
MIT License

Copyright (c) 2024 Souls Blood Donation Website

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

---

## 🎉 Success!

Your Souls Blood Donation Website is now ready to use! The system includes:

- ✅ **Complete blood donation management**
- ✅ **Real-time chat and search**
- ✅ **REST API for mobile apps**
- ✅ **Advanced security features**
- ✅ **Responsive design**
- ✅ **Automated setup process**

**Start helping save lives today!** 🩸❤️

---

**Version:** 2.0.0  
**Last Updated:** 2024-01-16  
**Setup Time:** ~2 minutes  
**Total Features:** 15+ advanced features  
**License:** MIT
