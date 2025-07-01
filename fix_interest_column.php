<?php
// Direct database connection to add the column
$host = "localhost";
$username = "root";
$password = "";
$database = "sfx";

try {
    // Connect to MySQL database
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n";
    
    // Check if column exists
    $checkSQL = "SELECT COUNT(*) AS column_exists 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = '$database' 
                AND TABLE_NAME = 'orders' 
                AND COLUMN_NAME = 'interest_start_date'";
                
    $stmt = $conn->query($checkSQL);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['column_exists'] == 0) {
        // Add the column
        $sql = "ALTER TABLE orders ADD COLUMN interest_start_date TIMESTAMP NULL";
        $conn->exec($sql);
        echo "Column interest_start_date added successfully.\n";
    } else {
        echo "Column interest_start_date already exists.\n";
    }
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>
