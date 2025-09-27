<?php

use Illuminate\Support\Facades\Route;

// Serve the Vue.js SPA for all routes
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');

require __DIR__.'/projects.php';
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
