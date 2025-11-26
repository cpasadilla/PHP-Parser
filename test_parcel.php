<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\parcel;

$parcel = parcel::with('pricelist')->first();
if ($parcel) {
    echo "Parcel itemId: " . $parcel->itemId . "\n";
    echo "Pricelist: " . ($parcel->pricelist ? $parcel->pricelist->category : 'null') . "\n";
} else {
    echo "No parcels\n";
}
?>