<?php
session_start();

// Database Connection
require_once '../php/config.php';
global $conn;

// Check admin login
checkAdminLogin();

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Get statistics
$stories_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM stories"))['count'] ?? 0;
$characters_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM characters"))['count'] ?? 0;
$users_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'] ?? 0;
$music_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM music"))['count'] ?? 0;
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

        /* Logout Modal */
        .logout-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .logout-modal-overlay.active {
            display: flex;
        }

        .logout-modal-content {
            background: #000;
            border: 4px solid #ff0000;
            padding: 50px 40px 60px 40px;
            max-width: 500px;
            width: 90%;
            text-align: center;
        }

        .logout-modal-icon {
            font-size: 60px;
            margin-bottom: 30px;
            animation: heartbeat 1.5s infinite;
        }

        .logout-modal-content h2 {
            color: #fff;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 1.5rem;
            margin-bottom: 20px;
            text-shadow: 3px 3px 0px #ff0000;
            letter-spacing: 2px;
        }

        .logout-modal-content p {
            color: #fff;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.8rem;
            margin-bottom: 50px;
            letter-spacing: 1px;
            line-height: 1.6;
        }

        .logout-modal-buttons {
            display: flex;
            gap: 25px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .logout-modal-btn {
            background: #000;
            color: #fff;
            border: 4px solid #fff;
            padding: 15px 30px;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
            position: relative;
            box-shadow: 5px 5px 0px rgba(255, 255, 255, 0.3);
            letter-spacing: 1px;
        }

        .logout-modal-btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 8px 8px 0px rgba(255, 255, 255, 0.5);
        }

        .logout-modal-btn:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0px rgba(255, 255, 255, 0.3);
        }

        .logout-modal-btn.confirm {
            border-color: #ff0000;
            color: #ff0000;
        }

        .logout-modal-btn.confirm:hover {
            background: #ff0000;
            color: #fff;
            box-shadow: 8px 8px 0px rgba(255, 0, 0, 0.5);
        }

        .logout-modal-btn.confirm:active {
            background: #ff0000;
            color: #fff;
        }

        .logout-modal-btn.cancel {
            border-color: #fff;
        }

        .logout-modal-btn.cancel:hover {
            background: #fff;
            color: #000;
            box-shadow: 8px 8px 0px rgba(255, 255, 255, 0.5);
        }

        .logout-modal-btn.cancel:active {
            background: #fff;
            color: #000;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
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
            <button onclick="openLogoutModal()" class="logout-btn">LOGOUT</button>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="logout-modal-overlay" id="logoutModalOverlay">
        <div class="logout-modal-content">
            <div class="logout-modal-icon">❤</div>
            <h2>Are you sure?</h2>
            <p>Do you really want to<br>leave?</p>
            <div class="logout-modal-buttons">
                <button class="logout-modal-btn confirm" onclick="confirmAdminLogout()">★ YES ★</button>
                <button class="logout-modal-btn cancel" onclick="closeLogoutModal()">★ NO ★</button>
            </div>
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

            <!-- Gallery Card -->
            <div class="dashboard-card">
                <h2>GALLERY</h2>
                <div class="stat-number"><?php echo mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM gallery"))['count'] ?? 0; ?></div>
                <p style="font-size: 0.7rem; color: #aaa;">Gallery items</p>
                <div class="card-actions">
                    <a href="gallery.php" class="btn primary">VIEW</a>
                    <a href="gallery_add.php" class="btn">ADD NEW</a>
                </div>
            </div>

            <!-- Music Card -->
            <div class="dashboard-card">
                <h2>MUSIC</h2>
                <div class="stat-number"><?php echo $music_count; ?></div>
                <p style="font-size: 0.7rem; color: #aaa;">Tracks uploaded</p>
                <div class="card-actions">
                    <a href="musics.php" class="btn primary">VIEW</a>
                    <a href="music_add.php" class="btn">ADD NEW</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openLogoutModal() {
            document.getElementById('logoutModalOverlay').classList.add('active');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModalOverlay').classList.remove('active');
        }

        function confirmAdminLogout() {
            window.location.href = '../php/admin_logout.php';
        }

        // Close modal when clicking outside
        document.getElementById('logoutModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogoutModal();
            }
        });
    </script>
</body>
</html>
