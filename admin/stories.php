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

$admin_username = $_SESSION['admin_username'];

// Get all stories
$result = mysqli_query($conn, "SELECT * FROM stories ORDER BY order_number ASC");
$stories = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $stories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stories - UNDERTALE Admin</title>
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
            max-width: 1200px;
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
        
        .btn-add {
            background: #0f0;
            color: #000;
            border: 2px solid #0f0;
            padding: 12px 20px;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.7rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            margin-bottom: 30px;
        }
        
        .btn-add:hover {
            background: #000;
            color: #0f0;
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
        
        .stories-table {
            width: 100%;
            border: 3px solid #fff;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .stories-table th,
        .stories-table td {
            border: 1px solid #fff;
            padding: 15px;
            text-align: left;
            font-size: 0.65rem;
        }
        
        .stories-table th {
            background: #fff;
            color: #000;
            font-weight: bold;
        }
        
        .stories-table tr:nth-child(even) {
            background: #111;
        }
        
        .stories-table tr:hover {
            background: #222;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit, .btn-delete {
            padding: 8px 12px;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.6rem;
            border: 2px solid;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background: #0f0;
            color: #000;
            border-color: #0f0;
        }
        
        .btn-edit:hover {
            background: #000;
            color: #0f0;
        }
        
        .btn-delete {
            background: #ff0000;
            color: #fff;
            border-color: #ff0000;
        }
        
        .btn-delete:hover {
            background: #fff;
            color: #ff0000;
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
        <div class="navbar-logo">★ STORIES MANAGEMENT ★</div>
        <div class="navbar-right">
            <a href="dashboard.php" class="back-btn">← DASHBOARD</a>
            <a href="../php/admin_logout.php" class="back-btn" style="background: #ff0000; border-color: #ff0000;">LOGOUT</a>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <h1>MANAGE STORIES</h1>
            <p style="font-size: 0.7rem; color: #aaa; margin: 0;">Total: <?php echo count($stories); ?> stories</p>
        </div>

        <a href="story_add.php" class="btn-add">★ ADD NEW STORY ★</a>

        <?php if (count($stories) > 0): ?>
            <table class="stories-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>TITLE</th>
                        <th>DESCRIPTION</th>
                        <th>ORDER</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stories as $index => $story): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($story['title']); ?></td>
                            <td><?php echo htmlspecialchars(substr($story['description'], 0, 50)) . '...'; ?></td>
                            <td><?php echo $story['order_number']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="story_edit.php?id=<?php echo $story['id']; ?>" class="btn-edit">EDIT</a>
                                    <a href="story_delete.php?id=<?php echo $story['id']; ?>" class="btn-delete" onclick="return confirm('Hapus cerita ini?');">DELETE</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-message">
                No stories found. <a href="story_add.php" style="color: #0f0;">Create the first story</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
