<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t = App\Models\Transaction::latest()->first();
$ti = $t->items->first();

$request = Illuminate\Http\Request::create('/member/downloads/' . $ti->id, 'GET');
$request->setUserResolver(function () use ($t) {
    return $t->user;
});

try {
    $controller = new App\Http\Controllers\Member\TransactionController();
    $response = $controller->download($request, $ti);
    echo "Response class: " . get_class($response) . "\n";
    if ($response instanceof Symfony\Component\HttpFoundation\StreamedResponse) {
        echo "Download headers: \n";
        print_r($response->headers->all());
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . " on line " . $e->getLine() . " in " . $e->getFile() . "\n";
}
