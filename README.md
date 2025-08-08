# DPS (Department of Public Safety) System

A comprehensive HR management system built with Laravel 11 and Vue.js, featuring payroll management, leave request system, attendance tracking, and applicant management.

## 🚀 Quick Start

### Automated Setup (Recommended)

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd dps_rework-main
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   # Edit .env file with your database credentials
   ```

3. **Run the startup script:**
   ```bash
   ./start.sh
   ```

The startup script will automatically:
- Install dependencies
- Generate application key
- Run database migrations
- Load sample data (if database is empty)
- Build frontend assets
- Start the development server

### Manual Setup

If you prefer manual setup:

1. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   # Edit .env file with your database credentials
   php artisan key:generate
   ```

3. **Set up database:**
   ```bash
   php artisan migrate
   php artisan dps:load-sample-data
   ```

4. **Build assets:**
   ```bash
   npm run build
   ```

5. **Start server:**
   ```bash
   php artisan serve
   ```

## 🔐 Login Credentials

### Admin Users
- **Email:** `admin@dps.gov.ph` | **Password:** `admin123`
- **Email:** `hr@dps.gov.ph` | **Password:** `admin123`

### Employee Users
- **Email:** `john.smith@dps.gov.ph` | **Password:** `employee123`
- **Email:** `maria.garcia@dps.gov.ph` | **Password:** `employee123`
- **Email:** `robert.johnson@dps.gov.ph` | **Password:** `employee123`

## 📊 Sample Data

The system comes with comprehensive sample data including:

- **5 Users** (2 admin, 3 employees)
- **3 Leave Rules** (Emergency, Sick, Vacation)
- **9 Leave Requests** (various statuses)
- **6 Payroll Records** (approved and pending)
- **45 Attendance Records** (15 days per employee)
- **3 Applicants** with applications

## 🛠️ Features

### Admin Features
- **User Management:** Create and manage users
- **Leave Management:** Approve/reject leave requests
- **Payroll Management:** Create and manage payroll records
- **Attendance Tracking:** View employee attendance
- **Applicant Management:** Review and process job applications
- **Email Integration:** Send payroll statements via email

### Employee Features
- **Leave Requests:** Submit and track leave requests
- **Payroll View:** View personal payroll records
- **Attendance:** Check personal attendance records
- **Profile Management:** Update personal information

### System Features
- **Role-Based Access Control:** Different permissions for admin and employees
- **Email Notifications:** Automated payroll email sending
- **Responsive Design:** Works on desktop and mobile devices
- **Real-time Validation:** Form validation and credit checking

## 📁 Project Structure

```
dps_rework-main/
├── app/
│   ├── Console/Commands/          # Artisan commands
│   ├── Http/Controllers/          # Application controllers
│   ├── Mail/                      # Email templates
│   ├── Models/                    # Eloquent models
│   └── Providers/                 # Service providers
├── database/
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
├── docs/                          # Documentation
├── resources/
│   ├── js/Pages/                  # Vue.js pages
│   └── views/                     # Blade templates
├── routes/                        # Application routes
├── start.sh                       # Automated startup script
└── README.md                      # This file
```

## 🔧 Available Commands

### Sample Data Management
```bash
# Load sample data (only if database is empty)
php artisan dps:load-sample-data

# Force reload sample data (clears existing data)
php artisan dps:load-sample-data --force
```

### Database Management
```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Reset database
php artisan migrate:fresh
```

### Development
```bash
# Start development server
php artisan serve

# Build frontend assets
npm run build

# Watch for frontend changes
npm run dev
```

## 📧 Email Configuration

The system includes email functionality for sending payroll statements. Configure your email settings in the `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email
MAIL_FROM_NAME="DPS System"
```

## 🧪 Testing

The system includes comprehensive tests for all major features:

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/PayrollEmailTest.php
```

## 📚 Documentation

Additional documentation is available in the `docs/` directory:

- `docs/payroll-email-feature.md` - Payroll email functionality
- `docs/employee-leave-request-system.md` - Leave request system
- `docs/sample_data.sql` - Sample data SQL file

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new features
5. Submit a pull request

## 📄 License

This project is proprietary software for the Department of Public Safety.

## 🆘 Support

For technical support or questions, please contact the development team.

---

**Note:** This system is designed for Philippine government use with specific features like "kinsenas" (bi-monthly) payroll calculations and local leave policies.
