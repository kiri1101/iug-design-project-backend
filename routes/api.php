<?php

use App\Http\Controllers\LeaveController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SignOutController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TestController;

Route::middleware(['auth:sanctum'])->get('/user', fn(Request $request) => $request->user());

Route::prefix('/auth')->group(function () {
    Route::post('sign-in', SignInController::class);
    Route::post('sign-up', RegistrationController::class);
    Route::controller(PasswordResetController::class)->group(function () {
        Route::post('password-reset/send-link', 'init');
        Route::post('password-reset/send-link/update', 'updatingPassword');
    });

    Route::middleware(['auth:sanctum'])->post('sign-out', SignOutController::class);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/users')->controller(UserController::class)->group(function () {
        Route::get('list', 'list');
        Route::post('create', 'store');
        Route::post('{user:uuid}/update', 'update');
        Route::post('{user:uuid}/delete', 'delete');
    });

    Route::prefix('/leave')->controller(LeaveController::class)->group(function () {
        Route::get('list', 'list');
        Route::post('store', 'store');
        Route::post('{leave:uuid}/update', 'updateLeave');
        Route::post('{leave:uuid}/delete', 'deleteLeave');
        Route::post('{leave:uuid}/status/update', 'updateLeaveStatus');
    });

    Route::prefix('/count')->controller(StatsController::class)->group(function () {
        Route::get('users', 'usersCount');
    });

    Route::get('/notifications', NotificationController::class);
});

Route::get('/testing/notification', [
    TestController::class,
    'sendMessage'
]);
