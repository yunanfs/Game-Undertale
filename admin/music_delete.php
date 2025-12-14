<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../php/admin_login.php');
    exit;
}

// Database Connection
require_once '../php/config.php';
global $conn;

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$music_id = intval($_GET['id'] ?? 0);

if ($music_id > 0) {
    // Get file path first
    $stmt = $conn->prepare("SELECT file_path FROM music WHERE id = ?");
    $stmt->bind_param("i", $music_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $music = $result->fetch_assoc();
    $stmt->close();
    
    if ($music) {
        // Delete file if exists
        if (!empty($music['file_path']) && file_exists(__DIR__ . '/../' . $music['file_path'])) {
            unlink(__DIR__ . '/../' . $music['file_path']);
        }
        
        // Delete record
        $stmt = $conn->prepare("DELETE FROM music WHERE id = ?");
        $stmt->bind_param("i", $music_id);
        $stmt->execute();
        $stmt->close();
    }
}

header('Location: musics.php');
exit;
?>
