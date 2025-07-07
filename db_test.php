<?php
// Simple database connection test
// Upload this file to your public_html and visit: https://sfxssli.shop/db_test.php

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=u341612763_sfx1',
        'u341612763_sfx1',
        'your_actual_database_password'
    );
    echo "✅ Database connection successful!";
    echo "<br>Server info: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage();
}
?>
