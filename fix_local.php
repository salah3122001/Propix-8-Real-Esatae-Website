<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

echo "Starting image fix...\n";

// 1. Define the source image path
$sourcePath = base_path('villa/modern-luxury-house-with-swimming-pool.jpg');

if (!file_exists($sourcePath)) {
    die('Source image not found at: ' . $sourcePath . "\n");
}

$disk = Storage::disk('public');
$sourceContent = file_get_contents($sourcePath);

// 2. Define standard targets
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

// 3. Process standard targets
foreach ($targets as $key => $config) {
    echo "Processing {$key}...\n";
    $disk->deleteDirectory($config['folder']);
    $disk->makeDirectory($config['folder']);

    $models = $config['model']::all();
    $count = 0;

    foreach ($models as $model) {
        $updates = [];
        foreach ($config['fields'] as $field) {
            $filename = $config['folder'] . '/' . Str::random(40) . '.jpg';
            $disk->put($filename, $sourceContent);
            $updates[$field] = $filename;
        }
        $model->update($updates);
        $count++;
    }
    echo "Updated {$count} records for {$key}.\n";
}

// 4. Process Units (UnitMedia)
echo "Processing Units (UnitMedia)...\n";
$disk->deleteDirectory('units/media');
$disk->makeDirectory('units/media');

// Clear all existing media records
\App\Models\UnitMedia::truncate();

$units = \App\Models\Unit::all();
$unitCount = 0;

foreach ($units as $unit) {
    $filename = 'units/media/' . Str::random(40) . '.jpg';
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
echo "Created media for {$unitCount} units.\n";

echo "All done!\n";
