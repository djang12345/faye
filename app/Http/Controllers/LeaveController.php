<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveRule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            // Admin can see all leave requests
            $leaveRequests = LeaveRequest::with(['user', 'approvedBy', 'leaveRule'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // Employee can only see their own leave requests
            $leaveRequests = $user->leaveRequests()
                ->with(['approvedBy', 'leaveRule'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }

        return Inertia::render('Leave/Index', [
            'leaveRequests' => $leaveRequests,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $leaveRules = LeaveRule::where('is_active', true)->get();
        
        // Get available credits for each leave type
        $availableCredits = [];
        foreach ($leaveRules as $rule) {
            $availableCredits[$rule->leave_type] = $rule->getAvailableCredits($user->id);
        }



        return Inertia::render('Leave/Create', [
            'leaveRules' => $leaveRules,
            'availableCredits' => $availableCredits,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'details' => 'required|string|min:10',
        ]);

        // Calculate days requested
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $daysRequested = $startDate->diffInDays($endDate) + 1;

        // Check if user has enough credits
        $leaveRule = LeaveRule::where('leave_type', $request->leave_type)
            ->where('is_active', true)
            ->first();

        if (!$leaveRule) {
            return back()->withErrors(['leave_type' => 'Invalid leave type.']);
        }

        $availableCredits = $leaveRule->getAvailableCredits($user->id);
        
        if ($availableCredits < $daysRequested) {
            return back()->withErrors(['days_requested' => "You only have {$availableCredits} credits available for {$leaveRule->display_name}. You are requesting {$daysRequested} days."]);
        }

        LeaveRequest::create([
            'user_id' => $user->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $daysRequested,
            'details' => $request->details,
            'status' => 'pending',
        ]);

        return redirect()->route('leave.index')->with('success', 'Leave request submitted successfully.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();
        
        // Check if user can view this leave request
        if (!$user->isAdmin() && $leaveRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // Load the leave request with relationships
        $leaveRequest->load(['user', 'approvedBy', 'leaveRule']);

        // Add employee stats for admin
        if ($user->isAdmin()) {
            $employeeStats = LeaveRequest::where('user_id', $leaveRequest->user_id)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $leaveRequest->user->total_approved_requests = $employeeStats['approved'] ?? 0;
            $leaveRequest->user->total_pending_requests = $employeeStats['pending'] ?? 0;
            $leaveRequest->user->total_rejected_requests = $employeeStats['rejected'] ?? 0;

            // Add leave credits information
            $leaveRules = LeaveRule::where('is_active', true)->get();
            foreach ($leaveRules as $rule) {
                $usedCredits = LeaveRequest::where('user_id', $leaveRequest->user_id)
                    ->where('leave_type', $rule->leave_type)
                    ->where('status', 'approved')
                    ->sum('days_requested');
                
                $availableCredits = $rule->default_credits - $usedCredits;
                $leaveRequest->user->{$rule->leave_type . '_credits'} = $availableCredits;
            }
        }

        return Inertia::render('Leave/Show', [
            'leaveRequest' => $leaveRequest,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function edit(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();
        
        // Check if user can edit this leave request
        if (!$user->isAdmin() && $leaveRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // For employees: Only allow editing of pending requests
        if (!$user->isAdmin() && $leaveRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'You cannot edit leave requests that have been approved or rejected.']);
        }

        // For admins: Allow editing of pending and approved requests
        if ($user->isAdmin() && !in_array($leaveRequest->status, ['pending', 'approved'])) {
            return back()->withErrors(['error' => 'Only pending and approved leave requests can be edited.']);
        }

        $leaveRequest->load(['user', 'leaveRule']);
        $leaveRules = LeaveRule::where('is_active', true)->get();

        return Inertia::render('Leave/Edit', [
            'leaveRequest' => $leaveRequest,
            'leaveRules' => $leaveRules,
            'isAdmin' => $user->isAdmin(),
        ]);
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $user = Auth::user();
        
        // Check if user can update this leave request
        if (!$user->isAdmin() && $leaveRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // For employees: Only allow updating of pending requests
        if (!$user->isAdmin() && $leaveRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'You cannot update leave requests that have been approved or rejected.']);
        }

        // For admins: Allow updating of pending and approved requests
        if ($user->isAdmin() && !in_array($leaveRequest->status, ['pending', 'approved'])) {
            return back()->withErrors(['error' => 'Only pending and approved leave requests can be updated.']);
        }

        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'details' => 'required|string|min:10',
        ]);

        // Calculate days requested
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $daysRequested = $startDate->diffInDays($endDate) + 1;

        // Check if user has enough credits (excluding current request)
        $leaveRule = LeaveRule::where('leave_type', $request->leave_type)
            ->where('is_active', true)
            ->first();

        if (!$leaveRule) {
            return back()->withErrors(['leave_type' => 'Invalid leave type.']);
        }

        // Calculate available credits excluding current request
        $usedCredits = LeaveRequest::where('user_id', $leaveRequest->user_id)
            ->where('leave_type', $request->leave_type)
            ->where('status', 'approved')
            ->where('id', '!=', $leaveRequest->id)
            ->sum('days_requested');

        $availableCredits = $leaveRule->default_credits - $usedCredits;
        
        if ($availableCredits < $daysRequested) {
            return back()->withErrors(['days_requested' => "You only have {$availableCredits} credits available for {$leaveRule->display_name}. You are requesting {$daysRequested} days."]);
        }

        $leaveRequest->update([
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $daysRequested,
            'details' => $request->details,
        ]);

        return redirect()->route('leave.index')->with('success', 'Leave request updated successfully.');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();
        
        // Check if user can delete this leave request
        if (!$user->isAdmin() && $leaveRequest->user_id !== $user->id) {
            abort(403, 'Access denied.');
        }

        // For employees: Only allow deletion of pending requests
        if (!$user->isAdmin() && $leaveRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'You cannot delete leave requests that have been approved or rejected.']);
        }

        // For admins: Allow deletion of pending and approved requests
        if ($user->isAdmin() && !in_array($leaveRequest->status, ['pending', 'approved'])) {
            return back()->withErrors(['error' => 'Only pending and approved leave requests can be deleted.']);
        }

        $leaveRequest->delete();

        return redirect()->route('leave.index')->with('success', 'Leave request deleted successfully.');
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin role required.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $leaveRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave request ' . $request->status . ' successfully.');
    }

    public function myCredits()
    {
        $user = Auth::user();
        $leaveRules = LeaveRule::where('is_active', true)->get();
        
        $credits = [];
        foreach ($leaveRules as $rule) {
            $usedCredits = LeaveRequest::where('user_id', $user->id)
                ->where('leave_type', $rule->leave_type)
                ->where('status', 'approved')
                ->sum('days_requested');

            $credits[$rule->leave_type] = [
                'type' => $rule->leave_type,
                'display_name' => $rule->display_name,
                'total_credits' => $rule->default_credits,
                'used_credits' => $usedCredits,
                'available_credits' => $rule->default_credits - $usedCredits,
            ];
        }

        return response()->json($credits);
    }

    // Admin-specific methods
    public function adminIndex()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin role required.');
        }

        $leaveRequests = LeaveRequest::with(['user', 'approvedBy', 'leaveRule'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $employees = User::where('role', 'employee')->get(['id', 'name', 'email']);
        $leaveRules = LeaveRule::where('is_active', true)->get();

        // Calculate available credits for each employee and leave type
        $employeeCredits = [];
        foreach ($employees as $employee) {
            $employeeCredits[$employee->id] = [];
            foreach ($leaveRules as $rule) {
                $usedCredits = LeaveRequest::where('user_id', $employee->id)
                    ->where('leave_type', $rule->leave_type)
                    ->where('status', 'approved')
                    ->sum('days_requested');
                
                $availableCredits = $rule->default_credits - $usedCredits;
                
                $employeeCredits[$employee->id][$rule->leave_type] = [
                    'total_credits' => $rule->default_credits,
                    'used_credits' => $usedCredits,
                    'available_credits' => $availableCredits,
                ];
            }
        }

        return Inertia::render('Leave/AdminIndex', [
            'leaveRequests' => $leaveRequests,
            'employees' => $employees,
            'leaveRules' => $leaveRules,
            'employeeCredits' => $employeeCredits,
        ]);
    }

    public function createForEmployee()
    {
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin role required.');
        }

        $employees = User::where('role', 'employee')->get(['id', 'name', 'email']);
        $leaveRules = LeaveRule::where('is_active', true)->get();

        // Calculate available credits for each employee and leave type
        $employeeCredits = [];
        foreach ($employees as $employee) {
            $employeeCredits[$employee->id] = [];
            foreach ($leaveRules as $rule) {
                $usedCredits = LeaveRequest::where('user_id', $employee->id)
                    ->where('leave_type', $rule->leave_type)
                    ->where('status', 'approved')
                    ->sum('days_requested');
                
                $availableCredits = $rule->default_credits - $usedCredits;
                
                $employeeCredits[$employee->id][$rule->leave_type] = [
                    'total_credits' => $rule->default_credits,
                    'used_credits' => $usedCredits,
                    'available_credits' => $availableCredits,
                ];
            }
        }

        return Inertia::render('Leave/CreateForEmployee', [
            'employees' => $employees,
            'leaveRules' => $leaveRules,
            'employeeCredits' => $employeeCredits,
        ]);
    }

    public function storeForEmployee(Request $request)
    {
        \Log::info('Leave request creation started', $request->all());
        
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403, 'Access denied. Admin role required.');
        }

        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'leave_type' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'details' => 'required|string|min:10',
                'status' => 'required|in:pending,approved,rejected',
                'admin_notes' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for leave request', $e->errors());
            throw $e;
        }

        // Calculate days requested
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $daysRequested = $startDate->diffInDays($endDate) + 1;

        $leaveRequest = LeaveRequest::create([
            'user_id' => $request->user_id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days_requested' => $daysRequested,
            'details' => $request->details,
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'approved_by' => $request->status !== 'pending' ? $user->id : null,
            'approved_at' => $request->status !== 'pending' ? now() : null,
        ]);

        \Log::info('Leave request created successfully', ['id' => $leaveRequest->id]);

        return redirect()->route('admin.leave.index')->with('success', 'Leave request created successfully.');
    }
}
