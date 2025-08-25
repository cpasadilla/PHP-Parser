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
    $len = is_numeric($p->length) ? floatval($p->length) : 0;
    $wid = is_numeric($p->width) ? floatval($p->width) : 0;
    $hei = is_numeric($p->height) ? floatval($p->height) : 0;
    $mul = is_numeric($p->multiplier) ? floatval($p->multiplier) : 0;
    $qty = is_numeric($p->quantity) ? floatval($p->quantity) : 0;
    $recomputedRate = 0;
    $recomputedTotal = 0;
    if ($len > 0 && $wid > 0 && $hei > 0 && $mul > 0) {
        $recomputedRate = $len * $wid * $hei * $mul;
        $recomputedTotal = $recomputedRate * $qty;
    }

    echo sprintf("parcel id:%d qty:%s storedPrice:%s storedTotal:%s recomputedRate:%s recomputedTotal:%s length:%s width:%s height:%s multiplier:%s measurements:%s unit:%s\n",
        $p->id,
        $p->quantity,
        $p->itemPrice,
        $p->total,
        number_format($recomputedRate,2),
        number_format($recomputedTotal,2),
        $p->length ?? 'NULL',
        $p->width ?? 'NULL',
        $p->height ?? 'NULL',
        $p->multiplier ?? 'NULL',
        json_encode($p->measurements),
        $p->unit ?? 'NULL'
    );
}
