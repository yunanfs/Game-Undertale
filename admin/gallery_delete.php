<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../php/admin_login.php');
    exit;
}

require_once '../php/config.php';
global $conn;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM gallery WHERE id=$id");
}

header("Location: gallery.php");
exit;
?>
