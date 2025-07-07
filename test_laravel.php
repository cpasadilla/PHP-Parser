<?php
// Simple Laravel Test
// Run this to test if Laravel is working: https://sfxssli.shop/test_laravel.php

echo "<h2>Laravel Function Test</h2>";
echo "Testing if Laravel is working properly...<br><br>";

try {
    // Load Laravel
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    // Boot the application
    $app->boot();
    
    echo "✅ Laravel loaded successfully<br>";
    
    // Test basic services
    $config = $app->make('config');
    echo "✅ Config service: " . get_class($config) . "<br>";
    
    $db = $app->make('db');
    echo "✅ Database service: " . get_class($db) . "<br>";
    
    // Test database connection
    $connection = $db->connection();
    $pdo = $connection->getPdo();
    echo "✅ Database PDO: " . get_class($pdo) . "<br>";
    
    // Test configuration values
    $appName = $config->get('app.name');
    echo "✅ App Name: " . $appName . "<br>";
    
    $dbConnection = $config->get('database.default');
    echo "✅ Database Connection: " . $dbConnection . "<br>";
    
    // Test if we can query the database
    $result = $db->select('SELECT 1 as test');
    echo "✅ Database query test: " . json_encode($result) . "<br>";
    
    echo "<br><h3>🎉 SUCCESS! Laravel is working properly!</h3>";
    echo "You can now test your website pages.<br>";
    
} catch (Exception $e) {
    echo "❌ Laravel test failed: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    
    echo "<br><h3>Still having issues. Try these steps:</h3>";
    echo "1. Check your database credentials in .env<br>";
    echo "2. Run: composer install --no-dev --optimize-autoloader<br>";
    echo "3. Run: php artisan config:clear<br>";
    echo "4. Run: php artisan cache:clear<br>";
}

echo "<br><strong>Delete this file after testing!</strong>";
?>
