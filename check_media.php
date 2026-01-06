<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$media = \App\Models\UnitMedia::where('unit_id', 4)->get();

if ($media->isEmpty()) {
    echo "No media found for Unit 4.\n";
} else {
    foreach ($media as $item) {
        echo "ID: " . $item->id . "\n";
        echo "URL: " . $item->url . "\n";
        echo "Type: " . $item->type . "\n";
        echo "Full Path: " . \Illuminate\Support\Facades\Storage::disk('public')->path($item->url) . "\n";
        echo "Exists: " . (\Illuminate\Support\Facades\Storage::disk('public')->exists($item->url) ? 'YES' : 'NO') . "\n";
        echo "-----------------------------------\n";
    }
}
