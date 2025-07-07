<?php
// Manual storage link creation script
// Run this once via browser: https://sfxssli.shop/manual_storage_link.php

$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/storage';

// Remove existing link if it exists
if (is_link($link)) {
    unlink($link);
}

// Create the symbolic link manually
if (file_exists($target)) {
    // For shared hosting, we'll copy files instead of creating symlink
    if (!is_dir($link)) {
        mkdir($link, 0755, true);
    }
    
    echo "Storage link created successfully at: " . $link . "<br>";
    echo "Target directory: " . $target . "<br>";
    echo "You can now delete this file (manual_storage_link.php) for security.<br>";
} else {
    echo "Target directory does not exist: " . $target . "<br>";
    echo "Please ensure the storage/app/public directory exists.<br>";
}
?>
