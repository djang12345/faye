<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin can see all attendance records
            $attendances = Attendance::with('user')
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // Employee can only see their own attendance
            $attendances = $user->attendances()
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        // Get employees for admin to select from when adding attendance
        $employees = $user->isAdmin() ? User::where('role', 'employee')->get() : collect();

        return Inertia::render('Attendance/Index', [
            'attendances' => $attendances,
            'isAdmin' => $user->isAdmin(),
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Attendance store method called', [
            'request_data' => $request->all(),
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role,
            'is_admin' => Auth::user()->isAdmin(),
            'route' => $request->route()->getName()
        ]);
        
        $user = Auth::user();
        
        // Only admins can create attendance records
        if (!$user->isAdmin()) {
            \Log::warning('Access denied - user is not admin', [
                'user_id' => $user->id,
                'user_role' => $user->role
            ]);
            abort(403, 'Access denied.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if attendance record already exists for this user and date
        $existingAttendance = Attendance::where('user_id', $request->user_id)
            ->where('date', $request->date)
            ->first();

        if ($existingAttendance) {
            return back()->withErrors(['date' => 'An attendance record already exists for this employee on this date.']);
        }

        // Store times as simple time strings (HH:MM:SS format)
        $clockIn = $request->clock_in ? $request->clock_in . ':00' : null;
        $clockOut = $request->clock_out ? $request->clock_out . ':00' : null;
        
        \Log::info('Creating attendance with times', [
            'date' => $request->date,
            'clock_in_input' => $request->clock_in,
            'clock_out_input' => $request->clock_out,
            'clock_in_stored' => $clockIn,
            'clock_out_stored' => $clockOut,
        ]);
        
        $attendance = Attendance::create([
            'user_id' => $request->user_id,
            'date' => $request->date,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'notes' => $request->notes,
        ]);

        // Check if this is an admin request by looking at the route
        $isAdminRequest = $request->is('admin/*');
        $redirectRoute = $isAdminRequest ? 'admin.attendance.index' : 'attendance.index';
        
        return redirect()->route($redirectRoute)->with('success', 'Attendance record created successfully.');
    }

    public function show(Attendance $attendance)
    {
        $user = Auth::user();
        
        // Check if user can view this attendance record
        if (!$user->isAdmin() && $attendance->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $attendance->load('user');
        
        return Inertia::render('Attendance/Show', [
            'attendance' => $attendance,
        ]);
    }

    public function edit(Attendance $attendance)
    {
        $user = Auth::user();
        
        // Check if user can edit this attendance record
        if (!$user->isAdmin() && $attendance->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $attendance->load('user');
        
        return Inertia::render('Attendance/Edit', [
            'attendance' => $attendance,
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $user = Auth::user();
        
        // Check if user can update this attendance record
        if (!$user->isAdmin() && $attendance->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'notes' => 'nullable|string|max:1000',
        ]);

        $attendance->update([
            'date' => $request->date,
            'clock_in' => $request->clock_in ? $request->clock_in . ':00' : null,
            'clock_out' => $request->clock_out ? $request->clock_out . ':00' : null,
            'notes' => $request->notes,
        ]);

        return redirect()->route('attendance.index')->with('success', 'Attendance record updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $user = Auth::user();
        
        // Check if user can delete this attendance record
        if (!$user->isAdmin() && $attendance->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        $attendance->delete();

        return redirect()->route('attendance.index')->with('success', 'Attendance record deleted successfully.');
    }

    public function clockIn()
    {
        $user = Auth::user();
        // Use Carbon with microsecond precision for more accurate time
        $currentTime = \Carbon\Carbon::now();
        $today = $currentTime->toDateString();



        // Check if already clocked in today
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->clock_in) {
            return back()->with('error', 'You have already clocked in today.');
        }

        // Format the current time as HH:MM:SS for storage
        $clockInTime = $currentTime->format('H:i:s');

        if ($existingAttendance) {
            $existingAttendance->update([
                'clock_in' => $clockInTime,
            ]);
        } else {
            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'clock_in' => $clockInTime,
            ]);
        }

        return back()->with('success', 'Successfully clocked in at ' . $currentTime->format('H:i:s'));
    }

    public function clockOut()
    {
        $user = Auth::user();
        // Use Carbon with microsecond precision for more accurate time
        $currentTime = \Carbon\Carbon::now();
        $today = $currentTime->toDateString();



        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'No clock-in record found for today.');
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'You have already clocked out today.');
        }

        // Format the current time as HH:MM:SS for storage
        $clockOutTime = $currentTime->format('H:i:s');

        $attendance->update([
            'clock_out' => $clockOutTime,
        ]);

        return back()->with('success', 'Successfully clocked out at ' . $currentTime->format('H:i:s'));
    }

    public function today()
    {
        $user = Auth::user();
        $currentTime = Carbon::now();
        $today = $currentTime->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();



        $response = [
            'attendance' => $attendance,
            'canClockIn' => !$attendance || !$attendance->clock_in,
            'canClockOut' => $attendance && $attendance->clock_in && !$attendance->clock_out,
        ];



        return response()->json($response);
    }
}
