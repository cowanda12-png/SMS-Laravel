<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\MpesaCallbackController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== ROOT ROUTE ====================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// ==================== BASIC PUBLIC ROUTES ====================
Route::view('/welcome', 'welcome');
Route::view('/about', 'about');
Route::view('/home', 'home');

// ==================== DASHBOARD ROUTE ====================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ==================== RESOURCE ROUTES ====================
Route::resource('students', StudentController::class)->middleware('auth');
Route::resource('courses', CourseController::class)->middleware('auth');
Route::resource('exams', ExamController::class)->middleware('auth');
Route::resource('fees', FeeController::class)->middleware('auth');

// ==================== FEE EXTRA ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::get('/fees/report', [FeeController::class, 'report'])->name('fees.report');
    Route::get('/fees/stats', [FeeController::class, 'stats'])->name('fees.stats');
    Route::get('/fees/student/{studentId}', [FeeController::class, 'studentFees'])->name('fees.student');
    Route::post('/fees/{id}/mark-paid', [FeeController::class, 'markAsPaid'])->name('fees.mark-paid');
    Route::post('/fees/{id}/mark-overdue', [FeeController::class, 'markAsOverdue'])->name('fees.mark-overdue');
    Route::post('/fees/bulk-delete', [FeeController::class, 'bulkDelete'])->name('fees.bulk-delete');
    Route::get('/fees/export', [FeeController::class, 'export'])->name('fees.export');
    Route::get('/fees/receipt/{id}', [FeeController::class, 'showReceipt'])->name('fees.receipt');
});

// ==================== STUDENT EXTRA ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::get('/students/dashboard', [StudentController::class, 'dashboard'])->name('students.dashboard');
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('/students/trash', [StudentController::class, 'trash'])->name('students.trash');
    Route::post('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('/students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.force-delete');
});

// ==================== COURSE EXTRA ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::get('/courses/{course}/students', [CourseController::class, 'students'])->name('courses.students');
    Route::post('/courses/{course}/assign-student', [CourseController::class, 'assignStudent'])->name('courses.assign-student');
    Route::post('/courses/{course}/remove-student', [CourseController::class, 'removeStudent'])->name('courses.remove-student');
    Route::get('/courses/export', [CourseController::class, 'export'])->name('courses.export');
});

// ==================== PROFILE ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/activity', [ProfileController::class, 'activity'])->name('profile.activity');
});

// ==================== AUTHENTICATION ROUTES ====================
require __DIR__.'/auth.php';

// ==================== M-PESA ROUTES ====================
// ⭐ M-Pesa Callback - MUST be public (no auth, no CSRF)
Route::post('/api/mpesa/callback', [FeeController::class, 'mpesaCallback'])
    ->name('mpesa.callback')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ==================== PAYMENT ROUTES ====================
Route::middleware('auth')->group(function () {
    // Payment form for students
    Route::get('/student/{student}/pay', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    
    // ⭐ M-Pesa STK Push Initiation (SINGLE DEFINITION)
    Route::post('/mpesa/stkpush', [FeeController::class, 'initiateMpesaPayment'])->name('mpesa.stkpush');
    
    // ⭐ M-Pesa Status Check (polling)
    Route::get('/mpesa/status', [FeeController::class, 'checkMpesaStatus'])->name('mpesa.status');
    
    // ⭐ M-Pesa Payment (alternative endpoint)
    Route::post('/mpesa/payment', [FeeController::class, 'mpesaPayment'])->name('mpesa.payment');

    // ⭐ M-Pesa Resend STK Push (for an existing pending/failed fee record)
    Route::post('/mpesa/resend', [FeeController::class, 'resendMpesaPayment'])->name('mpesa.resend');
});

// ==================== API ROUTES ====================
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/students', [StudentController::class, 'apiIndex'])->name('api.students');
    Route::get('/students/{id}', [StudentController::class, 'apiShow'])->name('api.students.show');
    Route::get('/courses', [CourseController::class, 'apiIndex'])->name('api.courses');
    Route::get('/courses/{id}', [CourseController::class, 'apiShow'])->name('api.courses.show');
    Route::get('/fees', [FeeController::class, 'apiIndex'])->name('api.fees');
    Route::get('/fees/stats', [FeeController::class, 'apiStats'])->name('api.fees.stats');
    Route::get('/fees/{id}', [FeeController::class, 'apiShow'])->name('api.fees.show');
    Route::get('/dashboard/stats', [DashboardController::class, 'apiStats'])->name('api.dashboard.stats');
    // ⭐ REMOVED DUPLICATE: Route::get('/fees/report', [FeeController::class, 'report'])->name('fees.report');
});

// ==================== REPORT ROUTES ====================
Route::prefix('reports')->middleware('auth')->group(function () {
    Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('reports.dashboard');
    Route::get('/student-statement', [ReportController::class, 'studentStatement'])->name('reports.student-statement');
    // ⭐ PDF Export Route
    Route::get('/student-statement/pdf', [ReportController::class, 'exportStudentStatementPDF'])->name('reports.student-statement.pdf');
    Route::get('/fee-collection', [ReportController::class, 'feeCollection'])->name('reports.fee-collection');
    Route::get('/outstanding-balances', [ReportController::class, 'outstandingBalances'])->name('reports.outstanding-balances');
    Route::get('/course-revenue', [ReportController::class, 'courseRevenue'])->name('reports.course-revenue');
    Route::get('/daily-collection', [ReportController::class, 'dailyCollection'])->name('reports.daily-collection');
    Route::get('/monthly-collection', [ReportController::class, 'monthlyCollection'])->name('reports.monthly-collection');
    Route::get('/mpesa-transactions', [ReportController::class, 'mpesaTransactions'])->name('reports.mpesa-transactions');
    Route::get('/payment-method-analysis', [ReportController::class, 'paymentMethodAnalysis'])->name('reports.payment-method-analysis');
    Route::get('/fee-summary', [ReportController::class, 'feeSummary'])->name('reports.fee-summary');
    Route::get('/export/{type}', [ReportController::class, 'export'])->name('reports.export');
});

// ==================== ERROR/DEBUG ROUTES ====================
Route::get('/maintenance', function () { 
    return view('maintenance'); 
})->name('maintenance');

Route::fallback(function () { 
    return view('errors.404'); 
});

// ==================== HEALTH CHECK ====================
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'environment' => app()->environment(),
        'app_name' => config('app.name'),
        'version' => '1.0.0',
    ]);
})->name('health');

// ==================== TEST ROUTES ====================
Route::get('/test-mpesa', function() {
    $controller = new App\Http\Controllers\FeeController();
    return response()->json([
        'configured' => $controller->isMpesaConfigured(),
        'env' => [
            'consumer_key' => env('MPESA_CONSUMER_KEY') ? 'set' : 'missing',
            'consumer_secret' => env('MPESA_CONSUMER_SECRET') ? 'set' : 'missing',
            'passkey' => env('MPESA_PASSKEY') ? 'set' : 'missing',
            'shortcode' => env('MPESA_SHORTCODE', '174379'),
            'environment' => env('MPESA_ENV', 'sandbox'),
        ]
    ]);
});