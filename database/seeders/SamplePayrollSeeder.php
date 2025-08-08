<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payroll;
use App\Models\User;
use Carbon\Carbon;

class SamplePayrollSeeder extends Seeder
{
    public function run()
    {
        // Get all employees
        $employees = User::where('role', 'employee')->get();
        
        if ($employees->isEmpty()) {
            $this->command->info('No employees found. Please run UserSeeder first.');
            return;
        }

        // Create sample payroll records for each employee
        foreach ($employees as $employee) {
            // Create payroll records for the last 3 months
            for ($i = 3; $i >= 1; $i--) {
                $date = Carbon::now()->subMonths($i);
                
                // 1st Half of the month
                Payroll::create([
                    'user_id' => $employee->id,
                    'start_date' => $date->copy()->startOfMonth()->format('Y-m-d'),
                    'end_date' => $date->copy()->startOfMonth()->addDays(14)->format('Y-m-d'),
                    'month' => $date->month,
                    'year' => $date->year,
                    'period' => '1st Half',
                    'basic_salary' => 25000.00,
                    'total_days_worked' => 11,
                    'total_hours_worked' => 88.00,
                    'overtime_hours' => 4.00,
                    'leave_days' => 0,
                    'absent_days' => 0,
                    'gross_salary' => 12500.00,
                    'overtime_pay' => 781.25,
                    'housing_allowance' => 2000.00,
                    'transport_allowance' => 1000.00,
                    'meal_allowance' => 1500.00,
                    'other_allowance' => 500.00,
                    'total_allowances' => 5000.00,
                    'sss_deduction' => 1200.00,
                    'philhealth_deduction' => 600.00,
                    'pagibig_deduction' => 100.00,
                    'advance_deduction' => 0.00,
                    'other_deduction' => 0.00,
                    'total_deductions' => 1900.00,
                    'net_salary' => 16381.25,
                    'status' => $i === 1 ? 'pending' : 'approved',
                    'approved_by' => 1, // Admin user
                    'approved_at' => $i === 1 ? null : $date->copy()->addDays(15),
                    'notes' => 'Sample payroll record for ' . $date->format('F Y') . ' - 1st Half',
                ]);

                // 2nd Half of the month
                Payroll::create([
                    'user_id' => $employee->id,
                    'start_date' => $date->copy()->startOfMonth()->addDays(15)->format('Y-m-d'),
                    'end_date' => $date->copy()->endOfMonth()->format('Y-m-d'),
                    'month' => $date->month,
                    'year' => $date->year,
                    'period' => '2nd Half',
                    'basic_salary' => 25000.00,
                    'total_days_worked' => 10,
                    'total_hours_worked' => 80.00,
                    'overtime_hours' => 2.00,
                    'leave_days' => 0,
                    'absent_days' => 0,
                    'gross_salary' => 12500.00,
                    'overtime_pay' => 390.63,
                    'housing_allowance' => 2000.00,
                    'transport_allowance' => 1000.00,
                    'meal_allowance' => 1500.00,
                    'other_allowance' => 500.00,
                    'total_allowances' => 5000.00,
                    'sss_deduction' => 1200.00,
                    'philhealth_deduction' => 600.00,
                    'pagibig_deduction' => 100.00,
                    'advance_deduction' => 0.00,
                    'other_deduction' => 0.00,
                    'total_deductions' => 1900.00,
                    'net_salary' => 15990.63,
                    'status' => $i === 1 ? 'draft' : 'paid',
                    'approved_by' => $i === 1 ? null : 1,
                    'approved_at' => $i === 1 ? null : $date->copy()->addDays(30),
                    'payment_date' => $i === 1 ? null : $date->copy()->addDays(30),
                    'notes' => 'Sample payroll record for ' . $date->format('F Y') . ' - 2nd Half',
                ]);
            }
        }

        $this->command->info('Sample payroll records created successfully!');
    }
}
