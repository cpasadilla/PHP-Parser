<?php
/**
 * MIGRATION RUNNER - Run hire_date nullable migration
 * Access: https://yourdomain.com/run_migration.php?password=YOUR_PASSWORD
 * 
 * ⚠️ DELETE THIS FILE AFTER USE!
 */

// Security check - change this password
$PASSWORD = 'your_secure_password_here';

if (!isset($_GET['password']) || $_GET['password'] !== $PASSWORD) {
    die('Access denied. Use: run_migration.php?password=your_secure_password_here');
}

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Running Migration...</h1>";
echo "<pre>";

try {
    echo "🔄 Running migration: make_hire_date_nullable_in_crews_table\n\n";
    
    $exitCode = $kernel->call('migrate', [
        '--path' => 'database/migrations/2025_10_16_120000_make_hire_date_nullable_in_crews_table.php',
        '--force' => true
    ]);
    
    echo "\n";
    
    if ($exitCode === 0) {
        echo "✅ Migration completed successfully!\n\n";
        
        // Verify the change
        echo "Verifying changes...\n";
        $column = DB::select("SHOW COLUMNS FROM crews WHERE Field = 'hire_date'");
        
        if (!empty($column)) {
            $nullable = $column[0]->Null === 'YES' ? '✅ NULLABLE' : '❌ NOT NULL';
            echo "hire_date column: {$nullable}\n";
        }
    } else {
        echo "⚠️ Migration returned exit code: {$exitCode}\n";
    }
    
    echo "\n⚠️ NOW DELETE THIS FILE (run_migration.php) FOR SECURITY!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
