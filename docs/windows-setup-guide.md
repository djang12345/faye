# 🪟 Windows Setup Guide - DPS System

## 📋 Prerequisites

Before setting up the DPS (Department of Public Safety) System on Windows, ensure you have the following installed:

### 🔧 Required Software

#### 1. **XAMPP** (Recommended)
- **Download**: https://www.apachefriends.org/download.html
- **Version**: 8.2.12 or higher
- **Includes**: Apache, MySQL, PHP 8.3, phpMyAdmin
- **Alternative**: WAMP, Laragon, or individual installations

#### 2. **Composer** (PHP Package Manager)
- **Download**: https://getcomposer.org/download/
- **Version**: 2.7.1 or higher
- **Installation**: Run the installer and follow the setup wizard

#### 3. **Node.js** (Frontend Build Tools)
- **Download**: https://nodejs.org/
- **Version**: 18.x or higher (LTS recommended)
- **Includes**: npm (Node Package Manager)

#### 4. **Git** (Version Control)
- **Download**: https://git-scm.com/download/win
- **Version**: 2.x or higher
- **Installation**: Use default settings during installation

### 💻 System Requirements

#### Minimum Requirements
- **OS**: Windows 10/11 (64-bit)
- **RAM**: 4GB (8GB recommended)
- **Storage**: 2GB free space
- **Processor**: Intel/AMD dual-core or higher

#### Recommended Requirements
- **OS**: Windows 11 (64-bit)
- **RAM**: 8GB or higher
- **Storage**: 5GB free space
- **Processor**: Intel i5/AMD Ryzen 5 or higher

## 🚀 Installation Steps

### Step 1: Install XAMPP

1. **Download XAMPP**
   - Visit https://www.apachefriends.org/download.html
   - Download the latest version for Windows
   - Choose the version with PHP 8.3

2. **Install XAMPP**
   ```bash
   # Run the installer as Administrator
   # Install to C:\xampp (recommended)
   # Select components: Apache, MySQL, PHP, phpMyAdmin
   ```

3. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL
   - Verify both services are running (green status)

### Step 2: Install Composer

1. **Download Composer**
   - Visit https://getcomposer.org/download/
   - Download Composer-Setup.exe

2. **Install Composer**
   ```bash
   # Run Composer-Setup.exe
   # Follow the installation wizard
   # Ensure PHP is detected from XAMPP
   ```

3. **Verify Installation**
   ```bash
   # Open Command Prompt
   composer --version
   ```

### Step 3: Install Node.js

1. **Download Node.js**
   - Visit https://nodejs.org/
   - Download LTS version (recommended)

2. **Install Node.js**
   ```bash
   # Run the installer
   # Use default settings
   # Ensure npm is included
   ```

3. **Verify Installation**
   ```bash
   # Open Command Prompt
   node --version
   npm --version
   ```

### Step 4: Install Git

1. **Download Git**
   - Visit https://git-scm.com/download/win
   - Download the latest version

2. **Install Git**
   ```bash
   # Run the installer
   # Use default settings
   # Ensure Git Bash is included
   ```

3. **Verify Installation**
   ```bash
   # Open Command Prompt
   git --version
   ```

## 📥 Project Setup

### Step 1: Clone the Repository

```bash
# Open Command Prompt or Git Bash
cd C:\xampp\htdocs
git clone https://github.com/your-username/dps-system.git
cd dps-system
```

### Step 2: Install PHP Dependencies

```bash
# Install Laravel dependencies
composer install

# If you get SSL errors, try:
composer install --ignore-platform-reqs
```

### Step 3: Configure Environment

```bash
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

1. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create new database: `dps_2`
   - Set collation: `utf8mb4_unicode_ci`

2. **Update .env File**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=dps_2
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Step 5: Run Database Migrations

```bash
# Run migrations
php artisan migrate

# Seed sample data
php artisan db:seed --class=SampleDataSeeder
```

### Step 6: Install Node.js Dependencies

```bash
# Install frontend dependencies
npm install

# Build frontend assets
npm run build
```

### Step 7: Configure Apache Virtual Host

1. **Edit Apache Configuration**
   - Open: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   - Add the following:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/dps-system/public"
    ServerName dps.local
    ServerAlias localhost
    
    <Directory "C:/xampp/htdocs/dps-system/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/dps_error.log"
    CustomLog "logs/dps_access.log" combined
</VirtualHost>
```

