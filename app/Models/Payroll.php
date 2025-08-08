<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'month',
        'year',
        'period',
        'basic_salary',
        'total_days_worked',
        'total_hours_worked',
        'overtime_hours',
        'leave_days',
        'absent_days',
        'gross_salary',
        'overtime_pay',
        'housing_allowance',
        'transport_allowance',
        'meal_allowance',
        'other_allowance',
        'total_allowances',
        'sss_deduction',
        'philhealth_deduction',
        'pagibig_deduction',
        'advance_deduction',
        'other_deduction',
        'total_deductions',
        'net_salary',
        'status',
        'approved_by',
        'approved_at',
        'payment_date',
        'payslip_generated',
        'payslip_url',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'total_hours_worked' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'sss_deduction' => 'decimal:2',
        'philhealth_deduction' => 'decimal:2',
        'pagibig_deduction' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'other_deduction' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'approved_at' => 'datetime',
        'payment_date' => 'datetime',
        'payslip_generated' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Calculate totals before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($payroll) {
            // Calculate total allowances
            $payroll->total_allowances = (
                $payroll->housing_allowance + 
                $payroll->transport_allowance + 
                $payroll->meal_allowance + 
                $payroll->other_allowance
            );

            // Calculate total deductions (no tax)
            $payroll->total_deductions = (
                $payroll->sss_deduction + 
                $payroll->philhealth_deduction + 
                $payroll->pagibig_deduction + 
                $payroll->advance_deduction + 
                $payroll->other_deduction
            );

            // Calculate net salary
            $payroll->net_salary = $payroll->gross_salary + $payroll->overtime_pay + $payroll->total_allowances - $payroll->total_deductions;
        });
    }

    // Static method to generate payroll for an employee
    public static function generatePayroll($userId, $startDate, $endDate, $basicSalary, $period = '1st Half')
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // Calculate working days (excluding weekends)
        $totalDaysWorked = $start->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $end) + 1;

        // Calculate hours worked (assuming 8 hours per day)
        $totalHoursWorked = $totalDaysWorked * 8;

        // Calculate gross salary (half month for kinsenas)
        $grossSalary = $basicSalary / 2;

        return self::create([
            'user_id' => $userId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'month' => $start->month,
            'year' => $start->year,
            'period' => $period,
            'basic_salary' => $basicSalary,
            'total_days_worked' => $totalDaysWorked,
            'total_hours_worked' => $totalHoursWorked,
            'overtime_hours' => 0,
            'leave_days' => 0,
            'absent_days' => 0,
            'gross_salary' => $grossSalary,
            'overtime_pay' => 0,
            'housing_allowance' => 0,
            'transport_allowance' => 0,
            'meal_allowance' => 0,
            'other_allowance' => 0,
            'sss_deduction' => 0,
            'philhealth_deduction' => 0,
            'pagibig_deduction' => 0,
            'advance_deduction' => 0,
            'other_deduction' => 0,
            'status' => 'draft',
        ]);
    }

    // Get period display name
    public function getPeriodDisplayAttribute()
    {
        $monthName = Carbon::createFromDate($this->year, $this->month, 1)->format('F');
        return "{$monthName} {$this->year} - {$this->period}";
    }

    // Get status badge class
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        return $classes[$this->status] ?? $classes['draft'];
    }
}
