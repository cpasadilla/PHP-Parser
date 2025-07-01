<?php
// Add interest_start_date column to orders table
// This script uses direct SQL queries

// Database configuration
$host = 'localhost';
$dbname = 'sfx';
$username = 'root';
$password = '';

try {
    // Create PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully\n";
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM orders LIKE 'interest_start_date'");
    $column_exists = $stmt->rowCount() > 0;
    
    if (!$column_exists) {
        // Add the column
        $sql = "ALTER TABLE orders ADD COLUMN interest_start_date TIMESTAMP NULL";
        $pdo->exec($sql);
        echo "Successfully added interest_start_date column to orders table\n";
    } else {
        echo "Column interest_start_date already exists in orders table\n";
    }
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
