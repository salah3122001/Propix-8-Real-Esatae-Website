<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Maintenance Routes
// Maintenance Routes
Route::get('/debug-storage', function () {
    $link = public_path('storage');
    $target = storage_path('app/public');

    $tags = [
        'is_link' => is_link($link) ? 'YES' : 'NO',
        'exists' => file_exists($link) ? 'YES' : 'NO',
        'link_path' => $link,
        'target_path' => $target,
        'actual_link_target' => is_link($link) ? readlink($link) : 'N/A',
        'is_target_dir' => is_dir($target) ? 'YES' : 'NO',
    ];

    // Try to create if missing
    if (!file_exists($link)) {
        try {
            symlink($target, $link);
            $tags['action'] = 'Created Symlink';
        } catch (\Exception $e) {
            $tags['error'] = $e->getMessage();
        }
    }

    return $tags;
});

Route::get('/storage-link', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Storage link command executed.';
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('route:cache');
    \Illuminate\Support\Facades\Artisan::call('view:cache');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    return 'All caches cleared!';
});

require __DIR__ . '/auth.php';
