<?php

use App\Http\Controllers\ProjectAttachmentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectUpdateController;
use Illuminate\Support\Facades\Route;

// Project routes
Route::resource('projects', ProjectController::class);

// Project update routes
Route::prefix('projects/{project}')->group(function () {
    Route::resource('updates', ProjectUpdateController::class)->except(['index']);
    Route::get('updates', [ProjectUpdateController::class, 'index'])->name('projects.updates.index');

    // Update workflow routes
    Route::post('updates/{update}/submit', [ProjectUpdateController::class, 'submit'])->name('projects.updates.submit');
    Route::post('updates/{update}/approve', [ProjectUpdateController::class, 'approve'])->name('projects.updates.approve');
    Route::post('updates/{update}/reject', [ProjectUpdateController::class, 'reject'])->name('projects.updates.reject');
});

// File attachment routes
Route::prefix('projects/{project}')->group(function () {
    Route::post('attachments', [ProjectAttachmentController::class, 'store'])->name('projects.attachments.store');
});

Route::get('attachments/{attachment}/download', [ProjectAttachmentController::class, 'download'])->name('attachments.download');
Route::put('attachments/{attachment}', [ProjectAttachmentController::class, 'update'])->name('attachments.update');
Route::delete('attachments/{attachment}', [ProjectAttachmentController::class, 'destroy'])->name('attachments.destroy');
