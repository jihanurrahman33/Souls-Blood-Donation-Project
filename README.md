# 🩸 Souls - Blood Donation Website

A comprehensive blood donation management system built with PHP, MySQL, and modern web technologies. Compatible with macOS, Windows, and Linux.

## 🌟 Features

- **User Management**: Registration, login, and profile management
- **Blood Donation**: Submit and manage blood donation requests
- **Blood Requests**: Request blood donations with urgency levels
- **Forum System**: Community discussions and support
- **Admin Panel**: Complete administrative control
- **Real-time Chat**: User communication system
- **Search Functionality**: Advanced search and filtering
- **API Endpoints**: RESTful API for mobile/desktop applications
- **Responsive Design**: Works on desktop, tablet, and mobile

## 🖥️ System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB 10.2+)
- **Web Server**: Apache 2.4+ or Nginx
- **Extensions**: PDO, PDO_MySQL, JSON, cURL, OpenSSL, Fileinfo

## 🚀 Quick Start

### Prerequisites
1. **Install XAMPP** from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. **Start XAMPP** and ensure Apache and MySQL are running
3. **Download/Clone** the project to your web server directory

### Setup Process
```bash
# Navigate to setup folder
cd setup/

# Run deployment check
php deploy.php

# Run setup script
php setup.php
```

### Access Application
- **URL**: `http://localhost/souls/`
- **Admin Login**: `admin` / `admin123`

## 📋 Platform-Specific Instructions

### 🍎 macOS with XAMPP

1. **Install XAMPP** from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. **Start XAMPP** and ensure Apache and MySQL are running
3. **Place project** in `/Applications/XAMPP/xamppfiles/htdocs/souls/`
4. **Run setup**:
   ```bash
   cd /Applications/XAMPP/xamppfiles/htdocs/souls
   cd setup/
   php deploy.php
   php setup.php
   ```
5. **Access**: `http://localhost/souls/`

### 🪟 Windows with XAMPP

1. **Install XAMPP** from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. **Start XAMPP** and ensure Apache and MySQL are running
3. **Place project** in `C:\xampp\htdocs\souls\`
4. **Run setup**:
   ```cmd
   cd C:\xampp\htdocs\souls
   cd setup
   php deploy.php
   php setup.php
   ```
5. **Access**: `http://localhost/souls/`

### 🐧 Linux (Ubuntu/Debian)

1. **Install LAMP Stack**:
   ```bash
   sudo apt update
   sudo apt install apache2 mysql-server php php-mysql php-curl php-json php-openssl php-fileinfo
   ```

2. **Configure MySQL**:
   ```bash
   sudo mysql_secure_installation
   sudo mysql -u root -p
   CREATE DATABASE blood_donation;
   ```

3. **Place project** in `/var/www/html/souls/`
4. **Set permissions**:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/souls
   sudo chmod -R 755 /var/www/html/souls
   ```

5. **Run setup**:
   ```bash
   cd /var/www/html/souls
   cd setup/
   php deploy.php
   php setup.php
   ```

6. **Access**: `http://localhost/souls/`

## 📁 Project Structure

```
souls/
├── 📁 .git/                    # Git repository (version control)
├── 📄 .htaccess               # Apache configuration & security
├── 📄 api.php                 # API endpoints handler
├── 📁 assets/                 # Static assets (CSS, JS, images)
├── 📁 config/                 # Configuration files
│   ├── 📄 config.php         # Main configuration
│   ├── 📄 database.php       # Database connection
│   └── 📄 platform_config.php # Platform-specific settings
├── 📁 controllers/            # MVC Controllers
├── 📄 health.php              # Health check endpoint
├── 📄 index.php               # Main application router
├── 📁 logs/                   # Application logs (protected)
├── 📁 models/                 # MVC Models
├── 📄 README.md               # This documentation file
├── 📁 services/               # Business logic services
├── 📁 setup/                  # Setup folder for new devices
│   ├── 📄 setup.php          # Complete setup script
│   ├── 📄 setup_database.sql # Database schema
│   ├── 📄 deploy.php         # Cross-platform deployment checker
│   ├── 📄 deploy.sh          # macOS/Linux deployment script
│   └── 📄 deploy.bat         # Windows deployment script
├── 📁 temp/                   # Temporary files
├── 📁 uploads/                # File uploads
└── 📁 views/                  # MVC Views
```

