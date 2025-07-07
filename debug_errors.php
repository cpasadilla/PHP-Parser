<?php
// Debug script to check Laravel errors
// Visit: https://sfxssli.shop/debug_errors.php

echo "<h2>Laravel Debug Information</h2>";

// Check if Laravel can bootstrap
try {
    $appPath = __DIR__;
    require_once $appPath . '/vendor/autoload.php';
    $app = require_once $appPath . '/bootstrap/app.php';
    
    echo "✅ Laravel bootstrap successful<br>";
    
    // Check database connection
    try {
        $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
        $request = Illuminate\Http\Request::createFromGlobals();
        $app->instance('request', $request);
        
        // Test database connection
        $db = $app->make('db');
        $db->connection()->getPdo();
        echo "✅ Database connection successful<br>";
        
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
    echo "Error details: " . $e->getFile() . " on line " . $e->getLine() . "<br>";
}

// Check environment
echo "<h3>Environment Information:</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Directory: " . __DIR__ . "<br>";
echo "App URL: " . (isset($_ENV['APP_URL']) ? $_ENV['APP_URL'] : 'Not set') . "<br>";

// Check if .env file exists
if (file_exists(__DIR__ . '/.env')) {
    echo "✅ .env file exists<br>";
} else {
    echo "❌ .env file missing<br>";
}

// Check if key directories exist
$dirs = ['storage', 'bootstrap/cache', 'vendor'];
foreach ($dirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "✅ {$dir} directory exists<br>";
    } else {
        echo "❌ {$dir} directory missing<br>";
    }
}

// Check permissions
$checkDirs = ['storage', 'bootstrap/cache'];
foreach ($checkDirs as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "Directory {$dir} permissions: {$perms}<br>";
        if (is_writable($path)) {
            echo "✅ {$dir} is writable<br>";
        } else {
            echo "❌ {$dir} is not writable<br>";
        }
    }
}

// Check latest log entries
$logPath = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    echo "<h3>Latest Log Entries:</h3>";
    $logs = file($logPath);
    $recentLogs = array_slice($logs, -20); // Last 20 lines
    echo "<pre>" . htmlspecialchars(implode('', $recentLogs)) . "</pre>";
} else {
    echo "❌ Log file not found at: {$logPath}<br>";
}

echo "<br><strong>Delete this file after debugging!</strong>";
?>
