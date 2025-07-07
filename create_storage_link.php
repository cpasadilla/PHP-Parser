<?php
// Temporary script to create storage link on Hostinger
// Upload this to your public_html folder and run it once

$target = $_SERVER['DOCUMENT_ROOT'] . '/storage/app/public';
$link = $_SERVER['DOCUMENT_ROOT'] . '/storage';

// Check if storage directory exists in public_html
if (!file_exists($link)) {
    // Create the symbolic link manually
    if (symlink($target, $link)) {
        echo "✅ Storage link created successfully!<br>";
        echo "Target: " . $target . "<br>";
        echo "Link: " . $link . "<br>";
    } else {
        echo "❌ Failed to create storage link.<br>";
        echo "This might be due to server restrictions.<br>";
    }
} else {
    echo "✅ Storage link already exists!<br>";
}

// Alternative: Copy files instead of symbolic link
$publicStorage = $_SERVER['DOCUMENT_ROOT'] . '/storage';
$appPublic = $_SERVER['DOCUMENT_ROOT'] . '/storage/app/public';

if (!file_exists($publicStorage) && file_exists($appPublic)) {
    echo "<br>📁 Trying to copy files instead of creating symbolic link...<br>";
    
    function copyDirectory($src, $dst) {
        $dir = opendir($src);
        if (!file_exists($dst)) {
            mkdir($dst, 0755, true);
        }
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    copyDirectory($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    
    try {
        copyDirectory($appPublic, $publicStorage);
        echo "✅ Files copied successfully as alternative to symbolic link!<br>";
    } catch (Exception $e) {
        echo "❌ Error copying files: " . $e->getMessage() . "<br>";
    }
}

echo "<br>🗑️ Remember to delete this file after running it for security!";
?>
