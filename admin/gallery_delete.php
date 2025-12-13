<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../php/admin_login.php');
    exit;
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';
$conn = new mysqli($host, $user, $pass, $db);

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM gallery WHERE id=$id");
}

header("Location: gallery.php");
exit;
?>
