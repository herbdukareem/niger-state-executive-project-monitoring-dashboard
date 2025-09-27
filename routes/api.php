<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\LgaController;
use App\Http\Controllers\Api\WardController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\ProjectUpdateController;
use App\Http\Controllers\ProjectAttachmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Dashboard API routes
Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

// Projects API routes
Route::apiResource('projects', ProjectController::class);

// Project Updates API routes
Route::apiResource('projects.updates', ProjectUpdateController::class)->except(['index', 'show']);
Route::get('/projects/{project}/updates', [ProjectUpdateController::class, 'index']);
Route::get('/projects/{project}/updates/{update}', [ProjectUpdateController::class, 'show']);

// Project Attachments API routes
Route::post('/projects/{project}/attachments', [ProjectAttachmentController::class, 'store']);
Route::delete('/attachments/{attachment}', [ProjectAttachmentController::class, 'destroy']);
Route::get('/attachments/{attachment}/download', [ProjectAttachmentController::class, 'download'])->name('attachments.download');

// LGAs API routes
Route::apiResource('lgas', LgaController::class)->only(['index', 'show']);
Route::get('/lgas/{lga}/wards', [LgaController::class, 'wards']);

// Wards API routes
Route::apiResource('wards', WardController::class)->only(['index', 'show']);