## 🔧 Configuration

### Database Configuration
The application automatically detects your platform and configures the database connection. For manual configuration, edit `config/config.php`:

```php
// Database Configuration
define("DB_HOST", "localhost");
define("DB_NAME", "blood_donation");
define("DB_USER", "root");
define("DB_PASS", "");

// Application Configuration
define("APP_NAME", "Souls");
define("APP_URL", "http://localhost/souls/");
```

### Email Configuration
Update email settings in `config/config.php` for notifications:

```php
// Email Configuration
define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_PORT", 587);
define("SMTP_USERNAME", "your-email@gmail.com");
define("SMTP_PASSWORD", "your-app-password");
```

## 👤 Default Admin Account

After setup, you can login with the default admin account:

- **Username**: `admin`
- **Password**: `admin123`

**Important**: Change the default password after first login!

## 🔌 API Endpoints

The application provides RESTful API endpoints:

- `GET /api/health` - Health check
- `GET /api/donations` - Get all donations
- `GET /api/requests` - Get all blood requests
- `GET /api/forum` - Get forum posts
- `GET /api/users` - Get all users
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration

## 📊 Available Pages

- **🏠 Home**: `http://localhost/souls/`
- **🔐 Login**: `http://localhost/souls/auth/login`
- **📝 Register**: `http://localhost/souls/auth/register`
- **🩸 Donate**: `http://localhost/souls/donate`
- **🩸 Request**: `http://localhost/souls/request`
- **💬 Forum**: `http://localhost/souls/forum`
- **👑 Admin**: `http://localhost/souls/admin`
- **🔍 Search**: `http://localhost/souls/search`

## 🛠️ Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Ensure MySQL is running
   - Check database credentials in `config/config.php`
   - Verify database exists: `blood_donation`

2. **Permission Denied**
   - Set proper file permissions: `chmod 755 logs uploads temp`
   - Ensure web server can write to these directories

3. **Page Not Found**
   - Check if Apache/Nginx is running
   - Verify `.htaccess` file exists
   - Ensure mod_rewrite is enabled

4. **Setup Script Errors**
   - Run `cd setup && php deploy.php` to check system requirements
   - Ensure all PHP extensions are installed
   - Check error logs in `logs/` directory

### Platform-Specific Issues

#### macOS
- Check XAMPP Control Panel for service status
- Verify MySQL socket: `/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock`
- Check XAMPP logs in `/Applications/XAMPP/xamppfiles/logs/`

#### Windows
- Check Windows Services for MySQL and Apache
- Verify firewall settings
- Check XAMPP logs in `C:\xampp\logs\`

#### Linux
- Check service status: `sudo systemctl status mysql apache2`
- Check logs: `sudo tail -f /var/log/apache2/error.log`
- Verify MySQL socket: `/var/run/mysqld/mysqld.sock`

## 🔒 Security Considerations

1. **Change Default Passwords**: Update admin and database passwords
2. **HTTPS**: Use SSL certificates for production deployment
3. **File Permissions**: Set appropriate file and directory permissions
4. **Database Security**: Use strong database passwords
5. **Regular Updates**: Keep PHP, MySQL, and application updated

## 📞 Support

For issues and questions:

1. **Check Troubleshooting** section above
2. **Run Deployment Check**: `cd setup && php deploy.php`
3. **Check Logs**: Review files in `logs/` directory
4. **Verify Requirements**: Ensure all system requirements are met

## 📄 License

This project is open source and available under the MIT License.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests.

---

**Souls Blood Donation Website** - Making blood donation accessible and efficient across all platforms! 🩸❤️
