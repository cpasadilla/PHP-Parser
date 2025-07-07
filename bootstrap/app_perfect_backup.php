<?php

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
                (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
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
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Register only essential services - lightweight approach
$app->singleton('files', function ($app) {
    return new Illuminate\Filesystem\Filesystem;
});

$app->singleton('config', function ($app) {
    return new Illuminate\Config\Repository([
        'app' => [
            'name' => env('APP_NAME', 'SFX-1'),
            'env' => env('APP_ENV', 'production'),
            'debug' => env('APP_DEBUG', false),
            'url' => env('APP_URL', 'https://sfxssli.shop'),
            'key' => env('APP_KEY'),
            'cipher' => 'AES-256-CBC',
        ],
        'database' => [
            'default' => env('DB_CONNECTION', 'mysql'),
            'connections' => [
                'mysql' => [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => env('DB_DATABASE', 'forge'),
                    'username' => env('DB_USERNAME', 'forge'),
                    'password' => env('DB_PASSWORD', ''),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                ]
            ]
        ]
    ]);
});

$app->singleton('filesystem', function ($app) {
    return new Illuminate\Filesystem\FilesystemManager($app);
});

$app->singleton('cache', function ($app) {
    return new Illuminate\Cache\CacheManager($app);
});

return $app;
