<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../php/admin_login.php');
    exit;
}

// Database Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch Gallery Items
$result = $conn->query("SELECT * FROM gallery ORDER BY id ASC");
$gallery_items = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $gallery_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - UNDERTALE Admin</title>
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
        
        .gallery-table {
            width: 100%;
            border: 3px solid #fff;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .gallery-table th,
        .gallery-table td {
            border: 1px solid #fff;
            padding: 15px;
            text-align: left;
            font-size: 0.65rem;
            vertical-align: middle;
        }
        
        .gallery-table th {
            background: #fff;
            color: #000;
            font-weight: bold;
        }
        
        .gallery-table tr:nth-child(even) {
            background: #111;
        }
        
        .gallery-table tr:hover {
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

        /* Modal Styles */
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
            text-decoration: none;
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
        
        .thumb-preview {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 2px solid #fff;
        }
        .thumb-emoji {
            font-size: 30px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="top-navbar">
        <div class="navbar-logo">★ GALLERY MANAGEMENT ★</div>
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
            <h1>MANAGE GALLERY</h1>
            <p style="font-size: 0.7rem; color: #aaa; margin: 0;">Total: <?php echo count($gallery_items); ?> items</p>
        </div>

        <a href="gallery_add.php" class="btn-add">★ ADD NEW ITEM ★</a>

        <?php if (count($gallery_items) > 0): ?>
            <table class="gallery-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>IMAGE</th>
                        <th>TITLE</th>
                        <th>DESCRIPTION</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gallery_items as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td>
                                <?php 
                                $img = $item['image_url'];
                                if (mb_strlen($img) < 10 && !strpos($img, '/')) {
                                    echo '<div class="thumb-emoji">'.$img.'</div>';
                                } else {
                                    echo '<img src="'.$img.'" class="thumb-preview" alt="Thumb">';
                                }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo htmlspecialchars(substr($item['description'], 0, 50)) . '...'; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="gallery_edit.php?id=<?php echo $item['id']; ?>" class="btn-edit">EDIT</a>
                                    <a href="#" onclick="return openDeleteModal('gallery_delete.php?id=<?php echo $item['id']; ?>');" class="btn-delete">DELETE</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-message">
                No gallery items found. <a href="gallery_add.php" style="color: #0f0;">Create the first item</a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="logout-modal-overlay" id="deleteModalOverlay">
        <div class="logout-modal-content">
            <div class="logout-modal-icon">⚠</div>
            <h2>DELETE ITEM?</h2>
            <p>Are you sure you want to<br>delete this gallery item?</p>
            <div class="logout-modal-buttons">
                <a href="#" id="confirmDeleteBtn" class="logout-modal-btn confirm">★ YES ★</a>
                <button class="logout-modal-btn cancel" onclick="closeDeleteModal()">★ NO ★</button>
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

        // Close logout modal when clicking outside
        document.getElementById('logoutModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLogoutModal();
            }
        });

        function openDeleteModal(deleteUrl) {
            document.getElementById('confirmDeleteBtn').href = deleteUrl;
            document.getElementById('deleteModalOverlay').classList.add('active');
            return false;
        }

        function closeDeleteModal() {
            document.getElementById('deleteModalOverlay').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
