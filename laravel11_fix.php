<?php
// Laravel 11 Service Container Fix
// Run this: https://sfxssli.shop/laravel11_fix.php

echo "<h2>Laravel 11 Service Container Fix</h2>";
echo "Attempting to fix Laravel 11 service container issues...<br><br>";

// 1. Force clear all cache and config
echo "<h3>1. Clearing All Cache Files:</h3>";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/routes.php',
    'bootstrap/cache/routes-v7.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/app.php'
];

foreach ($cacheFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        unlink($fullPath);
        echo "✅ Deleted: {$file}<br>";
    } else {
        echo "✅ Not found: {$file}<br>";
    }
}

// 2. Check if we can run artisan commands
echo "<h3>2. Testing Artisan Commands:</h3>";
try {
    // Try to run artisan config:clear programmatically
    $output = shell_exec('cd ' . __DIR__ . ' && php artisan config:clear 2>&1');
    if ($output !== null) {
        echo "✅ Artisan config:clear output: " . htmlspecialchars($output) . "<br>";
    }
    
    $output = shell_exec('cd ' . __DIR__ . ' && php artisan cache:clear 2>&1');
    if ($output !== null) {
        echo "✅ Artisan cache:clear output: " . htmlspecialchars($output) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Artisan commands failed: " . $e->getMessage() . "<br>";
}

// 3. Try to manually initialize Laravel
echo "<h3>3. Manual Laravel Initialization:</h3>";
try {
    // Load environment variables manually
    if (file_exists(__DIR__ . '/.env')) {
        $envContent = file_get_contents(__DIR__ . '/.env');
        $envLines = explode("\n", $envContent);
        
        foreach ($envLines as $line) {
            if (strpos($line, '=') !== false && !empty(trim($line)) && !str_starts_with(trim($line), '#')) {
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    // Remove quotes if present
                    if ((str_starts_with($value, '"') && str_ends_with($value, '"')) || 
                        (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                        $value = substr($value, 1, -1);
                    }
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
        echo "✅ Environment variables loaded manually<br>";
    }
    
    // Load autoloader
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Create application with explicit base path
    $app = \Illuminate\Foundation\Application::configure(
        basePath: __DIR__
    )
    ->withRouting(
        web: __DIR__ . '/routes/web.php',
        commands: __DIR__ . '/routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware) {
        //
    })
    ->withExceptions(function (\Illuminate\Foundation\Configuration\Exceptions $exceptions) {
        //
    })->create();
    
    echo "✅ Laravel application created<br>";
    
    // Try to boot the application
    $app->boot();
    echo "✅ Application booted<br>";
    
    // Test services
    $config = $app->make('config');
    echo "✅ Config service works<br>";
    
    $db = $app->make('db');
    echo "✅ Database service works<br>";
    
    // Test database connection
    $pdo = $db->connection()->getPdo();
    echo "✅ Database connection works<br>";
    
} catch (Exception $e) {
    echo "❌ Manual initialization failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// 4. Check composer autoload
echo "<h3>4. Composer Autoload Check:</h3>";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✅ Composer autoload exists<br>";
    
    // Check if composer.lock exists
    if (file_exists(__DIR__ . '/composer.lock')) {
        echo "✅ Composer lock file exists<br>";
    } else {
        echo "❌ Composer lock file missing - run composer install<br>";
    }
} else {
    echo "❌ Composer autoload missing<br>";
}

// 5. Generate autoload files
echo "<h3>5. Regenerating Autoload Files:</h3>";
try {
    $output = shell_exec('cd ' . __DIR__ . ' && composer dump-autoload 2>&1');
    if ($output !== null) {
        echo "✅ Composer dump-autoload output: " . htmlspecialchars($output) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Composer dump-autoload failed: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Final Actions Required:</h3>";
echo "1. If the manual initialization worked, your Laravel app should now function<br>";
echo "2. Test your website pages now<br>";
echo "3. If still having issues, run these commands in terminal:<br>";
echo "   - composer install --no-dev --optimize-autoloader<br>";
echo "   - php artisan config:clear<br>";
echo "   - php artisan cache:clear<br>";
echo "   - php artisan route:clear<br>";
echo "   - php artisan view:clear<br>";

echo "<br><strong>Delete this file after running!</strong>";
?>
