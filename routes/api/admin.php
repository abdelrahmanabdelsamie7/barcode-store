<?php
use App\Http\Controllers\API\AdminRevenueController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\API\AdminDashboardController;
Route::prefix('admin')->middleware('api')->group(function () {
    Route::post('/login', [AuthAdminController::class, 'login']);
    Route::middleware('auth:admins')->controller(AuthAdminController::class)->group(function () {
        Route::post('/add-admin', 'addAdmin');
        Route::get('/getaccount', 'getAccount');
        Route::get('/all-admins', 'allAdmins');
        Route::post('/logout', 'logout');
        Route::post('/refresh', 'refresh');
        Route::delete('/delete-admin/{id}', 'deleteAdmin');
        Route::put('/update-password/{id?}', 'updatePassword');
    });
});

// Admin Dashboard Overview
Route::middleware(['auth:admins'])->prefix('admin')->group(function () {
    Route::get('/dashboard-overview', [AdminDashboardController::class, 'overview']);
});
Route::middleware(['auth:admins'])->prefix('admin')->group(function () {
    Route::get('/dashboard/revenue-10-percent', [AdminRevenueController::class, 'tenPercentOfDeliveredOrders']);
});
