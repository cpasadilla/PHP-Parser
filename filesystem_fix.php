<?php
// Filesystem Service Fix for Laravel 11
// Run this: https://sfxssli.shop/filesystem_fix.php

echo "<h2>SFX-1 Filesystem Service Fix</h2>";
echo "Fixing the 'Target class [files] does not exist' error...<br><br>";

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

// 2. Check filesystem configuration
echo "<h3>2. Checking Filesystem Configuration:</h3>";
$filesystemConfig = __DIR__ . '/config/filesystems.php';
if (file_exists($filesystemConfig)) {
    echo "✅ Filesystem config exists<br>";
    $config = include $filesystemConfig;
    if (is_array($config)) {
        echo "✅ Filesystem config is valid array<br>";
        echo "✅ Default disk: " . ($config['default'] ?? 'not set') . "<br>";
    } else {
        echo "❌ Filesystem config is not a valid array<br>";
    }
} else {
    echo "❌ Filesystem config missing<br>";
}

// 3. Manual Laravel bootstrap with service registration
echo "<h3>3. Manual Laravel Bootstrap with Service Registration:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Create a custom Application instance
    $app = new \Illuminate\Foundation\Application(__DIR__);
    
    echo "✅ Application instance created<br>";
    
    // Register essential service providers manually
    $app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
    echo "✅ Filesystem service provider registered<br>";
    
    $app->register(\Illuminate\Database\DatabaseServiceProvider::class);
    echo "✅ Database service provider registered<br>";
    
    $app->register(\Illuminate\View\ViewServiceProvider::class);
    echo "✅ View service provider registered<br>";
    
    $app->register(\Illuminate\Auth\AuthServiceProvider::class);
    echo "✅ Auth service provider registered<br>";
    
    $app->register(\Illuminate\Cache\CacheServiceProvider::class);
    echo "✅ Cache service provider registered<br>";
    
    $app->register(\Illuminate\Session\SessionServiceProvider::class);
    echo "✅ Session service provider registered<br>";
    
    // Register your custom providers
    $app->register(\App\Providers\AppServiceProvider::class);
    echo "✅ App service provider registered<br>";
    
    $app->register(\App\Providers\RouteServiceProvider::class);
    echo "✅ Route service provider registered<br>";
    
    // Boot the application
    $app->boot();
    echo "✅ Application booted successfully<br>";
    
    // Test the filesystem service
    $files = $app->make('files');
    echo "✅ Filesystem service: " . get_class($files) . "<br>";
    
    // Test database service
    $db = $app->make('db');
    echo "✅ Database service: " . get_class($db) . "<br>";
    
    // Test configuration
    $config = $app->make('config');
    echo "✅ Config service: " . get_class($config) . "<br>";
    
    // Test database connection
    $pdo = $db->connection()->getPdo();
    echo "✅ Database connection: " . get_class($pdo) . "<br>";
    
    // Test a simple query
    $result = $db->select('SELECT DATABASE() as db_name');
    echo "✅ Connected to database: " . $result[0]->db_name . "<br>";
    
    echo "<br><h3>🎉 SUCCESS! All services are now working!</h3>";
    
} catch (Exception $e) {
    echo "❌ Manual bootstrap failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// 4. Create a completely fixed bootstrap file
echo "<h3>4. Creating Complete Bootstrap Fix:</h3>";
$completeBootstrap = '<?php

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

use Illuminate\Foundation\Application;

// Create application instance
$app = new Application(dirname(__DIR__));

// Register essential service providers
$app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(Illuminate\Database\DatabaseServiceProvider::class);
$app->register(Illuminate\View\ViewServiceProvider::class);
$app->register(Illuminate\Auth\AuthServiceProvider::class);
$app->register(Illuminate\Cache\CacheServiceProvider::class);
$app->register(Illuminate\Session\SessionServiceProvider::class);
$app->register(Illuminate\Cookie\CookieServiceProvider::class);
$app->register(Illuminate\Encryption\EncryptionServiceProvider::class);
$app->register(Illuminate\Queue\QueueServiceProvider::class);
$app->register(Illuminate\Translation\TranslationServiceProvider::class);
$app->register(Illuminate\Validation\ValidationServiceProvider::class);

// Register custom providers
$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\RouteServiceProvider::class);

// Set up singleton bindings
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

return $app;
';

if (file_put_contents(__DIR__ . '/bootstrap/app_complete.php', $completeBootstrap)) {
    echo "✅ Created complete bootstrap file: bootstrap/app_complete.php<br>";
} else {
    echo "❌ Failed to create complete bootstrap file<br>";
}

// 5. Create the missing Kernel classes if they don't exist
echo "<h3>5. Checking for Missing Classes:</h3>";
$kernelFile = __DIR__ . '/app/Http/Kernel.php';
if (!file_exists($kernelFile)) {
    echo "❌ Http/Kernel.php missing - creating it<br>";
    $kernelContent = '<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        "web" => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        "api" => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.":api",
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $middlewareAliases = [
        "auth" => \App\Http\Middleware\Authenticate::class,
        "auth.basic" => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        "auth.session" => \Illuminate\Session\Middleware\AuthenticateSession::class,
        "cache.headers" => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        "can" => \Illuminate\Auth\Middleware\Authorize::class,
        "guest" => \App\Http\Middleware\RedirectIfAuthenticated::class,
        "password.confirm" => \Illuminate\Auth\Middleware\RequirePassword::class,
        "precognitive" => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        "signed" => \App\Http\Middleware\ValidateSignature::class,
        "throttle" => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        "verified" => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];
}
';
    
    if (!is_dir(__DIR__ . '/app/Http')) {
        mkdir(__DIR__ . '/app/Http', 0755, true);
    }
    
    file_put_contents($kernelFile, $kernelContent);
    echo "✅ Created Http/Kernel.php<br>";
} else {
    echo "✅ Http/Kernel.php exists<br>";
}

echo "<br><h3>Final Steps:</h3>";
echo "1. To use the complete fix, rename files:<br>";
echo "   - mv bootstrap/app.php bootstrap/app_original.php<br>";
echo "   - mv bootstrap/app_complete.php bootstrap/app.php<br>";
echo "2. Test your website pages<br>";
echo "3. Run: php artisan migrate --force<br>";
echo "4. Clear any remaining caches<br>";

echo "<br><strong>Delete this file after the fix!</strong>";
?>
