<?php
$conn = new mysqli('localhost', 'root', '', 'undertale_game');
$result = $conn->query("DESCRIBE music");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Query failed: " . $conn->error;
}
?>
