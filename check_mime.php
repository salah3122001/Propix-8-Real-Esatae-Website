<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$media = \App\Models\UnitMedia::where('unit_id', 4)->get();

foreach ($media as $m) {
    $path = storage_path('app/public/' . $m->url);
    if (file_exists($path)) {
        echo "File: " . $m->url . "\n";
        echo "Mime: " . mime_content_type($path) . "\n";
    } else {
        echo "File not found: " . $m->url . "\n";
    }
}
