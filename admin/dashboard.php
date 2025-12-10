<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../php/admin_login.php');
    exit;
}

// Direct database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Get statistics
$stories_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM stories"))['count'] ?? 0;
$characters_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM characters"))['count'] ?? 0;
$users_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UNDERTALE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: #000;
            color: #fff;
            margin: 0;
            padding: 20px;
            font-family: 'Press Start 2P', monospace;
            padding-top: 80px;
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 50px;
            border-bottom: 3px solid #fff;
            padding-bottom: 30px;
        }
        
        .admin-header h1 {
            font-size: 2rem;
            margin: 0 0 10px 0;
            letter-spacing: 5px;
        }
        
        .admin-info {
            font-size: 0.7rem;
            color: #aaa;
            margin-bottom: 20px;
        }
        
        .logout-btn {
            background: #ff0000;
            color: #fff;
            border: 2px solid #ff0000;
            padding: 10px 20px;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.6rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: #fff;
            color: #ff0000;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto 50px;
        }
        
        .dashboard-card {
            border: 3px solid #fff;
            padding: 30px;
            text-align: center;
            background: #111;
        }
        
        .dashboard-card h2 {
            font-size: 1rem;
            margin: 0 0 20px 0;
            border-bottom: 2px solid #fff;
            padding-bottom: 15px;
            letter-spacing: 3px;
        }
        
        .stat-number {
            font-size: 2rem;
            color: #0f0;
            margin: 20px 0;
            font-weight: bold;
        }
        
        .card-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn {
            background: #fff;
            color: #000;
            border: 2px solid #fff;
            padding: 10px 15px;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.6rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            flex: 1;
            min-width: 100px;
        }
        
        .btn:hover {
            background: #000;
            color: #fff;
        }
        
        .btn.primary {
            background: #0f0;
            border-color: #0f0;
            color: #000;
        }
        
        .btn.primary:hover {
            background: #000;
            color: #0f0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .top-navbar {
            background: #000;
            border-bottom: 3px solid #fff;
            padding: 8px 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Press Start 2P', cursive, monospace;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .navbar-logo {
            color: #fff;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .navbar-center {
            display: flex;
            gap: 20px;
            flex: 1;
            justify-content: center;
            margin-left: -80px;
        }

        .navbar-center a {
            color: #fff;
            text-decoration: none;
            border: 2px solid #fff;
            padding: 10px 18px;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.65rem;
            transition: all 0.3s;
        }

        .navbar-center a:hover {
            background: #fff;
            color: #000;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
        }

        .admin-badge {
            background: #ff0000;
            color: #fff;
            padding: 8px 15px;
            border: 2px solid #ff0000;
            font-size: 0.6rem;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="top-navbar">
        <div class="navbar-logo">★ UNDERTALE ADMIN ★</div>
        <div class="navbar-right">
            <span class="admin-badge">★ <?php echo htmlspecialchars($admin_username); ?> ★</span>
            <a href="../php/admin_logout.php" class="logout-btn">LOGOUT</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="admin-header">
            <h1>ADMIN DASHBOARD</h1>
            <div class="admin-info">
                Welcome back, <strong><?php echo htmlspecialchars($admin_username); ?></strong>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Stories Card -->
            <div class="dashboard-card">
                <h2>STORIES</h2>
                <div class="stat-number"><?php echo $stories_count; ?></div>
                <p style="font-size: 0.7rem; color: #aaa;">Stories created</p>
                <div class="card-actions">
                    <a href="stories.php" class="btn primary">VIEW</a>
                    <a href="story_add.php" class="btn">ADD NEW</a>
                </div>
            </div>

            <!-- Characters Card -->
            <div class="dashboard-card">
                <h2>CHARACTERS</h2>
                <div class="stat-number"><?php echo $characters_count; ?></div>
                <p style="font-size: 0.7rem; color: #aaa;">Characters created</p>
                <div class="card-actions">
                    <a href="characters.php" class="btn primary">VIEW</a>
                    <a href="character_add.php" class="btn">ADD NEW</a>
                </div>
            </div>

            <!-- Users Card -->
            <div class="dashboard-card">
                <h2>USERS</h2>
                <div class="stat-number"><?php echo $users_count; ?></div>
                <p style="font-size: 0.7rem; color: #aaa;">Registered users</p>
                <div class="card-actions">
                    <a href="users.php" class="btn primary">VIEW</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
