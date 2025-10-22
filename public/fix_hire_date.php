<?php
/**
 * COMPREHENSIVE FIX SCRIPT
 * This script will:
 * 1. Check database structure
 * 2. Run migration if needed
 * 3. Clear all caches
 * 4. Provide diagnostic information
 * 
 * Access: https://yourdomain.com/fix_hire_date.php?password=YOUR_PASSWORD&action=check
 * 
 * Actions:
 * - check: Check current status
 * - fix: Run migration and clear caches
 * 
 * ⚠️ DELETE THIS FILE AFTER USE!
 */

// Security check - CHANGE THIS PASSWORD!
$PASSWORD = 'ChangeMe123!';

if (!isset($_GET['password']) || $_GET['password'] !== $PASSWORD) {
    die('<h1>Access Denied</h1><p>Use: fix_hire_date.php?password=YOUR_PASSWORD&action=check</p>');
}

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

header('Content-Type: text/html; charset=utf-8');

$action = $_GET['action'] ?? 'check';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Hire Date Fix Script</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #666; margin-top: 30px; }
        pre { background: #f9f9f9; padding: 15px; border-left: 4px solid #4CAF50; overflow-x: auto; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        .info { color: #2196F3; font-weight: bold; }
        .button { display: inline-block; padding: 12px 24px; margin: 10px 5px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .button:hover { background: #45a049; }
        .button.danger { background: #f44336; }
        .button.danger:hover { background: #da190b; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        tr:hover { background-color: #f5f5f5; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 Hire Date Fix Script</h1>
    
    <?php
    try {
        echo "<h2>📋 Current Status Check</h2>";
        
        // 1. Check hire_date column structure
        echo "<h3>1. Database Column Structure:</h3>";
        $columns = DB::select("SHOW COLUMNS FROM crews WHERE Field = 'hire_date'");
        
        if (!empty($columns)) {
            $column = $columns[0];
            $isNullable = $column->Null === 'YES';
            
            echo "<table>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Status</th></tr>";
            echo "<tr>";
            echo "<td>{$column->Field}</td>";
            echo "<td>{$column->Type}</td>";
            echo "<td>{$column->Null}</td>";
            echo "<td>" . ($column->Key ?? '') . "</td>";
            echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
            echo "<td>" . ($isNullable ? '<span class="success">✅ NULLABLE (GOOD)</span>' : '<span class="error">❌ NOT NULL (NEEDS FIX)</span>') . "</td>";
            echo "</tr>";
            echo "</table>";
            
            if (!$isNullable && $action !== 'fix') {
                echo '<p class="warning">⚠️ The hire_date column is NOT NULL. Click "Fix Now" to run the migration.</p>';
                echo '<a href="?password=' . urlencode($PASSWORD) . '&action=fix" class="button">Fix Now</a>';
            }
        }
        
        // 2. Check for crews with NULL hire_date
        echo "<h3>2. Crews with NULL hire_date:</h3>";
        $nullCrews = DB::table('crews')->whereNull('hire_date')->count();
        echo "<p>Found <strong>{$nullCrews}</strong> crew(s) with NULL hire_date</p>";
        
        if ($nullCrews > 0) {
            $crewList = DB::table('crews')->whereNull('hire_date')->select('id', 'employee_id', 'full_name')->limit(10)->get();
            echo "<table>";
            echo "<tr><th>ID</th><th>Employee ID</th><th>Full Name</th></tr>";
            foreach ($crewList as $crew) {
                echo "<tr><td>{$crew->id}</td><td>{$crew->employee_id}</td><td>{$crew->full_name}</td></tr>";
            }
            echo "</table>";
            if ($nullCrews > 10) {
                echo '<p class="info">... and ' . ($nullCrews - 10) . ' more</p>';
            }
        }
        
        // 3. Check migration status
        echo "<h3>3. Migration Status:</h3>";
        $migration = DB::table('migrations')
            ->where('migration', 'LIKE', '%hire_date_nullable%')
            ->first();
        
        if ($migration) {
            echo '<p class="success">✅ Migration has been run (Batch: ' . $migration->batch . ')</p>';
        } else {
            echo '<p class="error">❌ Migration has NOT been run yet</p>';
        }
        
        // 4. Check compiled views
        echo "<h3>4. Compiled Views Cache:</h3>";
        $viewPath = storage_path('framework/views');
        if (is_dir($viewPath)) {
            $files = glob($viewPath . '/*');
            $count = count($files);
            echo "<p>Found <strong>{$count}</strong> compiled view files</p>";
        }
        
        // ACTION: FIX
        if ($action === 'fix') {
            echo "<h2 class='success'>🔧 Running Fix...</h2>";
            
            // Run migration
            echo "<h3>Step 1: Running Migration</h3>";
            echo "<pre>";
            $exitCode = $kernel->call('migrate', [
                '--path' => 'database/migrations/2025_10_16_120000_make_hire_date_nullable_in_crews_table.php',
                '--force' => true
            ]);
            echo "</pre>";
            
            if ($exitCode === 0) {
                echo '<p class="success">✅ Migration completed successfully!</p>';
            } else {
                echo '<p class="warning">⚠️ Migration exit code: ' . $exitCode . '</p>';
            }
            
            // Clear caches
            echo "<h3>Step 2: Clearing Caches</h3>";
            echo "<pre>";
            
            echo "Clearing view cache...\n";
            $kernel->call('view:clear');
            
            echo "Clearing config cache...\n";
            $kernel->call('config:clear');
            
            echo "Clearing application cache...\n";
            $kernel->call('cache:clear');
            
            echo "Clearing route cache...\n";
            $kernel->call('route:clear');
            
            echo "Running optimize:clear...\n";
            $kernel->call('optimize:clear');
            
            echo "</pre>";
            echo '<p class="success">✅ All caches cleared!</p>';
            
            // Verify fix
            echo "<h3>Step 3: Verification</h3>";
            $columns = DB::select("SHOW COLUMNS FROM crews WHERE Field = 'hire_date'");
            if (!empty($columns)) {
                $column = $columns[0];
                $isNullable = $column->Null === 'YES';
                
                if ($isNullable) {
                    echo '<p class="success">✅✅✅ SUCCESS! hire_date is now NULLABLE</p>';
                    echo '<p class="info">You can now test the View button on your crew page!</p>';
                } else {
                    echo '<p class="error">❌ Something went wrong. hire_date is still NOT NULL</p>';
                }
            }
            
            echo '<a href="?password=' . urlencode($PASSWORD) . '&action=check" class="button">Check Status Again</a>';
        } else {
            echo '<hr>';
            echo '<h3>Actions:</h3>';
            echo '<a href="?password=' . urlencode($PASSWORD) . '&action=fix" class="button">Run Complete Fix</a>';
            echo '<a href="?password=' . urlencode($PASSWORD) . '&action=check" class="button">Refresh Status</a>';
        }
        
        echo '<hr>';
        echo '<h2 class="error">⚠️ IMPORTANT: DELETE THIS FILE NOW!</h2>';
        echo '<p>This file contains sensitive functionality. Delete <code>fix_hire_date.php</code> from your public folder immediately after fixing the issue.</p>';
        
    } catch (Exception $e) {
        echo '<div class="error">';
        echo '<h2>❌ Error Occurred</h2>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<h3>Stack Trace:</h3>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    }
    ?>
</div>
</body>
</html>
