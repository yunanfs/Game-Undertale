<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Database Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $_POST['result'] ?? ''; // 'win' or 'loss'
    $user_id = $_SESSION['user_id'];

    if ($result !== 'win' && $result !== 'loss') {
        echo json_encode(['success' => false, 'message' => 'Invalid result']);
        exit;
    }

    // Check if user has progress entry
    $checkStmt = $conn->prepare("SELECT id FROM user_progress WHERE user_id = ?");
    $checkStmt->bind_param("i", $user_id);
    $checkStmt->execute();
    $pgResult = $checkStmt->get_result();

    if ($pgResult->num_rows > 0) {
        // Update existing
        $col = ($result === 'win') ? 'battles_won' : 'battles_lost';
        $updateStmt = $conn->prepare("UPDATE user_progress SET $col = $col + 1 WHERE user_id = ?");
        $updateStmt->bind_param("i", $user_id);
        
        if ($updateStmt->execute()) {
             echo json_encode(['success' => true]);
        } else {
             echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
    } else {
        // Insert new (should rarely happen if progress created on register, but good fallback)
        $won = ($result === 'win') ? 1 : 0;
        $lost = ($result === 'loss') ? 1 : 0;
        
        $insertStmt = $conn->prepare("INSERT INTO user_progress (user_id, battles_won, battles_lost) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iii", $user_id, $won, $lost);
        
        if ($insertStmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Insert failed']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
}
?>
