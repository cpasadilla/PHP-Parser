<?php
// Database Setup and Migration Script
// Run this after fixing Laravel bootstrap: https://sfxssli.shop/setup_database.php

echo "<h2>SFX-1 Database Setup</h2>";
echo "Setting up database and running migrations...<br><br>";

// 1. Test Laravel Bootstrap
echo "<h3>1. Testing Laravel Bootstrap:</h3>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Laravel bootstrap successful<br>";
    
    // Test database connection
    try {
        $db = $app['db'];
        $connection = $db->connection();
        $pdo = $connection->getPdo();
        echo "✅ Database connection successful<br>";
        
        // Get database name
        $dbName = $connection->getDatabaseName();
        echo "Connected to database: {$dbName}<br>";
        
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
        echo "Please check your .env database credentials<br>";
        return;
    }
    
} catch (Exception $e) {
    echo "❌ Laravel bootstrap failed: " . $e->getMessage() . "<br>";
    return;
}

// 2. Check migration files
echo "<h3>2. Checking Migration Files:</h3>";
$migrationsDir = __DIR__ . '/database/migrations';
if (is_dir($migrationsDir)) {
    $migrations = scandir($migrationsDir);
    $migrationCount = count($migrations) - 2; // Exclude . and ..
    echo "✅ Found {$migrationCount} migration files<br>";
    
    foreach ($migrations as $migration) {
        if ($migration !== '.' && $migration !== '..') {
            echo "  - {$migration}<br>";
        }
    }
} else {
    echo "❌ Migrations directory not found<br>";
}

// 3. Check if migrations table exists
echo "<h3>3. Checking Database Tables:</h3>";
try {
    $tables = $db->select('SHOW TABLES');
    echo "✅ Found " . count($tables) . " tables in database<br>";
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "  - {$tableName}<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Could not check tables: " . $e->getMessage() . "<br>";
}

// 4. Artisan commands status
echo "<h3>4. Artisan Commands Status:</h3>";
try {
    $artisan = $app['Illuminate\Contracts\Console\Kernel'];
    echo "✅ Artisan kernel available<br>";
    
    // Note: We can't directly run artisan commands in a web script
    // User needs to run these in terminal
    echo "Run these commands in your terminal:<br>";
    echo "php artisan migrate --force<br>";
    echo "php artisan config:clear<br>";
    echo "php artisan route:clear<br>";
    echo "php artisan view:clear<br>";
    
} catch (Exception $e) {
    echo "❌ Artisan kernel failed: " . $e->getMessage() . "<br>";
}

echo "<br><h3>Manual Steps Required:</h3>";
echo "1. Open Hostinger Terminal or use SSH<br>";
echo "2. Navigate to your domain directory<br>";
echo "3. Run: php artisan migrate --force<br>";
echo "4. Run: php artisan config:clear<br>";
echo "5. Run: php artisan route:clear<br>";
echo "6. Run: php artisan view:clear<br>";
echo "7. Test your website<br>";

echo "<br><strong>Delete this file after running the setup!</strong>";
?>
