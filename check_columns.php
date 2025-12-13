<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check users table columns
$result = $conn->query("SHOW COLUMNS FROM users");
if ($result) {
    echo "Columns in 'users' table:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error getting columns: " . $conn->error;
}

$conn->close();
?>
