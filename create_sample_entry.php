<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->boot();

use App\Models\DailyCashCollectionEntry;

// Create a sample trading entry
$entry = DailyCashCollectionEntry::create([
    'type' => 'trading',
    'entry_date' => '2025-09-03',
    'ar' => 'AR001',
    'or' => 'OR001',
    'customer_name' => 'Test Customer One',
    'gravel_sand' => 5000.00,
    'chb' => 3000.00,
    'other_income_cement' => 2000.00,
    'other_income_df' => 1500.00,
    'others' => 500.00,
    'interest' => 100.00,
    'total' => 12100.00,
    'remark' => 'Test entry for trading'
]);

echo "Entry created with ID: " . $entry->id . "\n";

// Create another sample entry
$entry2 = DailyCashCollectionEntry::create([
    'type' => 'trading',
    'entry_date' => '2025-09-03',
    'ar' => 'AR002',
    'or' => 'OR002',
    'customer_name' => 'Test Customer Two',
    'gravel_sand' => 7500.00,
    'chb' => 4000.00,
    'other_income_cement' => 1800.00,
    'other_income_df' => 2200.00,
    'others' => 750.00,
    'interest' => 250.00,
    'total' => 16500.00,
    'remark' => 'Second test entry'
]);

echo "Entry created with ID: " . $entry2->id . "\n";
echo "Total entries: " . DailyCashCollectionEntry::count() . "\n";
