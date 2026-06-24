<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = App\Models\Product::where('product_type', 'source_code')->where(function($q) {
    $q->whereNull('file_path')->orWhere('file_path', '');
})->count();

echo "Empty file paths: " . $count . "\n";

App\Models\Product::where('product_type', 'source_code')->where(function($q) {
    $q->whereNull('file_path')->orWhere('file_path', '');
})->update(['file_path' => 'https://drive.google.com/file/d/dummy-link-123/view?usp=sharing']);

echo "Updated\n";
