<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking checker data...\n";

// Get some sample orders with checker names
$orders = DB::table('orders')
    ->whereNotNull('checkName')
    ->where('checkName', '!=', '')
    ->take(10)
    ->get(['id', 'orderId', 'checkName']);

echo "Found " . count($orders) . " orders with checker names:\n";
foreach ($orders as $order) {
    echo "Order ID: {$order->orderId}, Checker: {$order->checkName}\n";
}

// Check if any checker names contain ' / ' (multiple checkers)
echo "\nLooking for orders with multiple checkers (/ separated):\n";
$ordersWithMultipleCheckers = DB::table('orders')
    ->whereNotNull('checkName')
    ->where('checkName', 'like', '%/%')
    ->take(10)
    ->get(['id', 'orderId', 'checkName']);

if (count($ordersWithMultipleCheckers) > 0) {
    foreach ($ordersWithMultipleCheckers as $order) {
        echo "Order ID: {$order->orderId}, Checkers: {$order->checkName}\n";
    }
} else {
    echo "No orders found with / separated checkers.\n";
}
