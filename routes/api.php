<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\ImmediateFamilyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Health Check Endpoint
Route::get('/health', [HealthController::class, 'check'])
    ->middleware('throttle:60,1')
    ->name('health.check');

// Authentication Routes (no version prefix)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// API Version 1
Route::prefix('v1')->group(function () {
    // EMPLOYEES CRUD - Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('immediate-family', ImmediateFamilyController::class);
        Route::get('employees/{employeeId}/immediate-family', [ImmediateFamilyController::class, 'getByEmployee']);
    });
});
