<?php
// Test storage functionality
// Visit: https://sfxssli.shop/test_storage.php

// Test if storage directory exists
$storagePath = __DIR__ . '/storage';
$storageAppPath = __DIR__ . '/storage/app/public';

echo "<h2>Storage Test Results:</h2>";

if (is_dir($storagePath)) {
    echo "✅ Storage directory exists<br>";
} else {
    echo "❌ Storage directory missing<br>";
}

if (is_dir($storageAppPath)) {
    echo "✅ Storage app/public directory exists<br>";
} else {
    echo "❌ Storage app/public directory missing<br>";
}

// Test write permissions
$testFile = $storagePath . '/test.txt';
if (file_put_contents($testFile, 'Test content')) {
    echo "✅ Storage directory is writable<br>";
    unlink($testFile); // Clean up
} else {
    echo "❌ Storage directory is not writable<br>";
}

// Test Laravel storage facade
try {
    $appPath = __DIR__;
    require_once $appPath . '/vendor/autoload.php';
    $app = require_once $appPath . '/bootstrap/app.php';
    
    echo "✅ Laravel bootstrap successful<br>";
    
    // Test storage URL
    echo "Storage URL: " . asset('storage') . "<br>";
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Delete this file after testing!</strong>";
?>
