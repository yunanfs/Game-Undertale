<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: index.php');
    exit;
} elseif (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin/dashboard.php');
    exit;
}

// Database connection
// Database connection
require_once 'php/config.php';
global $conn;

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$error = '';
$success = '';
$login_type = ''; // 'player' or 'admin'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        // First check if admin exists
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? AND is_active = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $admin_result = $stmt->get_result();
        $stmt->close();
        
        // If admin found, try admin login
        if ($admin_result->num_rows === 1) {
            $admin = $admin_result->fetch_assoc();
            
            if (password_verify($password, $admin['password'])) {
                // Admin login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                // Update last login
                $update_stmt = $conn->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                $update_stmt->bind_param("i", $admin['id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Redirect to admin dashboard
                header('Location: admin/dashboard.php');
                exit;
            } else {
                $error = 'Username atau password salah!';
            }
        } else {
            // Admin not found, check if player exists
            $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $player_result = $stmt->get_result();
            $stmt->close();
            
            if ($player_result->num_rows === 1) {
                $player = $player_result->fetch_assoc();
                
                if (password_verify($password, $player['password'])) {
                    // Player login successful
                    $_SESSION['logged_in'] = true;
                    $_SESSION['user_id'] = $player['id'];
                    $_SESSION['username'] = $player['username'];
                    
                    // Update last login
                    $update_stmt = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                    $update_stmt->bind_param("i", $player['id']);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    // Redirect to home
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Username atau password salah!';
                }
            } else {
                $error = 'Username atau password salah!';
            }
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
    <title>Login - UNDERTALE</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #000;
            margin: 0;
            padding: 20px;
            font-family: 'Press Start 2P', monospace;
        }
        
        .login-container {
            border: 5px solid #fff;
            padding: 50px;
            text-align: center;
            max-width: 450px;
            width: 100%;
            background: #000;
        }
        
        .login-container h1 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 15px;
            letter-spacing: 5px;
        }
        
        .login-container h1 span {
            color: #ff0000;
        }
        
        .login-subtitle {
            color: #aaa;
            font-size: 0.6rem;
            margin-bottom: 40px;
            font-family: 'Press Start 2P', monospace;
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
            font-weight: bold;
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
        
        .links {
            margin-top: 30px;
            text-align: center;
            font-size: 0.6rem;
        }
        
        .links a {
            color: #0f0;
            text-decoration: none;
            font-family: 'Press Start 2P', monospace;
            margin: 0 10px;
            display: block;
            margin-bottom: 10px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .info-box {
            background: rgba(0, 255, 0, 0.1);
            border: 2px dashed #0f0;
            padding: 15px;
            margin-top: 30px;
            font-size: 0.6rem;
            color: #0f0;
            text-align: left;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>★ WELCOME <span>LOGIN</span> ★</h1>
        <p class="login-subtitle">Enter your credentials to continue</p>
        
        <?php if ($error): ?>
            <div class="error-message">⚠ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">★ USERNAME</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">★ PASSWORD</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">★ LOGIN ★</button>
        </form>
        
        <div class="links">
            <a href="php/create_user.php">Don't have account? Register here</a>
            <a href="index.php">← Back to Home</a>
        </div>
        
        <div class="info-box">
            <strong>Login Info:</strong><br>
            ► PLAYER: Username & Password biasa<br>
            ► ADMIN: Username 'admin' & Password 'admin123'<br>
            <br>
            Sistem akan otomatis mendeteksi apakah Anda admin atau player berdasarkan username.
        </div>
    </div>
</body>
</html>
