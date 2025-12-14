<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

session_start();

// Redirect if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: ../index.php');
    exit;
}
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ../admin/dashboard.php');
    exit;
}

// Database configuration
require_once 'config.php';
global $conn;

// Log function for debugging
function logDebug($message) {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, $logDir . '/debug.log');
}

// ========== HANDLE AJAX REGISTER REQUEST ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    logDebug("Register attempt for username: " . $username);
    
    // Validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (!empty($errors)) {
        logDebug("Validation errors: " . implode(', ', $errors));
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }
    
    try {
        // Use global connection
        global $conn;
        
        if ($conn->connect_error) {
            logDebug("Connection failed: " . $conn->connect_error);
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        logDebug("Database connected successfully");
        
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            logDebug("Username already exists: " . $username);
            echo json_encode([
                'success' => false,
                'message' => 'Username already exists'
            ]);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();
        
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            logDebug("Email already registered: " . $email);
            echo json_encode([
                'success' => false,
                'message' => 'Email already registered'
            ]);
            $stmt->close();
            $conn->close();
            exit;
        }
        $stmt->close();
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        logDebug("Password hashed successfully");
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            $userId = $conn->insert_id;
            logDebug("User registered successfully: " . $username . " (ID: " . $userId . ")");
            
            // Auto login
            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            
            logDebug("Session created for new user: " . $username);
            
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => $userId,
                    'username' => $username
                ]
            ]);
        } else {
            logDebug("Registration failed: " . $stmt->error);
            throw new Exception("Registration failed: " . $stmt->error);
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (Exception $e) {
        logDebug("Exception: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
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
    <title>Register - UNDERTALE</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: #000;
            color: #fff;
            font-family: 'Press Start 2P', cursive, monospace;
            margin: 0;
            padding: 0;
        }

        .register-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-box {
            background: #000;
            border: 8px dotted #fff;
            padding: 50px;
            max-width: 500px;
            width: 100%;
        }

        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .register-header h1 {
            font-size: 2.5rem;
            margin: 0 0 20px 0;
        }

        .register-header p {
            font-size: 0.8rem;
            color: #aaa;
            line-height: 2;
            margin: 0;
        }

        .register-header .heart-small {
            width: 40px;
            height: 40px;
            background: #ff0000;
            transform: rotate(0deg);
            position: relative;
            margin: 0 auto 30px;
            animation: heartbeat 1.5s infinite;
        }

        .register-header .heart-small::before,
        .register-header .heart-small::after {
            content: '';
            width: 40px;
            height: 40px;
            background: #ff0000;
            border-radius: 50%;
            position: absolute;
        }

        .register-header .heart-small::before {
            top: -20px;
            left: 0;
        }

        .register-header .heart-small::after {
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
            0%, 100% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(0deg) scale(1.1); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <div class="heart-small"></div>
                <h1>REGISTER</h1>
                <p style="font-size: 0.8rem; color: #aaa; line-height: 2;">
                    Create your account<br>
                    and begin your journey
                </p>
            </div>

            <div class="error-message" id="errorMessage">
                <span id="errorText">* Registration failed!</span>
            </div>

            <div class="success-message" id="successMessage">
                * Registration successful!<br>
                * Loading game...
            </div>

            <form id="registerForm">
                <div class="form-group">
                    <label for="username">★ USERNAME</label>
                    <input type="text" id="username" name="username" required placeholder="3+ chars (letters, numbers, _)">
                </div>

                <div class="form-group">
                    <label for="email">★ EMAIL</label>
                    <input type="email" id="email" name="email" required placeholder="your@email.com">
                </div>

                <div class="form-group">
                    <label for="password">★ PASSWORD</label>
                    <input type="password" id="password" name="password" required placeholder="6+ characters">
                </div>

                <div class="form-group">
                    <label for="confirm_password">★ CONFIRM PASSWORD</label>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm password">
                </div>

                <button type="submit" class="submit-btn">★ REGISTER ★</button>
            </form>

            <div class="form-links">
                <p>Already have an account?<br><a href="login.php">Login here</a></p>
            </div>


        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Send AJAX request
            fetch('register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&confirm_password=${encodeURIComponent(confirmPassword)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('successMessage').style.display = 'block';
                    document.getElementById('errorMessage').style.display = 'none';
                    
                    setTimeout(() => {
                        window.location.href = '../index.php';
                    }, 1500);
                } else {
                    // Update error message
                    const errorText = document.getElementById('errorText');
                    if (errorText) {
                        errorText.innerHTML = '* ' + (data.message || 'Registration failed') + '<br>* Please try again.';
                    } else {
                        document.getElementById('errorMessage').textContent = data.message || 'Registration failed';
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
