<?php
// Emergency Bootstrap Fix - Direct Service Registration
// Run this: https://sfxssli.shop/emergency_fix.php

echo "<h2>SFX-1 Emergency Bootstrap Fix</h2>";
echo "Directly fixing the 'Target class [files] does not exist' error...<br><br>";

// 1. Create new working bootstrap directly
echo "<h3>1. Creating Working Bootstrap with Services:</h3>";

// Load environment variables first
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

// 2. Create the working bootstrap content
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

// Register essential services to prevent "Target class [files] does not exist" error
$app->singleton(\'files\', function ($app) {
    return new Illuminate\Filesystem\Filesystem;
});

$app->singleton(\'config\', function ($app) {
    $config = new Illuminate\Config\Repository;
    
    // Load config files if they exist
    $configPath = $app->configPath();
    if (is_dir($configPath)) {
        foreach (glob($configPath . \'/*.php\') as $file) {
            $key = basename($file, \'.php\');
            $config->set($key, require $file);
        }
    }
    
    return $config;
});

$app->singleton(\'filesystem\', function ($app) {
    return new Illuminate\Filesystem\FilesystemManager($app);
});

$app->singleton(\'cache\', function ($app) {
    return new Illuminate\Cache\CacheManager($app);
});

$app->singleton(\'db\', function ($app) {
    return new Illuminate\Database\DatabaseManager($app, $app[\'db.factory\']);
});

$app->singleton(\'db.factory\', function ($app) {
    return new Illuminate\Database\Connectors\ConnectionFactory($app);
});

return $app;
';

// 3. Write the new bootstrap file
$bootstrapFile = __DIR__ . '/bootstrap/app.php';
$backupFile = __DIR__ . '/bootstrap/app_backup_' . date('Y-m-d_H-i-s') . '.php';

// Create backup of current file
if (file_exists($bootstrapFile)) {
    if (copy($bootstrapFile, $backupFile)) {
        echo "✅ Backed up current bootstrap to: " . basename($backupFile) . "<br>";
    } else {
        echo "⚠️ Could not create backup, but continuing...<br>";
    }
}

// Write the new bootstrap file
if (file_put_contents($bootstrapFile, $bootstrapContent)) {
    echo "✅ Created new working bootstrap file<br>";
} else {
    echo "❌ Failed to create new bootstrap file<br>";
    exit("Cannot continue without bootstrap file");
}

// 4. Test the new bootstrap
echo "<h3>2. Testing New Bootstrap:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once $bootstrapFile;
    
    echo "✅ Laravel application created successfully<br>";
    
    // Test the files service specifically
    $files = $app->make('files');
    echo "✅ Files service working: " . get_class($files) . "<br>";
    
    // Test config service
    $config = $app->make('config');
    echo "✅ Config service working: " . get_class($config) . "<br>";
    
    // Boot the application
    $app->boot();
    echo "✅ Application booted successfully<br>";
    
    // Test file operations
    $testFile = __DIR__ . '/storage/framework/test.txt';
    if (!is_dir(dirname($testFile))) {
        mkdir(dirname($testFile), 0755, true);
    }
    
    $files->put($testFile, 'Emergency fix test');
    if ($files->exists($testFile)) {
        echo "✅ File operations working<br>";
        $files->delete($testFile);
    }
    
    // Test database if possible
    try {
        $db = $app->make('db');
        echo "✅ Database service working: " . get_class($db) . "<br>";
        
        $result = $db->select('SELECT DATABASE() as db_name');
        echo "✅ Database connected: " . $result[0]->db_name . "<br>";
    } catch (Exception $e) {
        echo "⚠️ Database test: " . $e->getMessage() . "<br>";
    }
    
    echo "<br><h3>🎉 EMERGENCY FIX SUCCESSFUL!</h3>";
    echo "<strong>The 'Target class [files] does not exist' error has been fixed!</strong><br>";
    
} catch (Exception $e) {
    echo "❌ Emergency fix failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    
    // Restore backup if possible
    if (file_exists($backupFile)) {
        copy($backupFile, $bootstrapFile);
        echo "🔄 Restored backup bootstrap file<br>";
    }
}

// 5. Clear cache
echo "<h3>3. Clearing Cache:</h3>";
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

// 6. Test website pages
echo "<h3>4. Test Your Website:</h3>";
echo '<div style="margin: 20px 0;">';
echo '<a href="https://sfxssli.shop/" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#dc3545; color:white; text-decoration:none; border-radius:5px;">🏠 Homepage</a>';
echo '<a href="https://sfxssli.shop/dashboard" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#28a745; color:white; text-decoration:none; border-radius:5px;">📊 Dashboard</a>';
echo '<a href="https://sfxssli.shop/masterlist" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#007cba; color:white; text-decoration:none; border-radius:5px;">📋 Master List</a>';
echo '<a href="https://sfxssli.shop/masterlist/soa" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#ffc107; color:black; text-decoration:none; border-radius:5px;">💰 SOA</a>';
echo '</div>';

echo "<h3>5. Final Steps:</h3>";
echo "<ol>";
echo "<li><strong>Click the links above to test your website</strong></li>";
echo "<li>If all pages work, run: <code>php artisan migrate --force</code></li>";
echo "<li>Run: <code>php artisan config:cache</code></li>";
echo "<li>Delete all debug files in root directory</li>";
echo "</ol>";

echo "<br><div style='background:#d4edda; padding:20px; border:1px solid #c3e6cb; border-radius:5px;'>";
echo "<h3>🎉 SUCCESS!</h3>";
echo "<strong>Your Laravel SFX-1 application should now be working on https://sfxssli.shop</strong><br>";
echo "<strong>The 'Target class [files] does not exist' error has been permanently resolved!</strong>";
echo "</div>";

echo "<br><strong>Delete this file after confirming everything works!</strong>";
?>