2. **Edit Hosts File**
   - Open: `C:\Windows\System32\drivers\etc\hosts`
   - Add: `127.0.0.1 dps.local`

3. **Restart Apache**
   - Open XAMPP Control Panel
   - Stop and Start Apache

## 🧪 Testing the Setup

### Step 1: Verify Services

```bash
# Check if services are running
# XAMPP Control Panel should show:
# - Apache: Running (green)
# - MySQL: Running (green)
```

### Step 2: Test Application

1. **Access the Application**
   - Open browser: http://localhost
   - Or: http://dps.local

2. **Test Sample Users**

#### Admin Users
| Email | Password | Role |
|-------|----------|------|
| `admin@dps.gov.ph` | `admin123` | Super Admin |
| `system.admin@dps.gov.ph` | `system123` | System Admin |
| `dept.admin@dps.gov.ph` | `dept123` | Department Admin |

#### Applicant Users
| Email | Password | Status |
|-------|----------|--------|
| `juan.delacruz@email.com` | `applicant123` | PENDING |
| `maria.garcia@email.com` | `applicant123` | PENDING |
| `pedro.santos@email.com` | `applicant123` | ACCEPTED |

## 🛠️ Development Commands

### Laravel Commands
```bash
# Start development server
php artisan serve

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed --class=SampleDataSeeder
```

### Frontend Commands
```bash
# Install dependencies
npm install

# Build for production
npm run build

# Watch for changes (development)
npm run dev
```

### Database Commands
```bash
# Access MySQL via command line
mysql -u root -p

# Or use phpMyAdmin
# http://localhost/phpmyadmin
```

## 🔧 Troubleshooting

### Common Issues

#### 1. **Composer SSL Errors**
```bash
# Disable SSL verification (not recommended for production)
composer config --global disable-tls true
composer config --global secure-http false
```

#### 2. **Permission Issues**
```bash
# Run Command Prompt as Administrator
# Or modify folder permissions
```

#### 3. **Port Conflicts**
```bash
# Check if ports are in use
netstat -ano | findstr :80
netstat -ano | findstr :3306

# Change ports in XAMPP if needed
```

#### 4. **PHP Extensions Missing**
```bash
# Enable extensions in php.ini
# Location: C:\xampp\php\php.ini
# Uncomment: extension=curl, extension=mbstring, extension=openssl
```

#### 5. **Node.js Build Errors**
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and reinstall
rmdir /s node_modules
npm install
```

### Error Logs

#### Apache Error Log
```
C:\xampp\apache\logs\error.log
```

#### Laravel Error Log
```
storage\logs\laravel.log
```

#### PHP Error Log
```
C:\xampp\php\logs\php_error_log
```

## 📚 Additional Resources

### Documentation
- **Laravel**: https://laravel.com/docs
- **XAMPP**: https://www.apachefriends.org/docs.html
- **Composer**: https://getcomposer.org/doc/
- **Node.js**: https://nodejs.org/docs/

### Useful Tools
- **phpMyAdmin**: http://localhost/phpmyadmin
- **XAMPP Control Panel**: Start menu → XAMPP
- **Git Bash**: Alternative to Command Prompt

### Development Tools (Optional)
- **VS Code**: https://code.visualstudio.com/
- **PHPStorm**: https://www.jetbrains.com/phpstorm/
- **MySQL Workbench**: https://www.mysql.com/products/workbench/

## 🔐 Security Notes

### For Development
- Default passwords are for testing only
- Change passwords before production use
- Keep XAMPP updated
- Use firewall to restrict access

### For Production
- Use strong passwords
- Enable SSL/HTTPS
- Configure proper file permissions
- Set up regular backups
- Use environment variables for sensitive data

## 📞 Support

### Getting Help
1. Check error logs first
2. Verify all services are running
3. Test with sample users
4. Check documentation files in `docs/` folder

### Useful Commands
```bash
# Check PHP version
php --version

# Check Composer version
composer --version

# Check Node.js version
node --version

# Check Git version
git --version

# Check Apache status
# XAMPP Control Panel
```

---

**Last Updated**: August 7, 2025
**Version**: 1.0
**Compatibility**: Windows 10/11
**Status**: ✅ Ready for Windows Setup
