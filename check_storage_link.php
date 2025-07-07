<?php
// Storage Link Diagnostic Script
// Upload this to your public_html folder and access it via browser

echo "<h2>🔍 Storage Link Diagnostic Report</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { color: blue; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .code { background: #f4f4f4; padding: 10px; border-radius: 5px; margin: 10px 0; }
</style>";

echo "<hr>";

// Check if storage directory exists in public_html
$publicStoragePath = $_SERVER['DOCUMENT_ROOT'] . '/storage';
$storageAppPublicPath = $_SERVER['DOCUMENT_ROOT'] . '/storage/app/public';

echo "<h3>📁 Directory Structure Check:</h3>";
echo "<div class='code'>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Public Storage Path: " . $publicStoragePath . "<br>";
echo "Storage App Public Path: " . $storageAppPublicPath . "<br>";
echo "</div>";

echo "<h3>🔗 Storage Link Status:</h3>";

// Check if public/storage exists
if (file_exists($publicStoragePath)) {
    echo "<div class='success'>✅ Storage link/directory exists at: " . $publicStoragePath . "</div>";
    
    // Check if it's a symbolic link
    if (is_link($publicStoragePath)) {
        echo "<div class='info'>🔗 This is a symbolic link</div>";
        echo "<div class='code'>Link target: " . readlink($publicStoragePath) . "</div>";
    } else {
        echo "<div class='warning'>📁 This is a regular directory (not a symbolic link)</div>";
    }
    
    // List contents
    echo "<h4>📂 Contents of storage directory:</h4>";
    echo "<div class='code'>";
    $files = scandir($publicStoragePath);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- " . $file . "<br>";
        }
    }
    echo "</div>";
    
} else {
    echo "<div class='error'>❌ Storage link/directory does not exist</div>";
}

// Check if storage/app/public exists
echo "<h3>📂 Storage App Public Directory:</h3>";
if (file_exists($storageAppPublicPath)) {
    echo "<div class='success'>✅ Storage app/public directory exists</div>";
    
    // List contents
    echo "<h4>📂 Contents of storage/app/public:</h4>";
    echo "<div class='code'>";
    $files = scandir($storageAppPublicPath);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- " . $file . "<br>";
        }
    }
    echo "</div>";
} else {
    echo "<div class='error'>❌ Storage app/public directory does not exist</div>";
}

// Test file access
echo "<h3>🧪 File Access Test:</h3>";
$testFile = $publicStoragePath . '/test.txt';
$testContent = "Storage link test - " . date('Y-m-d H:i:s');

try {
    // Try to create a test file
    if (file_put_contents($testFile, $testContent)) {
        echo "<div class='success'>✅ Can write to storage directory</div>";
        
        // Try to read it back
        if (file_get_contents($testFile) === $testContent) {
            echo "<div class='success'>✅ Can read from storage directory</div>";
        } else {
            echo "<div class='error'>❌ Cannot read from storage directory</div>";
        }
        
        // Clean up
        unlink($testFile);
    } else {
        echo "<div class='error'>❌ Cannot write to storage directory</div>";
    }
} catch (Exception $e) {
    echo "<div class='error'>❌ Error testing file access: " . $e->getMessage() . "</div>";
}

// Laravel URL test
echo "<h3>🌐 Laravel Asset URL Test:</h3>";
echo "<div class='code'>";
echo "Storage URL should be: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . "/storage/<br>";
echo "Example file URL: " . (isset($_SERVER['HTTPS']) ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . "/storage/example.jpg<br>";
echo "</div>";

// Recommendations
echo "<h3>💡 Recommendations:</h3>";
echo "<div class='code'>";
if (!file_exists($publicStoragePath)) {
    echo "❗ You need to create the storage link. Use the create_storage_link.php script.<br>";
} else {
    echo "✅ Storage link is working properly!<br>";
}
echo "</div>";

echo "<hr>";
echo "<p><strong>🗑️ Remember to delete this diagnostic file after checking!</strong></p>";
?>
