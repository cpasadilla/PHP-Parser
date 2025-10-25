<?php
/**
 * Hostinger Laravel Fix Script
 * This script fixes common issues when deploying Laravel to Hostinger
 * 
 * Upload this to your root Laravel directory (where artisan file is located)
 * Then access it via SSH or run it through browser if you add it to public folder
 */

echo "🔧 HOSTINGER LARAVEL FIX SCRIPT\n";
echo "================================\n\n";

// Check if we're in the correct directory
if (!file_exists('artisan')) {
    die("❌ ERROR: Please run this script from your Laravel root directory (where artisan file is located)\n");
}

echo "✅ Laravel installation detected\n\n";

// Function to run command and show output
function runCommand($command, $description) {
    echo "Running: $description\n";
    echo "Command: $command\n";
    exec($command . " 2>&1", $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "✅ Success\n";
    } else {
        echo "⚠️  Command returned code: $returnVar\n";
    }
    
    if (!empty($output)) {
        echo "Output:\n";
        foreach ($output as $line) {
            echo "  $line\n";
        }
    }
    echo "\n";
}

// 1. Fix storage permissions
echo "1. FIXING STORAGE AND CACHE PERMISSIONS\n";
echo "---------------------------------------\n";
$storageDir = __DIR__ . '/storage';
$bootstrapCacheDir = __DIR__ . '/bootstrap/cache';

if (is_dir($storageDir)) {
    chmod($storageDir, 0755);
    
    // Fix subdirectories
    $dirs = ['app', 'framework', 'framework/cache', 'framework/sessions', 'framework/views', 'logs'];
    foreach ($dirs as $dir) {
        $fullPath = $storageDir . '/' . $dir;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
            echo "✅ Created: $fullPath\n";
        } else {
            chmod($fullPath, 0755);
            echo "✅ Fixed permissions: $fullPath\n";
        }
    }
}

if (is_dir($bootstrapCacheDir)) {
    chmod($bootstrapCacheDir, 0755);
    echo "✅ Fixed permissions: $bootstrapCacheDir\n";
}

echo "\n";

// 2. Clear all caches
echo "2. CLEARING LARAVEL CACHES\n";
echo "--------------------------\n";
runCommand('php artisan config:clear', 'Clear config cache');
runCommand('php artisan route:clear', 'Clear route cache');
runCommand('php artisan view:clear', 'Clear view cache');
runCommand('php artisan cache:clear', 'Clear application cache');

// 3. Optimize autoloader
echo "3. OPTIMIZING AUTOLOADER\n";
echo "------------------------\n";
runCommand('composer dump-autoload -o', 'Optimize Composer autoloader');

// 4. Check .env file
echo "4. CHECKING ENVIRONMENT CONFIGURATION\n";
echo "-------------------------------------\n";
if (!file_exists('.env')) {
    echo "❌ .env file not found!\n";
    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        echo "✅ Created .env file from .env.example\n";
        echo "⚠️  Please update your database credentials in .env file\n";
    }
} else {
    echo "✅ .env file exists\n";
    
    // Check important settings
    $envContent = file_get_contents('.env');
    
    // Check APP_DEBUG
    if (strpos($envContent, 'APP_DEBUG=true') !== false) {
        echo "⚠️  WARNING: APP_DEBUG is set to true\n";
        echo "   For production, set APP_DEBUG=false in .env\n";
    } else {
        echo "✅ APP_DEBUG setting found\n";
    }
    
    // Check database settings
    if (strpos($envContent, 'DB_DATABASE=') !== false) {
        echo "✅ Database configuration found\n";
    } else {
        echo "❌ Database configuration missing\n";
    }
}
echo "\n";

// 5. Generate application key if missing
echo "5. CHECKING APPLICATION KEY\n";
echo "---------------------------\n";
$envContent = file_get_contents('.env');
if (strpos($envContent, 'APP_KEY=base64:') === false || strpos($envContent, 'APP_KEY=') === false) {
    echo "⚠️  Application key is missing or invalid\n";
    runCommand('php artisan key:generate', 'Generate application key');
} else {
    echo "✅ Application key is set\n";
}
echo "\n";

// 6. Check public .htaccess
echo "6. CHECKING PUBLIC .HTACCESS FILE\n";
echo "---------------------------------\n";
$htaccessFile = __DIR__ . '/public/.htaccess';
if (!file_exists($htaccessFile)) {
    echo "❌ .htaccess file missing in public folder\n";
    echo "Creating .htaccess file...\n";
    
    $htaccessContent = <<<'HTACCESS'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS;
    
    file_put_contents($htaccessFile, $htaccessContent);
    echo "✅ Created .htaccess file\n";
} else {
    echo "✅ .htaccess file exists\n";
}
echo "\n";

// 7. Create storage link
echo "7. CREATING STORAGE SYMBOLIC LINK\n";
echo "----------------------------------\n";
runCommand('php artisan storage:link', 'Create storage link');

// 8. Test database connection
echo "8. TESTING DATABASE CONNECTION\n";
echo "-------------------------------\n";
try {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    DB::connection()->getPdo();
    echo "✅ Database connection successful\n";
    echo "   Database: " . DB::connection()->getDatabaseName() . "\n";
} catch (Exception $e) {
    echo "❌ Database connection failed\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   Please check your .env database credentials\n";
}
echo "\n";

// 9. Final recommendations
echo "9. FINAL RECOMMENDATIONS\n";
echo "------------------------\n";
echo "✅ Script completed!\n\n";
echo "Next steps:\n";
echo "1. Test your application in the browser\n";
echo "2. If you still see 500 errors:\n";
echo "   - Check storage/logs/laravel.log for detailed errors\n";
echo "   - Set APP_DEBUG=true in .env temporarily to see error details\n";
echo "   - Verify PHP version is 8.1 or higher\n";
echo "   - Check that all Composer dependencies are installed\n";
echo "3. For Hostinger specifically:\n";
echo "   - Make sure your public_html points to the 'public' folder\n";
echo "   - Or move contents of 'public' folder to public_html\n";
echo "   - Verify mod_rewrite is enabled\n\n";

echo "================================\n";
echo "🎉 FIX SCRIPT COMPLETED\n";
?>
