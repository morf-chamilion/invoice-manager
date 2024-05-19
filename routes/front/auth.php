<?php

use App\Http\Controllers\Front\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Front\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Front\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Front\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Front\Auth\NewPasswordController;
use App\Http\Controllers\Front\Auth\PasswordController;
use App\Http\Controllers\Front\Auth\PasswordResetLinkController;
use App\Http\Controllers\Front\Auth\RegisteredCustomerController;
use App\Http\Controllers\Front\Auth\VerifyEmailController;
use App\RoutePaths\Front\Auth\AuthRoutePath;
use Illuminate\Support\Facades\Route;

Route::get('login', [AuthenticatedSessionController::class, 'create'])
    ->name(AuthRoutePath::LOGIN);

Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->name(AuthRoutePath::LOGIN_STORE);

Route::get('register', [RegisteredCustomerController::class, 'create'])
    ->name(AuthRoutePath::REGISTER);

Route::post('register', [RegisteredCustomerController::class, 'store'])
    ->name(AuthRoutePath::REGISTER_STORE);

Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->name(AuthRoutePath::PASSWORD_FORGET);

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name(AuthRoutePath::PASSWORD_MAIL);

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name(AuthRoutePath::PASSWORD_RESET);

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->name(AuthRoutePath::PASSWORD_STORE);

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name(AuthRoutePath::VERIFICATION_VERIFY);

Route::middleware('auth.customer')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name(AuthRoutePath::VERIFICATION_NOTICE);

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name(AuthRoutePath::VERIFICATION_SEND);

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name(AuthRoutePath::VERIFICATION_CONFIRM);

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->name(AuthRoutePath::PASSWORD_CONFIRM);

    Route::put('password', [PasswordController::class, 'update'])
        ->name(AuthRoutePath::PASSWORD_UPDATE);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name(AuthRoutePath::LOGOUT);
});
