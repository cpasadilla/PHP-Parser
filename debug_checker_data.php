<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking checker data structure...\n";

// Get some sample orders with checker names
$orders = DB::table('orders')
    ->whereNotNull('checkName')
    ->where('checkName', '!=', '')
    ->take(10)
    ->get(['id', 'orderId', 'checkName']);

echo "Sample orders with checker data:\n";
foreach ($orders as $order) {
    echo "Order ID: {$order->orderId}, Checker: '{$order->checkName}'\n";
}

// Check if any orders have multiple checkers
$multipleCheckers = DB::table('orders')
    ->whereNotNull('checkName')
    ->where('checkName', 'like', '%/%')
    ->take(10)
    ->get(['id', 'orderId', 'checkName']);

echo "\nOrders with multiple checkers:\n";
if (count($multipleCheckers) > 0) {
    foreach ($multipleCheckers as $order) {
        echo "Order ID: {$order->orderId}, Checkers: '{$order->checkName}'\n";
    }
} else {
    echo "No orders found with multiple checkers (using / separator).\n";
}

// Check available checkers in the checkers table
$checkers = DB::table('checkers')
    ->orderBy('name')
    ->get(['name', 'location']);

echo "\nAvailable checkers in database:\n";
foreach ($checkers as $checker) {
    echo "Checker: {$checker->name} (Location: {$checker->location})\n";
}
