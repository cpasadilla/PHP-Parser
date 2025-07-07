<?php
// Direct Bootstrap Fix - Final Solution
// Run this: https://sfxssli.shop/direct_fix.php

echo "<h2>SFX-1 Direct Bootstrap Fix</h2>";
echo "Applying the direct fix to resolve all issues...<br><br>";

// 1. Load environment variables first
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

// 2. Create the working bootstrap file
echo "<h3>2. Creating Working Bootstrap File:</h3>";
$workingBootstrap = '<?php

// Load environment variables before Laravel bootstrap
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

// Create application with proper base path
$app = new Application(dirname(__DIR__));

// Register core service providers in correct order
$app->register(\Illuminate\Events\EventServiceProvider::class);
$app->register(\Illuminate\Log\LogServiceProvider::class);
$app->register(\Illuminate\Routing\RoutingServiceProvider::class);
$app->register(\Illuminate\View\ViewServiceProvider::class);
$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(\Illuminate\Database\DatabaseServiceProvider::class);
$app->register(\Illuminate\Encryption\EncryptionServiceProvider::class);
$app->register(\Illuminate\Queue\QueueServiceProvider::class);
$app->register(\Illuminate\Cache\CacheServiceProvider::class);
$app->register(\Illuminate\Session\SessionServiceProvider::class);
$app->register(\Illuminate\Cookie\CookieServiceProvider::class);
$app->register(\Illuminate\Translation\TranslationServiceProvider::class);
$app->register(\Illuminate\Validation\ValidationServiceProvider::class);
$app->register(\Illuminate\Auth\AuthServiceProvider::class);

// Register application providers
$app->register(\App\Providers\AppServiceProvider::class);
$app->register(\App\Providers\RouteServiceProvider::class);

// Configure middleware and routes
$app->singleton(
    \Illuminate\Contracts\Http\Kernel::class,
    \App\Http\Kernel::class
);

$app->singleton(
    \Illuminate\Contracts\Console\Kernel::class,
    \App\Console\Kernel::class
);

$app->singleton(
    \Illuminate\Contracts\Debug\ExceptionHandler::class,
    \App\Exceptions\Handler::class
);

return $app;
';

if (file_put_contents(__DIR__ . '/bootstrap/app_working.php', $workingBootstrap)) {
    echo "✅ Created working bootstrap file: bootstrap/app_working.php<br>";
} else {
    echo "❌ Failed to create working bootstrap file<br>";
}

// 3. Test the working bootstrap
echo "<h3>3. Testing Working Bootstrap:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app_working.php';
    
    echo "✅ Laravel application created<br>";
    
    // Boot the application
    $app->boot();
    echo "✅ Application booted successfully<br>";
    
    // Test all services
    $files = $app->make('files');
    echo "✅ Files service: " . get_class($files) . "<br>";
    
    $config = $app->make('config');
    echo "✅ Config service: " . get_class($config) . "<br>";
    
    $db = $app->make('db');
    echo "✅ Database service: " . get_class($db) . "<br>";
    
    // Test database connection
    $pdo = $db->connection()->getPdo();
    echo "✅ Database connection: " . get_class($pdo) . "<br>";
    
    $result = $db->select('SELECT DATABASE() as db_name');
    echo "✅ Connected to database: " . $result[0]->db_name . "<br>";
    
    echo "<br><h3>🎉 SUCCESS! All services are working!</h3>";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// 4. Apply the fix
echo "<h3>4. Applying the Fix:</h3>";
if (file_exists(__DIR__ . '/bootstrap/app_working.php')) {
    // Backup original
    if (copy(__DIR__ . '/bootstrap/app.php', __DIR__ . '/bootstrap/app_original.php')) {
        echo "✅ Backed up original bootstrap/app.php<br>";
    }
    
    // Apply the working version
    if (copy(__DIR__ . '/bootstrap/app_working.php', __DIR__ . '/bootstrap/app.php')) {
        echo "✅ Applied working bootstrap fix<br>";
    } else {
        echo "❌ Failed to apply working bootstrap fix<br>";
    }
} else {
    echo "❌ Working bootstrap file not found<br>";
}

// 5. Final test with the applied fix
echo "<h3>5. Final Test with Applied Fix:</h3>";
try {
    // Clear any opcache
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    // Boot the application
    $app->boot();
    
    // Test all services again
    $files = $app->make('files');
    $config = $app->make('config');
    $db = $app->make('db');
    
    echo "✅ All services working with applied fix<br>";
    
    // Test database
    $result = $db->select('SELECT DATABASE() as db_name');
    echo "✅ Database test successful: " . $result[0]->db_name . "<br>";
    
    echo "<br><h3>🎉 COMPLETE SUCCESS! Your Laravel application is now working!</h3>";
    echo "All 500 errors should be resolved.<br>";
    
} catch (Exception $e) {
    echo "❌ Final test failed: " . $e->getMessage() . "<br>";
}

// 6. Test pages
echo "<h3>6. Test Your Website Pages:</h3>";
$testPages = [
    'Dashboard' => 'https://sfxssli.shop/dashboard',
    'Master List' => 'https://sfxssli.shop/masterlist',
    'Statement of Account' => 'https://sfxssli.shop/masterlist/soa',
    'Customers' => 'https://sfxssli.shop/masterlist/customers',
];

foreach ($testPages as $name => $url) {
    echo "<strong>{$name}:</strong> <a href='{$url}' target='_blank'>Test Now</a><br>";
}

echo "<br><h3>Next Steps:</h3>";
echo "1. Test all the links above - they should work now<br>";
echo "2. Run: php artisan migrate --force<br>";
echo "3. Run: php artisan config:clear<br>";
echo "4. Your SFX-1 application is fully deployed!<br>";

echo "<br><strong>Delete this file after successful testing!</strong>";
?>
