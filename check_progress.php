<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Checking 'user_progress' table...\n";
$result = $conn->query("SHOW COLUMNS FROM user_progress");
if ($result) {
    echo "Columns in 'user_progress' table:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error getting columns for user_progress: " . $conn->error;
}

$conn->close();
?>
