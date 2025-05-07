<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Auth\AuthController;
use Laravel\Passport\Http\Controllers\AccessTokenController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-mfa', [AuthController::class, 'verifyMfa']);

Route::post('/oauth/token', [AccessTokenController::class, 'issueToken']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('customers', CustomerController::class);
});
