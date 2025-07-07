<?php
// Deployment Fix Script
// Run this once: https://sfxssli.shop/fix_deployment.php

echo "<h2>SFX-1 Deployment Fix</h2>";
echo "Starting deployment fixes...<br><br>";

// 1. Check and create necessary directories
$directories = [
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/app/public',
    'bootstrap/cache'
];

echo "<h3>1. Creating/Checking Required Directories:</h3>";
foreach ($directories as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (!is_dir($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Created: {$dir}<br>";
        } else {
            echo "❌ Failed to create: {$dir}<br>";
        }
    } else {
        echo "✅ Exists: {$dir}<br>";
    }
}

// 2. Set proper permissions
echo "<h3>2. Setting Proper Permissions:</h3>";
$permissionDirs = ['storage', 'bootstrap/cache'];
foreach ($permissionDirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (is_dir($fullPath)) {
        // Recursively set permissions
        chmod($fullPath, 0755);
        echo "✅ Set permissions for: {$dir}<br>";
    }
}

// 3. Create empty log file if it doesn't exist
$logFile = __DIR__ . '/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    file_put_contents($logFile, '');
    chmod($logFile, 0644);
    echo "✅ Created empty log file<br>";
}

// 4. Check .env file
echo "<h3>3. Environment Configuration:</h3>";
if (file_exists(__DIR__ . '/.env')) {
    echo "✅ .env file exists<br>";
} else {
    echo "❌ .env file missing - please rename env_fixed to .env<br>";
}

// 5. Test Laravel bootstrap
echo "<h3>4. Testing Laravel Bootstrap:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Laravel bootstrap successful<br>";
    
    // Test configuration
    $config = $app->make('config');
    echo "✅ Configuration loaded<br>";
    
    // Test database (if configured)
    if (env('DB_HOST') && env('DB_HOST') !== 'localhost') {
        try {
            $db = $app->make('db');
            $db->connection()->getPdo();
            echo "✅ Database connection successful<br>";
        } catch (Exception $e) {
            echo "⚠️ Database connection issue: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "⚠️ Database not configured yet<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}

// 6. Check if composer dependencies are installed
echo "<h3>5. Checking Composer Dependencies:</h3>";
if (is_dir(__DIR__ . '/vendor')) {
    echo "✅ Vendor directory exists<br>";
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        echo "✅ Composer autoload exists<br>";
    } else {
        echo "❌ Composer autoload missing - run composer install<br>";
    }
} else {
    echo "❌ Vendor directory missing - run composer install<br>";
}

// 7. Check route files
echo "<h3>6. Checking Route Files:</h3>";
$routeFiles = ['routes/web.php', 'routes/auth.php'];
foreach ($routeFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ {$file} exists<br>";
    } else {
        echo "❌ {$file} missing<br>";
    }
}

// 8. Check critical view files
echo "<h3>7. Checking Critical View Files:</h3>";
$viewFiles = [
    'resources/views/masterlist/list.blade.php',
    'resources/views/masterlist/bl_list.blade.php',
    'resources/views/masterlist/container-details.blade.php',
    'resources/views/masterlist/soa.blade.php'
];

foreach ($viewFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ {$file} exists<br>";
    } else {
        echo "❌ {$file} missing<br>";
    }
}

echo "<br><h3>Next Steps:</h3>";
echo "1. If .env file is missing, rename 'env_fixed' to '.env'<br>";
echo "2. Update database credentials in .env file<br>";
echo "3. Run: php artisan migrate --force<br>";
echo "4. Run: php artisan config:clear<br>";
echo "5. Run: php artisan route:clear<br>";
echo "6. Run: php artisan view:clear<br>";
echo "7. Test your website<br>";

echo "<br><strong>Delete this file after running the fixes!</strong>";
?>
