# 🧪 Testing Users Documentation

## Overview
This document contains sample user data for testing the DPS (Department of Public Safety) System.

## 👥 Sample Users

### Admin Users

#### 1. Super Admin
- **Email**: admin@dps.gov.ph
- **Password**: admin123
- **Name**: John Admin
- **Role**: Super Administrator
- **Permissions**: Full system access

#### 2. System Admin
- **Email**: system.admin@dps.gov.ph
- **Password**: system123
- **Name**: Maria Santos
- **Role**: System Administrator
- **Permissions**: User management, application review

#### 3. Department Admin
- **Email**: dept.admin@dps.gov.ph
- **Password**: dept123
- **Name**: Carlos Rodriguez
- **Role**: Department Administrator
- **Permissions**: Application processing, reporting

### Applicant Users

#### 1. Sample Applicant - Juan Dela Cruz
- **Email**: juan.delacruz@email.com
- **Password**: applicant123
- **Name**: Juan Dela Cruz
- **Age**: 25
- **Sex**: Male
- **Height**: 5'8"
- **Weight**: 70kg
- **Birthdate**: 1998-05-15
- **Status**: Single
- **Citizenship**: Filipino
- **Address**: 
  - Barangay: San Antonio
  - Municipality: Quezon City
  - Province: Metro Manila
  - Country: Philippines

#### 2. Sample Applicant - Maria Garcia
- **Email**: maria.garcia@email.com
- **Password**: applicant123
- **Name**: Maria Garcia
- **Age**: 28
- **Sex**: Female
- **Height**: 5'5"
- **Weight**: 55kg
- **Birthdate**: 1995-08-22
- **Status**: Married
- **Citizenship**: Filipino
- **Address**:
  - Barangay: Makati
  - Municipality: Makati City
  - Province: Metro Manila
  - Country: Philippines

#### 3. Sample Applicant - Pedro Santos
- **Email**: pedro.santos@email.com
- **Password**: applicant123
- **Name**: Pedro Santos
- **Age**: 30
- **Sex**: Male
- **Height**: 5'10"
- **Weight**: 75kg
- **Birthdate**: 1993-12-10
- **Status**: Single
- **Citizenship**: Filipino
- **Address**:
  - Barangay: Tondo
  - Municipality: Manila
  - Province: Metro Manila
  - Country: Philippines

## 👨‍👩‍👧‍👦 Sample Parent Information

### Juan Dela Cruz's Parents
- **Mother**: Ana Dela Cruz (Teacher)
- **Father**: Jose Dela Cruz (Engineer)

### Maria Garcia's Parents
- **Mother**: Carmen Garcia (Nurse)
- **Father**: Roberto Garcia (Police Officer)

### Pedro Santos's Parents
- **Mother**: Elena Santos (Business Owner)
- **Father**: Manuel Santos (Retired)

## 📋 Application Status Samples

### Pending Applications
- Juan Dela Cruz: PENDING
- Maria Garcia: PENDING

### Processed Applications
- Pedro Santos: ACCEPTED (Interview scheduled)

## 🔐 Login Credentials Summary

### Admin Access
```
Email: admin@dps.gov.ph
Password: admin123

Email: system.admin@dps.gov.ph
Password: system123

Email: dept.admin@dps.gov.ph
Password: dept123
```

### Applicant Access
```
Email: juan.delacruz@email.com
Password: applicant123

Email: maria.garcia@email.com
Password: applicant123

Email: pedro.santos@email.com
Password: applicant123
```

## 🧪 Testing Scenarios

### 1. Admin Testing
- **Login as Admin**: Test full system access
- **User Management**: Create, edit, delete users
- **Application Review**: Process pending applications
- **Reports**: Generate system reports

### 2. Applicant Testing
- **Registration**: Create new applicant account
- **Application Submission**: Submit new application
- **Status Check**: View application status
- **Profile Update**: Update personal information

### 3. System Testing
- **Authentication**: Test login/logout
- **Authorization**: Test role-based access
- **Data Validation**: Test form submissions
- **Email Notifications**: Test email functionality

## 📊 Database Sample Data

### Users Table
```sql
-- Admin Users
INSERT INTO users (name, email, password, created_at, updated_at) VALUES
('John Admin', 'admin@dps.gov.ph', '$2y$10$...', NOW(), NOW()),
('Maria Santos', 'system.admin@dps.gov.ph', '$2y$10$...', NOW(), NOW()),
('Carlos Rodriguez', 'dept.admin@dps.gov.ph', '$2y$10$...', NOW(), NOW());

-- Applicant Users
INSERT INTO users (name, email, password, created_at, updated_at) VALUES
('Juan Dela Cruz', 'juan.delacruz@email.com', '$2y$10$...', NOW(), NOW()),
('Maria Garcia', 'maria.garcia@email.com', '$2y$10$...', NOW(), NOW()),
('Pedro Santos', 'pedro.santos@email.com', '$2y$10$...', NOW(), NOW());
```

### Applicants Table
```sql
INSERT INTO applicants (firstname, lastname, email, age, sex, height, weight, birthdate, status, citizenship, barangay, municipality, province, country, created_at, updated_at) VALUES
('Juan', 'Dela Cruz', 'juan.delacruz@email.com', '25', 'Male', '5\'8"', '70kg', '1998-05-15', 'Single', 'Filipino', 'San Antonio', 'Quezon City', 'Metro Manila', 'Philippines', NOW(), NOW()),
('Maria', 'Garcia', 'maria.garcia@email.com', '28', 'Female', '5\'5"', '55kg', '1995-08-22', 'Married', 'Filipino', 'Makati', 'Makati City', 'Metro Manila', 'Philippines', NOW(), NOW()),
('Pedro', 'Santos', 'pedro.santos@email.com', '30', 'Male', '5\'10"', '75kg', '1993-12-10', 'Single', 'Filipino', 'Tondo', 'Manila', 'Metro Manila', 'Philippines', NOW(), NOW());
```

## 🚀 Quick Setup Commands

### Add Sample Users to Database
```bash
# Run the seeder
php artisan db:seed --class=SampleDataSeeder

# Or manually add users via tinker
php artisan tinker
```

### Test Login
```bash
# Test admin login
curl -X POST http://localhost/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@dps.gov.ph","password":"admin123"}'

# Test applicant login
curl -X POST http://localhost/login \
  -H "Content-Type: application/json" \
  -d '{"email":"juan.delacruz@email.com","password":"applicant123"}'
```

## 📝 Notes

- All passwords are hashed using Laravel's bcrypt
- Email addresses are fictional for testing purposes
- Personal information is sample data only
- Application statuses are for demonstration

## 🔄 Maintenance

### Update Sample Data
```bash
# Clear existing data
php artisan migrate:fresh

# Add new sample data
php artisan db:seed --class=SampleDataSeeder
```

### Backup Sample Data
```bash
# Export sample data
php artisan tinker --execute="echo json_encode(\App\Models\User::all()->toArray());"
```

---

**Last Updated**: August 7, 2025
**Version**: 1.0
**Status**: Ready for Testing
