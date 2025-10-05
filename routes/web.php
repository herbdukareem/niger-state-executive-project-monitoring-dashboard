<?php

use Illuminate\Support\Facades\Route;

// Home route (redirects to dashboard)
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

// Named routes for common paths (for Laravel redirects)
Route::get('/dashboard', function () {
    return view('app');
})->middleware('auth')->name('dashboard');

Route::get('/login', function () {
    return view('app');
})->name('login');

require __DIR__.'/projects.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Serve the Vue.js SPA for all other routes (must be last)
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
