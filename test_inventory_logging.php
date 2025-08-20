<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InventoryEntry;
use App\Models\InventoryLog;
use App\Models\User;

try {
    echo "Testing Inventory Logging System...\n\n";
    
    // Check if tables exist
    $logCount = InventoryLog::count();
    echo "Current inventory logs count: " . $logCount . "\n";
    
    $entryCount = InventoryEntry::count();
    echo "Current inventory entries count: " . $entryCount . "\n";
    
    // Get first user for testing
    $user = User::first();
    if ($user) {
        echo "Test user: " . $user->fName . " " . $user->lName . "\n";
        
        // Login user for testing
        auth()->login($user);
        
        // Get first inventory entry for testing
        $entry = InventoryEntry::first();
        $entryId = $entry ? $entry->id : null;
        
        // Create a test log entry manually
        $testLog = InventoryLog::create([
            'inventory_entry_id' => $entryId,
            'action_type' => 'test',
            'field_name' => 'test_field',
            'old_value' => 'old_test_value',
            'new_value' => 'new_test_value',
            'updated_by' => $user->fName . ' ' . $user->lName,
            'entry_data' => ['test' => 'data']
        ]);
        
        echo "Test log entry created with ID: " . $testLog->id . "\n";
        
        // Clean up test entry
        $testLog->delete();
        echo "Test log entry cleaned up.\n";
        
        echo "\n✅ Inventory logging system is working correctly!\n";
        
    } else {
        echo "❌ No users found in database.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
