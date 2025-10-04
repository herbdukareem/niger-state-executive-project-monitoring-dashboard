<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\LgaController;
use App\Http\Controllers\Api\WardController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProjectUpdateController;
use App\Http\Controllers\Api\ProjectAttachmentController;
use App\Http\Controllers\Api\WorkPlanActivityController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication routes (public)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication routes (protected)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);

    // Dashboard API routes
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Projects API routes
    Route::apiResource('projects', ProjectController::class);

    // Project Updates API routes
    Route::apiResource('projects.updates', ProjectUpdateController::class)->except(['index', 'show']);
    Route::get('/projects/{project}/updates', [ProjectUpdateController::class, 'index']);
    Route::get('/projects/{project}/updates/{update}', [ProjectUpdateController::class, 'show']);

    // Project Attachments API routes
    Route::get('/projects/{project}/attachments', [ProjectAttachmentController::class, 'index']);
    Route::post('/projects/{project}/attachments', [ProjectAttachmentController::class, 'store']);
    Route::delete('/attachments/{attachment}', [ProjectAttachmentController::class, 'destroy']);
    Route::get('/attachments/{attachment}/download', [ProjectAttachmentController::class, 'download'])->name('attachments.download');

    // Work Plan Activities API routes
    Route::get('/projects/{project}/work-plan-activities', [WorkPlanActivityController::class, 'index']);
    Route::post('/projects/{project}/work-plan-activities', [WorkPlanActivityController::class, 'store']);
    Route::put('/projects/{project}/work-plan-activities/{activity}', [WorkPlanActivityController::class, 'update']);
    Route::delete('/projects/{project}/work-plan-activities/{activity}', [WorkPlanActivityController::class, 'destroy']);

    // LGAs API routes
    Route::apiResource('lgas', LgaController::class)->only(['index', 'show']);
    Route::get('/lgas/{lga}/wards', [LgaController::class, 'wards']);

    // Wards API routes
    Route::apiResource('wards', WardController::class)->only(['index', 'show']);

    // Form data endpoints
    Route::get('/form-data/sectors', function () {
        return response()->json([
            'data' => [
                'Agriculture',
                'Education',
                'Health',
                'Infrastructure',
                'Water & Sanitation',
                'Energy',
                'Environment',
                'Social Protection',
                'Governance',
                'Economic Development',
                'Technology',
                'Other',
            ]
        ]);
    });

    Route::get('/form-data/project-managers', function () {
        return response()->json([
            'data' => \App\Models\User::whereHas('role', function ($query) {
                    $query->where('name', 'project_manager');
                })
                ->where('is_active', true)
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->get()
        ]);
    });
});

// User Management API routes (Protected by permissions)
Route::middleware(['auth:sanctum', 'role:governor,super_admin,admin'])->group(function () {
    // User management routes - only for super admins and admins
    Route::get('/users/roles', [UserController::class, 'getRoles']);
    Route::apiResource('users', UserController::class);
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
});

// Role and Permission Management API routes (Protected by permissions)
Route::middleware(['auth:sanctum', 'role:governor,super_admin,admin'])->group(function () {
    // Role management routes - only for governor, super admins, and admins
    Route::get('/roles/permissions', [RoleController::class, 'getPermissions']);
    Route::apiResource('roles', RoleController::class);
    Route::patch('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus']);
    Route::post('/roles/{role}/assign-permission', [RoleController::class, 'assignPermission']);
    Route::post('/roles/{role}/revoke-permission', [RoleController::class, 'revokePermission']);
    Route::post('/roles/{role}/sync-permissions', [RoleController::class, 'syncPermissions']);

    // Permission management routes - only for governor, super admins, and admins
    Route::get('/permissions/categories', [PermissionController::class, 'getCategories']);
    Route::get('/permissions/grouped', [PermissionController::class, 'getGroupedPermissions']);
    Route::apiResource('permissions', PermissionController::class);
    Route::patch('/permissions/{permission}/toggle-status', [PermissionController::class, 'toggleStatus']);
});

// Test route for debugging (public)
Route::get('/test', function () {
    return response()->json(['message' => 'API is working', 'timestamp' => now()]);
});
