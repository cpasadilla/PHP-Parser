<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\PriceList;
use App\Models\Parcel;

echo "=== Categories in PriceList table ===\n";
$categories = PriceList::select('item_code', 'category')
    ->whereNotNull('category')
    ->where('category', '!=', '')
    ->take(10)
    ->get();

foreach($categories as $item) {
    echo "Item Code: {$item->item_code} -> Category: {$item->category}\n";
}

echo "\n=== Sample Parcels with ItemId ===\n";
$parcels = Parcel::select('id', 'itemId', 'itemName', 'desc')
    ->whereNotNull('itemId')
    ->where('itemId', '!=', '')
    ->take(10)
    ->get();

foreach($parcels as $parcel) {
    echo "Parcel ID: {$parcel->id} -> ItemId: {$parcel->itemId} -> ItemName: {$parcel->itemName}\n";
}

echo "\n=== Checking if itemIds match between parcels and pricelists ===\n";
$parcelItemIds = Parcel::whereNotNull('itemId')->where('itemId', '!=', '')->pluck('itemId')->unique()->take(5);
foreach($parcelItemIds as $itemId) {
    $pricelistItem = PriceList::where('item_code', $itemId)->first();
    if($pricelistItem) {
        echo "Match found: ItemId {$itemId} -> Category: {$pricelistItem->category}\n";
    } else {
        echo "No match: ItemId {$itemId} not found in pricelists\n";
    }
}

?>