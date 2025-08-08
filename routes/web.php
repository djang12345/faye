<?php

use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');



Route::group(['middleware' => 'auth', 'prefix' => 'dashboard'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/years', [DashboardController::class, 'getYears'])->name('dashboard.years');
    Route::get('/line-graph', [DashboardController::class, 'lineGraphData'])->name('dashboard.lineGraph');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin routes (must come before employee routes to avoid route conflicts)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::post('/admin/attendance', [AttendanceController::class, 'store'])->name('admin.attendance.store');
    Route::post('/admin/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('admin.attendance.clock-in');
    Route::post('/admin/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('admin.attendance.clock-out');
    Route::get('/admin/attendance/today', [AttendanceController::class, 'today'])->name('admin.attendance.today');
    Route::get('/admin/attendance/{attendance}', [AttendanceController::class, 'show'])->name('admin.attendance.show');
    Route::get('/admin/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('admin.attendance.edit');
    Route::put('/admin/attendance/{attendance}', [AttendanceController::class, 'update'])->name('admin.attendance.update');
    Route::delete('/admin/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('admin.attendance.destroy');
    Route::get('/admin/payroll', [PayrollController::class, 'index'])->name('admin.payroll.index');
    Route::get('/admin/payroll/create', [PayrollController::class, 'create'])->name('admin.payroll.create');
    Route::post('/admin/payroll', [PayrollController::class, 'store'])->name('admin.payroll.store');
    Route::get('/admin/payroll/{payroll}', [PayrollController::class, 'show'])->name('admin.payroll.show');
    Route::get('/admin/payroll/{payroll}/edit', [PayrollController::class, 'edit'])->name('admin.payroll.edit');
    Route::put('/admin/payroll/{payroll}', [PayrollController::class, 'update'])->name('admin.payroll.update');
    Route::delete('/admin/payroll/{payroll}', [PayrollController::class, 'destroy'])->name('admin.payroll.destroy');
    Route::patch('/admin/payroll/{payroll}/status', [PayrollController::class, 'updateStatus'])->name('admin.payroll.update-status');
    Route::post('/admin/payroll/{payroll}/send-email', [PayrollController::class, 'sendEmail'])->name('admin.payroll.send-email');
    
    // Admin Leave Management Routes
    Route::get('/admin/leave', [LeaveController::class, 'adminIndex'])->name('admin.leave.index');
    Route::get('/admin/leave/create', [LeaveController::class, 'createForEmployee'])->name('admin.leave.create');
    Route::post('/admin/leave', [LeaveController::class, 'storeForEmployee'])->name('admin.leave.store');
    Route::get('/admin/leave/{leaveRequest}', [LeaveController::class, 'show'])->name('admin.leave.show');
    Route::get('/admin/leave/{leaveRequest}/edit', [LeaveController::class, 'edit'])->name('admin.leave.edit');
    Route::put('/admin/leave/{leaveRequest}', [LeaveController::class, 'update'])->name('admin.leave.update');
    Route::delete('/admin/leave/{leaveRequest}', [LeaveController::class, 'destroy'])->name('admin.leave.destroy');
    Route::patch('/admin/leave/{leaveRequest}/approve', [LeaveController::class, 'approve'])->name('admin.leave.approve');
});

// Employee routes
Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
    Route::get('/attendance/today', [AttendanceController::class, 'today'])->name('attendance.today');
    Route::get('/attendance/current-time', function() {
        $currentTime = Carbon::now();
        return response()->json([
            'current_time' => $currentTime->toDateTimeString(),
            'timezone' => config('app.timezone'),
            'formatted_time' => $currentTime->format('H:i:s'),
            'date' => $currentTime->toDateString(),
            'microseconds' => $currentTime->micro,
            'timestamp' => $currentTime->timestamp
        ]);
    })->name('attendance.current-time');
    Route::get('/attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
});

// Mixed routes (both employee and admin can access)
Route::middleware('auth')->group(function () {
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');
    
    // Leave routes - redirect admins to admin pages
    Route::get('/leave', function() {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.leave.index');
        }
        return app(LeaveController::class)->index();
    })->name('leave.index');
    
    Route::get('/leave/create', function() {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.leave.create');
        }
        return app(LeaveController::class)->create();
    })->name('leave.create');
    
    Route::post('/leave', [LeaveController::class, 'store'])->name('leave.store');
    Route::get('/leave/{leaveRequest}', [LeaveController::class, 'show'])->name('leave.show');
    Route::get('/leave/{leaveRequest}/edit', [LeaveController::class, 'edit'])->name('leave.edit');
    Route::put('/leave/{leaveRequest}', [LeaveController::class, 'update'])->name('leave.update');
    Route::delete('/leave/{leaveRequest}', [LeaveController::class, 'destroy'])->name('leave.destroy');
    Route::get('/leave/credits/my', [LeaveController::class, 'myCredits'])->name('leave.my-credits');
});

Route::prefix('application')->group(function () {
    Route::get('/application', [ApplicationController::class, 'create'])->name('application.create');
    Route::post('/application/personal', [ApplicationController::class, 'validatePersonalInfo'])->name('application.validate.personalInfo');
    Route::post('/application/parents', [ApplicationController::class, 'validateParentsBackground'])->name('application.validate.parentsBackground');
    Route::post('/application/submit', [ApplicationController::class, 'submit'])->name('application.submit');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('applicant')->group(function () {
        Route::get('/pending', [ApplicantController::class, 'indexPending'])->name('applicant.pending.index');
        Route::get('/interview-scheduled', [ApplicantController::class, 'indexInterviewScheduled'])->name('applicant.interview-scheduled.index');
        Route::get('/interviewed', [ApplicantController::class, 'indexInterviewed'])->name('applicant.interviewed.index');
        Route::get('/accepted', [ApplicantController::class, 'indexAccepted'])->name('applicant.accepted.index');
        Route::get('/rejected', [ApplicantController::class, 'indexRejected'])->name('applicant.rejected.index');
        Route::get('/{id}', [ApplicantController::class, 'view'])->name('applicant.view');
        Route::post('/{id}/schedule-interview', [ApplicantController::class, 'scheduleInterview'])->name('applicant.schedule-interview');
        Route::post('/{id}/conduct-interview', [ApplicantController::class, 'conductInterview'])->name('applicant.conduct-interview');
        Route::post('/{id}/accept', [ApplicantController::class, 'accept'])->name('applicant.accept');
        Route::post('/{id}/reject', [ApplicantController::class, 'reject'])->name('applicant.reject');
        Route::post('/{id}/reject-after-interview', [ApplicantController::class, 'rejectAfterInterview'])->name('applicant.reject-after-interview');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('user.index');
        Route::post('/', [UserController::class, 'store'])->name('user.store');
        Route::post('/create-from-applicant', [UserController::class, 'createFromApplicant'])->name('user.create-from-applicant');
        Route::get('/edit/{user}', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('user.destroy');
        Route::post('/{user}/resend-credentials', [UserController::class, 'resendCredentials'])->name('user.resend-credentials');
    });

    Route::prefix('employees')->group(function () {
        Route::get('/', [UserController::class, 'employees'])->name('employee.index');
        Route::get('/{user}', [UserController::class, 'showEmployee'])->name('employee.show');
    });
});

require __DIR__ . '/auth.php';
