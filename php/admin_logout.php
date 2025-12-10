<?php
session_start();

// Destroy admin session
$_SESSION['admin_logged_in'] = false;
$_SESSION['admin_id'] = null;
$_SESSION['admin_username'] = null;
session_destroy();

// Redirect to admin login
header('Location: admin_login.php');
exit;
