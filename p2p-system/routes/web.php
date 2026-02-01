<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
    return Redirect::route('login');
});

Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- Employee Purchase Request Routes ---
Route::middleware(['auth'])->group(function () {
    // This will be the main "My Requests" dashboard for employees
    Route::get('/my-requests', function () {
        return redirect()->route('dashboard');
    })->name('requests.index');

    Route::get('/my-requests/create', [PurchaseRequestController::class, 'create'])
         ->name('requests.create');
    Route::post('/my-requests', [PurchaseRequestController::class, 'store'])
         ->name('requests.store');
    
    Route::get('/my-requests/{purchaseRequest}', [PurchaseRequestController::class, 'show'])
         ->name('requests.show')
         ->missing(function () {
             return redirect()->route('requests.index')
                 ->with('error', 'The requested item was not found or you do not have permission to view it.');
         });
    Route::post('/my-requests/{purchaseRequest}/confirm', [PurchaseRequestController::class, 'confirm'])
         ->name('requests.confirm');
    
    Route::post('/my-requests/{purchaseRequest}/resubmit', [PurchaseRequestController::class, 'resubmit'])
         ->name('requests.resubmit');
});

// --- Approval Workflow Routes ---
Route::middleware(['auth', 'can:is-approver'])->group(function () {
    Route::get('/approval-queue', [ApprovalController::class, 'index'])
         ->name('approval.queue');

    Route::get('/approval-queue/{purchaseRequest}', [ApprovalController::class, 'show'])
         ->name('approval.show')
         ->missing(function () {
             return redirect()->route('approval.queue')
                 ->with('info', 'The requested item was not found or has been processed already. Please check the current approval queue below.');
         });
    Route::post('/approval-queue/{purchaseRequest}', [ApprovalController::class, 'process'])
         ->name('approval.process');
});

// --- Procurement Offer Routes ---
Route::middleware(['auth'])->group(function () {
    // Only Procurement and Admin can access this
    // List of requests needing quotations
    Route::get('/procurement/quotations', [OfferController::class, 'index'])
         ->middleware('can:is-procurement')
         ->name('offers.index');

    // List of requests where CASH IS READY (Ready to Buy)
    Route::get('/procurement/ready-to-buy', [OfferController::class, 'readyToBuy'])
         ->middleware('can:is-procurement')
         ->name('offers.ready_to_buy');
         
    Route::get('/offers/{purchaseRequest}/manage', [OfferController::class, 'create'])
         ->middleware('can:is-procurement')
         ->name('offers.create');

    Route::post('/offers/{purchaseRequest}', [OfferController::class, 'store'])
         ->middleware('can:is-procurement')
         ->name('offers.store');
    
    // Deprecated? Kept for backward compatibility if needed, but we use submitRecommendation now
    Route::post('/offers/{purchaseRequest}/select/{offer}', [OfferController::class, 'select'])
         ->middleware('can:is-procurement')
         ->name('offers.select');
         
    Route::post('/offers/{purchaseRequest}/submit-recommendation', [OfferController::class, 'submitRecommendation'])
         ->middleware('can:is-procurement')
         ->name('offers.submitRecommendation');

    Route::get('/offers/{purchaseRequest}/print-po', [OfferController::class, 'printPo'])
         ->middleware('can:is-procurement')
         ->name('offers.print_po');

    // Vendor Management
    Route::resource('vendors', \App\Http\Controllers\VendorController::class)
         ->middleware('can:can-manage-vendors');
});

// --- Offer Approval Routes (Finance & Manager) ---
Route::middleware(['auth'])->group(function () {
    // Finance Review
    Route::get('/finance/review/{purchaseRequest}', [OfferController::class, 'financeReview'])
         ->middleware('can:is-finance')
         ->name('offers.financeReview');
         
    Route::post('/finance/review/{purchaseRequest}', [OfferController::class, 'financeSubmit'])
         ->middleware('can:is-finance')
         ->name('offers.financeSubmit');

    // Manager Review
    Route::get('/manager/review/{purchaseRequest}', [OfferController::class, 'managerReview'])
         ->middleware('can:is-manager')
         ->name('offers.managerReview');
         
    Route::post('/manager/review/{purchaseRequest}', [OfferController::class, 'managerApprove'])
         ->middleware('can:is-manager')
         ->name('offers.managerApprove');
});

// --- Management Routes ---
Route::middleware(['auth', 'can:can-manage-budgets'])->group(function () {
    Route::get('/budget-management', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budget-management', [BudgetController::class, 'store'])->name('budgets.store');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/user/{user}', [AnalyticsController::class, 'employeeHistory'])->name('analytics.employee');
});

// --- Admin-Only Routes ---
Route::middleware(['auth', 'can:is-admin'])->group(function () {
    // We use 'prefix' to make all routes start with '/admin', e.g., /admin/users
    Route::prefix('admin')->name('admin.')->group(function () {
        // Creates routes like admin.users.index, admin.users.create, etc.
        Route::resource('users', UserManagementController::class);
        
        // Site Settings
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::get('/settings/remove-logo', [\App\Http\Controllers\Admin\SettingsController::class, 'removeLogo'])->name('settings.remove-logo');
    });
});

// --- Notification Routes ---
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead'])
         ->name('notifications.read');
});

require __DIR__.'/auth.php';


