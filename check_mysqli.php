<?php
if (class_exists('mysqli')) {
    echo "MySQLi class exists.\n";
} else {
    echo "MySQLi class NOT found.\n";
}

try {
    $conn = new mysqli("localhost", "root", "", "undertale_game");
    if ($conn->connect_error) {
        echo "Connection failed: " . $conn->connect_error . "\n";
    } else {
        echo "Connected successfully to database.\n";
        $conn->close();
    }
} catch (Exception $e) {
    echo "Exception caught: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "Error caught: " . $e->getMessage() . "\n";
}
?>
