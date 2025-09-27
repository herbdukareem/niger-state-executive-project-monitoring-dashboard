<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\LgaController;
use App\Http\Controllers\Api\WardController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Dashboard API routes
Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

// Projects API routes
Route::apiResource('projects', ProjectController::class);

// LGAs API routes
Route::apiResource('lgas', LgaController::class)->only(['index', 'show']);
Route::get('/lgas/{lga}/wards', [LgaController::class, 'wards']);

// Wards API routes
Route::apiResource('wards', WardController::class)->only(['index', 'show']);
