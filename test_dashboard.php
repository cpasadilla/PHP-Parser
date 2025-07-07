<?php

// Test dashboard route directly
echo "Testing dashboard route...\n";

// Check if we can access the route
try {
    $response = file_get_contents('https://sfxssli.shop/dashboard');
    echo "Dashboard route accessible\n";
} catch (Exception $e) {
    echo "Dashboard route failed: " . $e->getMessage() . "\n";
}

// Check Laravel logs for errors
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    echo "Checking Laravel logs...\n";
    $logs = file_get_contents($logFile);
    $recentLogs = substr($logs, -5000); // Get last 5000 characters
    echo "Recent log content:\n";
    echo $recentLogs;
} else {
    echo "No Laravel log file found\n";
}

?>
