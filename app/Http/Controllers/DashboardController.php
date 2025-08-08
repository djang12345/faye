<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin dashboard - show applicant statistics
            $totalApplicants = Application::count();
            $acceptedApplicants = Application::where('status', 'ACCEPTED')->count();
            $rejectedApplicants = Application::where('status', 'REJECTED')->count();
            $pendingApplicants = Application::where('status', 'PENDING')->count();
            
            // Additional admin stats
            $totalEmployees = User::where('role', 'employee')->count();
            $totalAttendance = Attendance::count();
            $totalPayrolls = Payroll::count();
            
            return Inertia::render('Dashboard', [
                'isAdmin' => true,
                'totalApplicants' => $totalApplicants,
                'acceptedApplicants' => $acceptedApplicants,
                'rejectedApplicants' => $rejectedApplicants,
                'pendingApplicants' => $pendingApplicants,
                'totalEmployees' => $totalEmployees,
                'totalAttendance' => $totalAttendance,
                'totalPayrolls' => $totalPayrolls,
            ]);
        } else {
            // Employee dashboard - show personal attendance and payroll summary
            $todayAttendance = Attendance::where('user_id', $user->id)
                ->where('date', now()->toDateString())
                ->first();
                
            $recentAttendance = Attendance::where('user_id', $user->id)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();
                
            $recentPayrolls = Payroll::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
                
            $totalDaysWorked = Attendance::where('user_id', $user->id)
                ->whereNotNull('clock_out')
                ->count();
                
            // Calculate total hours worked from clock in/out times
            $totalHoursWorked = Attendance::where('user_id', $user->id)
                ->whereNotNull('clock_out')
                ->get()
                ->sum(function ($attendance) {
                    if ($attendance->clock_in && $attendance->clock_out) {
                        $clockIn = Carbon::parse($attendance->clock_in);
                        $clockOut = Carbon::parse($attendance->clock_out);
                        return $clockOut->diffInHours($clockIn);
                    }
                    return 0;
                });
            
            return Inertia::render('Dashboard', [
                'isAdmin' => false,
                'todayAttendance' => $todayAttendance,
                'recentAttendance' => $recentAttendance,
                'recentPayrolls' => $recentPayrolls,
                'totalDaysWorked' => $totalDaysWorked,
                'totalHoursWorked' => $totalHoursWorked,
            ]);
        }
    }

    public function getYears()
    {
        $years = Application::selectRaw('YEAR(created_at) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();
        return response()->json($years);
    }

    public function lineGraphData(Request $request)
    {
        $year = $request->input('year'); // Get selected year
        $month = $request->input('month'); // Get selected month (optional)

        // Check if a month is selected
        if ($month) {
            // If a month is selected, group data by day within that month
            $lineGraphData = Application::selectRaw('DAY(created_at) as day, COUNT(*) as count')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->groupBy('day')
                ->get();
        } else {
            // If no month is selected, group data by month within the selected year
            $lineGraphData = Application::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', $year)
                ->groupBy('month')
                ->get();
        }

        return response()->json($lineGraphData);
    }
}
