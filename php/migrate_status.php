<?php
/**
 * Add status column to user_progress table if not exists
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'undertale_game');

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Check if status column exists
    $result = $conn->query("SHOW COLUMNS FROM user_progress LIKE 'status'");
    
    if ($result->num_rows === 0) {
        // Add status column
        $sql = "ALTER TABLE user_progress ADD COLUMN status VARCHAR(200) DEFAULT NULL AFTER battles_lost";
        if ($conn->query($sql)) {
            echo "<h2>✓ Status column added successfully!</h2>";
        } else {
            echo "<h2>✗ Error adding status column: " . $conn->error . "</h2>";
        }
    } else {
        echo "<h2>✓ Status column already exists!</h2>";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<h2>✗ Error: " . $e->getMessage() . "</h2>";
}
?>
