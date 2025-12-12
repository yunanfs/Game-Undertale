<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "undertale_game";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Database connected successfully.\n";

$sql = "SELECT id, title, file_path FROM music";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Track: " . $row["title"] . "\n";
        echo "DB Path: " . $row["file_path"] . "\n";
        
        $full_path = __DIR__ . "/" . $row["file_path"];
        echo "Full Path Check: " . $full_path . "\n";
        
        if (file_exists($full_path)) {
            echo "Status: FILE EXISTS\n";
        } else {
            echo "Status: FILE MISSING\n";
        }
        echo "-------------------\n";
    }
} else {
    echo "0 results in database\n";
}
$conn->close();
?>
