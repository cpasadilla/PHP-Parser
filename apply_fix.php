<?php
// Apply Bootstrap Fix Automatically
// Run this: https://sfxssli.shop/apply_fix.php

echo "<h2>SFX-1 Apply Bootstrap Fix</h2>";
echo "Automatically applying the bootstrap fix...<br><br>";

// 1. Backup original bootstrap file
echo "<h3>1. Backing Up Original Files:</h3>";
if (file_exists(__DIR__ . '/bootstrap/app.php')) {
    if (copy(__DIR__ . '/bootstrap/app.php', __DIR__ . '/bootstrap/app_original.php')) {
        echo "✅ Backed up original bootstrap/app.php<br>";
    } else {
        echo "❌ Failed to backup original bootstrap/app.php<br>";
    }
}

// 2. Apply the complete bootstrap fix
echo "<h3>2. Applying Complete Bootstrap Fix:</h3>";
if (file_exists(__DIR__ . '/bootstrap/app_complete.php')) {
    if (copy(__DIR__ . '/bootstrap/app_complete.php', __DIR__ . '/bootstrap/app.php')) {
        echo "✅ Applied complete bootstrap fix<br>";
    } else {
        echo "❌ Failed to apply bootstrap fix<br>";
    }
} else {
    echo "❌ Complete bootstrap file not found - run filesystem_fix.php first<br>";
}

// 3. Test the fix
echo "<h3>3. Testing the Fix:</h3>";
try {
    // Load environment variables
    if (file_exists(__DIR__ . '/.env')) {
        $envContent = file_get_contents(__DIR__ . '/.env');
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
    }
    
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    echo "✅ Laravel bootstrap successful<br>";
    
    // Test key services
    $files = $app->make('files');
    echo "✅ Filesystem service: " . get_class($files) . "<br>";
    
    $db = $app->make('db');
    echo "✅ Database service: " . get_class($db) . "<br>";
    
    $config = $app->make('config');
    echo "✅ Config service: " . get_class($config) . "<br>";
    
    // Test database connection
    $pdo = $db->connection()->getPdo();
    echo "✅ Database connection: " . get_class($pdo) . "<br>";
    
    $result = $db->select('SELECT DATABASE() as db_name');
    echo "✅ Connected to database: " . $result[0]->db_name . "<br>";
    
    echo "<br><h3>🎉 SUCCESS! Laravel is now fully functional!</h3>";
    echo "Your website should now work without any 500 errors.<br>";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

// 4. Test critical pages
echo "<h3>4. Test Your Pages:</h3>";
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
echo "1. Click the test links above to verify all pages work<br>";
echo "2. If pages work, run: php artisan migrate --force<br>";
echo "3. Clear any remaining caches: php artisan config:clear<br>";
echo "4. Your SFX-1 Laravel application is now fully deployed!<br>";

echo "<br><strong>Delete this file after successful testing!</strong>";
?>
