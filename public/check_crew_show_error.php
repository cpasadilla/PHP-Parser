<?php
/**
 * Diagnostic script to check crew.show route errors
 * Upload this to your Hostinger public folder and access it via browser
 * Example: https://yourdomain.com/check_crew_show_error.php?crew_id=1
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Crew Show Route Diagnostic</h2>";
echo "<hr>";

// Check if Laravel bootstrap exists
$laravelPath = __DIR__ . '/../bootstrap/app.php';
if (!file_exists($laravelPath)) {
    die("❌ ERROR: Laravel bootstrap file not found at: $laravelPath");
}

echo "✅ Laravel bootstrap file found<br>";

try {
    // Bootstrap Laravel
    require $laravelPath;
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    echo "✅ Laravel application bootstrapped<br>";
    
    // Get crew ID from URL parameter
    $crewId = $_GET['crew_id'] ?? 1;
    echo "🔍 Testing with Crew ID: $crewId<br><br>";
    
    // Try to fetch crew data
    echo "<h3>Testing Database Connection and Crew Model</h3>";
    
    // Check if we can connect to database
    try {
        $pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD')
        );
        echo "✅ Database connection successful<br>";
    } catch (PDOException $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    }
    
    // Try to load the crew
    echo "<br><h3>Testing Crew Data Loading</h3>";
    
    $crew = \App\Models\Crew::find($crewId);
    
    if (!$crew) {
        echo "❌ No crew found with ID: $crewId<br>";
        echo "Please try a different crew_id in the URL parameter<br>";
    } else {
        echo "✅ Crew found: " . $crew->full_name . "<br>";
        echo "✅ Employee ID: " . ($crew->employee_id ?? 'NULL') . "<br>";
        echo "✅ Hire Date: " . ($crew->hire_date ? $crew->hire_date->format('Y-m-d') : 'NULL') . "<br><br>";
        
        // Test relationships
        echo "<h3>Testing Relationships</h3>";
        
        try {
            $crew->load(['ship', 'documents', 'leaves', 'leaveApplications', 'embarkations.ship', 'currentEmbarkation.ship']);
            echo "✅ All relationships loaded successfully<br>";
            echo "- Ship: " . ($crew->ship ? 'Loaded' : 'NULL') . "<br>";
            echo "- Documents count: " . $crew->documents->count() . "<br>";
            echo "- Leaves count: " . $crew->leaves->count() . "<br>";
            echo "- Leave Applications count: " . $crew->leaveApplications->count() . "<br>";
            echo "- Embarkations count: " . $crew->embarkations->count() . "<br>";
            echo "- Current Embarkation: " . ($crew->currentEmbarkation ? 'Active' : 'None') . "<br>";
        } catch (Exception $e) {
            echo "❌ Error loading relationships: " . $e->getMessage() . "<br>";
            echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
        }
        
        echo "<br><h3>Testing View Rendering</h3>";
        try {
            $viewPath = resource_path('views/crew/show.blade.php');
            if (file_exists($viewPath)) {
                echo "✅ View file exists: $viewPath<br>";
            } else {
                echo "❌ View file NOT found: $viewPath<br>";
            }
            
            // Try to render the view
            $view = view('crew.show', compact('crew'));
            echo "✅ View compiled successfully<br>";
            echo "<br><h3>🎉 SUCCESS - No errors detected!</h3>";
            echo "<p>The crew.show route should be working. If you still see errors, check:</p>";
            echo "<ul>";
            echo "<li>Clear Laravel cache: php artisan optimize:clear</li>";
            echo "<li>Check storage/logs/laravel.log for detailed errors</li>";
            echo "<li>Ensure .htaccess file exists in public folder</li>";
            echo "<li>Verify PHP version is 8.1 or higher</li>";
            echo "</ul>";
            
        } catch (Exception $e) {
            echo "❌ Error rendering view: " . $e->getMessage() . "<br>";
            echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
        }
    }
    
} catch (Exception $e) {
    echo "<h3>❌ CRITICAL ERROR</h3>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Upload this file to your Hostinger public_html or public folder</li>";
echo "<li>Access it via: https://yourdomain.com/check_crew_show_error.php?crew_id=1</li>";
echo "<li>Change the crew_id parameter to test different crew members</li>";
echo "<li>Check the output above to identify the exact issue</li>";
echo "</ol>";
?>
