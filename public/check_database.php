<?php
/**
 * DATABASE STRUCTURE CHECKER
 * Access: https://yourdomain.com/check_database.php?password=YOUR_PASSWORD
 * 
 * ⚠️ DELETE THIS FILE AFTER USE!
 */

// Security check - change this password
$PASSWORD = 'your_secure_password_here';

if (!isset($_GET['password']) || $_GET['password'] !== $PASSWORD) {
    die('Access denied. Use: check_database.php?password=your_secure_password_here');
}

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Database Structure Check</h1>";
echo "<pre>";

try {
    // Check crews table structure
    echo "📋 CREWS TABLE STRUCTURE:\n";
    echo "=" . str_repeat("=", 80) . "\n\n";
    
    $columns = DB::select('DESCRIBE crews');
    
    printf("%-30s %-20s %-10s %-10s %-20s\n", "Field", "Type", "Null", "Key", "Default");
    echo str_repeat("-", 100) . "\n";
    
    foreach ($columns as $column) {
        printf("%-30s %-20s %-10s %-10s %-20s\n", 
            $column->Field, 
            $column->Type, 
            $column->Null, 
            $column->Key ?? '', 
            $column->Default ?? 'NULL'
        );
    }
    
    echo "\n\n";
    echo "🔍 DATE FIELDS CHECK:\n";
    echo str_repeat("-", 80) . "\n";
    
    $dateFields = ['hire_date', 'contract_expiry', 'basic_safety_training', 'medical_certificate', 'dcoc_expiry', 'marina_license_expiry'];
    
    foreach ($columns as $column) {
        if (in_array($column->Field, $dateFields)) {
            $nullable = $column->Null === 'YES' ? '✅ NULLABLE' : '❌ NOT NULL';
            echo "{$column->Field}: {$nullable}\n";
        }
    }
    
    echo "\n\n";
    echo "📊 CREWS WITH NULL HIRE_DATE:\n";
    echo str_repeat("-", 80) . "\n";
    
    $nullHireDateCrews = DB::table('crews')
        ->whereNull('hire_date')
        ->select('id', 'employee_id', 'full_name', 'hire_date')
        ->get();
    
    if ($nullHireDateCrews->count() > 0) {
        echo "Found {$nullHireDateCrews->count()} crew(s) with NULL hire_date:\n\n";
        foreach ($nullHireDateCrews as $crew) {
            echo "  - ID: {$crew->id} | Employee ID: {$crew->employee_id} | Name: {$crew->full_name}\n";
        }
    } else {
        echo "✅ No crews with NULL hire_date found.\n";
    }
    
    echo "\n\n";
    echo "📈 MIGRATION STATUS:\n";
    echo str_repeat("-", 80) . "\n";
    
    $migrations = DB::table('migrations')
        ->where('migration', 'like', '%hire_date%')
        ->orWhere('migration', 'like', '%nullable%')
        ->orderBy('batch', 'desc')
        ->get();
    
    if ($migrations->count() > 0) {
        foreach ($migrations as $migration) {
            echo "✅ {$migration->migration} (Batch: {$migration->batch})\n";
        }
    } else {
        echo "❌ No hire_date related migrations found!\n";
        echo "   You may need to run: php artisan migrate\n";
    }
    
    echo "\n\n";
    echo "✅ Diagnostic completed!\n";
    echo "\n⚠️ DELETE THIS FILE (check_database.php) NOW!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
?>
