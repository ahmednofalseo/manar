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
Route::post('/login', function () {
    // سيتم تنفيذ منطق تسجيل الدخول هنا
    return redirect()->back()->with('error', 'يرجى إعداد نظام المصادقة أولاً');
})->name('login.submit');

// Placeholder routes
Route::get('/register', function () {
    return redirect('/login')->with('info', 'صفحة التسجيل قيد التطوير');
})->name('register');

Route::get('/password/reset', function () {
    return redirect('/login')->with('info', 'صفحة استعادة كلمة المرور قيد التطوير');
})->name('password.request');

// Dashboard Routes
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard.index');

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
    Route::put('/{id}/stage', [App\Http\Controllers\ProjectsController::class, 'updateStage'])->name('stage.update');
    Route::get('/{id}/tasks', [App\Http\Controllers\ProjectsController::class, 'tasksIndex'])->name('tasks.index');
    Route::get('/{id}/financials', [App\Http\Controllers\ProjectsController::class, 'financialsIndex'])->name('financials.index');
    Route::post('/{id}/invoices', [App\Http\Controllers\ProjectsController::class, 'storeInvoice'])->name('invoices.store');
    Route::post('/{id}/thirdparty', [App\Http\Controllers\ProjectsController::class, 'storeThirdParty'])->name('thirdparty.store');
});

// Tasks Routes
Route::prefix('tasks')->name('tasks.')->group(function () {
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
Route::prefix('clients')->name('clients.')->group(function () {
    Route::get('/', [App\Http\Controllers\ClientsController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ClientsController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ClientsController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\ClientsController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\ClientsController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\ClientsController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\ClientsController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/attachments', [App\Http\Controllers\ClientsController::class, 'storeAttachment'])->name('attachments.store');
    Route::post('/{id}/notes', [App\Http\Controllers\ClientsController::class, 'storeNote'])->name('notes.store');
    Route::get('/export', [App\Http\Controllers\ClientsController::class, 'export'])->name('export');
    Route::post('/import', [App\Http\Controllers\ClientsController::class, 'import'])->name('import');
});

// Financials (Invoices & Payments) Routes
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
Route::prefix('expenses')->name('expenses.')->group(function () {
    Route::get('/', [App\Http\Controllers\ExpensesController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ExpensesController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ExpensesController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\ExpensesController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\ExpensesController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\ExpensesController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\ExpensesController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/approve', [App\Http\Controllers\ExpensesController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [App\Http\Controllers\ExpensesController::class, 'reject'])->name('reject');
});

// Admin Users Routes
Route::prefix('admin/users')->name('admin.users.')->group(function () {
    Route::get('/', [App\Http\Controllers\UsersController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\UsersController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\UsersController::class, 'store'])->name('store');
    Route::get('/{id}', [App\Http\Controllers\UsersController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [App\Http\Controllers\UsersController::class, 'edit'])->name('edit');
    Route::put('/{id}', [App\Http\Controllers\UsersController::class, 'update'])->name('update');
    Route::delete('/{id}', [App\Http\Controllers\UsersController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle', [App\Http\Controllers\UsersController::class, 'toggle'])->name('toggle');
    Route::post('/{id}/reset-password', [App\Http\Controllers\UsersController::class, 'resetPassword'])->name('reset-password');
    Route::post('/{id}/roles', [App\Http\Controllers\UsersController::class, 'updateRoles'])->name('roles.update');
    Route::get('/export', [App\Http\Controllers\UsersController::class, 'export'])->name('export');
    Route::post('/import', [App\Http\Controllers\UsersController::class, 'import'])->name('import');
});

// Legacy route
Route::get('/users', function () {
    return redirect()->route('admin.users.index');
})->name('users.index');

Route::get('/settings', function () {
    return redirect('/dashboard')->with('info', 'صفحة الإعدادات قيد التطوير');
})->name('settings.index');
