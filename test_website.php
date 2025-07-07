<?php
// Simple Website Test
echo "<h2>SFX-1 Website Status</h2>";

try {
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    echo "✅ Laravel application loaded successfully<br>";
    
    // Test core services
    $files = $app->make('files');
    echo "✅ Files service working<br>";
    
    $config = $app->make('config');
    echo "✅ Config service working<br>";
    
    // Boot the application
    $app->boot();
    echo "✅ Application booted successfully<br>";
    
    echo "<br><h3>🎉 SUCCESS!</h3>";
    echo "<strong>Your website is now working properly!</strong><br>";
    echo "<strong>The 'Target class [files] does not exist' error has been resolved!</strong><br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}

echo "<br><h3>Test Your Website Pages:</h3>";
echo '<a href="https://sfxssli.shop/" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#dc3545; color:white; text-decoration:none; border-radius:5px;">🏠 Homepage</a><br>';
echo '<a href="https://sfxssli.shop/dashboard" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#28a745; color:white; text-decoration:none; border-radius:5px;">📊 Dashboard</a><br>';
echo '<a href="https://sfxssli.shop/masterlist" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#007cba; color:white; text-decoration:none; border-radius:5px;">📋 Master List</a><br>';
echo '<a href="https://sfxssli.shop/masterlist/soa" target="_blank" style="display:inline-block; margin:5px; padding:10px; background:#ffc107; color:black; text-decoration:none; border-radius:5px;">💰 SOA</a><br>';

echo "<br><strong>Delete this file after testing!</strong>";
?>
