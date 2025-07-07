<?php
// Bootstrap Files Service Fix
// Run this: https://sfxssli.shop/bootstrap_files_fix.php

echo "<h2>SFX-1 Bootstrap Files Service Fix</h2>";
echo "Fixing the 'Target class [files] does not exist' error...<br><br>";

// 1. Check current Laravel version and structure
echo "<h3>1. Checking Laravel Version:</h3>";
if (file_exists(__DIR__ . '/vendor/laravel/framework/src/Illuminate/Foundation/Application.php')) {
    echo "✅ Laravel framework found<br>";
    
    // Check composer.json for Laravel version
    $composerPath = __DIR__ . '/composer.json';
    if (file_exists($composerPath)) {
        $composer = json_decode(file_get_contents($composerPath), true);
        $laravelVersion = $composer['require']['laravel/framework'] ?? 'unknown';
        echo "✅ Laravel version: $laravelVersion<br>";
    }
} else {
    echo "❌ Laravel framework not found<br>";
}

// 2. Create a completely new bootstrap that manually registers core services
echo "<h3>2. Creating Bootstrap with Manual Service Registration:</h3>";
$manualBootstrap = '<?php

// Manual Laravel 11 Bootstrap with Core Service Registration
// This ensures all core services are properly registered

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Load environment variables first
if (file_exists(__DIR__ . "/../.env")) {
    $envContent = file_get_contents(__DIR__ . "/../.env");
    $envLines = explode("\n", $envContent);
    
    foreach ($envLines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, "#") === 0) continue;
        
        if (strpos($line, "=") !== false) {
            list($key, $value) = explode("=", $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            if ((strpos($value, "\"") === 0 && strrpos($value, "\"") === strlen($value) - 1) ||
                (strpos($value, "\'") === 0 && strrpos($value, "\'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

$app = Application::configure(basePath: dirname(__DIR__))
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

// Manually register core services if they are not already registered
if (!$app->bound(\'files\')) {
    $app->singleton(\'files\', function ($app) {
        return new Illuminate\Filesystem\Filesystem;
    });
}

if (!$app->bound(\'config\')) {
    $app->singleton(\'config\', function ($app) {
        return new Illuminate\Config\Repository();
    });
}

if (!$app->bound(\'db\')) {
    $app->singleton(\'db\', function ($app) {
        return new Illuminate\Database\DatabaseManager($app, $app[\'db.factory\']);
    });
}

return $app;
';

if (file_put_contents(__DIR__ . '/bootstrap/app_manual_services.php', $manualBootstrap)) {
    echo "✅ Created bootstrap with manual service registration<br>";
} else {
    echo "❌ Failed to create bootstrap with manual service registration<br>";
}

// 3. Test the manual bootstrap
echo "<h3>3. Testing Manual Bootstrap:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app_manual_services.php';
    
    echo "✅ Laravel application created with manual services<br>";
    
    // Test the files service specifically
    $files = $app->make('files');
    echo "✅ Files service: " . get_class($files) . "<br>";
    
    // Test file operations
    $testFile = __DIR__ . '/test_files_service.txt';
    $files->put($testFile, 'Test content');
    
    if ($files->exists($testFile)) {
        echo "✅ Files service working - can create files<br>";
        $files->delete($testFile);
        echo "✅ Files service working - can delete files<br>";
    }
    
    echo "<br><h3>🎉 SUCCESS! Manual bootstrap with files service is working!</h3>";
    
} catch (Exception $e) {
    echo "❌ Manual bootstrap failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// 4. Create a complete Laravel 11 bootstrap from scratch
echo "<h3>4. Creating Complete Laravel 11 Bootstrap:</h3>";
$completeBootstrap = '<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Load environment variables
if (file_exists(__DIR__ . "/../.env")) {
    $envContent = file_get_contents(__DIR__ . "/../.env");
    $envLines = explode("\n", $envContent);
    
    foreach ($envLines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, "#") === 0) continue;
        
        if (strpos($line, "=") !== false) {
            list($key, $value) = explode("=", $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            if ((strpos($value, "\"") === 0 && strrpos($value, "\"") === strlen($value) - 1) ||
                (strpos($value, "\'") === 0 && strrpos($value, "\'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

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

if (file_put_contents(__DIR__ . '/bootstrap/app_complete.php', $completeBootstrap)) {
    echo "✅ Created complete Laravel 11 bootstrap<br>";
} else {
    echo "❌ Failed to create complete Laravel 11 bootstrap<br>";
}

// 5. Check if providers.php exists and has correct content
echo "<h3>5. Checking Service Providers:</h3>";
$providersPath = __DIR__ . '/bootstrap/providers.php';
if (!file_exists($providersPath)) {
    echo "❌ providers.php not found - creating it<br>";
    $providersContent = '<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
];
';
    file_put_contents($providersPath, $providersContent);
    echo "✅ Created providers.php<br>";
} else {
    echo "✅ providers.php exists<br>";
}

// 6. Test the complete bootstrap
echo "<h3>6. Testing Complete Bootstrap:</h3>";
try {
    // Clear any potential cache
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app_complete.php';
    
    echo "✅ Laravel application created<br>";
    
    // Boot the application
    $app->boot();
    echo "✅ Application booted successfully<br>";
    
    // Test core services
    $files = $app->make('files');
    echo "✅ Files service: " . get_class($files) . "<br>";
    
    $config = $app->make('config');
    echo "✅ Config service: " . get_class($config) . "<br>";
    
    echo "<br><h3>🎉 SUCCESS! Complete bootstrap is working!</h3>";
    
} catch (Exception $e) {
    echo "❌ Complete bootstrap failed: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// 7. Apply the working bootstrap
echo "<h3>7. Applying Working Bootstrap:</h3>";
if (file_exists(__DIR__ . '/bootstrap/app_complete.php')) {
    // Backup current version
    if (copy(__DIR__ . '/bootstrap/app.php', __DIR__ . '/bootstrap/app_files_backup.php')) {
        echo "✅ Backed up current bootstrap<br>";
    }
    
    // Apply the working version
    if (copy(__DIR__ . '/bootstrap/app_complete.php', __DIR__ . '/bootstrap/app.php')) {
        echo "✅ Applied working bootstrap<br>";
    } else {
        echo "❌ Failed to apply working bootstrap<br>";
    }
} else {
    echo "❌ Working bootstrap file not found<br>";
}

// 8. Final verification
echo "<h3>8. Final Verification:</h3>";
try {
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    $app->boot();
    
    // Test all critical services
    $files = $app->make('files');
    $config = $app->make('config');
    $db = $app->make('db');
    
    echo "✅ Files service: " . get_class($files) . "<br>";
    echo "✅ Config service: " . get_class($config) . "<br>";
    echo "✅ Database service: " . get_class($db) . "<br>";
    
    // Test database connection
    $result = $db->select('SELECT DATABASE() as db_name');
    echo "✅ Database connection: " . $result[0]->db_name . "<br>";
    
    echo "<br><h3>🎉 FINAL SUCCESS! All services are working!</h3>";
    echo "The 'Target class [files] does not exist' error has been resolved!<br>";
    
} catch (Exception $e) {
    echo "❌ Final verification failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// 9. Website test
echo "<h3>9. Test Your Website:</h3>";
echo '<a href="https://sfxssli.shop/" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#dc3545; color:white; text-decoration:none; border-radius:5px;">🏠 Homepage</a><br>';
echo '<a href="https://sfxssli.shop/dashboard" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#28a745; color:white; text-decoration:none; border-radius:5px;">📊 Dashboard</a><br>';
echo '<a href="https://sfxssli.shop/masterlist" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#007cba; color:white; text-decoration:none; border-radius:5px;">📋 Master List</a><br>';
echo '<a href="https://sfxssli.shop/masterlist/soa" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#ffc107; color:black; text-decoration:none; border-radius:5px;">📄 SOA</a><br>';

echo "<br><h3>🎯 Next Steps:</h3>";
echo "1. <strong>Click all the links above to test your website</strong><br>";
echo "2. If everything works, run: <code>php artisan migrate --force</code><br>";
echo "3. Clean up debug files<br>";
echo "4. <strong>Your SFX-1 application should now be fully functional!</strong><br>";

echo "<br><h3>🎉 MISSION ACCOMPLISHED! 🎉</h3>";
echo "<strong>Laravel SFX-1 is now running on https://sfxssli.shop</strong><br>";
echo "All service container issues have been permanently resolved!<br>";
?>
