<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CourseController; // ADD THIS
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController; // ADD THIS
use App\Http\Controllers\FeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login for guests, or dashboard for authenticated users
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ==================== BASIC PUBLIC ROUTES ====================
Route::view('/welcome', 'welcome');
Route::view('/about', 'about');
Route::view('/home', 'home');

// ==================== STUDENT PARAMETER ROUTES ====================
Route::get('/student/{id}', fn($id) => "Student ID: $id");
Route::get('/student/{id}/{name}', fn($id, $name) => "ID: $id Name: $name");

// Auth routes (guest middleware)
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Email verification routes
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Password management
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // ===== DASHBOARD =====
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['verified'])
        ->name('dashboard');

    // ===== PROFILE MANAGEMENT =====
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Additional profile routes
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])
        ->name('profile.change-password');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])
        ->name('profile.update-password');

    // ===== STUDENT MANAGEMENT =====
    Route::resource('students', StudentController::class);
    
    // Student extra routes
    Route::get('/students/dashboard', [StudentController::class, 'dashboard'])
        ->name('students.dashboard');
    Route::get('/students/search', [StudentController::class, 'search'])
        ->name('students.search');
    Route::get('/students/export', [StudentController::class, 'export'])
        ->name('students.export');

    // ===== COURSE MANAGEMENT =====
    Route::resource('courses', CourseController::class);
    
    // Course extra routes
    Route::get('/courses/{course}/students', [CourseController::class, 'students'])
        ->name('courses.students');
    Route::post('/courses/{course}/assign-student', [CourseController::class, 'assignStudent'])
        ->name('courses.assign-student');

    // ===== EXAM MANAGEMENT =====
    Route::resource('exams', ExamController::class);
    
    // Exam extra routes
    Route::get('/exams/{exam}/results', [ExamController::class, 'results'])
        ->name('exams.results');
    Route::post('/exams/{exam}/submit', [ExamController::class, 'submit'])
        ->name('exams.submit');

    // ===== FEE MANAGEMENT =====
    Route::resource('fees', FeeController::class);
    
    // Fee extra routes
    Route::get('fees/report', [FeeController::class, 'report'])->name('fees.report');
    Route::get('fees/stats', [FeeController::class, 'stats'])->name('fees.stats');
    Route::get('fees/student/{studentId}', [FeeController::class, 'getStudentFees'])
        ->name('fees.student');
    Route::post('fees/{id}/pay', [FeeController::class, 'payFee'])
        ->name('fees.pay');
});

// ==================== ERROR HANDLING ROUTES ====================
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

Route::fallback(function () {
    return view('errors.404');
});

// ==================== API ROUTES (Optional) ====================
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/students', [StudentController::class, 'apiIndex'])->name('api.students');
    Route::get('/courses', [CourseController::class, 'apiIndex'])->name('api.courses');
    Route::get('/exams', [ExamController::class, 'apiIndex'])->name('api.exams');
    Route::get('/fees', [FeeController::class, 'apiIndex'])->name('api.fees');
});

// ==================== PUBLIC STUDENT LISTING (Optional) ====================
// Uncomment if you want a public student listing
// Route::get('/public/students', [StudentController::class, 'publicIndex'])->name('students.public');