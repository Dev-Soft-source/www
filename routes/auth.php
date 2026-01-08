<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest.admin')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest.admin');

Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest.admin')
    ->name('admin.login');

Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest.admin');

Route::get('admin/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest.admin')
    ->name('password.request');

Route::post('admin/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest.admin')
    ->name('password.email');

Route::get('/admin/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest.admin')
    ->name('password.reset');

Route::post('/admin/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest.admin')
    ->name('admin.password.update');

Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
    ->middleware('admin.auth')
    ->name('verification.notice');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['admin.auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['admin.auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
    ->middleware('admin.auth')
    ->name('password.confirm');

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware('admin.auth');

Route::post('/admin/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('admin.auth')
    ->name('admin.logout');
