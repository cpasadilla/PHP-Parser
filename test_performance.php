<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\voyage;
use App\Models\order;
use App\Models\Ship;

// Test the performance of voyage ID 24
$voyageId = 24;

echo "=== Performance Analysis for Voyage ID {$voyageId} ===\n\n";

$startTime = microtime(true);

// Get the specific voyage by ID
$voyage = voyage::findOrFail($voyageId);
echo "1. Found voyage: {$voyage->v_num} for ship {$voyage->ship}\n";

// Determine the voyage key for orders lookup
$ship = Ship::where('ship_number', $voyage->ship)->first();
if ($ship && ($ship->ship_number == 'I' || $ship->ship_number == 'II')) {
    $voyageKey = $voyage->v_num . '-' . $voyage->inOut;
} else {
    $voyageKey = $voyage->v_num;
}
echo "2. Voyage key: {$voyageKey}\n";

// Get orders for this specific voyage and dock number
$queryStart = microtime(true);
$orders = order::where('shipNum', $voyage->ship)
    ->where('voyageNum', $voyageKey)
    ->where('dock_number', $voyage->dock_number ?? 0)
    ->with('parcels')
    ->orderBy('orderId', 'asc')
    ->get();
$queryTime = microtime(true) - $queryStart;

echo "3. Orders found: {$orders->count()}\n";
echo "4. Database query time: " . number_format($queryTime, 4) . " seconds\n";

// Test the expensive view operations
$viewStart = microtime(true);

// Simulate the expensive operations from the view
$uniqueOrderIds = $orders->pluck('orderId')->unique()->sort();
$uniqueContainers = $orders->pluck('containerNum')->unique()->sort();
$uniqueShippers = $orders->pluck('shipperName')->unique()->sort();
$uniqueConsignees = $orders->pluck('recName')->unique()->sort();

$viewTime = microtime(true) - $viewStart;

echo "5. View processing time: " . number_format($viewTime, 4) . " seconds\n";
echo "6. Unique order IDs: {$uniqueOrderIds->count()}\n";
echo "7. Unique containers: {$uniqueContainers->count()}\n";

$totalTime = microtime(true) - $startTime;
echo "\n=== Total Time: " . number_format($totalTime, 4) . " seconds ===\n";

if ($totalTime > 2) {
    echo "\n⚠️  PERFORMANCE ISSUE DETECTED!\n";
    echo "Recommendations:\n";
    echo "- Optimize database queries\n";
    echo "- Reduce view processing operations\n";
    echo "- Add database indexes\n";
    echo "- Implement caching\n";
}

?>
