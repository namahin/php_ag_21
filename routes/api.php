<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\TokenVerificationMiddleware;
use Illuminate\Support\Facades\Route;


// Authentication routes
Route::post('/user-registration', [UserController::class, 'UserRegistration']);
Route::post('/user-login', [UserController::class, 'UserLogin']);
Route::post('/send-otp', [UserController::class, 'SendOTPCode']);
Route::post('/verify-otp', [UserController::class, 'VerifyOTP']);
Route::post('/reset-password', [UserController::class, 'ResetPassword'])->middleware([TokenVerificationMiddleware::class]);


// Todo routes with TokenVerificationMiddleware
Route::middleware('token.verify')->group(function () {
    Route::get('/todo', [TodoController::class, 'index']);
    Route::post('/todo/create', [TodoController::class, 'store']);
    Route::get('/todo/read/{id}', [TodoController::class, 'show']);
    Route::put('/todo/update/{id}', [TodoController::class, 'update']);
    Route::delete('/todo/delete/{id}', [TodoController::class, 'destroy']);
});
