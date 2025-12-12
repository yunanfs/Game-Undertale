<?php
header('Content-Type: application/json');

$char_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($char_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid character ID'
    ]);
    exit;
}

try {
    $conn = new mysqli('localhost', 'root', '', 'undertale_game');
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed");
    }
    
    $conn->set_charset("utf8mb4");
    
    $stmt = $conn->prepare("SELECT id, name, description, role, image_url, bio FROM characters WHERE id = ?");
    $stmt->bind_param("i", $char_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $character = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'character' => $character
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Character not found'
        ]);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error'
    ]);
}
?>
