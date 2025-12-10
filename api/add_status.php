<?php
session_start();
header('Content-Type: application/json');

// Check if logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'undertale_game');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$status = isset($_POST['status']) ? trim($_POST['status']) : '';

if (empty($status) || strlen($status) > 200) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Connection failed");
    }
    
    $user_id = $_SESSION['user_id'];
    
    // Check if user_progress exists
    $checkStmt = $conn->prepare("SELECT id FROM user_progress WHERE user_id = ?");
    $checkStmt->bind_param("i", $user_id);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows > 0) {
        // Update status
        $stmt = $conn->prepare("UPDATE user_progress SET status = ? WHERE user_id = ?");
        $stmt->bind_param("si", $status, $user_id);
    } else {
        // Insert new progress with status
        $stmt = $conn->prepare("INSERT INTO user_progress (user_id, status) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $status);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status added']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Operation failed']);
    }
    
    $checkStmt->close();
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
