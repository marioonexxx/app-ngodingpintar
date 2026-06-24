<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/admin/products', 'POST', [
    'name' => 'API Test 2',
    'price' => 100,
    'status' => 'draft',
    'product_category_id' => 1
]);

try {
    $controller = app(App\Http\Controllers\Admin\ProductController::class);
    $response = $controller->store($request);
    dump($response->headers->all());
} catch (\Exception $e) {
    dump($e->getMessage());
    if(method_exists($e, 'errors')) {
        dump($e->errors());
    }
}
