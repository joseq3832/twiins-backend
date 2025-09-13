<?php

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

// API Version 1
Route::prefix('v1')->group(function () {
    // EMPLOYEES CRUD
    Route::apiResource('employees', EmployeeController::class);

    // IMMEDIATE FAMILY CRUD
    Route::apiResource('immediate-family', ImmediateFamilyController::class);

    // Get immediate family by employee
    Route::get('employees/{employeeId}/immediate-family', [ImmediateFamilyController::class, 'getByEmployee']);
});
