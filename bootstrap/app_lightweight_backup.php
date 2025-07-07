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

// Register essential services to prevent "Target class [files] does not exist" error
$app->singleton('files', function ($app) {
    return new Illuminate\Filesystem\Filesystem;
});

$app->singleton('config', function ($app) {
    $config = new Illuminate\Config\Repository;
    
    // Load config files if they exist
    $configPath = $app->configPath();
    if (is_dir($configPath)) {
        foreach (glob($configPath . '/*.php') as $file) {
            $key = basename($file, '.php');
            $config->set($key, require $file);
        }
    }
    
    return $config;
});

$app->singleton('filesystem', function ($app) {
    return new Illuminate\Filesystem\FilesystemManager($app);
});

$app->singleton('cache', function ($app) {
    return new Illuminate\Cache\CacheManager($app);
});

$app->singleton('db', function ($app) {
    return new Illuminate\Database\DatabaseManager($app, $app['db.factory']);
});

$app->singleton('db.factory', function ($app) {
    return new Illuminate\Database\Connectors\ConnectionFactory($app);
});

return $app;
