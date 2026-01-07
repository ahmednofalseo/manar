<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProjectsController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('web');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Password Reset Routes
Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Email Verification Routes
Route::get('/email/verify', [App\Http\Controllers\Auth\EmailVerificationController::class, 'show'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [App\Http\Controllers\Auth\EmailVerificationController::class, 'resend'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Placeholder routes
Route::get('/register', function () {
    return redirect('/login')->with('info', 'صفحة التسجيل قيد التطوير');
})->name('register');

// Language Switch Route
Route::get('/language/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

// Notifications Routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationsController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationsController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [App\Http\Controllers\NotificationsController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [App\Http\Controllers\NotificationsController::class, 'destroyAll'])->name('notifications.destroy-all');
});

// Dashboard Routes
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index')->middleware('auth');

// Placeholder routes for dashboard modules
// Projects Routes
Route::prefix('projects')->name('projects.')->group(function () {
    Route::get('/', [App\Http\Controllers\ProjectsController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ProjectsController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ProjectsController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\ProjectsController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\ProjectsController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\ProjectsController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\ProjectsController::class, 'destroy'])->name('destroy');
    
    // Sub routes
    Route::post('/{id}/attachments', [App\Http\Controllers\ProjectsController::class, 'storeAttachment'])->name('attachments.store');
    Route::delete('/{id}/attachments/{attachmentId}', [App\Http\Controllers\ProjectsController::class, 'destroyAttachment'])->name('attachments.destroy');
    Route::put('/{id}/stage', [App\Http\Controllers\ProjectsController::class, 'updateStage'])->name('stage.update');
    Route::get('/{id}/tasks', [App\Http\Controllers\ProjectsController::class, 'tasksIndex'])->name('tasks.index');
    Route::get('/{id}/financials', [App\Http\Controllers\ProjectsController::class, 'financialsIndex'])->name('financials.index');
    Route::post('/{id}/invoices', [App\Http\Controllers\ProjectsController::class, 'storeInvoice'])->name('invoices.store');
    Route::post('/{id}/thirdparty', [App\Http\Controllers\ProjectsController::class, 'storeThirdParty'])->name('thirdparty.store');
    Route::delete('/{id}/thirdparty/{thirdPartyId}', [App\Http\Controllers\ProjectsController::class, 'destroyThirdParty'])->name('thirdparty.destroy');
});

// Tasks Routes
Route::prefix('tasks')->name('tasks.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\TasksController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\TasksController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\TasksController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\TasksController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\TasksController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\TasksController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\TasksController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/status', [App\Http\Controllers\TasksController::class, 'changeStatus'])->name('change-status');
    Route::post('/{id}/comment', [App\Http\Controllers\TasksController::class, 'comment'])->name('comment');
});

// Clients Routes
Route::prefix('clients')->name('clients.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\ClientsController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ClientsController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ClientsController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\ClientsController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\ClientsController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\ClientsController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\ClientsController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/attachments', [App\Http\Controllers\ClientsController::class, 'storeAttachment'])->name('attachments.store');
    Route::delete('/{clientId}/attachments/{attachmentId}', [App\Http\Controllers\ClientsController::class, 'destroyAttachment'])->name('attachments.destroy');
    Route::post('/{id}/notes', [App\Http\Controllers\ClientsController::class, 'storeNote'])->name('notes.store');
    Route::get('/export', [App\Http\Controllers\ClientsController::class, 'export'])->name('export');
    Route::post('/import', [App\Http\Controllers\ClientsController::class, 'import'])->name('import');
    Route::post('/bulk-delete', [App\Http\Controllers\ClientsController::class, 'bulkDelete'])->name('bulk-delete');
});

// Approvals Routes
Route::prefix('approvals')->name('approvals.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\ApprovalsController::class, 'index'])->name('index');
    Route::post('/request', [App\Http\Controllers\ApprovalsController::class, 'request'])->name('request');
    Route::post('/{approval}/decide', [App\Http\Controllers\ApprovalsController::class, 'decide'])->name('decide');
    Route::get('/{approval}', [App\Http\Controllers\ApprovalsController::class, 'show'])->name('show');
});

