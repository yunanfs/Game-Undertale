<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create gallery table
$sql = "CREATE TABLE IF NOT EXISTS gallery (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table gallery created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

// Check if empty, insert samples if needed
$result = $conn->query("SELECT count(*) as count FROM gallery");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $stmt = $conn->prepare("INSERT INTO gallery (title, description, image_url) VALUES (?, ?, ?)");
    
    $samples = [
        ['The Ruins', 'The start of your journey.', '🏛️'],
        ['Snowdin', 'A snowy landscape with friendly folks.', '❄️'],
        ['Waterfall', 'A place where the stars fall.', '💧']
    ];
    
    foreach ($samples as $sample) {
        $stmt->bind_param("sss", $sample[0], $sample[1], $sample[2]);
        $stmt->execute();
    }
    echo "<br>Sample data inserted.";
}

$conn->close();
?>
