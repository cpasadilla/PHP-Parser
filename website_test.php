<?php
// Simple Website Test
// Test this after running env_fix.php: https://sfxssli.shop/website_test.php

echo "<h2>SFX-1 Website Test</h2>";
echo "Testing if your website pages are now working...<br><br>";

// 1. Test Laravel bootstrap
echo "<h3>1. Laravel Bootstrap Test:</h3>";
try {
    // Load environment manually first
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
    $app->boot();
    
    echo "✅ Laravel loaded successfully<br>";
    
    // Test database
    $db = $app->make('db');
    $result = $db->select('SELECT 1 as test');
    echo "✅ Database connection working<br>";
    
    // Test config
    $config = $app->make('config');
    echo "✅ App Name: " . $config->get('app.name') . "<br>";
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
    return;
}

// 2. Test critical pages
echo "<h3>2. Testing Critical Pages:</h3>";
$testUrls = [
    'Dashboard' => '/dashboard',
    'Master List' => '/masterlist',
    'Statement of Account' => '/masterlist/soa',
];

foreach ($testUrls as $name => $url) {
    echo "<strong>{$name}:</strong> ";
    echo '<a href="https://sfxssli.shop' . $url . '" target="_blank">Test Link</a><br>';
}

echo "<br><h3>3. Database Tables Check:</h3>";
try {
    $tables = $db->select('SHOW TABLES');
    echo "✅ Database has " . count($tables) . " tables<br>";
    
    // Check for important tables
    $importantTables = ['users', 'customers', 'orders', 'ships', 'voyages'];
    foreach ($importantTables as $table) {
        try {
            $count = $db->select("SELECT COUNT(*) as count FROM {$table}")[0]->count;
            echo "✅ Table '{$table}' has {$count} records<br>";
        } catch (Exception $e) {
            echo "❌ Table '{$table}' missing or error: " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database tables check failed: " . $e->getMessage() . "<br>";
}

echo "<br><h3>🎉 Your Laravel SFX-1 Application Status:</h3>";
echo "✅ Laravel framework is working<br>";
echo "✅ Database connection is established<br>";
echo "✅ Environment variables are loaded<br>";
echo "✅ Configuration is accessible<br>";
echo "<br><strong>Your website should now be fully functional!</strong><br>";
echo "<br>Test the links above to verify all pages are working.<br>";

echo "<br><strong>Delete this file after testing!</strong>";
?>
