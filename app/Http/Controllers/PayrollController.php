<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use App\Mail\PayrollEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin can see all payroll records
            $payrolls = Payroll::with('user')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->orderBy('period', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // Employee can only see their own payroll
            $payrolls = $user->payrolls()
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->orderBy('period', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return Inertia::render('Payroll/Index', [
            'payrolls' => $payrolls,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function create()
    {
        $employees = User::where('role', 'employee')->get();

        return Inertia::render('Payroll/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'period' => 'required|in:1st Half,2nd Half',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'leave_days' => 'nullable|integer|min:0',
            'absent_days' => 'nullable|integer|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'sss_deduction' => 'nullable|numeric|min:0',
            'philhealth_deduction' => 'nullable|numeric|min:0',
            'pagibig_deduction' => 'nullable|numeric|min:0',
            'advance_deduction' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Calculate working days (excluding weekends)
        $totalDaysWorked = $startDate->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $endDate) + 1;

        // Calculate hours worked (assuming 8 hours per day)
        $totalHoursWorked = $totalDaysWorked * 8;

        // Calculate gross salary (half month for kinsenas)
        $grossSalary = $request->basic_salary / 2;

        // Calculate overtime pay (assuming 1.25x rate for overtime)
        $overtimePay = ($request->overtime_hours ?? 0) * ($request->basic_salary / 160) * 1.25;

        Payroll::create([
            'user_id' => $request->user_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'month' => $startDate->month,
            'year' => $startDate->year,
            'period' => $request->period,
            'basic_salary' => $request->basic_salary,
            'total_days_worked' => $totalDaysWorked,
            'total_hours_worked' => $totalHoursWorked,
            'overtime_hours' => $request->overtime_hours ?? 0,
            'leave_days' => $request->leave_days ?? 0,
            'absent_days' => $request->absent_days ?? 0,
            'gross_salary' => $grossSalary,
            'overtime_pay' => $overtimePay,
            'housing_allowance' => $request->housing_allowance ?? 0,
            'transport_allowance' => $request->transport_allowance ?? 0,
            'meal_allowance' => $request->meal_allowance ?? 0,
            'other_allowance' => $request->other_allowance ?? 0,
            'sss_deduction' => $request->sss_deduction ?? 0,
            'philhealth_deduction' => $request->philhealth_deduction ?? 0,
            'pagibig_deduction' => $request->pagibig_deduction ?? 0,
            'advance_deduction' => $request->advance_deduction ?? 0,
            'other_deduction' => $request->other_deduction ?? 0,
            'notes' => $request->notes,
            'status' => 'draft',
        ]);

        return redirect()->route('admin.payroll.index')->with('success', 'Payroll record created successfully.');
    }

    public function show(Payroll $payroll)
    {
        $user = Auth::user();
        
        // Check if user can view this payroll record
        if (!$user->isAdmin() && $payroll->user_id !== $user->id) {
            abort(403);
        }

        return Inertia::render('Payroll/Show', [
            'payroll' => $payroll->load(['user', 'approvedBy']),
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function updateStatus(Request $request, Payroll $payroll)
    {
        $request->validate([
            'status' => 'required|in:draft,pending,approved,paid,cancelled',
        ]);

        $updates = ['status' => $request->status];

        if ($request->status === 'approved') {
            $updates['approved_by'] = Auth::id();
            $updates['approved_at'] = now();
        } elseif ($request->status === 'paid') {
            $updates['payment_date'] = now();
        }

        $payroll->update($updates);

        return back()->with('success', 'Payroll status updated successfully.');
    }

    public function generatePayroll(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'basic_salary' => 'required|numeric|min:0',
            'period' => 'required|in:1st Half,2nd Half',
        ]);

        $payroll = Payroll::generatePayroll(
            $request->user_id,
            $request->start_date,
            $request->end_date,
            $request->basic_salary,
            $request->period
        );

        return redirect()->route('admin.payroll.show', $payroll)->with('success', 'Payroll generated successfully.');
    }

    public function edit(Payroll $payroll)
    {
        $user = Auth::user();
        
        // Check if user can edit this payroll record
        if (!$user->isAdmin()) {
            abort(403);
        }

        return Inertia::render('Payroll/Edit', [
            'payroll' => $payroll->load(['user', 'approvedBy']),
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function update(Request $request, Payroll $payroll)
    {
        $user = Auth::user();
        
        // Check if user can update this payroll record
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'period' => 'required|in:1st Half,2nd Half',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'leave_days' => 'nullable|integer|min:0',
            'absent_days' => 'nullable|integer|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'meal_allowance' => 'nullable|numeric|min:0',
            'other_allowance' => 'nullable|numeric|min:0',
            'sss_deduction' => 'nullable|numeric|min:0',
            'philhealth_deduction' => 'nullable|numeric|min:0',
            'pagibig_deduction' => 'nullable|numeric|min:0',
            'advance_deduction' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,pending,approved,paid,cancelled',
            'notes' => 'nullable|string',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Calculate working days (excluding weekends)
        $totalDaysWorked = $startDate->diffInDaysFiltered(function (Carbon $date) {
            return !$date->isWeekend();
        }, $endDate) + 1;

        // Calculate hours worked (assuming 8 hours per day)
        $totalHoursWorked = $totalDaysWorked * 8;

        // Calculate gross salary (half month for kinsenas)
        $grossSalary = $request->basic_salary / 2;

        // Calculate overtime pay (assuming 1.25x rate for overtime)
        $overtimePay = ($request->overtime_hours ?? 0) * ($request->basic_salary / 160) * 1.25;

        $updates = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'month' => $startDate->month,
            'year' => $startDate->year,
            'period' => $request->period,
            'basic_salary' => $request->basic_salary,
            'total_days_worked' => $totalDaysWorked,
            'total_hours_worked' => $totalHoursWorked,
            'overtime_hours' => $request->overtime_hours ?? 0,
            'leave_days' => $request->leave_days ?? 0,
            'absent_days' => $request->absent_days ?? 0,
            'gross_salary' => $grossSalary,
            'overtime_pay' => $overtimePay,
            'housing_allowance' => $request->housing_allowance ?? 0,
            'transport_allowance' => $request->transport_allowance ?? 0,
            'meal_allowance' => $request->meal_allowance ?? 0,
            'other_allowance' => $request->other_allowance ?? 0,
            'sss_deduction' => $request->sss_deduction ?? 0,
            'philhealth_deduction' => $request->philhealth_deduction ?? 0,
            'pagibig_deduction' => $request->pagibig_deduction ?? 0,
            'advance_deduction' => $request->advance_deduction ?? 0,
            'other_deduction' => $request->other_deduction ?? 0,
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        // Handle approval status
        if ($request->status === 'approved' && $payroll->status !== 'approved') {
            $updates['approved_by'] = Auth::id();
            $updates['approved_at'] = now();
        } elseif ($request->status === 'paid' && $payroll->status !== 'paid') {
            $updates['payment_date'] = now();
        }

        $payroll->update($updates);

        return redirect()->route('admin.payroll.index')->with('success', 'Payroll record updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $user = Auth::user();
        
        // Check if user can delete this payroll record
        if (!$user->isAdmin()) {
            abort(403);
        }

        $payroll->delete();

        return redirect()->route('admin.payroll.index')->with('success', 'Payroll record deleted successfully.');
    }

    public function sendEmail(Payroll $payroll)
    {
        $user = Auth::user();
        
        // Check if user can send email (admin or HR officer)
        if (!$user->isAdmin()) {
            abort(403);
        }

        try {
            // Load the user relationship
            $payroll->load('user');
            
            // Send email to the employee
            Mail::to($payroll->user->email)->send(new PayrollEmail($payroll));
            
            return back()->with('success', 'Payroll statement has been sent to ' . $payroll->user->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email. Please try again.');
        }
    }
}
