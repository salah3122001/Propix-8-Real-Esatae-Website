<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

// Maintenance Routes
Route::get('/debug-storage', function () {
    $link = public_path('storage');
    $target = storage_path('app/public');

    $tags = [
        'timestamp' => time(), // Cache buster
        'active_app_url' => config('app.url'),
        'active_filesystem_url' => config('filesystems.disks.public.url'),
        'is_link' => is_link($link) ? 'YES' : 'NO',
        'exists' => file_exists($link) ? 'YES' : 'NO',
        'link_path' => $link,
        'target_path' => $target,
        'actual_link_target' => is_link($link) ? readlink($link) : 'N/A',
        'is_target_dir' => is_dir($target) ? 'YES' : 'NO',
    ];

    // Check specific file availability
    $relativePath = 'unit-types/01KE44WM384SQWTM70PF8B9PVB.jpeg';
    $targetFile = $target . '/' . $relativePath;

    $tags['file_check'] = [
        'path' => $targetFile,
        'exists' => file_exists($targetFile) ? 'YES' : 'NO',
        'generated_url' => \Illuminate\Support\Facades\Storage::disk('public')->url($relativePath),
        'perms' => file_exists($targetFile) ? substr(sprintf('%o', fileperms($targetFile)), -4) : 'N/A',
        'dir_exists' => is_dir(dirname($targetFile)) ? 'YES' : 'NO',
        'dir_perms' => is_dir(dirname($targetFile)) ? substr(sprintf('%o', fileperms(dirname($targetFile))), -4) : 'N/A',
    ];

    // List files in unit-types if dir exists
    if (is_dir($target . '/unit-types')) {
        $tags['dir_content'] = array_slice(scandir($target . '/unit-types'), 0, 10);
    }

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
