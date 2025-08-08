<?php

namespace Database\Factories;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', 'now');
        $endDate = Carbon::parse($startDate)->addDays(14);
        
        $basicSalary = $this->faker->numberBetween(15000, 50000);
        $grossSalary = $basicSalary / 2;
        $overtimeHours = $this->faker->numberBetween(0, 20);
        $overtimePay = $overtimeHours * ($basicSalary / 160) * 1.25;
        
        $housingAllowance = $this->faker->numberBetween(0, 5000);
        $transportAllowance = $this->faker->numberBetween(0, 3000);
        $mealAllowance = $this->faker->numberBetween(0, 2000);
        $otherAllowance = $this->faker->numberBetween(0, 1000);
        $totalAllowances = $housingAllowance + $transportAllowance + $mealAllowance + $otherAllowance;
        
        $sssDeduction = $this->faker->numberBetween(0, 1000);
        $philhealthDeduction = $this->faker->numberBetween(0, 500);
        $pagibigDeduction = $this->faker->numberBetween(0, 500);
        $advanceDeduction = $this->faker->numberBetween(0, 2000);
        $otherDeduction = $this->faker->numberBetween(0, 500);
        $totalDeductions = $sssDeduction + $philhealthDeduction + $pagibigDeduction + $advanceDeduction + $otherDeduction;
        
        $netSalary = $grossSalary + $overtimePay + $totalAllowances - $totalDeductions;
        
        return [
            'user_id' => User::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'month' => Carbon::parse($startDate)->month,
            'year' => Carbon::parse($startDate)->year,
            'period' => $this->faker->randomElement(['1st Half', '2nd Half']),
            'basic_salary' => $basicSalary,
            'total_days_worked' => $this->faker->numberBetween(10, 15),
            'total_hours_worked' => $this->faker->numberBetween(80, 120),
            'overtime_hours' => $overtimeHours,
            'leave_days' => $this->faker->numberBetween(0, 3),
            'absent_days' => $this->faker->numberBetween(0, 2),
            'gross_salary' => $grossSalary,
            'overtime_pay' => $overtimePay,
            'housing_allowance' => $housingAllowance,
            'transport_allowance' => $transportAllowance,
            'meal_allowance' => $mealAllowance,
            'other_allowance' => $otherAllowance,
            'total_allowances' => $totalAllowances,
            'sss_deduction' => $sssDeduction,
            'philhealth_deduction' => $philhealthDeduction,
            'pagibig_deduction' => $pagibigDeduction,
            'advance_deduction' => $advanceDeduction,
            'other_deduction' => $otherDeduction,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'status' => $this->faker->randomElement(['draft', 'pending', 'approved', 'paid', 'cancelled']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
