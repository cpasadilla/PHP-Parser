<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Order;
use App\Models\Parcel;
$o = Order::latest()->first();
if (!$o) { echo "No orders\n"; exit(0); }
echo "Order id:".$o->id." orderId:".$o->orderId."\n";
$parcels = Parcel::where('orderId', $o->id)->get();
foreach ($parcels as $p) {
    echo "parcel id:".$p->id." qty:".$p->quantity." measurements:".json_encode($p->measurements)." length:".($p->length ?? 'NULL')." unit:".($p->unit ?? 'NULL')."\n";
}
