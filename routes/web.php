<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\RepairOrderController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DeviceTypeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\NewReportController;
use Illuminate\Support\Facades\Route;

// Landing page route
Route::get('/', [LandingController::class, 'index'])->name('landing');

// About Us page route
Route::get('/about-us', [LandingController::class, 'aboutUs'])->name('about-us');

// Public feedback submission
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// Admin login redirect
Route::get('/admin', function () {
    return redirect()->route('login');
})->name('admin');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/dashboard/technician-view', [DashboardController::class, 'technicianView'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.technician-view');

// Common authenticated routes (accessible by both admin and technician)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin-only routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Customer routes
    Route::resource('customers', CustomerController::class);

    // Device routes
    Route::resource('devices', DeviceController::class);
    Route::get('/customers/{customer}/devices', [DeviceController::class, 'getCustomerDevices'])
        ->name('customers.devices');
    Route::get('/devices/{device}/parts', [DeviceController::class, 'getDeviceParts'])
        ->name('devices.parts');

    // Repair Order routes (admin can access all)
    Route::resource('repair-orders', RepairOrderController::class);
    Route::patch('repair-orders/{repairOrder}/mark-delivered', [RepairOrderController::class, 'markDelivered'])->name('repair-orders.mark-delivered');
    Route::patch('repair-orders/{repairOrder}/cancel', [RepairOrderController::class, 'cancel'])->name('repair-orders.cancel');
    Route::get('/devices/{device}/parts', [RepairOrderController::class, 'getPartsForDevice'])->name('devices.parts');

    // Technician management routes
    Route::resource('technicians', TechnicianController::class);
    Route::patch('technicians/{technician}/toggle-status', [TechnicianController::class, 'toggleStatus'])->name('technicians.toggle-status');

    // Service routes
    Route::resource('services', ServiceController::class);

    // Device Type routes (for device categories)
    Route::post('device-types', [DeviceTypeController::class, 'store'])->name('device-types.store');
    Route::put('device-types/{deviceType}', [DeviceTypeController::class, 'update'])->name('device-types.update');
    Route::delete('device-types/{deviceType}', [DeviceTypeController::class, 'destroy'])->name('device-types.destroy');

    // Invoice routes
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-as-paid');
    Route::post('invoices/bulk-mark-paid', [InvoiceController::class, 'bulkMarkAsPaid'])->name('invoices.bulk-mark-paid');
    Route::delete('invoices/bulk-delete', [InvoiceController::class, 'bulkDelete'])->name('invoices.bulk-delete');
    Route::post('repair-orders/{repairOrder}/generate-invoice', [InvoiceController::class, 'generateFromRepairOrder'])->name('repair-orders.generate-invoice');

    // Parts routes
    Route::resource('parts', PartController::class);
    Route::patch('parts/{part}/update-stock', [PartController::class, 'updateStock'])->name('parts.update-stock');
    Route::patch('parts/{part}/update-status', [PartController::class, 'updateStatus'])->name('parts.update-status');

    // Reports routes - Comprehensive Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [NewReportController::class, 'index'])->name('index');
        Route::get('/customer-analytics', [NewReportController::class, 'customerAnalytics'])->name('customer-analytics');
        Route::get('/sales-analytics', [NewReportController::class, 'salesAnalytics'])->name('sales-analytics');
        Route::get('/parts-analytics', [NewReportController::class, 'partsAnalytics'])->name('parts-analytics');
        Route::get('/device-repair-analytics', [NewReportController::class, 'deviceRepairAnalytics'])->name('device-repair-analytics');
        Route::get('/technician-performance', [NewReportController::class, 'technicianPerformance'])->name('technician-performance');
        Route::get('/service-analytics', [NewReportController::class, 'serviceAnalytics'])->name('service-analytics');
    });

    // Feedback management routes
    Route::resource('feedback', FeedbackController::class)->except(['store']);
    Route::patch('feedback/{feedback}/toggle-featured', [FeedbackController::class, 'toggleFeatured'])->name('feedback.toggle-featured');
    Route::get('feedback/{feedback}/share', [FeedbackController::class, 'share'])->name('feedback.share');
    
    // Settings routes
    Route::get('settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::patch('settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
});

// Technician and Admin (as technician) routes
Route::middleware(['auth'])->group(function () {
    // Technician orders view (accessible by technicians and admin users)
    Route::get('technician/orders', [RepairOrderController::class, 'technicianOrders'])->name('technician.orders');
});

// Shared routes (both admin and technician can access)
Route::middleware('auth')->group(function () {
    // Repair order status updates (both admin and technician can update status)
    Route::patch('repair-orders/{repairOrder}/status', [RepairOrderController::class, 'updateStatus'])->name('repair-orders.update-status');

    // Repair order viewing (both can view individual orders)
    Route::get('repair-orders/{repairOrder}', [RepairOrderController::class, 'show'])->name('repair-orders.show');
});

require __DIR__.'/auth.php';
