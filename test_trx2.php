<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t = App\Models\Transaction::with(['items.product', 'user'])->latest()->first();
echo json_encode($t->toArray(), JSON_PRETTY_PRINT);
