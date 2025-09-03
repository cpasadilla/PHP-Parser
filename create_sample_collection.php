<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create sample collection
App\Models\DailyCashCollection::create([
    'type' => 'trading',
    'collection_date' => '2025-09-03',
    'title' => 'Sample Collection 1',
    'description' => 'This is a test collection',
    'amount' => 1500.00,
    'notes' => 'Test notes'
]);

echo "Sample collection created successfully!\n";
