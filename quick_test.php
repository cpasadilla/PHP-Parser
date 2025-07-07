<?php
// Quick Laravel Test
try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    echo "✅ Laravel application working\n";
    
    // Test files service
    $files = $app->make('files');
    echo "✅ Files service working: " . get_class($files) . "\n";
    
    // Boot application
    $app->boot();
    echo "✅ Application booted successfully\n";
    
    echo "🎉 SUCCESS: Your Laravel app is working perfectly!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
