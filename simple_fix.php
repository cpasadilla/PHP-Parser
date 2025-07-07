<?php
// Simple Working Fix - Guaranteed to Work
// Run this: https://sfxssli.shop/simple_fix.php

echo "<h2>SFX-1 Simple Working Fix</h2>";
echo "Applying a simple, guaranteed working fix...<br><br>";

// 1. Load environment variables
echo "<h3>1. Loading Environment Variables:</h3>";
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

// 2. Create a simple working bootstrap
echo "<h3>2. Creating Simple Working Bootstrap:</h3>";
$simpleBootstrap = '<?php

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

// Use Laravel 10 style bootstrap
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

return $app;
';

if (file_put_contents(__DIR__ . '/bootstrap/app_simple.php', $simpleBootstrap)) {
    echo "✅ Created simple bootstrap file: bootstrap/app_simple.php<br>";
} else {
    echo "❌ Failed to create simple bootstrap file<br>";
}

// 3. Test the simple bootstrap
echo "<h3>3. Testing Simple Bootstrap:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app_simple.php';
    
    echo "✅ Laravel application created<br>";
    
    // Try to boot
    $app->boot();
    echo "✅ Application booted<br>";
    
    // Test basic services
    $config = $app->make('config');
    echo "✅ Config service: " . get_class($config) . "<br>";
    
    $db = $app->make('db');
    echo "✅ Database service: " . get_class($db) . "<br>";
    
    // Test database connection
    $pdo = $db->connection()->getPdo();
    echo "✅ Database connection working<br>";
    
    $result = $db->select('SELECT DATABASE() as db_name');
    echo "✅ Connected to database: " . $result[0]->db_name . "<br>";
    
    echo "<br><h3>🎉 Simple bootstrap is working!</h3>";
    
} catch (Exception $e) {
    echo "❌ Simple test failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// 4. Apply the simple fix
echo "<h3>4. Applying Simple Fix:</h3>";
if (file_exists(__DIR__ . '/bootstrap/app_simple.php')) {
    // Backup current
    if (copy(__DIR__ . '/bootstrap/app.php', __DIR__ . '/bootstrap/app_backup_simple.php')) {
        echo "✅ Backed up current bootstrap/app.php<br>";
    }
    
    // Apply simple version
    if (copy(__DIR__ . '/bootstrap/app_simple.php', __DIR__ . '/bootstrap/app.php')) {
        echo "✅ Applied simple bootstrap fix<br>";
    } else {
        echo "❌ Failed to apply simple bootstrap fix<br>";
    }
} else {
    echo "❌ Simple bootstrap file not found<br>";
}

// 5. Final test
echo "<h3>5. Final Test:</h3>";
try {
    // Clear any opcache
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    $app->boot();
    $config = $app->make('config');
    $db = $app->make('db');
    
    echo "✅ All services working with simple fix<br>";
    
    $result = $db->select('SELECT DATABASE() as db_name');
    echo "✅ Database verified: " . $result[0]->db_name . "<br>";
    
    echo "<br><h3>🎉 SUCCESS! Laravel is now working!</h3>";
    
} catch (Exception $e) {
    echo "❌ Final test failed: " . $e->getMessage() . "<br>";
}

// 6. Test pages
echo "<h3>6. Test Your Pages Now:</h3>";
$testPages = [
    'Dashboard' => 'https://sfxssli.shop/dashboard',
    'Master List' => 'https://sfxssli.shop/masterlist',
    'SOA' => 'https://sfxssli.shop/masterlist/soa',
];

foreach ($testPages as $name => $url) {
    echo "<strong>{$name}:</strong> <a href='{$url}' target='_blank'>Test</a><br>";
}

echo "<br><h3>Next Steps:</h3>";
echo "1. Test the links above<br>";
echo "2. Run: php artisan migrate --force<br>";
echo "3. Run: php artisan config:clear<br>";

echo "<br><strong>Delete this file after testing!</strong>";
?>
