<?php

use App\Models\Offer;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$requestId = $argv[1] ?? 9;
$offers = Offer::where('purchase_request_id', $requestId)->get();

echo "=== Offers for Request #{$requestId} ===\n";
foreach($offers as $o) {
    echo "ID: {$o->id} | {$o->vendor_name} | Price: {$o->price} {$o->currency}\n";
    echo "  - is_chosen: " . ($o->is_chosen ? 'YES' : 'NO') . "\n";
    echo "  - is_procurement_recommended: " . ($o->is_procurement_recommended ? 'YES' : 'NO') . "\n";
    echo "  - is_finance_recommended: " . ($o->is_finance_recommended ? 'YES' : 'NO') . "\n";
    echo "\n";
}

if ($offers->isEmpty()) {
    echo "No offers found.\n";
}
