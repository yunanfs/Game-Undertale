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

$character_id = intval($_GET['id'] ?? 0);

if ($character_id > 0) {
    $stmt = $conn->prepare("DELETE FROM characters WHERE id = ?");
    $stmt->bind_param("i", $character_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header('Location: characters.php');
exit;
