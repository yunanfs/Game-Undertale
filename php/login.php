<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'undertale_game');

// Log function for debugging
function logDebug($message) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, $logDir . '/debug.log');
}

// ========== HANDLE AJAX LOGIN REQUEST ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    logDebug("Login attempt for username: " . $username);
    
    // Validate input
    if (empty($username) || empty($password)) {
        logDebug("Empty username or password");
        echo json_encode([
            'success' => false,
            'message' => 'Username and password are required'
        ]);
        exit;
    }
    
    try {
        // Create database connection
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            logDebug("Connection failed: " . $conn->connect_error);
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        logDebug("Database connected successfully");
        
        // ===== STEP 1: Check if username is admin =====
        logDebug("Checking admin table...");
        $admin_stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? AND is_active = 1");
        
        if (!$admin_stmt) {
            logDebug("Prepare failed: " . $conn->error);
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $admin_stmt->bind_param("s", $username);
        $admin_stmt->execute();
        $admin_result = $admin_stmt->get_result();
        
        // Check if admin found
        if ($admin_result->num_rows === 1) {
            $admin = $admin_result->fetch_assoc();
            logDebug("Admin found: " . $admin['username']);
            
            // Verify admin password
            if (password_verify($password, $admin['password'])) {
                // Admin login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                logDebug("Admin session created for: " . $admin['username']);
                
                // Update last login
                $updateStmt = $conn->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                if ($updateStmt) {
                    $updateStmt->bind_param("i", $admin['id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
                
                $admin_stmt->close();
                $conn->close();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Admin login successful',
                    'type' => 'admin',
                    'user' => [
                        'id' => $admin['id'],
                        'username' => $admin['username']
                    ]
                ]);
                exit;
            } else {
                logDebug("Admin password verification FAILED");
                $admin_stmt->close();
                $conn->close();
                
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid username or password'
                ]);
                exit;
            }
        }
        
        $admin_stmt->close();
        
        // ===== STEP 2: Check if username is regular user =====
        logDebug("Checking users table...");
        $stmt = $conn->prepare("SELECT id, username, password, email FROM users WHERE username = ?");
        
        if (!$stmt) {
            logDebug("Prepare failed: " . $conn->error);
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        logDebug("Query executed. Rows found: " . $result->num_rows);
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            logDebug("User found: " . $user['username']);
            
            // Verify password
            $passwordVerified = password_verify($password, $user['password']);
            logDebug("Password verification: " . ($passwordVerified ? "SUCCESS" : "FAILED"));
            
            if ($passwordVerified) {
                // User login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['logged_in'] = true;
                
                logDebug("Session created for user: " . $user['username']);
                
                // Update last login
                $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                if ($updateStmt) {
                    $updateStmt->bind_param("i", $user['id']);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
                
                $stmt->close();
                $conn->close();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'type' => 'user',
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username']
                    ]
                ]);
                exit;
            } else {
                // Invalid password
                logDebug("Invalid password for user: " . $username);
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid username or password'
                ]);
            }
        } else {
            // User not found
            logDebug("User not found: " . $username);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid username or password'
            ]);
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        logDebug("Exception: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database error'
        ]);
    }
    exit;
}

