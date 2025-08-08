-- DPS Sample Data SQL File
-- This file contains sample data for the DPS (Department of Public Safety) system
-- Includes admin users, employees, leave requests, payroll records, and related data

-- =====================================================
-- USERS (Admin and Employees)
-- =====================================================

-- Admin Users
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES
('Admin User', 'admin@dps.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW(), NOW()),
('HR Manager', 'hr@dps.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW(), NOW());

-- Employee Users
INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at) VALUES
('John Smith', 'john.smith@dps.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW(), NOW()),
('Maria Garcia', 'maria.garcia@dps.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW(), NOW()),
('Robert Johnson', 'robert.johnson@dps.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'employee', NOW(), NOW(), NOW());

-- =====================================================
-- LEAVE RULES
-- =====================================================

INSERT INTO leave_rules (leave_type, display_name, default_credits, description, is_active, created_at, updated_at) VALUES
('emergency', 'Emergency Leave', 5, 'Emergency leave for urgent personal matters', 1, NOW(), NOW()),
('sick', 'Sick Leave', 5, 'Medical leave for health-related issues', 1, NOW(), NOW()),
('vacation', 'Vacation Leave', 10, 'Annual vacation leave for rest and recreation', 1, NOW(), NOW());

-- =====================================================
-- LEAVE REQUESTS (Sample Data)
-- =====================================================

-- John Smith's Leave Requests
INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, days_requested, details, status, admin_notes, approved_by, approved_at, created_at, updated_at) VALUES
(4, 'vacation', '2024-12-20', '2024-12-22', 3, 'Family vacation to visit relatives in the province. Will ensure all pending work is completed before leave.', 'approved', 'Approved - Enjoy your vacation!', 1, NOW(), NOW(), NOW()),
(4, 'sick', '2024-12-15', '2024-12-15', 1, 'Not feeling well today, experiencing flu-like symptoms. Will return to work tomorrow.', 'approved', 'Get well soon!', 1, NOW(), NOW(), NOW()),
(4, 'emergency', '2024-12-10', '2024-12-10', 1, 'Emergency dental appointment due to severe toothache. Cannot be postponed.', 'approved', 'Emergency approved', 1, NOW(), NOW(), NOW());

-- Maria Garcia's Leave Requests
INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, days_requested, details, status, admin_notes, approved_by, approved_at, created_at, updated_at) VALUES
(5, 'vacation', '2024-12-25', '2024-12-27', 3, 'Christmas vacation with family. Will coordinate with team for work coverage.', 'pending', NULL, NULL, NULL, NOW(), NOW()),
(5, 'sick', '2024-12-12', '2024-12-13', 2, 'Medical appointment and recovery from minor surgery. Doctor\'s note provided.', 'approved', 'Medical leave approved with documentation', 1, NOW(), NOW(), NOW()),
(5, 'emergency', '2024-12-05', '2024-12-05', 1, 'Emergency family matter requiring immediate attention.', 'rejected', 'Please provide more details about the emergency', 1, NOW(), NOW(), NOW());

-- Robert Johnson's Leave Requests
INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, days_requested, details, status, admin_notes, approved_by, approved_at, created_at, updated_at) VALUES
(6, 'vacation', '2024-12-30', '2025-01-03', 4, 'New Year vacation with family. Will ensure all year-end tasks are completed.', 'pending', NULL, NULL, NULL, NOW(), NOW()),
(6, 'sick', '2024-12-18', '2024-12-18', 1, 'Not feeling well, experiencing headache and fever. Will return when symptoms improve.', 'approved', 'Take care and get well soon', 1, NOW(), NOW(), NOW()),
(6, 'emergency', '2024-12-08', '2024-12-08', 1, 'Emergency car repair appointment. Vehicle needed for work commute.', 'approved', 'Emergency approved', 1, NOW(), NOW(), NOW());

-- =====================================================
-- PAYROLL RECORDS (Sample Data)
-- =====================================================

-- John Smith's Payroll Records
INSERT INTO payrolls (user_id, start_date, end_date, month, year, period, basic_salary, total_days_worked, total_hours_worked, overtime_hours, leave_days, absent_days, gross_salary, overtime_pay, housing_allowance, transport_allowance, meal_allowance, other_allowance, total_allowances, sss_deduction, philhealth_deduction, pagibig_deduction, advance_deduction, other_deduction, total_deductions, net_salary, status, approved_by, approved_at, payment_date, payslip_generated, payslip_url, notes, created_at, updated_at) VALUES
(4, '2024-12-01', '2024-12-15', 12, 2024, '1st Half', 25000.00, 11, 88.00, 4.00, 1, 0, 12500.00, 312.50, 2000.00, 1500.00, 1000.00, 500.00, 5000.00, 1200.00, 600.00, 400.00, 0.00, 0.00, 2200.00, 15612.50, 'approved', 1, NOW(), NOW(), 1, NULL, 'First half December payroll', NOW(), NOW()),
(4, '2024-12-16', '2024-12-31', 12, 2024, '2nd Half', 25000.00, 10, 80.00, 2.00, 2, 0, 12500.00, 156.25, 2000.00, 1500.00, 1000.00, 500.00, 5000.00, 1200.00, 600.00, 400.00, 0.00, 0.00, 2200.00, 15456.25, 'pending', NULL, NULL, NULL, 0, NULL, 'Second half December payroll', NOW(), NOW());

-- Maria Garcia's Payroll Records
INSERT INTO payrolls (user_id, start_date, end_date, month, year, period, basic_salary, total_days_worked, total_hours_worked, overtime_hours, leave_days, absent_days, gross_salary, overtime_pay, housing_allowance, transport_allowance, meal_allowance, other_allowance, total_allowances, sss_deduction, philhealth_deduction, pagibig_deduction, advance_deduction, other_deduction, total_deductions, net_salary, status, approved_by, approved_at, payment_date, payslip_generated, payslip_url, notes, created_at, updated_at) VALUES
(5, '2024-12-01', '2024-12-15', 12, 2024, '1st Half', 30000.00, 12, 96.00, 6.00, 0, 0, 15000.00, 468.75, 2500.00, 1800.00, 1200.00, 600.00, 6100.00, 1400.00, 700.00, 500.00, 0.00, 0.00, 2600.00, 18968.75, 'approved', 1, NOW(), NOW(), 1, NULL, 'First half December payroll', NOW(), NOW()),
(5, '2024-12-16', '2024-12-31', 12, 2024, '2nd Half', 30000.00, 11, 88.00, 3.00, 1, 0, 15000.00, 234.38, 2500.00, 1800.00, 1200.00, 600.00, 6100.00, 1400.00, 700.00, 500.00, 0.00, 0.00, 2600.00, 18734.38, 'pending', NULL, NULL, NULL, 0, NULL, 'Second half December payroll', NOW(), NOW());

-- Robert Johnson's Payroll Records
INSERT INTO payrolls (user_id, start_date, end_date, month, year, period, basic_salary, total_days_worked, total_hours_worked, overtime_hours, leave_days, absent_days, gross_salary, overtime_pay, housing_allowance, transport_allowance, meal_allowance, other_allowance, total_allowances, sss_deduction, philhealth_deduction, pagibig_deduction, advance_deduction, other_deduction, total_deductions, net_salary, status, approved_by, approved_at, payment_date, payslip_generated, payslip_url, notes, created_at, updated_at) VALUES
(6, '2024-12-01', '2024-12-15', 12, 2024, '1st Half', 28000.00, 13, 104.00, 8.00, 0, 0, 14000.00, 625.00, 2200.00, 1600.00, 1100.00, 550.00, 5450.00, 1300.00, 650.00, 450.00, 0.00, 0.00, 2400.00, 17675.00, 'approved', 1, NOW(), NOW(), 1, NULL, 'First half December payroll', NOW(), NOW()),
(6, '2024-12-16', '2024-12-31', 12, 2024, '2nd Half', 28000.00, 12, 96.00, 5.00, 1, 0, 14000.00, 390.63, 2200.00, 1600.00, 1100.00, 550.00, 5450.00, 1300.00, 650.00, 450.00, 0.00, 0.00, 2400.00, 17440.63, 'pending', NULL, NULL, NULL, 0, NULL, 'Second half December payroll', NOW(), NOW());

-- =====================================================
-- ATTENDANCE RECORDS (Sample Data)
-- =====================================================

-- John Smith's Attendance (December 2024)
INSERT INTO attendances (user_id, date, clock_in, clock_out, notes, created_at, updated_at) VALUES
(4, '2024-12-01', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-02', '08:15:00', '17:30:00', 'Overtime work', NOW(), NOW()),
(4, '2024-12-03', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-04', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-05', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-06', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-07', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-08', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-09', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-10', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-11', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-12', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-13', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-14', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(4, '2024-12-15', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW());

-- Maria Garcia's Attendance (December 2024)
INSERT INTO attendances (user_id, date, clock_in, clock_out, notes, created_at, updated_at) VALUES
(5, '2024-12-01', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-02', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-03', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-04', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-05', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-06', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-07', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-08', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-09', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-10', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-11', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-12', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-13', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-14', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(5, '2024-12-15', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW());

-- Robert Johnson's Attendance (December 2024)
INSERT INTO attendances (user_id, date, clock_in, clock_out, notes, created_at, updated_at) VALUES
(6, '2024-12-01', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-02', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-03', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-04', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-05', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-06', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-07', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-08', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-09', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-10', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-11', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-12', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-13', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-14', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW()),
(6, '2024-12-15', '08:00:00', '17:00:00', 'Regular work day', NOW(), NOW());

-- =====================================================
-- APPLICANTS (Sample Data)
-- =====================================================

-- Sample Applicants
INSERT INTO applicants (firstname, middlename, lastname, age, sex, birthdate, height, weight, status, citizenship, barangay, municipality, province, country, email, parents_id, created_at, updated_at) VALUES
('Juan', 'Santos', 'Dela Cruz', 25, 'Male', '1999-05-15', 170, 65, 'Single', 'Filipino', 'Barangay 1', 'Manila', 'Metro Manila', 'Philippines', 'juan.delacruz@email.com', NULL, NOW(), NOW()),
('Ana', 'Maria', 'Santos', 28, 'Female', '1996-08-22', 160, 55, 'Single', 'Filipino', 'Barangay 2', 'Quezon City', 'Metro Manila', 'Philippines', 'ana.santos@email.com', NULL, NOW(), NOW()),
('Pedro', 'Garcia', 'Martinez', 30, 'Male', '1994-03-10', 175, 70, 'Married', 'Filipino', 'Barangay 3', 'Makati', 'Metro Manila', 'Philippines', 'pedro.martinez@email.com', NULL, NOW(), NOW());

-- =====================================================
-- APPLICATIONS (Sample Data)
-- =====================================================

INSERT INTO applications (applicant_id, status, interview_date, interview_time, interview_location, interviewer_name, interview_result, interview_score, interview_notes, created_at, updated_at) VALUES
(1, 'PENDING', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NOW(), NOW()),
(2, 'INTERVIEW_SCHEDULED', '2024-12-20', '10:00:00', 'Conference Room A', 'HR Manager', NULL, NULL, NULL, NOW(), NOW()),
(3, 'INTERVIEWED', '2024-12-18', '14:00:00', 'Conference Room B', 'HR Manager', 'PASSED', 85, 'Good communication skills and relevant experience', NOW(), NOW());

-- =====================================================
-- SAMPLE DATA SUMMARY
-- =====================================================

/*
SAMPLE DATA SUMMARY:

USERS:
- 2 Admin Users: admin@dps.gov.ph, hr@dps.gov.ph (password: admin123)
- 3 Employee Users: john.smith@dps.gov.ph, maria.garcia@dps.gov.ph, robert.johnson@dps.gov.ph (password: employee123)

LEAVE RULES:
- Emergency Leave: 5 credits
- Sick Leave: 5 credits  
- Vacation Leave: 10 credits

LEAVE REQUESTS:
- John Smith: 3 requests (2 approved, 1 pending)
- Maria Garcia: 3 requests (1 approved, 1 pending, 1 rejected)
- Robert Johnson: 3 requests (1 approved, 1 pending, 1 pending)

PAYROLL RECORDS:
- Each employee has 2 payroll records (1st and 2nd half of December 2024)
- Different salary levels and allowances
- Mix of approved and pending statuses

ATTENDANCE RECORDS:
- 15 days of attendance for each employee in December 2024
- Regular 8-hour work days

APPLICANTS:
- 3 sample applicants with different statuses
- Mix of pending, scheduled, and interviewed applications

LOGIN CREDENTIALS:
Admin Users:
- Email: admin@dps.gov.ph, Password: admin123
- Email: hr@dps.gov.ph, Password: admin123

Employee Users:
- Email: john.smith@dps.gov.ph, Password: employee123
- Email: maria.garcia@dps.gov.ph, Password: employee123
- Email: robert.johnson@dps.gov.ph, Password: employee123
*/

-- =====================================================
-- USAGE INSTRUCTIONS
-- =====================================================

/*
TO IMPORT THIS SAMPLE DATA:

1. Make sure your database is set up and migrations are run:
   php artisan migrate

2. Import the sample data:
   mysql -u your_username -p your_database_name < docs/sample_data.sql

3. Or run the SQL commands directly in your database management tool

4. Test the system with the provided login credentials

FEATURES TO TEST:

ADMIN FEATURES:
- Login as admin@dps.gov.ph or hr@dps.gov.ph
- View all leave requests
- Approve/reject leave requests
- Create payroll records
- View attendance records
- Manage applicants

EMPLOYEE FEATURES:
- Login as any employee user
- Submit leave requests
- View own payroll records
- Check attendance
- View leave credits

PAYROLL EMAIL FEATURE:
- Login as admin
- Go to payroll management
- Click email button to send payroll statements
- Check email functionality

LEAVE MANAGEMENT:
- Test leave request submission
- Test credit validation
- Test approval workflow
- Test employee restrictions
*/
