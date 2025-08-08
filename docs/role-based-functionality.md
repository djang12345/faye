# Role-Based Functionality Documentation

## Overview
The DPS system now supports role-based access control with two main user roles: **Employee** and **Admin/HR Officer**. The payroll system follows the Philippine **kinsenas** (bi-monthly) format.

## User Roles

### Employee Role
**Features:**
- **Attendance Management**
  - Clock in and clock out functionality
  - View own attendance records
  - Real-time attendance status
- **Payroll Access**
  - View own payroll records
  - Access to detailed salary information
  - View payslip details
- **Logout**
  - Standard logout functionality

**Navigation Menu:**
- Dashboard
- Attendance (My Attendance)
- Payroll (My Payroll)
- Logout

### Admin/HR Officer Role
**Features:**
- **Dashboard**
  - Full system overview
  - Analytics and reports
- **Users Management**
  - Create, edit, delete users
  - Manage user roles
- **Applicants Management**
  - Review pending applications
  - Accept/reject applicants
  - View application history
- **Attendance Management**
  - View all employee attendance
  - Monitor attendance patterns
  - Generate attendance reports
- **Payroll Management**
  - Create payroll records (kinsenas format)
  - Manage detailed salary information
  - Update payroll status
  - Generate payroll reports
  - Approve payroll records

**Navigation Menu:**
- Dashboard
- Users
- Applicants (with submenu: Pending, Accepted, Rejected)
- Attendance (All Employees)
- Payroll (All Employees)
- Logout

## Database Structure

### Users Table
- Added `role` field (enum: 'employee', 'admin')
- Default role: 'employee'

### Attendances Table
- `user_id` - Foreign key to users
- `date` - Date of attendance
- `clock_in` - Clock in time
- `clock_out` - Clock out time
- `notes` - Optional notes
- Unique constraint on `user_id` and `date`

### Payrolls Table (Philippine Kinsenas Format)
- `user_id` - Foreign key to users
- **Pay Period**
  - `start_date` - Start date of pay period
  - `end_date` - End date of pay period
  - `month` - Month number (1-12)
  - `year` - Year
  - `period` - Kinsenas period ('1st Half', '2nd Half')
- **Basic Salary Information**
  - `basic_salary` - Monthly basic salary
- **Attendance-based Calculations**
  - `total_days_worked` - Total working days
  - `total_hours_worked` - Total hours worked
  - `overtime_hours` - Overtime hours
  - `leave_days` - Leave days taken
  - `absent_days` - Absent days
- **Earnings**
  - `gross_salary` - Half-month gross salary
  - `overtime_pay` - Overtime compensation
- **Allowances**
  - `housing_allowance` - Housing allowance
  - `transport_allowance` - Transport allowance
  - `meal_allowance` - Meal allowance
  - `other_allowance` - Other allowances
  - `total_allowances` - Total allowances
- **Deductions (No tax - as per agency requirement)**
  - `sss_deduction` - SSS contribution
  - `philhealth_deduction` - PhilHealth contribution
  - `pagibig_deduction` - Pag-IBIG contribution
  - `advance_deduction` - Salary advances
  - `other_deduction` - Other deductions
  - `total_deductions` - Total deductions
- **Final Calculation**
  - `net_salary` - Net salary after all calculations
- **Status Management**
  - `status` - Payroll status (draft, pending, approved, paid, cancelled)
  - `approved_by` - User who approved the payroll
  - `approved_at` - Approval timestamp
  - `payment_date` - Payment date
- **Payslip Features**
  - `payslip_generated` - Whether payslip was generated
  - `payslip_url` - Payslip file URL
- **Additional Information**
  - `notes` - Optional notes
- Unique constraint on `user_id`, `start_date`, `end_date`, and `period`

## Sample Users

### Admin User
- **Email:** admin@dps.gov.ph
- **Password:** admin123
- **Role:** admin

### Employee Users
- **Email:** employee@dps.gov.ph
- **Password:** employee123
- **Role:** employee

