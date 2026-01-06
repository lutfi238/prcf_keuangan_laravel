<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProposalApprovalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportApprovalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Finance\VillageController;
use App\Http\Controllers\Finance\ProjectController;
use App\Http\Controllers\Finance\ExpenseCodeController;
use App\Http\Controllers\Finance\DonorController;
use App\Http\Controllers\BankBookController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\DonorReportController;
use App\Http\Controllers\BudgetController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Redirect root ke login
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    // Login routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    
    // OTP verification routes
    Route::get('/verify-otp', [LoginController::class, 'showOtpForm'])->name('auth.verify-otp');
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp'])->name('auth.verify-otp.submit');
    Route::post('/resend-otp', [LoginController::class, 'resendOtp'])->name('auth.resend-otp');
    
    // Registration routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
    
    // Forgot password routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
    
    // Pending account page
    Route::get('/pending', function () {
        return view('auth.pending');
    })->name('auth.pending');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Sudah Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard - semua role menggunakan single dashboard dengan tampilan berbeda
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Role-specific dashboard routes (alias)
    Route::get('/dashboard/admin', [DashboardController::class, 'index'])->name('dashboard.admin');
    Route::get('/dashboard/fm', [DashboardController::class, 'index'])->name('dashboard.fm');
    Route::get('/dashboard/pm', [DashboardController::class, 'index'])->name('dashboard.pm');
    Route::get('/dashboard/sa', [DashboardController::class, 'index'])->name('dashboard.sa');
    Route::get('/dashboard/dir', [DashboardController::class, 'index'])->name('dashboard.dir');

    /*
    |--------------------------------------------------------------------------
    | Proposal Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('proposals')->name('proposals.')->group(function () {
        Route::get('/', [ProposalController::class, 'index'])->name('index');
        Route::get('/create', [ProposalController::class, 'create'])->name('create');
        Route::post('/', [ProposalController::class, 'store'])->name('store');
        Route::get('/{proposal}', [ProposalController::class, 'show'])->name('show');
        Route::get('/{proposal}/edit', [ProposalController::class, 'edit'])->name('edit');
        Route::put('/{proposal}', [ProposalController::class, 'update'])->name('update');
        Route::delete('/{proposal}', [ProposalController::class, 'destroy'])->name('destroy');
        Route::post('/{proposal}/submit', [ProposalController::class, 'submit'])->name('submit');
        
        // Approval routes
        Route::get('/{proposal}/review-fm', [ProposalApprovalController::class, 'reviewFm'])->name('review-fm');
        Route::post('/{proposal}/approve-fm', [ProposalApprovalController::class, 'approveFm'])->name('approve-fm');
        Route::post('/{proposal}/reject', [ProposalApprovalController::class, 'reject'])->name('reject');
    });

    /*
    |--------------------------------------------------------------------------
    | Report Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/create', [ReportController::class, 'create'])->name('create');
        Route::post('/', [ReportController::class, 'store'])->name('store');
        Route::get('/{report}', [ReportController::class, 'show'])->name('show');
        Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('edit');
        Route::put('/{report}', [ReportController::class, 'update'])->name('update');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
        Route::post('/{report}/submit', [ReportController::class, 'submit'])->name('submit');
        
        // Approval routes
        Route::get('/{report}/verify-sa', [ReportApprovalController::class, 'verifySa'])->name('verify-sa');
        Route::post('/{report}/verify-sa', [ReportApprovalController::class, 'verifySaSubmit'])->name('verify-sa.submit');
        Route::get('/{report}/approve-fm', [ReportApprovalController::class, 'approveFm'])->name('approve-fm');
        Route::post('/{report}/approve-fm', [ReportApprovalController::class, 'approveFmSubmit'])->name('approve-fm.submit');
        Route::post('/{report}/reject', [ReportApprovalController::class, 'reject'])->name('reject');
        Route::post('/{report}/request-revision', [ReportApprovalController::class, 'requestRevision'])->name('request-revision');
        
        // Donor Reports
        Route::prefix('donor')->name('donor.')->group(function () {
            Route::get('/', [DonorReportController::class, 'index'])->name('index');
            Route::get('/create', [DonorReportController::class, 'create'])->name('create');
            Route::post('/', [DonorReportController::class, 'store'])->name('store');
            Route::get('/{report}', [DonorReportController::class, 'show'])->name('show');
            Route::get('/{report}/download', [DonorReportController::class, 'download'])->name('download');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Financial Books Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('books')->name('books.')->group(function () {
        // Buku Bank
        Route::get('/bank', [BankBookController::class, 'index'])->name('bank.index');
        Route::get('/bank/{id}', [BankBookController::class, 'show'])->name('bank.show');
        Route::get('/bank/export/excel', [BankBookController::class, 'exportExcel'])->name('bank.export');
        
        // Buku Piutang
        Route::get('/receivables', [ReceivableController::class, 'index'])->name('receivables.index');
        Route::get('/receivables/{id}', [ReceivableController::class, 'show'])->name('receivables.show');
        Route::get('/receivables/export/excel', [ReceivableController::class, 'exportExcel'])->name('receivables.export');
    });

    /*
    |--------------------------------------------------------------------------
    | Finance Manager Routes (Includes Master Data Management)
    |--------------------------------------------------------------------------
    */
    Route::prefix('finance')->name('finance.')->middleware('role:Finance Manager')->group(function () {
        // Master Data
        Route::resource('villages', VillageController::class)->except(['show']);
        Route::resource('projects', ProjectController::class);
        Route::resource('donors', DonorController::class)->except(['show']);
        Route::resource('expense-codes', ExpenseCodeController::class)->except(['show']);

        // Budget Management
        Route::resource('budgets', BudgetController::class)->except(['show']);
        Route::get('/api/exp-codes', [BudgetController::class, 'getExpCodes'])->name('budgets.exp-codes');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Monitoring & User Management)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:Admin')->group(function () {
        // Users
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        
        // Activity Log
        Route::get('/activity-log', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-log.index');
        
        // System Control
        Route::get('/system-control', [\App\Http\Controllers\Admin\SystemControlController::class, 'index'])->name('system-control.index');
        Route::post('/system-control/toggle-maintenance', [\App\Http\Controllers\Admin\SystemControlController::class, 'toggleMaintenance'])->name('system-control.toggle-maintenance');
        Route::post('/system-control/toggle-registration', [\App\Http\Controllers\Admin\SystemControlController::class, 'toggleRegistration'])->name('system-control.toggle-registration');
        Route::get('/system-control/health', [\App\Http\Controllers\Admin\SystemControlController::class, 'health'])->name('system-control.health');
    });
});
