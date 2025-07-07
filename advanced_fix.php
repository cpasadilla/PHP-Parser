<?php
// Advanced Laravel Service Container Fix
// Run this: https://sfxssli.shop/advanced_fix.php

echo "<h2>SFX-1 Advanced Laravel Service Container Fix</h2>";
echo "Diagnosing and fixing service container issues...<br><br>";

// 1. Check Laravel version and structure
echo "<h3>1. Laravel Version and Structure Check:</h3>";
if (file_exists(__DIR__ . '/composer.json')) {
    $composer = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
    $laravelVersion = $composer['require']['laravel/framework'] ?? 'Unknown';
    echo "Laravel Framework Version: {$laravelVersion}<br>";
}

// 2. Check if this is Laravel 11 structure
echo "<h3>2. Laravel 11 Structure Analysis:</h3>";
if (file_exists(__DIR__ . '/bootstrap/app.php')) {
    $appContent = file_get_contents(__DIR__ . '/bootstrap/app.php');
    if (strpos($appContent, 'Application::configure') !== false) {
        echo "✅ Laravel 11 structure detected<br>";
    } else {
        echo "❌ Laravel 11 structure not detected<br>";
    }
}

// 3. Manual service container fix
echo "<h3>3. Manual Service Container Bootstrap:</h3>";
try {
    // Load composer autoloader
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Create Laravel application instance
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    echo "✅ Application instance created<br>";
    
    // Check if app is an instance of Application
    if ($app instanceof \Illuminate\Foundation\Application) {
        echo "✅ Application is correct type<br>";
    } else {
        echo "❌ Application is wrong type: " . get_class($app) . "<br>";
    }
    
    // Try to boot the application
    if (method_exists($app, 'boot')) {
        try {
            $app->boot();
            echo "✅ Application booted successfully<br>";
        } catch (Exception $e) {
            echo "❌ Application boot failed: " . $e->getMessage() . "<br>";
        }
    }
    
    // Try to access services directly
    echo "<h4>Service Container Contents:</h4>";
    if (method_exists($app, 'getBindings')) {
        $bindings = $app->getBindings();
        echo "Registered bindings: " . count($bindings) . "<br>";
        
        // Check for specific services
        $expectedServices = ['config', 'db', 'cache', 'view', 'auth'];
        foreach ($expectedServices as $service) {
            if (isset($bindings[$service])) {
                echo "✅ {$service} service registered<br>";
            } else {
                echo "❌ {$service} service missing<br>";
            }
        }
    }
    
    // Alternative way to check services
    echo "<h4>Direct Service Check:</h4>";
    $services = ['config', 'db', 'cache', 'view', 'auth'];
    foreach ($services as $service) {
        try {
            if ($app->bound($service)) {
                echo "✅ {$service} bound<br>";
                // Try to resolve it
                $instance = $app->make($service);
                echo "✅ {$service} resolved successfully<br>";
            } else {
                echo "❌ {$service} not bound<br>";
            }
        } catch (Exception $e) {
            echo "❌ {$service} failed: " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Critical error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "Trace: " . $e->getTraceAsString() . "<br>";
}

// 4. Check configuration files
echo "<h3>4. Configuration Files Check:</h3>";
$configFiles = ['app.php', 'database.php', 'cache.php', 'session.php'];
foreach ($configFiles as $file) {
    $path = __DIR__ . '/config/' . $file;
    if (file_exists($path)) {
        echo "✅ {$file} exists<br>";
    } else {
        echo "❌ {$file} missing<br>";
    }
}

// 5. Check if .env is being loaded
echo "<h3>5. Environment Variables Check:</h3>";
if (function_exists('env')) {
    echo "✅ env() function available<br>";
    
    $envVars = ['APP_NAME', 'APP_ENV', 'APP_KEY', 'DB_CONNECTION'];
    foreach ($envVars as $var) {
        $value = env($var);
        if ($value !== null) {
            echo "✅ {$var} = " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "<br>";
        } else {
            echo "❌ {$var} not set<br>";
        }
    }
} else {
    echo "❌ env() function not available<br>";
}

echo "<br><h3>Recommended Actions:</h3>";
echo "1. This appears to be a Laravel 11 service container issue<br>";
echo "2. Try running: php artisan config:clear<br>";
echo "3. Try running: php artisan cache:clear<br>";
echo "4. Check if your .env file is correctly formatted<br>";
echo "5. Consider running: composer dump-autoload<br>";

echo "<br><strong>Delete this file after diagnosis!</strong>";
?>
