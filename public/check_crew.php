<?php
/**
 * DIAGNOSTIC SCRIPT - Check Crew Data
 * Access: https://yourdomain.com/check_crew.php?id=CREW_ID
 * 
 * ⚠️ DELETE THIS FILE AFTER USE!
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Crew Data Diagnostic</h1>";
echo "<pre>";

try {
    $crewId = $_GET['id'] ?? null;
    
    if (!$crewId) {
        die("Usage: check_crew.php?id=CREW_ID\nExample: check_crew.php?id=1");
    }
    
    $crew = DB::table('crews')->where('id', $crewId)->first();
    
    if (!$crew) {
        die("❌ Crew ID {$crewId} not found!");
    }
    
    echo "✅ Crew Found: {$crew->full_name}\n\n";
    echo "📋 Date Fields Check:\n";
    echo "-------------------\n";
    echo "hire_date: " . ($crew->hire_date ?? 'NULL') . "\n";
    echo "contract_expiry: " . ($crew->contract_expiry ?? 'NULL') . "\n";
    echo "basic_safety_training: " . ($crew->basic_safety_training ?? 'NULL') . "\n";
    echo "medical_certificate: " . ($crew->medical_certificate ?? 'NULL') . "\n";
    echo "dcoc_expiry: " . ($crew->dcoc_expiry ?? 'NULL') . "\n";
    echo "marina_license_expiry: " . ($crew->marina_license_expiry ?? 'NULL') . "\n";
    
    echo "\n📊 Full Crew Data:\n";
    echo "-------------------\n";
    print_r($crew);
    
    echo "\n✅ Script completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
echo "<p><strong>⚠️ DELETE THIS FILE (check_crew.php) AFTER USE!</strong></p>";
?>
