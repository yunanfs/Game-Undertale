<?php
$conn = new mysqli('localhost', 'root', '', 'undertale_game');
$result = $conn->query("SELECT * FROM music");
while ($row = $result->fetch_assoc()) {
    echo "Title: " . $row['title'] . "\n";
    echo "File Path: " . $row['file_path'] . "\n";
}
?>
