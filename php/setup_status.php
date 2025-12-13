<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain');

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to database.\n";

// 1. Check if user_progress table exists, create if not
$tableExists = $conn->query("SHOW TABLES LIKE 'user_progress'")->num_rows > 0;
if (!$tableExists) {
    echo "Creating user_progress table...\n";
    $sql = "CREATE TABLE user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        level INT DEFAULT 1,
        exp INT DEFAULT 0,
        gold INT DEFAULT 0,
        location VARCHAR(50) DEFAULT 'ruins',
        status VARCHAR(255) DEFAULT '',
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    if ($conn->query($sql) === TRUE) {
        echo "Table user_progress created successfully.\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
} else {
    echo "Table user_progress exists.\n";
}

// 2. Check if status column exists in user_progress
$columnExists = $conn->query("SHOW COLUMNS FROM user_progress LIKE 'status'")->num_rows > 0;
if (!$columnExists) {
    echo "Adding status column to user_progress...\n";
    $sql = "ALTER TABLE user_progress ADD COLUMN status VARCHAR(255) DEFAULT ''";
    if ($conn->query($sql) === TRUE) {
        echo "Column status added successfully.\n";
    } else {
        echo "Error adding column: " . $conn->error . "\n";
    }
} else {
    echo "Column status already exists.\n";
}

$conn->close();
?>
