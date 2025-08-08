<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\LeaveRule;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\Applicant;
use App\Models\Application;
use Illuminate\Support\Facades\Hash;

class LoadSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dps:load-sample-data {--force : Force reload even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load sample data for DPS system if database is empty';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking database status...');

        // Check if database has any data
        $hasData = $this->checkIfDatabaseHasData();

        if ($hasData && !$this->option('force')) {
            $this->warn('⚠️  Database already contains data. Use --force to reload sample data.');
            return 1;
        }

        if ($this->option('force')) {
            $this->warn('🔄 Force reloading sample data...');
            $this->clearExistingData();
        }

        $this->info('📦 Loading sample data...');

        try {
            $this->loadUsers();
            $this->loadLeaveRules();
            $this->loadLeaveRequests();
            $this->loadPayrollRecords();
            $this->loadAttendanceRecords();
            $this->loadApplicants();
            $this->loadApplications();

            $this->info('✅ Sample data loaded successfully!');
            $this->displayLoginCredentials();

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error loading sample data: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if database has any data
     */
    private function checkIfDatabaseHasData(): bool
    {
        return User::count() > 0 || LeaveRule::count() > 0;
    }

    /**
     * Clear existing data
     */
    private function clearExistingData(): void
    {
        $this->info('🗑️  Clearing existing data...');
        
        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear in reverse order of dependencies
        Application::truncate();
        Applicant::truncate();
        Attendance::truncate();
        Payroll::truncate();
        LeaveRequest::truncate();
        LeaveRule::truncate();
        User::truncate();
        
        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Load users
     */
    private function loadUsers(): void
    {
        $this->info('👥 Loading users...');

        // Admin Users
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@dps.gov.ph',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'HR Manager',
            'email' => 'hr@dps.gov.ph',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Employee Users
        User::create([
            'name' => 'John Smith',
            'email' => 'john.smith@dps.gov.ph',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Maria Garcia',
            'email' => 'maria.garcia@dps.gov.ph',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Robert Johnson',
            'email' => 'robert.johnson@dps.gov.ph',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
            'email_verified_at' => now(),
        ]);

        $this->info('✅ Users loaded: 5');
    }

    /**
     * Load leave rules
     */
    private function loadLeaveRules(): void
    {
        $this->info('📋 Loading leave rules...');

        LeaveRule::create([
            'leave_type' => 'emergency',
            'display_name' => 'Emergency Leave',
            'default_credits' => 5,
            'description' => 'Emergency leave for urgent personal matters',
            'is_active' => true,
        ]);

        LeaveRule::create([
            'leave_type' => 'sick',
            'display_name' => 'Sick Leave',
            'default_credits' => 5,
            'description' => 'Medical leave for health-related issues',
            'is_active' => true,
        ]);

        LeaveRule::create([
            'leave_type' => 'vacation',
            'display_name' => 'Vacation Leave',
            'default_credits' => 10,
            'description' => 'Annual vacation leave for rest and recreation',
            'is_active' => true,
        ]);

        $this->info('✅ Leave rules loaded: 3');
    }

    /**
     * Load leave requests
     */
    private function loadLeaveRequests(): void
    {
        $this->info('📝 Loading leave requests...');

        $admin = User::where('role', 'admin')->first();
        $john = User::where('email', 'john.smith@dps.gov.ph')->first();
        $maria = User::where('email', 'maria.garcia@dps.gov.ph')->first();
        $robert = User::where('email', 'robert.johnson@dps.gov.ph')->first();

        // John Smith's Leave Requests
        LeaveRequest::create([
            'user_id' => $john->id,
            'leave_type' => 'vacation',
            'start_date' => '2024-12-20',
            'end_date' => '2024-12-22',
            'days_requested' => 3,
            'details' => 'Family vacation to visit relatives in the province. Will ensure all pending work is completed before leave.',
            'status' => 'approved',
            'admin_notes' => 'Approved - Enjoy your vacation!',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        LeaveRequest::create([
            'user_id' => $john->id,
            'leave_type' => 'sick',
            'start_date' => '2024-12-15',
            'end_date' => '2024-12-15',
            'days_requested' => 1,
            'details' => 'Not feeling well today, experiencing flu-like symptoms. Will return to work tomorrow.',
            'status' => 'approved',
            'admin_notes' => 'Get well soon!',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        LeaveRequest::create([
            'user_id' => $john->id,
            'leave_type' => 'emergency',
            'start_date' => '2024-12-10',
            'end_date' => '2024-12-10',
            'days_requested' => 1,
            'details' => 'Emergency dental appointment due to severe toothache. Cannot be postponed.',
            'status' => 'approved',
            'admin_notes' => 'Emergency approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        // Maria Garcia's Leave Requests
        LeaveRequest::create([
            'user_id' => $maria->id,
            'leave_type' => 'vacation',
            'start_date' => '2024-12-25',
            'end_date' => '2024-12-27',
            'days_requested' => 3,
            'details' => 'Christmas vacation with family. Will coordinate with team for work coverage.',
            'status' => 'pending',
        ]);

        LeaveRequest::create([
            'user_id' => $maria->id,
            'leave_type' => 'sick',
            'start_date' => '2024-12-12',
            'end_date' => '2024-12-13',
            'days_requested' => 2,
            'details' => 'Medical appointment and recovery from minor surgery. Doctor\'s note provided.',
            'status' => 'approved',
            'admin_notes' => 'Medical leave approved with documentation',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        LeaveRequest::create([
            'user_id' => $maria->id,
            'leave_type' => 'emergency',
            'start_date' => '2024-12-05',
            'end_date' => '2024-12-05',
            'days_requested' => 1,
            'details' => 'Emergency family matter requiring immediate attention.',
            'status' => 'rejected',
            'admin_notes' => 'Please provide more details about the emergency',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        // Robert Johnson's Leave Requests
        LeaveRequest::create([
            'user_id' => $robert->id,
            'leave_type' => 'vacation',
            'start_date' => '2024-12-30',
            'end_date' => '2025-01-03',
            'days_requested' => 4,
            'details' => 'New Year vacation with family. Will ensure all year-end tasks are completed.',
            'status' => 'pending',
        ]);

        LeaveRequest::create([
            'user_id' => $robert->id,
            'leave_type' => 'sick',
            'start_date' => '2024-12-18',
            'end_date' => '2024-12-18',
            'days_requested' => 1,
            'details' => 'Not feeling well, experiencing headache and fever. Will return when symptoms improve.',
            'status' => 'approved',
            'admin_notes' => 'Take care and get well soon',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        LeaveRequest::create([
            'user_id' => $robert->id,
            'leave_type' => 'emergency',
            'start_date' => '2024-12-08',
            'end_date' => '2024-12-08',
            'days_requested' => 1,
            'details' => 'Emergency car repair appointment. Vehicle needed for work commute.',
            'status' => 'approved',
            'admin_notes' => 'Emergency approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $this->info('✅ Leave requests loaded: 9');
    }

    /**
     * Load payroll records
     */
    private function loadPayrollRecords(): void
    {
        $this->info('💰 Loading payroll records...');

        $john = User::where('email', 'john.smith@dps.gov.ph')->first();
        $maria = User::where('email', 'maria.garcia@dps.gov.ph')->first();
        $robert = User::where('email', 'robert.johnson@dps.gov.ph')->first();
        $admin = User::where('role', 'admin')->first();

        // John Smith's Payroll Records
        Payroll::create([
            'user_id' => $john->id,
            'start_date' => '2024-12-01',
            'end_date' => '2024-12-15',
            'month' => 12,
            'year' => 2024,
            'period' => '1st Half',
            'basic_salary' => 25000.00,
            'total_days_worked' => 11,
            'total_hours_worked' => 88.00,
            'overtime_hours' => 4.00,
            'leave_days' => 1,
            'absent_days' => 0,
            'gross_salary' => 12500.00,
            'overtime_pay' => 312.50,
            'housing_allowance' => 2000.00,
            'transport_allowance' => 1500.00,
            'meal_allowance' => 1000.00,
            'other_allowance' => 500.00,
            'total_allowances' => 5000.00,
            'sss_deduction' => 1200.00,
            'philhealth_deduction' => 600.00,
            'pagibig_deduction' => 400.00,
            'advance_deduction' => 0.00,
            'other_deduction' => 0.00,
            'total_deductions' => 2200.00,
            'net_salary' => 15612.50,
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'payment_date' => now(),
            'payslip_generated' => true,
            'notes' => 'First half December payroll',
        ]);

        Payroll::create([
            'user_id' => $john->id,
            'start_date' => '2024-12-16',
            'end_date' => '2024-12-31',
            'month' => 12,
            'year' => 2024,
            'period' => '2nd Half',
            'basic_salary' => 25000.00,
            'total_days_worked' => 10,
            'total_hours_worked' => 80.00,
            'overtime_hours' => 2.00,
            'leave_days' => 2,
            'absent_days' => 0,
            'gross_salary' => 12500.00,
            'overtime_pay' => 156.25,
            'housing_allowance' => 2000.00,
            'transport_allowance' => 1500.00,
            'meal_allowance' => 1000.00,
            'other_allowance' => 500.00,
            'total_allowances' => 5000.00,
            'sss_deduction' => 1200.00,
            'philhealth_deduction' => 600.00,
            'pagibig_deduction' => 400.00,
            'advance_deduction' => 0.00,
            'other_deduction' => 0.00,
            'total_deductions' => 2200.00,
            'net_salary' => 15456.25,
            'status' => 'pending',
            'notes' => 'Second half December payroll',
        ]);

        // Maria Garcia's Payroll Records
        Payroll::create([
            'user_id' => $maria->id,
            'start_date' => '2024-12-01',
            'end_date' => '2024-12-15',
            'month' => 12,
            'year' => 2024,
            'period' => '1st Half',
            'basic_salary' => 30000.00,
            'total_days_worked' => 12,
            'total_hours_worked' => 96.00,
            'overtime_hours' => 6.00,
            'leave_days' => 0,
            'absent_days' => 0,
            'gross_salary' => 15000.00,
            'overtime_pay' => 468.75,
            'housing_allowance' => 2500.00,
            'transport_allowance' => 1800.00,
            'meal_allowance' => 1200.00,
            'other_allowance' => 600.00,
            'total_allowances' => 6100.00,
            'sss_deduction' => 1400.00,
            'philhealth_deduction' => 700.00,
            'pagibig_deduction' => 500.00,
            'advance_deduction' => 0.00,
            'other_deduction' => 0.00,
            'total_deductions' => 2600.00,
            'net_salary' => 18968.75,
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'payment_date' => now(),
            'payslip_generated' => true,
            'notes' => 'First half December payroll',
        ]);

        Payroll::create([
            'user_id' => $maria->id,
            'start_date' => '2024-12-16',
            'end_date' => '2024-12-31',
            'month' => 12,
            'year' => 2024,
            'period' => '2nd Half',
            'basic_salary' => 30000.00,
            'total_days_worked' => 11,
            'total_hours_worked' => 88.00,
            'overtime_hours' => 3.00,
            'leave_days' => 1,
            'absent_days' => 0,
            'gross_salary' => 15000.00,
            'overtime_pay' => 234.38,
            'housing_allowance' => 2500.00,
            'transport_allowance' => 1800.00,
            'meal_allowance' => 1200.00,
            'other_allowance' => 600.00,
            'total_allowances' => 6100.00,
            'sss_deduction' => 1400.00,
            'philhealth_deduction' => 700.00,
            'pagibig_deduction' => 500.00,
            'advance_deduction' => 0.00,
            'other_deduction' => 0.00,
            'total_deductions' => 2600.00,
            'net_salary' => 18734.38,
            'status' => 'pending',
            'notes' => 'Second half December payroll',
        ]);

        // Robert Johnson's Payroll Records
        Payroll::create([
            'user_id' => $robert->id,
            'start_date' => '2024-12-01',
            'end_date' => '2024-12-15',
            'month' => 12,
            'year' => 2024,
            'period' => '1st Half',
            'basic_salary' => 28000.00,
            'total_days_worked' => 13,
            'total_hours_worked' => 104.00,
            'overtime_hours' => 8.00,
            'leave_days' => 0,
            'absent_days' => 0,
            'gross_salary' => 14000.00,
            'overtime_pay' => 625.00,
            'housing_allowance' => 2200.00,
            'transport_allowance' => 1600.00,
            'meal_allowance' => 1100.00,
            'other_allowance' => 550.00,
            'total_allowances' => 5450.00,
            'sss_deduction' => 1300.00,
            'philhealth_deduction' => 650.00,
            'pagibig_deduction' => 450.00,
            'advance_deduction' => 0.00,
            'other_deduction' => 0.00,
            'total_deductions' => 2400.00,
            'net_salary' => 17675.00,
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'payment_date' => now(),
            'payslip_generated' => true,
            'notes' => 'First half December payroll',
        ]);

        Payroll::create([
            'user_id' => $robert->id,
            'start_date' => '2024-12-16',
            'end_date' => '2024-12-31',
            'month' => 12,
            'year' => 2024,
            'period' => '2nd Half',
            'basic_salary' => 28000.00,
            'total_days_worked' => 12,
            'total_hours_worked' => 96.00,
            'overtime_hours' => 5.00,
            'leave_days' => 1,
            'absent_days' => 0,
            'gross_salary' => 14000.00,
            'overtime_pay' => 390.63,
            'housing_allowance' => 2200.00,
            'transport_allowance' => 1600.00,
            'meal_allowance' => 1100.00,
            'other_allowance' => 550.00,
            'total_allowances' => 5450.00,
            'sss_deduction' => 1300.00,
            'philhealth_deduction' => 650.00,
            'pagibig_deduction' => 450.00,
            'advance_deduction' => 0.00,
            'other_deduction' => 0.00,
            'total_deductions' => 2400.00,
            'net_salary' => 17440.63,
            'status' => 'pending',
            'notes' => 'Second half December payroll',
        ]);

        $this->info('✅ Payroll records loaded: 6');
    }

    /**
     * Load attendance records
     */
    private function loadAttendanceRecords(): void
    {
        $this->info('⏰ Loading attendance records...');

        $john = User::where('email', 'john.smith@dps.gov.ph')->first();
        $maria = User::where('email', 'maria.garcia@dps.gov.ph')->first();
        $robert = User::where('email', 'robert.johnson@dps.gov.ph')->first();

        $dates = [
            '2024-12-01', '2024-12-02', '2024-12-03', '2024-12-04', '2024-12-05',
            '2024-12-06', '2024-12-07', '2024-12-08', '2024-12-09', '2024-12-10',
            '2024-12-11', '2024-12-12', '2024-12-13', '2024-12-14', '2024-12-15'
        ];

        foreach ($dates as $date) {
            // John Smith's attendance
            Attendance::create([
                'user_id' => $john->id,
                'date' => $date,
                'clock_in' => '08:00:00',
                'clock_out' => $date === '2024-12-02' ? '17:30:00' : '17:00:00',
                'notes' => $date === '2024-12-02' ? 'Overtime work' : 'Regular work day',
            ]);

            // Maria Garcia's attendance
            Attendance::create([
                'user_id' => $maria->id,
                'date' => $date,
                'clock_in' => '08:00:00',
                'clock_out' => '17:00:00',
                'notes' => 'Regular work day',
            ]);

            // Robert Johnson's attendance
            Attendance::create([
                'user_id' => $robert->id,
                'date' => $date,
                'clock_in' => '08:00:00',
                'clock_out' => '17:00:00',
                'notes' => 'Regular work day',
            ]);
        }

        $this->info('✅ Attendance records loaded: 45');
    }

    /**
     * Load applicants
     */
    private function loadApplicants(): void
    {
        $this->info('👤 Loading applicants...');

        Applicant::create([
            'firstname' => 'Juan',
            'middlename' => 'Santos',
            'lastname' => 'Dela Cruz',
            'age' => 25,
            'sex' => 'Male',
            'birthdate' => '1999-05-15',
            'height' => 170,
            'weight' => 65,
            'status' => 'Single',
            'citizenship' => 'Filipino',
            'barangay' => 'Barangay 1',
            'municipality' => 'Manila',
            'province' => 'Metro Manila',
            'country' => 'Philippines',
            'email' => 'juan.delacruz@email.com',
        ]);

        Applicant::create([
            'firstname' => 'Ana',
            'middlename' => 'Maria',
            'lastname' => 'Santos',
            'age' => 28,
            'sex' => 'Female',
            'birthdate' => '1996-08-22',
            'height' => 160,
            'weight' => 55,
            'status' => 'Single',
            'citizenship' => 'Filipino',
            'barangay' => 'Barangay 2',
            'municipality' => 'Quezon City',
            'province' => 'Metro Manila',
            'country' => 'Philippines',
            'email' => 'ana.santos@email.com',
        ]);

        Applicant::create([
            'firstname' => 'Pedro',
            'middlename' => 'Garcia',
            'lastname' => 'Martinez',
            'age' => 30,
            'sex' => 'Male',
            'birthdate' => '1994-03-10',
            'height' => 175,
            'weight' => 70,
            'status' => 'Married',
            'citizenship' => 'Filipino',
            'barangay' => 'Barangay 3',
            'municipality' => 'Makati',
            'province' => 'Metro Manila',
            'country' => 'Philippines',
            'email' => 'pedro.martinez@email.com',
        ]);

        $this->info('✅ Applicants loaded: 3');
    }

    /**
     * Load applications
     */
    private function loadApplications(): void
    {
        $this->info('📋 Loading applications...');

        $applicants = Applicant::all();
        $hrManager = User::where('email', 'hr@dps.gov.ph')->first();

        Application::create([
            'applicant_id' => $applicants[0]->id,
            'status' => 'PENDING',
        ]);

        Application::create([
            'applicant_id' => $applicants[1]->id,
            'status' => 'INTERVIEW_SCHEDULED',
            'interview_date' => '2024-12-20',
            'interview_time' => '10:00:00',
            'interview_location' => 'Conference Room A',
            'interviewer_name' => 'HR Manager',
        ]);

        Application::create([
            'applicant_id' => $applicants[2]->id,
            'status' => 'INTERVIEWED',
            'interview_date' => '2024-12-18',
            'interview_time' => '14:00:00',
            'interview_location' => 'Conference Room B',
            'interviewer_name' => 'HR Manager',
            'interview_result' => 'PASSED',
            'interview_score' => 85,
            'interview_notes' => 'Good communication skills and relevant experience',
        ]);

        $this->info('✅ Applications loaded: 3');
    }

    /**
     * Display login credentials
     */
    private function displayLoginCredentials(): void
    {
        $this->newLine();
        $this->info('🔐 Login Credentials:');
        $this->newLine();
        
        $this->info('ADMIN USERS:');
        $this->line('  • admin@dps.gov.ph / admin123');
        $this->line('  • hr@dps.gov.ph / admin123');
        $this->newLine();
        
        $this->info('EMPLOYEE USERS:');
        $this->line('  • john.smith@dps.gov.ph / employee123');
        $this->line('  • maria.garcia@dps.gov.ph / employee123');
        $this->line('  • robert.johnson@dps.gov.ph / employee123');
        $this->newLine();
        
        $this->info('📊 Sample Data Summary:');
        $this->line('  • Users: 5 (2 admin, 3 employees)');
        $this->line('  • Leave Rules: 3');
        $this->line('  • Leave Requests: 9');
        $this->line('  • Payroll Records: 6');
        $this->line('  • Attendance Records: 45');
        $this->line('  • Applicants: 3');
        $this->line('  • Applications: 3');
        $this->newLine();
        
        $this->info('🚀 System is ready! Run "php artisan serve" to start the application.');
    }
}
