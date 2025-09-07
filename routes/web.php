<?php

use App\Http\Controllers\Enforcer\EnforcerForgotPasswordController;
use App\Http\Controllers\Admin\AdminForgotPasswordController;
use App\Http\Controllers\Enforcer\EnforcerController;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\FineTicketController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\SmsLogController;
use App\Http\Controllers\IntroController;
use Illuminate\Support\Facades\Route;

// Admin Routes
// Animation Intro Page
Route::get('/', [IntroController::class, 'welcome']);
Route::get('/gov', [IntroController::class, 'gov']);

// Admin Login Page
Route::get('/admin-login', [AdminController::class, 'admin']);
Route::post('/admin-login', [AdminController::class, 'login'])->name('admin.login.submit');

// Admin Dashboard (protected by session check)
Route::get('/admin-dashboard', [AdminController::class, 'adminDashboard'])->name('admin.admin-dashboard');
// Route::get('/admin-create', [AdminController::class, 'create'])->name('admin.create');
Route::post('/admin-store', [AdminController::class, 'store'])->name('admin.store');
Route::get('/admin-logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::get('/admin/fetch-summary', [AdminController::class, 'fetchSummary'])
    ->name('admin.fetch-summary');

// View Drivers
Route::get('/view_all_drivers', [DriverController::class, 'view'])->name('admin.view.drivers');
Route::post('/admin/driver/details', [DriverController::class, 'getDriverDetails']);
Route::post('/admin/driver/update', [DriverController::class, 'update']);
Route::post('/admin/driver/archive', [DriverController::class, 'archive'])->name('driver.archive');
Route::get('/admin/driver/archived', [DriverController::class, 'archived'])->name('drivers.archived');


// Restore Driver
Route::post('/admin/drivers/restore/{licenseId}', [DriverController::class, 'restore'])->name('drivers.restore');

// View Enforcers
Route::get('/view_all_enforcers', [EnforcerController::class, 'index'])->name('enforcers.view');
Route::post('/admin/enforcer/details', [EnforcerController::class, 'getEnforcerDetails']);
Route::post('/admin/enforcer/update', [EnforcerController::class, 'update']);

Route::post('/admin/enforcer/archive', [EnforcerController::class, 'archive']);
Route::get('/admin/enforcer/archived', [EnforcerController::class, 'archived'])->name('enforcers.archived');

Route::post('/admin/enforcer/restore', [EnforcerController::class, 'restore'])->name('enforcers.restore');

// Add Enforcers
Route::get('/add_enforcer', [EnforcerController::class, 'create'])->name('enforcers.create');
Route::post('/enforcers/store', [EnforcerController::class, 'store'])->name('enforcers.store');


// Payment Status Routes
Route::prefix('pending_fine_tickets')->name('admin.pendingTickets.')->group(function () {
    Route::get('/', [FineTicketController::class, 'pendingTickets'])->name('index');
    Route::get('/fetch', [FineTicketController::class, 'fetchPendingTickets'])->name('fetch');
    Route::post('/details', [FineTicketController::class, 'ticketDetails'])->name('details');
    Route::post('/pay', [FineTicketController::class, 'payFine'])->name('pay');
});

Route::get('/paid_fine_tickets', [FineTicketController::class, 'viewPaidFines'])->name('admin.paidTickets');
Route::post('/admin/paid_fine_tickets/details', [FineTicketController::class, 'paidTicketDetails'])->name('admin.paidTickets.details');

// Admin Change password
Route::get('/admin-profile', [AdminController::class, 'editProfile'])->name('admin.profile');
Route::post('/admin/update-password', [AdminController::class, 'updatePassword'])->name('admin.updatePassword');

// Traffic Admin Forgot Password Routes
Route::get('/admin-forgot-password', [AdminForgotPasswordController::class, 'showForm'])->name('admin.forgot');
Route::post('/forgot-password', [AdminForgotPasswordController::class, 'sendCode'])->name('admin.forgot.send');

Route::get('/verify-code', [AdminForgotPasswordController::class, 'showVerifyCodeForm'])->name('admin.verification.code');
Route::post('/verify-code', [AdminForgotPasswordController::class, 'verifyCode'])->name('admin.verification.code.check');

Route::get('/reset-password', [AdminForgotPasswordController::class, 'showResetForm'])->name('admin.reset.password');
Route::post('/reset-password', [AdminForgotPasswordController::class, 'resetPassword'])->name('admin.reset.password.submit');

Route::get('/admin/fetch-issued-fines', [AdminController::class, 'fetchIssuedFines']);
Route::get('/admin/barangay-violations', [AdminController::class, 'fetchBarangayViolations']);
Route::get('/admin/fetch-total-amount', [AdminController::class, 'totalIssuedAmount']);
Route::get('/admin/fine-amount-by-enforcer', [AdminController::class, 'getFineAmountByEnforcer']);




// Enforcers Routes
Route::get('/enforcer-login', [EnforcerController::class, 'enforcer'])->name('enforcer.login');
Route::post('/enforcer-login', [EnforcerController::class, 'login'])->name('enforcer.login.submit');

// Toggle single and all enforcer lock
Route::post('/enforcer/toggle-lock', [EnforcerController::class, 'toggleLock'])->name('enforcer.toggleLock');
Route::post('/enforcer/toggle-lock-all', [EnforcerController::class, 'toggleLockAll'])->name('enforcer.toggleLockAll');

// Enforcer Dashboard (protected by session check)
Route::get('/enforcer-dashboard', [EnforcerController::class, 'enforcerDashboard'])->name('enforcer.enforcer-dashboard');
Route::get('/enforcer.logout', [EnforcerController::class, 'logout'])->name('enforcer.logout');

Route::get('/add_driver', [DriverController::class, 'create'])->name('drivers.create');
Route::post('/drivers/store', [DriverController::class, 'store'])->name('drivers.store');

Route::get('/add_new_fine/{license_id}', [FineTicketController::class, 'create'])->name('fine.create');
Route::post('/fine/store', [FineTicketController::class, 'store'])->name('fine.store');
Route::get('/clear-ticket', [FineTicketController::class, 'clearTicket'])->name('fine.clearTicket');

// Route::get('/past_fines', [FineTicketController::class, 'pastFines'])->name('fine.past');
Route::get('/enforcer.enforcer-dashboard', [FineTicketController::class, 'searchPastFines'])->name('fine.past');
Route::get('/view_reported_fine', [FineTicketController::class, 'viewReportedFines'])->name('enforcer.view_fines');

// SMS Function
Route::get('/send-sms/{licenseId}', [SmsLogController::class, 'sendSMSViaVonage']);
Route::get('/driver/view-ticket/{token}', [FineTicketController::class, 'viewSecureTicket'])->name('ticket.view.secure');
Route::get('/driver/ticket/download/{licenseId}', [DriverController::class, 'downloadPDF'])->name('download.ticket.pdf');

Route::get('/sms_activity_log', [SmsLogController::class, 'sms']);
Route::post('/resend-sms', [SmsLogController::class, 'resend'])->name('sms.resend');

// Traffic Violation Ticket
Route::get('/manage_traffic_violations', [ViolationController::class, 'create'])->name('violation.create');
Route::get('/manage_traffic_violations/view', [ViolationController::class, 'view'])->name('violations.view');
Route::post('/violation/store', [ViolationController::class, 'store'])->name('violation.store');

Route::post('/admin/violation/details', [ViolationController::class, 'getViolationDetails']);
Route::post('/admin/violation/update', [ViolationController::class, 'update']);
Route::post('/admin/violation/archive', [ViolationController::class, 'archive'])->name('violation.archive');
Route::post('/admin/violation/restore', [ViolationController::class, 'restore'])->name('violations.restore');

// Show archived Traffic Violation
Route::get('/admin/violation/archived', [ViolationController::class, 'archived'])->name('violations.archived');

Route::post('/enforcer/check-lock', [EnforcerController::class, 'checkLock'])->name('enforcer.check-lock');
Route::post('/ajax/past-fines-search', [FineTicketController::class, 'ajaxSearchPastFines'])->name('fines.ajax-search');

// Enforcer Change password
Route::get('/enforcer-profile', [EnforcerController::class, 'edit'])->name('enforcer.profile');
Route::post('/enforcer/profile/update-password', [EnforcerController::class, 'updatePassword'])->name('enforcer.profile.updatePassword');

// Traffic Enforcer Forgot Password Routes
Route::get('/enforcer-forgot-password', [EnforcerForgotPasswordController::class, 'showForgotForm'])->name('enforcer.forgot.form');
Route::post('/enforcer/forgot-password', [EnforcerForgotPasswordController::class, 'sendResetCode'])->name('enforcer.forgot.send');

Route::get('/enforcer/verify-code', [EnforcerForgotPasswordController::class, 'showVerifyCodeForm'])->name('enforcer.verify.form');
Route::post('/enforcer/verify-code', [EnforcerForgotPasswordController::class, 'verifyCode'])->name('enforcer.verify.code');

Route::get('/enforcer/reset-password', [EnforcerForgotPasswordController::class, 'showResetForm'])->name('enforcer.reset.form');
Route::post('/enforcer/reset-password', [EnforcerForgotPasswordController::class, 'resetPassword'])->name('enforcer.reset.password');

Route::post('/admin/enforcer/violation', [EnforcerController::class, 'issueViolation'])->name('enforcer.issueViolation');
Route::post('/admin/enforcer/violation/settle', [EnforcerController::class, 'settleSingleViolation'])->name('enforcer.violation.settle');
