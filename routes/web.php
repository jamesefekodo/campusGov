<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Config\SystemConfigurationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Frontend\FrontendController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/clear-all', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'All cleared!';
});

// basic/general routes
Route::middleware(['authenticationMiddlewares'])->group(function () {

    Route::get('/', function () {
        return redirect()->route('homepage');
    });
    Route::get('/home', [FrontendController::class, 'homepage'])->name('homepage');

    Route::get('register', [AuthController::class, 'register'])->name('registrationPage');
    Route::post('register/submit', [AuthController::class, 'submitUserRegistrationRequest'])->name('submitUserRegistrationRequest');
    Route::get('login', [AuthController::class, 'login'])->name('loginPage');
    Route::post('login/submit', [AuthController::class, 'authLogin'])->name('submitLoginRequest');
    Route::get('logout', [AuthController::class, 'AuthLogout'])->name('logoutRequest');
    Route::get('forgot_password', [AuthController::class, 'forgotPassword'])->name('forgotPassword');
    Route::post('forgot_password/send_password_reset_link', [AuthController::class, 'requestPasswordResetLink'])->name('requestPasswordResetLink');
    Route::get('reset_password/{token}', [AuthController::class, 'passwordResetPage'])->name('passwordResetLink');
    Route::post('reset_password/change_password/{token}', [AuthController::class, 'submitPasswordResetRequest'])->name('submitPasswordResetRequest');
});



Route::middleware(['authenticationMiddlewares', 'adminMiddlewares'])->group(function () {
    Route::get('admin/dashboard', [DashboardController::class, 'dashboard'])->name('adminDashboard');

    Route::get('admin/dashboard', [SystemConfigurationController::class, 'userPermisssions'])->name('config.userPermisssions');
});
