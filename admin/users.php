<?php
session_start();

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
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="top-navbar">
        <div class="navbar-logo">★ USERS MANAGEMENT ★</div>
        <div class="navbar-right">
            <a href="dashboard.php" class="back-btn">← DASHBOARD</a>
            <a href="../php/admin_logout.php" class="back-btn" style="background: #ff0000; border-color: #ff0000;">LOGOUT</a>
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
</body>
</html>
