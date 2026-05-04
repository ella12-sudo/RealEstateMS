<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;

// Redirect home to login
Route::get('/', function () {
    return redirect()->route('login');
});

// All these routes require login
Route::middleware(['auth'])->group(function () {

    // --- Admin Routes ---
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('properties', PropertyController::class);
    Route::resource('tenants', TenantController::class);
    
    Route::patch('tenants/{tenant}/vacate', [TenantController::class, 'vacate'])
        ->name('tenants.vacate');

    Route::resource('payments', PaymentController::class);

    // ADDED: Route for Admin to approve a pending payment
    Route::patch('/payments/{payment}/approve', [PaymentController::class, 'approve'])
        ->name('payments.approve');

    /**
     * MAINTENANCE ROUTE FIX
     * This stops the 404/405 errors by disabling the non-existent show/edit pages.
     */
    Route::resource('maintenance', MaintenanceController::class)->except(['show', 'edit']);

    // ADDED: Archive and Restore maintenance
    Route::patch('/maintenance/{id}/archive', [MaintenanceController::class, 'archive'])
        ->name('maintenance.archive');
    Route::patch('/maintenance/{id}/restore', [MaintenanceController::class, 'restore'])
        ->name('maintenance.restore');

    // --- Tenant Portal Routes ---
    Route::prefix('tenant')->name('tenant.')->group(function () {
        
        // Dashboard View
        Route::get('/dashboard', [DashboardController::class, 'tenant'])
            ->name('dashboard');

        // Payments View
        Route::get('/payments', [DashboardController::class, 'payments'])
            ->name('payments');

        // ADDED: Route to handle the submission of new payments
        Route::post('/payments/store', [DashboardController::class, 'storePayment'])
            ->name('payments.store');

        // ADDED: Route to view the payment receipt
        Route::get('/payments/{payment}/receipt', [DashboardController::class, 'showReceipt'])
            ->name('payments.receipt');

        // Maintenance View & Submission
        Route::get('/maintenance', [DashboardController::class, 'maintenance'])
            ->name('maintenance');

        // Route to handle the submission of new maintenance requests
        Route::post('/maintenance/store', [DashboardController::class, 'storeMaintenance'])
            ->name('maintenance.store');
    });

    // ADDED: Route to clear notifications (Used by the Admin Bell icon)
    Route::get('/notifications/mark-as-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAsRead');

    // ADDED: Route to mark a single notification as read and redirect to payments
    Route::get('/notifications/{id}/read', function($id) {
        auth()->user()->notifications()->find($id)?->markAsRead();
        return redirect()->route('payments.index');
    })->name('notifications.read');

});

require __DIR__.'/auth.php';