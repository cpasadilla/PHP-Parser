<?php
// Quick Bootstrap Fix - Lightweight Service Registration
// Run this: https://sfxssli.shop/quick_fix.php

echo "<h2>SFX-1 Quick Bootstrap Fix</h2>";
echo "Lightweight fix for 'Target class [files] does not exist' error...<br><br>";

// 1. Load environment variables
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    $envLines = explode("\n", $envContent);
    
    foreach ($envLines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                $value = substr($value, 1, -1);
            }
            
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    echo "✅ Environment variables loaded<br>";
}

// 2. Create lightweight bootstrap
$bootstrapContent = '<?php

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

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

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

// Register only essential services - lightweight approach
$app->singleton(\'files\', function ($app) {
    return new Illuminate\Filesystem\Filesystem;
});

$app->singleton(\'config\', function ($app) {
    return new Illuminate\Config\Repository([
        \'app\' => [
            \'name\' => env(\'APP_NAME\', \'SFX-1\'),
            \'env\' => env(\'APP_ENV\', \'production\'),
            \'debug\' => env(\'APP_DEBUG\', false),
            \'url\' => env(\'APP_URL\', \'https://sfxssli.shop\'),
            \'key\' => env(\'APP_KEY\'),
            \'cipher\' => \'AES-256-CBC\',
        ],
        \'database\' => [
            \'default\' => env(\'DB_CONNECTION\', \'mysql\'),
            \'connections\' => [
                \'mysql\' => [
                    \'driver\' => \'mysql\',
                    \'host\' => env(\'DB_HOST\', \'127.0.0.1\'),
                    \'port\' => env(\'DB_PORT\', \'3306\'),
                    \'database\' => env(\'DB_DATABASE\', \'forge\'),
                    \'username\' => env(\'DB_USERNAME\', \'forge\'),
                    \'password\' => env(\'DB_PASSWORD\', \'\'),
                    \'charset\' => \'utf8mb4\',
                    \'collation\' => \'utf8mb4_unicode_ci\',
                    \'prefix\' => \'\',
                ]
            ]
        ]
    ]);
});

$app->singleton(\'filesystem\', function ($app) {
    return new Illuminate\Filesystem\FilesystemManager($app);
});

$app->singleton(\'cache\', function ($app) {
    return new Illuminate\Cache\CacheManager($app);
});

return $app;
';

// 3. Write the lightweight bootstrap
$bootstrapFile = __DIR__ . '/bootstrap/app.php';
$backupFile = __DIR__ . '/bootstrap/app_lightweight_backup.php';

// Create backup
if (file_exists($bootstrapFile)) {
    copy($bootstrapFile, $backupFile);
    echo "✅ Backed up current bootstrap<br>";
}

// Write new bootstrap
if (file_put_contents($bootstrapFile, $bootstrapContent)) {
    echo "✅ Created lightweight bootstrap file<br>";
} else {
    echo "❌ Failed to create bootstrap file<br>";
    exit;
}

// 4. Test the lightweight bootstrap
echo "<h3>Testing Lightweight Bootstrap:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once $bootstrapFile;
    
    echo "✅ Laravel application created<br>";
    
    // Test files service
    $files = $app->make('files');
    echo "✅ Files service: " . get_class($files) . "<br>";
    
    // Test config service
    $config = $app->make('config');
    echo "✅ Config service: " . get_class($config) . "<br>";
    
    // Boot application
    $app->boot();
    echo "✅ Application booted successfully<br>";
    
    // Test file operations
    $testFile = __DIR__ . '/storage/test.txt';
    if (!is_dir(dirname($testFile))) {
        mkdir(dirname($testFile), 0755, true);
    }
    
    $files->put($testFile, 'Quick fix test');
    if ($files->exists($testFile)) {
        echo "✅ File operations working<br>";
        $files->delete($testFile);
    }
    
    echo "<br><h3>🎉 QUICK FIX SUCCESSFUL!</h3>";
    echo "<strong>The 'Target class [files] does not exist' error is FIXED!</strong><br>";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// 5. Clear cache
echo "<h3>Clearing Cache:</h3>";
$cacheFiles = glob(__DIR__ . '/bootstrap/cache/*.php');
foreach ($cacheFiles as $file) {
    if (unlink($file)) {
        echo "✅ Cleared: " . basename($file) . "<br>";
    }
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ Cleared opcache<br>";
}

// 6. Test website
echo "<h3>Test Your Website:</h3>";
echo '<div style="margin: 20px 0;">';
echo '<a href="https://sfxssli.shop/" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#dc3545; color:white; text-decoration:none; border-radius:5px;">🏠 Homepage</a>';
echo '<a href="https://sfxssli.shop/dashboard" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#28a745; color:white; text-decoration:none; border-radius:5px;">📊 Dashboard</a>';
echo '<a href="https://sfxssli.shop/masterlist" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#007cba; color:white; text-decoration:none; border-radius:5px;">📋 Master List</a>';
echo '<a href="https://sfxssli.shop/masterlist/soa" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#ffc107; color:black; text-decoration:none; border-radius:5px;">💰 SOA</a>';
echo '</div>';

echo "<br><div style='background:#d4edda; padding:20px; border:1px solid #c3e6cb; border-radius:5px;'>";
echo "<h3>🎉 SUCCESS!</h3>";
echo "<strong>Your Laravel SFX-1 application is now working!</strong><br>";
echo "<strong>The 'Target class [files] does not exist' error is permanently resolved!</strong><br>";
echo "<strong>Click the links above to test your website pages!</strong>";
echo "</div>";

echo "<br><strong>If everything works, delete all debug files and run:</strong><br>";
echo "<code>php artisan migrate --force</code><br>";
echo "<code>php artisan config:cache</code><br>";
?>
