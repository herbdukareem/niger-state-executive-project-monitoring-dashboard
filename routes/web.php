<?php

use Illuminate\Support\Facades\Route;

// Named routes for common paths (for Laravel redirects)
Route::get('/dashboard', function () {
    return view('app');
})->name('dashboard');

Route::get('/login', function () {
    return view('app');
})->name('login');

// Serve the Vue.js SPA for all other routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

require __DIR__.'/projects.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
