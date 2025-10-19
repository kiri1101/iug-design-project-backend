<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PasswordResetController;

Route::middleware(['auth:sanctum'])->get('/user', fn(Request $request) => $request->user());

Route::prefix('/auth')->group(function () {
    Route::post('sign-in', SignInController::class);
    Route::post('sign-up', RegistrationController::class);
    Route::controller(PasswordResetController::class)->group(function () {
        Route::post('password-reset/send-link', 'init');
        Route::post('password-reset/send-link/update', 'updatingPassword');
    });
});
