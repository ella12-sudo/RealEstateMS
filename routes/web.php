<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MaintenanceController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('properties', PropertyController::class);
    Route::resource('tenants', TenantController::class);
    Route::patch('tenants/{tenant}/vacate', [TenantController::class, 'vacate'])->name('tenants.vacate');

    Route::resource('payments', PaymentController::class);
    Route::patch('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::patch('/payments/{id}/archive', [PaymentController::class, 'archive'])->name('payments.archive');

    // Maintenance — only excluding 'show' now so edit/update work via modal
    Route::resource('maintenance', MaintenanceController::class)->except(['show']);

    Route::patch('/maintenance/{id}/archive', [MaintenanceController::class, 'archive'])->name('maintenance.archive');
    Route::patch('/maintenance/{id}/restore', [MaintenanceController::class, 'restore'])->name('maintenance.restore');

    Route::prefix('tenant')->name('tenant.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'tenant'])->name('dashboard');
        Route::get('/payments', [DashboardController::class, 'payments'])->name('payments');
        Route::post('/payments/store', [DashboardController::class, 'storePayment'])->name('payments.store');
        Route::get('/payments/{payment}/receipt', [DashboardController::class, 'showReceipt'])->name('payments.receipt');
        Route::get('/maintenance', [DashboardController::class, 'maintenance'])->name('maintenance');
        Route::post('/maintenance/store', [DashboardController::class, 'storeMaintenance'])->name('maintenance.store');
    });

    Route::get('/notifications/mark-as-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAsRead');

    Route::get('/notifications/{id}/read', function($id) {
        auth()->user()->notifications()->find($id)?->markAsRead();
        return redirect()->route('payments.index');
    })->name('notifications.read');

});

require __DIR__.'/auth.php';