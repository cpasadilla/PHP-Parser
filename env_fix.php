<?php
// Environment Loading Fix for Laravel 11
// Run this: https://sfxssli.shop/env_fix.php

echo "<h2>SFX-1 Environment Loading Fix</h2>";
echo "Fixing environment variable loading issues...<br><br>";

// 1. Check .env file
echo "<h3>1. Environment File Check:</h3>";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "✅ .env file exists<br>";
    $envContent = file_get_contents($envPath);
    echo "✅ .env file size: " . strlen($envContent) . " bytes<br>";
    
    // Check if it contains expected variables
    if (strpos($envContent, 'APP_NAME=') !== false) {
        echo "✅ APP_NAME found in .env<br>";
    } else {
        echo "❌ APP_NAME not found in .env<br>";
    }
    
    if (strpos($envContent, 'APP_KEY=') !== false) {
        echo "✅ APP_KEY found in .env<br>";
    } else {
        echo "❌ APP_KEY not found in .env<br>";
    }
} else {
    echo "❌ .env file not found<br>";
}

// 2. Manually load environment variables
echo "<h3>2. Manual Environment Loading:</h3>";
try {
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        $envLines = explode("\n", $envContent);
        
        $loadedVars = 0;
        foreach ($envLines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue; // Skip empty lines and comments
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }
                
                // Set environment variable
                $_ENV[$key] = $value;
                putenv("$key=$value");
                $loadedVars++;
            }
        }
        
        echo "✅ Loaded {$loadedVars} environment variables<br>";
        
        // Test if variables are now accessible
        $testVars = ['APP_NAME', 'APP_ENV', 'APP_KEY', 'DB_CONNECTION'];
        foreach ($testVars as $var) {
            $value = getenv($var);
            if ($value !== false) {
                echo "✅ {$var} = " . (strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value) . "<br>";
            } else {
                echo "❌ {$var} not loaded<br>";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Manual loading failed: " . $e->getMessage() . "<br>";
}

// 3. Test Laravel with manually loaded environment
echo "<h3>3. Testing Laravel with Manual Environment:</h3>";
try {
    // Load composer autoloader
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Create Laravel application
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
    
    // Try to boot
    $app->boot();
    echo "✅ Application booted successfully<br>";
    
    // Test services
    $config = $app->make('config');
    echo "✅ Config service: " . get_class($config) . "<br>";
    
    $db = $app->make('db');
    echo "✅ Database service: " . get_class($db) . "<br>";
    
    // Test configuration values
    echo "✅ App Name from config: " . $config->get('app.name') . "<br>";
    echo "✅ App Environment: " . $config->get('app.env') . "<br>";
    echo "✅ Database Connection: " . $config->get('database.default') . "<br>";
    
    // Test database connection
    $pdo = $db->connection()->getPdo();
    echo "✅ Database connection: " . get_class($pdo) . "<br>";
    
    // Test a simple query
    $result = $db->select('SELECT DATABASE() as db_name');
    echo "✅ Connected to database: " . $result[0]->db_name . "<br>";
    
    echo "<br><h3>🎉 SUCCESS! Laravel is now working correctly!</h3>";
    echo "Your website should now load without 500 errors.<br>";
    
} catch (Exception $e) {
    echo "❌ Laravel test failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// 4. Create a fixed bootstrap file
echo "<h3>4. Creating Environment-Aware Bootstrap:</h3>";
$fixedBootstrap = '<?php

// Load environment variables before Laravel bootstrap
if (file_exists(__DIR__ . "/../.env")) {
    $envContent = file_get_contents(__DIR__ . "/../.env");
    $envLines = explode("\n", $envContent);
    
    foreach ($envLines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, "#") === 0) {
            continue;
        }
        
        if (strpos($line, "=") !== false) {
            list($key, $value) = explode("=", $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes
            if ((strpos($value, "\"") === 0 && strrpos($value, "\"") === strlen($value) - 1) ||
                (strpos($value, "\'") === 0 && strrpos($value, "\'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.\'/../routes/web.php\',
        commands: __DIR__.\'/../routes/console.php\',
        health: \'/up\',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
';

if (file_put_contents(__DIR__ . '/bootstrap/app_fixed.php', $fixedBootstrap)) {
    echo "✅ Created fixed bootstrap file: bootstrap/app_fixed.php<br>";
} else {
    echo "❌ Failed to create fixed bootstrap file<br>";
}

echo "<br><h3>Final Steps:</h3>";
echo "1. If the test above succeeded, your Laravel app should now work<br>";
echo "2. Test your website pages now<br>";
echo "3. If you want to use the fixed bootstrap permanently, rename:<br>";
echo "   - bootstrap/app.php to bootstrap/app_original.php<br>";
echo "   - bootstrap/app_fixed.php to bootstrap/app.php<br>";
echo "4. Run: php artisan migrate --force (if you haven\'t already)<br>";

echo "<br><strong>Delete this file after the fix!</strong>";
?>