## Routes

### Employee Routes
- `GET /attendance` - View own attendance
- `POST /attendance/clock-in` - Clock in
- `POST /attendance/clock-out` - Clock out
- `GET /attendance/today` - Get today's attendance status
- `GET /payroll` - View own payroll records
- `GET /payroll/{id}` - View specific payroll record

### Admin Routes
- `GET /admin/attendance` - View all attendance records
- `GET /admin/payroll` - View all payroll records
- `GET /admin/payroll/create` - Create new payroll record
- `POST /admin/payroll` - Store new payroll record
- `GET /admin/payroll/{id}` - View specific payroll record
- `PATCH /admin/payroll/{id}/status` - Update payroll status
- `POST /admin/payroll/generate` - Generate payroll automatically

## Middleware

### CheckRole Middleware
- Validates user roles for protected routes
- Redirects unauthorized access
- Supports 'admin' and 'employee' role checks

## Vue Components

### Attendance Components
- `Attendance/Index.vue` - Main attendance page
- Clock in/out functionality for employees
- Attendance records table
- Real-time status updates

### Payroll Components
- `Payroll/Index.vue` - Main payroll page with kinsenas format
- `Payroll/Create.vue` - Create payroll record (admin only)
- `Payroll/Show.vue` - View detailed payroll information
- Detailed salary breakdown and calculations

## Features

### Attendance System
- **Clock In/Out:** Employees can clock in and out with time tracking
- **Daily Limits:** Prevents multiple clock-ins per day
- **Status Tracking:** Real-time attendance status
- **Admin View:** Admins can see all employee attendance
- **Hours Calculation:** Automatic calculation of work hours

### Payroll System (Philippine Kinsenas Format)
- **Bi-monthly Payroll:** 1st Half and 2nd Half periods
- **Salary Management:** Basic salary, allowances, and deductions
- **Net Salary Calculation:** Automatic calculation with Philippine deductions
- **Status Tracking:** Draft, pending, approved, paid, and cancelled statuses
- **Period Management:** Kinsenas periods with date ranges
- **Admin Controls:** Create and manage payroll records
- **Employee View:** Employees can view their own payroll
- **Detailed Breakdown:** Housing, transport, meal, and other allowances
- **Philippine Deductions:** SSS, PhilHealth, Pag-IBIG, advances
- **Overtime Calculation:** 1.25x rate for overtime hours
- **Working Days Calculation:** Excludes weekends automatically

## Philippine Payroll Features

### Kinsenas Periods
- **1st Half:** 1st to 15th of the month
- **2nd Half:** 16th to end of the month

### Allowances
- **Housing Allowance:** Housing subsidy
- **Transport Allowance:** Transportation subsidy
- **Meal Allowance:** Meal subsidy
- **Other Allowance:** Additional benefits

### Deductions (No Tax)
- **SSS:** Social Security System contribution
- **PhilHealth:** Philippine Health Insurance contribution
- **Pag-IBIG:** Home Development Mutual Fund contribution
- **Advance:** Salary advances
- **Other Deductions:** Additional deductions

### Calculations
- **Gross Salary:** Basic salary ÷ 2 (half month)
- **Overtime Pay:** Overtime hours × hourly rate × 1.25
- **Net Salary:** Gross + Overtime + Allowances - Deductions

## Security Features
- Role-based access control
- Middleware protection for routes
- User-specific data access
- Admin-only administrative functions

## Testing
1. **Login as Admin:**
   - Email: admin@dps.gov.ph
   - Password: admin123
   - Test all admin features

2. **Login as Employee:**
   - Email: employee@dps.gov.ph
   - Password: employee123
   - Test attendance and payroll access

## Future Enhancements
- Time-off management
- Leave requests
- Advanced reporting
- Email notifications
- Mobile app integration
- Biometric integration
- Advanced payroll calculations
- Payslip generation
- Bank integration
- Tax calculations (if required)
- Multiple currency support