// Financials (Invoices & Payments) Routes - Protected by permission (checked in controller)
Route::prefix('financials')->name('financials.')->group(function () {
    Route::get('/', [App\Http\Controllers\FinancialsController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\FinancialsController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\FinancialsController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\FinancialsController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\FinancialsController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\FinancialsController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\FinancialsController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/pdf', [App\Http\Controllers\FinancialsController::class, 'generatePdf'])->name('pdf');
    Route::post('/{id}/payments', [App\Http\Controllers\FinancialsController::class, 'storePayment'])->name('payments.store');
    Route::put('/{id}/payments/{paymentId}', [App\Http\Controllers\FinancialsController::class, 'updatePaymentStatus'])->name('payments.update');
});

// Legacy route for invoices
Route::get('/invoices', function () {
    return redirect()->route('financials.index');
})->name('invoices.index');

// Expenses Routes
Route::prefix('expenses')->name('expenses.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\ExpensesController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ExpensesController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ExpensesController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\ExpensesController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\ExpensesController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\ExpensesController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\ExpensesController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/approve', [App\Http\Controllers\ExpensesController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [App\Http\Controllers\ExpensesController::class, 'reject'])->name('reject');
    Route::get('/{id}/attachments/{attachmentId}/download', [App\Http\Controllers\ExpensesController::class, 'downloadAttachment'])->name('attachments.download');
    Route::delete('/{id}/attachments/{attachmentId}', [App\Http\Controllers\ExpensesController::class, 'deleteAttachment'])->name('attachments.delete');
});

// Profile Routes
Route::prefix('profile')->name('profile.')->middleware('auth')->group(function () {
    Route::get('/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('update');
});

// Admin Settings Routes
Route::prefix('admin/settings')->name('admin.settings.')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
    Route::put('/{group}', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
    Route::post('/test-email', [App\Http\Controllers\Admin\SettingsController::class, 'testEmail'])->name('test-email');
});

// Admin Users Routes
Route::prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/', [App\Http\Controllers\UsersController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\UsersController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\UsersController::class, 'store'])->name('store');
    Route::get('/export', [App\Http\Controllers\UsersController::class, 'export'])->name('export');
    Route::post('/import', [App\Http\Controllers\UsersController::class, 'import'])->name('import');
    
    // Roles and Permissions Management (must be before /{id} route)
    Route::get('/roles-permissions', [App\Http\Controllers\Admin\RolesAndPermissionsController::class, 'index'])->name('roles-permissions.index');
    Route::post('/roles', [App\Http\Controllers\Admin\RolesAndPermissionsController::class, 'storeRole'])->name('roles.store');
    Route::put('/roles/{id}', [App\Http\Controllers\Admin\RolesAndPermissionsController::class, 'updateRole'])->name('roles.update');
    Route::delete('/roles/{id}', [App\Http\Controllers\Admin\RolesAndPermissionsController::class, 'deleteRole'])->name('roles.delete');
    Route::post('/permissions', [App\Http\Controllers\Admin\RolesAndPermissionsController::class, 'storePermission'])->name('permissions.store');
    Route::put('/permissions/{id}', [App\Http\Controllers\Admin\RolesAndPermissionsController::class, 'updatePermission'])->name('permissions.update');
    Route::delete('/permissions/{id}', [App\Http\Controllers\Admin\RolesAndPermissionsController::class, 'deletePermission'])->name('permissions.delete');
    Route::post('/roles/{id}/permissions', [App\Http\Controllers\Admin\RolesAndPermissionsController::class, 'updateRolePermissions'])->name('roles.permissions.update');
    
    // User-specific routes (must be after specific routes)
    Route::get('/{id}', [App\Http\Controllers\UsersController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\UsersController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\UsersController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\UsersController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle', [App\Http\Controllers\UsersController::class, 'toggle'])->name('toggle');
    Route::post('/{id}/reset-password', [App\Http\Controllers\UsersController::class, 'resetPassword'])->name('reset-password');
    Route::post('/{id}/roles', [App\Http\Controllers\UsersController::class, 'updateRoles'])->name('roles.update');
});

// Legacy route
Route::get('/users', function () {
    return redirect()->route('admin.users.index');
})->name('users.index');

Route::get('/settings', function () {
    return redirect('/dashboard')->with('info', 'صفحة الإعدادات قيد التطوير');
})->name('settings.index');
