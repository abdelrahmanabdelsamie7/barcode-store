<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthUserController;
Route::prefix('user')->controller(AuthUserController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
    Route::get('/getaccount', 'getaccount');
});
