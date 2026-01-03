<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Maintenance Routes
Route::get('/storage-link', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Storage link created successfully!';
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return 'All caches cleared!';
});

require __DIR__ . '/auth.php';
