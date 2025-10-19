<?php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SignInController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', fn(Request $request) => $request->user());

Route::prefix('/auth')->group(function () {
    Route::post('sign-in', SignInController::class);
    Route::post('sign-up', RegistrationController::class);
});
