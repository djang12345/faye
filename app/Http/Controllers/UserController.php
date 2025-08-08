<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Applicant;
use App\Mail\EmployeeCredentials;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index() {
        \Log::info('User index method called');
        
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        
        // Get accepted applicants who don't have user accounts yet
        $acceptedApplicants = \App\Models\Application::where('status', 'ACCEPTED')
            ->whereDoesntHave('applicant.user')
            ->with('applicant')
            ->get();
        
        \Log::info('Users retrieved', [
            'total_users' => $users->total(),
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'data_count' => $users->count(),
        ]);
        
        // Log the actual user data for debugging
        \Log::info('User data sample', [
            'first_user' => $users->first() ? $users->first()->toArray() : null,
        ]);
        
        // Log the pagination structure
        \Log::info('Pagination structure', [
            'total' => $users->total(),
            'per_page' => $users->perPage(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'data_count' => $users->count(),
        ]);
        
        return Inertia::render('User/Index', [
            'users' => $users,
            'acceptedApplicants' => $acceptedApplicants,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:admin,employee',
        ]);
    
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Send welcome email if it's an employee
        if ($request->role === 'employee') {
            Mail::to($user->email)->send(new EmployeeCredentials($user, $request->password));
        }
    
        return Redirect::route('user.index')->with('success', 'User registered successfully.');
    }

    public function createFromApplicant(Request $request)
    {
        \Log::info('createFromApplicant method called', [
            'request_data' => $request->all()
        ]);
        
        $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'role' => 'required|in:employee,admin',
        ]);

        $applicant = Applicant::findOrFail($request->applicant_id);
        
        \Log::info('Applicant found', [
            'applicant_id' => $applicant->id,
            'applicant_name' => $applicant->firstname . ' ' . $applicant->lastname,
            'applicant_email' => $applicant->email
        ]);
        
        // Check if user already exists with this email
        $existingUser = User::where('email', $applicant->email)->first();
        
        if ($existingUser) {
            \Log::warning('User already exists with this email', ['email' => $applicant->email]);
            return back()->withErrors(['email' => 'A user with this email already exists.']);
        }

        // Generate default password
        $defaultPassword = 'employee' . date('Y');
        
        \Log::info('Creating user account', [
            'name' => $applicant->firstname . ' ' . $applicant->lastname,
            'email' => $applicant->email,
            'role' => $request->role,
            'password' => $defaultPassword
        ]);
        
        // Create new user account
        $user = User::create([
            'name' => $applicant->firstname . ' ' . $applicant->lastname,
            'email' => $applicant->email,
            'password' => Hash::make($defaultPassword),
            'role' => $request->role,
        ]);

        \Log::info('User created successfully', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email
        ]);

        // Link the user to the applicant
        $applicant->update(['user_id' => $user->id]);

        \Log::info('Applicant linked to user', [
            'applicant_id' => $applicant->id,
            'user_id' => $user->id
        ]);

        // Send credentials email
        try {
            Mail::to($user->email)->send(new EmployeeCredentials($user, $defaultPassword));
            \Log::info('Credentials email sent successfully', ['email' => $user->email]);
        } catch (\Exception $e) {
            \Log::error('Failed to send credentials email', [
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }

        return Redirect::route('user.index')->with('success', 'Employee account created successfully. Credentials sent to ' . $user->email);
    }

    public function destroy(User $user) {
        $user->delete();
        return Redirect::back()->with('success', 'User Deleted Successfully');
    }

    public function edit(User $user) {
        return Inertia::render('User/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,employee',
            'password' => 'nullable|string|min:8',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('user.index')->with('success', 'User updated successfully.');
    }

    public function employees() {
        $employees = User::where('role', 'employee')
            ->with('applicant')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return Inertia::render('Employee/Index', [
            'employees' => $employees,
        ]);
    }

    public function showEmployee(User $user) {
        if ($user->role !== 'employee') {
            abort(404);
        }
        
        $user->load('applicant');
        
        return Inertia::render('Employee/Show', [
            'employee' => $user,
        ]);
    }

    public function resendCredentials(User $user)
    {
        // Generate new temporary password
        $newPassword = 'temp' . rand(1000, 9999);
        $user->update(['password' => Hash::make($newPassword)]);

        // Send new credentials
        Mail::to($user->email)->send(new EmployeeCredentials($user, $newPassword));

        return back()->with('success', 'New credentials sent to ' . $user->email);
    }
}
