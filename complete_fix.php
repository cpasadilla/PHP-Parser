<?php
// Complete Laravel Fix Script
// Run this after fixing the providers: https://sfxssli.shop/complete_fix.php

echo "<h2>SFX-1 Complete Laravel Fix</h2>";
echo "Running comprehensive fixes...<br><br>";

// 1. Clear all cached files that might be causing issues
echo "<h3>1. Clearing All Cache Files:</h3>";

$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/routes-v7.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php'
];

foreach ($cacheFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            echo "✅ Deleted: {$file}<br>";
        } else {
            echo "❌ Failed to delete: {$file}<br>";
        }
    } else {
        echo "✅ Not found (OK): {$file}<br>";
    }
}

// 2. Test Laravel Bootstrap again
echo "<h3>2. Testing Laravel Bootstrap:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Laravel bootstrap successful<br>";
    
    // Test basic services
    try {
        $config = $app['config'];
        echo "✅ Config service available<br>";
    } catch (Exception $e) {
        echo "❌ Config service failed: " . $e->getMessage() . "<br>";
    }
    
    try {
        $db = $app['db'];
        echo "✅ Database service available<br>";
    } catch (Exception $e) {
        echo "❌ Database service failed: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}

// 3. Check .env configuration
echo "<h3>3. Environment Configuration Check:</h3>";
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    
    if (strpos($envContent, 'your_database_name') !== false) {
        echo "⚠️ Database credentials still contain placeholders<br>";
        echo "Please update your .env file with actual database credentials<br>";
    } else {
        echo "✅ .env file appears to be configured<br>";
    }
    
    if (strpos($envContent, 'APP_KEY=') !== false && strpos($envContent, 'base64:') !== false) {
        echo "✅ APP_KEY is set<br>";
    } else {
        echo "❌ APP_KEY is missing or invalid<br>";
    }
} else {
    echo "❌ .env file not found<br>";
}

// 4. Check if we can access routes
echo "<h3>4. Testing Route Access:</h3>";
try {
    if (function_exists('route')) {
        echo "✅ Route helper function available<br>";
    } else {
        echo "❌ Route helper function not available<br>";
    }
} catch (Exception $e) {
    echo "❌ Route testing failed: " . $e->getMessage() . "<br>";
}

// 5. Check middleware
echo "<h3>5. Testing Middleware:</h3>";
try {
    $middlewareFile = __DIR__ . '/app/Http/Middleware/CheckPagePermission.php';
    if (file_exists($middlewareFile)) {
        echo "✅ CheckPagePermission middleware exists<br>";
    } else {
        echo "❌ CheckPagePermission middleware missing<br>";
    }
} catch (Exception $e) {
    echo "❌ Middleware check failed: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Next Steps:</h3>";
echo "1. If Laravel bootstrap is now successful, proceed to database setup<br>";
echo "2. Update .env file with actual database credentials<br>";
echo "3. Run: php artisan migrate --force<br>";
echo "4. Test your website pages<br>";
echo "5. If still having issues, check the error logs<br>";

echo "<br><strong>Delete this file after running the fixes!</strong>";
?>
