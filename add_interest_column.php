<?php
// This script adds the interest_start_date column to orders table if it doesn't exist

// Load Laravel environment
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Check if the column already exists
if (!Schema::hasColumn('orders', 'interest_start_date')) {
    try {
        // Add the column
        DB::statement('ALTER TABLE orders ADD COLUMN interest_start_date TIMESTAMP NULL AFTER originalFreight');
        echo "Column interest_start_date added successfully to the orders table.\n";
    } catch (Exception $e) {
        echo "Error adding column: " . $e->getMessage() . "\n";
    }
} else {
    echo "Column interest_start_date already exists in orders table.\n";
}
