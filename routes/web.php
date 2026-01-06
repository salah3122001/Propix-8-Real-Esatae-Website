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

Route::get('/fix-images', function () {
    // 1. Define the source image path
    $sourcePath = base_path('villa/modern-luxury-house-with-swimming-pool.jpg');

    if (!file_exists($sourcePath)) {
        return 'Source image not found at: ' . $sourcePath;
    }

    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    $sourceContent = file_get_contents($sourcePath);

    // 2. Define models and their image fields (excluding Units handled separately)
    $targets = [
        'unit_types' => [
            'model' => \App\Models\UnitType::class,
            'fields' => ['icon'],
            'folder' => 'unit-types'
        ],
        'amenities' => [
            'model' => \App\Models\Amenity::class,
            'fields' => ['icon'],
            'folder' => 'amenities'
        ],
        'developers' => [
            'model' => \App\Models\Developer::class,
            'fields' => ['logo'],
            'folder' => 'developers'
        ],
        'users' => [
            'model' => \App\Models\User::class,
            'fields' => ['avatar', 'id_photo'],
            'folder' => 'users'
        ],
    ];

    $log = [];

    // 3. Process standard targets
    foreach ($targets as $key => $config) {
        $disk->deleteDirectory($config['folder']);
        $disk->makeDirectory($config['folder']);

        $models = $config['model']::all();
        $count = 0;

        foreach ($models as $model) {
            $updates = [];
            foreach ($config['fields'] as $field) {
                $filename = $config['folder'] . '/' . \Illuminate\Support\Str::random(40) . '.jpg';
                $disk->put($filename, $sourceContent);
                $updates[$field] = $filename;
            }
            $model->update($updates);
            $count++;
        }
        $log[] = "Updated {$count} records for {$key}.";
    }

    // 4. Process Units (UnitMedia)
    $log[] = "Processing Units...";
    $disk->deleteDirectory('units/media');
    $disk->makeDirectory('units/media');

    // Clear all existing media records
    \App\Models\UnitMedia::truncate();

    $units = \App\Models\Unit::all();
    $unitCount = 0;

    foreach ($units as $unit) {
        $filename = 'units/media/' . \Illuminate\Support\Str::random(40) . '.jpg';
        $disk->put($filename, $sourceContent);

        \App\Models\UnitMedia::create([
            'unit_id' => $unit->id,
            'type' => 'image',
            'url' => $filename,
            'order' => 1,
            'processing_status' => 'completed',
        ]);
        $unitCount++;
    }
    $log[] = "Created media for {$unitCount} units.";

    return implode('<br>', $log);
});

// Route::get('/login', function () {
//     return response()->json([
//         'message' => 'Login via POST /api/login',
//         'info' => 'This page is a placeholder for the frontend login page. If you arrived here after email verification, your email has been successfully verified.'
//     ]);
// })->name('login');

require __DIR__ . '/auth.php';
