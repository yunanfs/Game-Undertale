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
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    
    if (empty($name)) {
        $error = 'Name harus diisi!';
    } else {
        $stmt = $conn->prepare("INSERT INTO characters (name, description, role, bio, image_url, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $name, $description, $role, $bio, $image_url, $admin_id);
        
        if ($stmt->execute()) {
            $success = 'Character berhasil ditambahkan!';
            $_POST = [];
        } else {
            $error = 'Gagal menambahkan character: ' . $stmt->error;
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
    <title>Add Character - UNDERTALE Admin</title>
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
        input[type="url"],
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
            min-height: 150px;
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
        <div class="navbar-logo">★ ADD NEW CHARACTER ★</div>
        <div class="navbar-right">
            <a href="characters.php" class="back-btn">← BACK</a>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h1>CREATE NEW CHARACTER</h1>
            
            <?php if ($error): ?>
                <div class="error-message">⚠ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message">✓ <?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">CHARACTER NAME *</label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="role">ROLE</label>
                        <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($_POST['role'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">DESCRIPTION</label>
                    <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($_POST['description'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="image_url">IMAGE URL</label>
                    <input type="url" id="image_url" name="image_url" value="<?php echo htmlspecialchars($_POST['image_url'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="bio">BIOGRAPHY</label>
                    <textarea id="bio" name="bio"><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
                </div>
                
                <div class="button-group">
                    <button type="submit">★ SAVE CHARACTER ★</button>
                    <a href="characters.php" class="btn-cancel">← CANCEL</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
