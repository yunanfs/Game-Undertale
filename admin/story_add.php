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

$admin_id = $_SESSION['admin_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $order_number = intval($_POST['order_number'] ?? 0);
    
    if (empty($title) || empty($content)) {
        $error = 'Title dan content harus diisi!';
    } else {
        $stmt = $conn->prepare("INSERT INTO stories (title, description, content, order_number, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $title, $description, $content, $order_number, $admin_id);
        
        if ($stmt->execute()) {
            $success = 'Story berhasil ditambahkan!';
            $_POST = [];
        } else {
            $error = 'Gagal menambahkan story: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Story - UNDERTALE Admin</title>
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
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .form-container {
            border: 3px solid #fff;
            padding: 40px;
            background: #111;
        }
        
        h1 {
            text-align: center;
            font-size: 1.5rem;
            margin: 0 0 30px 0;
            border-bottom: 2px solid #fff;
            padding-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            font-size: 0.7rem;
            margin-bottom: 10px;
            color: #fff;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #fff;
            background: #000;
            color: #fff;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.65rem;
            box-sizing: border-box;
        }
        
        input:focus,
        textarea:focus {
            outline: none;
            border-color: #0f0;
            box-shadow: 0 0 10px rgba(0, 255, 0, 0.3);
        }
        
        textarea {
            resize: vertical;
            min-height: 200px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            justify-content: center;
        }
        
        button, a {
            padding: 15px 30px;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.7rem;
            border: 2px solid;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            min-width: 150px;
            text-align: center;
        }
        
        button {
            background: #0f0;
            color: #000;
            border-color: #0f0;
        }
        
        button:hover {
            background: #000;
            color: #0f0;
        }
        
        .btn-cancel {
            background: #fff;
            color: #000;
            border-color: #fff;
        }
        
        .btn-cancel:hover {
            background: #000;
            color: #fff;
        }
        
        .error-message {
            background: rgba(255, 0, 0, 0.2);
            border: 2px solid #f00;
            color: #f00;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.7rem;
        }
        
        .success-message {
            background: rgba(0, 255, 0, 0.2);
            border: 2px solid #0f0;
            color: #0f0;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.7rem;
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
            min-width: auto;
        }
        
        .back-btn:hover {
            background: #000;
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="top-navbar">
        <div class="navbar-logo">★ ADD NEW STORY ★</div>
        <div class="navbar-right">
            <a href="stories.php" class="back-btn">← BACK</a>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h1>CREATE NEW STORY</h1>
            
            <?php if ($error): ?>
                <div class="error-message">⚠ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">✓ <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">STORY TITLE</label>
                    <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">DESCRIPTION</label>
                    <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($_POST['description'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="order_number">ORDER NUMBER</label>
                    <input type="number" id="order_number" name="order_number" min="0" value="<?php echo $_POST['order_number'] ?? '0'; ?>">
                </div>
                
                <div class="form-group">
                    <label for="content">STORY CONTENT</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                </div>
                
                <div class="button-group">
                    <button type="submit">★ SAVE STORY ★</button>
                    <a href="stories.php" class="btn-cancel">← CANCEL</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
