<?php
// Admin DB Connection Helper
function getAdminConnection() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'undertale_game';
    
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Check admin login
function checkAdminLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ../php/admin_login.php');
        exit;
    }
}
?>
