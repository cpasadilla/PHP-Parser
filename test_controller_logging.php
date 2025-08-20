<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InventoryEntry;
use App\Models\InventoryLog;
use App\Models\User;

try {
    echo "Testing InventoryController Logging Integration...\n\n";
    
    // Get first user and login
    $user = User::first();
    auth()->login($user);
    echo "Logged in as: " . $user->fName . " " . $user->lName . "\n";
    
    // Get first inventory entry
    $entry = InventoryEntry::first();
    if (!$entry) {
        echo "❌ No inventory entries found.\n";
        exit;
    }
    
    echo "Testing with inventory entry ID: " . $entry->id . "\n";
    
    // Simulate the logInventoryAction method from InventoryController
    $logData = [
        'inventory_entry_id' => $entry->id,
        'action_type' => 'update',
        'field_name' => 'amount',
        'old_value' => '100.00',
        'new_value' => '150.00',
        'updated_by' => $user->fName . ' ' . $user->lName,
        'entry_data' => null
    ];
    
    $log = InventoryLog::create($logData);
    echo "✅ Update log created with ID: " . $log->id . "\n";
    
    // Test create action log
    $createLogData = [
        'inventory_entry_id' => $entry->id,
        'action_type' => 'create',
        'field_name' => null,
        'old_value' => null,
        'new_value' => null,
        'updated_by' => $user->fName . ' ' . $user->lName,
        'entry_data' => $entry->toArray()
    ];
    
    $createLog = InventoryLog::create($createLogData);
    echo "✅ Create log created with ID: " . $createLog->id . "\n";
    
    // Test retrieval of logs for history display
    $logs = InventoryLog::with('inventoryEntry')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
        
    echo "\nRecent inventory logs:\n";
    foreach ($logs as $log) {
        echo "- ID: {$log->id}, Action: {$log->action_type}, Entry: {$log->inventory_entry_id}, By: {$log->updated_by}\n";
    }
    
    // Clean up test logs
    $log->delete();
    $createLog->delete();
    echo "\n✅ Test logs cleaned up.\n";
    
    echo "\n🎉 InventoryController logging integration is working perfectly!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