// ========== DISPLAY HTML FORM (GET REQUEST) ==========
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UNDERTALE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: #000;
            color: #fff;
            font-family: 'Press Start 2P', cursive, monospace;
            margin: 0;
            padding: 0;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-box {
            background: #000;
            border: 8px dotted #fff;
            padding: 50px;
            max-width: 500px;
            width: 100%;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-size: 2.5rem;
            margin: 0 0 20px 0;
        }

        .login-header p {
            font-size: 0.8rem;
            color: #aaa;
            line-height: 2;
            margin: 0;
        }

        .login-header .heart-small {
            width: 40px;
            height: 40px;
            background: #ff0000;
            transform: rotate(45deg);
            position: relative;
            margin: 0 auto 30px;
            animation: heartbeat 1.5s infinite;
        }

        .login-header .heart-small::before,
        .login-header .heart-small::after {
            content: '';
            width: 40px;
            height: 40px;
            background: #ff0000;
            border-radius: 50%;
            position: absolute;
        }

        .login-header .heart-small::before {
            top: -20px;
            left: 0;
        }

        .login-header .heart-small::after {
            left: 20px;
            top: 0;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-size: 0.9rem;
            color: #fff;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            background: #000;
            border: 4px solid #fff;
            color: #fff;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 0.8rem;
            box-sizing: border-box;
        }

        .form-group input::placeholder {
            color: #888;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff0000;
        }

        .submit-btn {
            width: 100%;
            background: #fff;
            color: #000;
            border: 4px solid #fff;
            padding: 15px;
            font-family: 'Press Start 2P', cursive, monospace;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
            box-sizing: border-box;
        }

        .submit-btn:hover {
            background: #000;
            color: #fff;
            transform: scale(1.05);
        }

        .form-links {
            text-align: center;
            margin-top: 25px;
            font-size: 0.7rem;
        }

        .form-links p {
            margin: 10px 0;
        }

        .form-links a {
            color: #fff;
            text-decoration: none;
            border-bottom: 2px solid #fff;
        }

        .form-links a:hover {
            color: #ff0000;
            border-bottom-color: #ff0000;
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            color: #fff;
            text-decoration: none;
            font-size: 0.8rem;
            padding: 10px 20px;
            border: 3px solid #fff;
            display: inline-block;
            transition: all 0.3s;
        }

        .back-link a:hover {
            background: #fff;
            color: #000;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.2);
            border: 3px solid #ff0000;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.7rem;
            line-height: 1.8;
            display: none;
        }

        .success-message {
            background: rgba(0, 255, 0, 0.2);
            border: 3px solid #00ff00;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.7rem;
            line-height: 1.8;
            display: none;
        }

        @keyframes heartbeat {
            0%, 100% { transform: rotate(45deg) scale(1); }
            50% { transform: rotate(45deg) scale(1.1); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="heart-small"></div>
                <h1>WELCOME</h1>
                <p style="font-size: 0.8rem; color: #aaa; line-height: 2;">
                    Please enter your credentials<br>
                    to continue your journey
                </p>
            </div>

            <div class="error-message" id="errorMessage">
                <span id="errorText">* Login failed!<br>* Please check your credentials.</span>
            </div>

            <div class="success-message" id="successMessage">
                * Login successful!<br>
                * Redirecting to game...
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label for="username">★ USERNAME</label>
                    <input type="text" id="username" name="username" required placeholder="Enter username">
                </div>

                <div class="form-group">
                    <label for="password">★ PASSWORD</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password">
                </div>

                <button type="submit" class="submit-btn">★ LOGIN</button>

                <div class="form-links">
                    <p>Don't have an account? <a href="register.html">Register here</a></p>
                    <p style="margin-top: 10px;"><a href="forgot-password.html">Forgot password?</a></p>
                </div>

                <div class="back-link">
                    <a href="../index.php">← BACK TO HOME</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            // Send AJAX request
            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('successMessage').style.display = 'block';
                    document.getElementById('errorMessage').style.display = 'none';
                    
                    // Determine redirect based on login type
                    let redirectUrl = '../index.php';
                    if (data.type === 'admin') {
                        redirectUrl = '../admin/dashboard.php';
                    }
                    
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 1500);
                } else {
                    // Update error message
                    const errorText = document.getElementById('errorText');
                    if (errorText) {
                        errorText.innerHTML = '* ' + (data.message || 'Login failed') + '<br>* Please try again.';
                    } else {
                        document.getElementById('errorMessage').textContent = data.message || 'Login failed';
                    }
                    document.getElementById('errorMessage').style.display = 'block';
                    document.getElementById('successMessage').style.display = 'none';
                }
            })
            .catch(error => {
                document.getElementById('errorMessage').style.display = 'block';
                document.getElementById('errorText').innerHTML = '* Connection error<br>* Please try again.';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
