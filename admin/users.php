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

// Get all users
$result = mysqli_query($conn, "SELECT u.id, u.username, u.email, u.created_at, u.last_login, up.battles_won, up.battles_lost FROM users u LEFT JOIN user_progress up ON u.id = up.user_id ORDER BY u.created_at DESC");
$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - UNDERTALE Admin</title>
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
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #fff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 1.8rem;
            margin: 0 0 15px 0;
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

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
        }
        
        .back-btn {
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
        }
        
        .back-btn:hover {
            background: #000;
            color: #fff;
        }
        
        .users-table {
            width: 100%;
            border: 3px solid #fff;
            border-collapse: collapse;
            margin-bottom: 30px;
            overflow-x: auto;
        }
        
        .users-table th,
        .users-table td {
            border: 1px solid #fff;
            padding: 12px;
            text-align: left;
            font-size: 0.6rem;
        }
        
        .users-table th {
            background: #fff;
            color: #000;
            font-weight: bold;
        }
        
        .users-table tr:nth-child(even) {
            background: #111;
        }
        
        .users-table tr:hover {
            background: #222;
        }
        
        .empty-message {
            text-align: center;
            padding: 50px;
            border: 3px dashed #fff;
            font-size: 0.8rem;
            color: #aaa;
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
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="top-navbar">
        <div class="navbar-logo">★ USERS MANAGEMENT ★</div>
        <div class="navbar-right">
            <a href="dashboard.php" class="back-btn">← DASHBOARD</a>
            <button onclick="openLogoutModal()" class="back-btn" style="background: #ff0000; border-color: #ff0000; cursor: pointer;">LOGOUT</button>
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

    <div class="container">
        <div class="header">
            <h1>VIEW USERS</h1>
            <p style="font-size: 0.7rem; color: #aaa; margin: 0;">Total: <?php echo count($users); ?> users</p>
        </div>

        <?php if (count($users) > 0): ?>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>USERNAME</th>
                        <th>EMAIL</th>
                        <th>JOINED</th>
                        <th>LAST LOGIN</th>
                        <th>BATTLES WON</th>
                        <th>BATTLES LOST</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                            <td><?php echo $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : '-'; ?></td>
                            <td><?php echo $user['battles_won'] ?? 0; ?></td>
                            <td><?php echo $user['battles_lost'] ?? 0; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-message">
                No users found.
            </div>
        <?php endif; ?>
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
