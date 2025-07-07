<?php
// Environment File Recreation Script
// Run this: https://sfxssli.shop/recreate_env.php

echo "<h2>SFX-1 Environment File Recreation</h2>";
echo "Recreating .env file with proper format...<br><br>";

// 1. Check current .env file
echo "<h3>1. Current .env File Analysis:</h3>";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    echo "✅ .env file exists<br>";
    
    $envContent = file_get_contents($envPath);
    $filesize = filesize($envPath);
    echo "File size: {$filesize} bytes<br>";
    
    // Check for common issues
    if (empty(trim($envContent))) {
        echo "❌ .env file is empty<br>";
    } elseif (strpos($envContent, 'APP_NAME=') === false) {
        echo "❌ .env file missing APP_NAME<br>";
    } else {
        echo "✅ .env file has content<br>";
    }
} else {
    echo "❌ .env file not found<br>";
}

// 2. Create a new .env file with proper settings
echo "<h3>2. Creating New .env File:</h3>";
$newEnvContent = 'APP_NAME=SFX-1
APP_ENV=production
APP_KEY=base64:ybSDulkkYQAJXLeI0oS33kGIY0ts857tjVxrziTD1jY=
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://sfxssli.shop

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@sfxssli.shop"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
';

// Backup existing .env file if it exists
if (file_exists($envPath)) {
    $backupPath = __DIR__ . '/.env.backup.' . date('Y-m-d-H-i-s');
    if (copy($envPath, $backupPath)) {
        echo "✅ Backed up existing .env to: .env.backup." . date('Y-m-d-H-i-s') . "<br>";
    }
}

// Write new .env file
if (file_put_contents($envPath, $newEnvContent)) {
    echo "✅ Created new .env file<br>";
    echo "File size: " . filesize($envPath) . " bytes<br>";
} else {
    echo "❌ Failed to create .env file<br>";
}

// 3. Test the new environment file
echo "<h3>3. Testing New Environment File:</h3>";
try {
    // Clear any existing environment variables
    foreach ($_ENV as $key => $value) {
        if (strpos($key, 'APP_') === 0 || strpos($key, 'DB_') === 0) {
            unset($_ENV[$key]);
        }
    }
    
    // Load the new environment file
    $lines = explode("\n", $newEnvContent);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line) && !str_starts_with($line, '#') && strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    
    echo "✅ Environment variables loaded<br>";
    
    // Test key variables
    $testVars = ['APP_NAME', 'APP_ENV', 'APP_KEY', 'DB_CONNECTION'];
    foreach ($testVars as $var) {
        if (isset($_ENV[$var]) && !empty($_ENV[$var])) {
            echo "✅ {$var} = " . $_ENV[$var] . "<br>";
        } else {
            echo "❌ {$var} not set<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Environment test failed: " . $e->getMessage() . "<br>";
}

// 4. Test Laravel with new environment
echo "<h3>4. Testing Laravel with New Environment:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    
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
    
    $app->boot();
    
    echo "✅ Laravel application created and booted<br>";
    
    $config = $app->make('config');
    echo "✅ Config service working<br>";
    
    echo "App Name: " . $config->get('app.name') . "<br>";
    echo "App Environment: " . $config->get('app.env') . "<br>";
    
    echo "<br><h3>🎉 SUCCESS! Your Laravel application should now work!</h3>";
    
} catch (Exception $e) {
    echo "❌ Laravel test failed: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Important Notes:</h3>";
echo "1. ✅ Created new .env file with proper format<br>";
echo "2. ⚠️ Update database credentials in .env file with your actual Hostinger database details<br>";
echo "3. ✅ Your Laravel application should now load without 500 errors<br>";
echo "4. 🔄 Test your website pages now<br>";

echo "<br><strong>Delete this file after testing!</strong>";
?>
