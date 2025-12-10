<?php
session_start();

// Redirect if already logged in as admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ../admin/dashboard.php');
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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        // Query with prepared statement
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? AND is_active = 1");
        
        if (!$stmt) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $admin = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $admin['password'])) {
                    // Login successful
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    
                    // Update last login
                    $update_stmt = $conn->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                    $update_stmt->bind_param("i", $admin['id']);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    // Redirect to admin dashboard
                    header('Location: ../admin/dashboard.php');
                    exit;
                } else {
                    $error = 'Username atau password salah!';
                }
            } else {
                $error = 'Username atau password salah!';
            }
            
            $stmt->close();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - UNDERTALE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #000;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            border: 5px solid #fff;
            padding: 50px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            background: #000;
        }
        
        .login-container h1 {
            font-family: 'Press Start 2P', monospace;
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 40px;
            letter-spacing: 5px;
        }
        
        .login-container h1 span {
            color: #ff0000;
        }
        
        .form-group {
            margin-bottom: 30px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            font-family: 'Press Start 2P', monospace;
            color: #fff;
            font-size: 0.7rem;
            margin-bottom: 10px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #fff;
            background: #000;
            color: #fff;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.7rem;
            box-sizing: border-box;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ff0000;
            box-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
        }
        
        .error-message {
            background: rgba(255, 0, 0, 0.2);
            border: 2px solid #f00;
            color: #f00;
            padding: 15px;
            margin-bottom: 20px;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.6rem;
        }
        
        .success-message {
            background: rgba(0, 255, 0, 0.2);
            border: 2px solid #0f0;
            color: #0f0;
            padding: 15px;
            margin-bottom: 20px;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.6rem;
        }
        
        .btn-login {
            width: 100%;
            padding: 15px;
            background: #fff;
            color: #000;
            border: 2px solid #fff;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        .btn-login:hover {
            background: #000;
            color: #fff;
        }
        
        .back-link {
            margin-top: 30px;
            text-align: center;
        }
        
        .back-link a {
            color: #0f0;
            text-decoration: none;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.6rem;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>★ ADMIN <span>LOGIN</span> ★</h1>
        
        <?php if ($error): ?>
            <div class="error-message">⚠ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">USERNAME</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">PASSWORD</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">★ LOGIN ★</button>
        </form>
        
        <div class="back-link">
            <a href="../index.php">← BACK TO HOME</a>
        </div>
    </div>
</body>
</html>
