<?php
/**
 * Script untuk membuat user secara manual
 * Jalankan file ini di browser: http://localhost/undertale_game/php/create_user.php
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'undertale_game');

echo "<h2>UNDERTALE - Create User Manually</h2>";
echo "<hr>";

// Form untuk input user
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['create'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Create User - UNDERTALE</title>
        <style>
            body {
                font-family: monospace;
                background: #000;
                color: #fff;
                padding: 20px;
            }
            input, button {
                padding: 10px;
                margin: 5px 0;
                width: 300px;
                display: block;
                font-family: monospace;
            }
            button {
                background: #fff;
                color: #000;
                border: 2px solid #fff;
                cursor: pointer;
            }
            .info {
                background: #222;
                padding: 15px;
                margin: 20px 0;
                border: 2px solid #fff;
            }
        </style>
    </head>
    <body>
        <h2>★ CREATE NEW USER ★</h2>
        
        <div class="info">
            <strong>Default Users in Database:</strong><br>
            Username: player1 | Password: undertale123<br>
            Username: frisk | Password: undertale123
        </div>
        
        <form method="GET">
            <input type="hidden" name="create" value="1">
            <label>Username:</label>
            <input type="text" name="username" required placeholder="Enter username">
            
            <label>Email:</label>
            <input type="email" name="email" required placeholder="Enter email">
            
            <label>Password:</label>
            <input type="text" name="password" required placeholder="Enter password (min 6 chars)">
            
            <button type="submit">CREATE USER</button>
        </form>
        
        <hr>
        <a href="test_login.php" style="color: #fff;">Test Login System</a>
    </body>
    </html>
    <?php
    exit;
}

// Process create user
if (isset($_GET['create'])) {
    $username = trim($_GET['username']);
    $email = trim($_GET['email']);
    $password = $_GET['password'];
    
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        echo "<div style='background: #222; padding: 15px; margin: 10px 0; border: 2px solid #0f0;'>";
        echo "✓ Database connected successfully<br>";
        
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<span style='color: #f00;'>✗ Username already exists!</span><br>";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            echo "✓ Password hashed: " . substr($hashedPassword, 0, 30) . "...<br>";
            
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);
            
            if ($stmt->execute()) {
                $userId = $conn->insert_id;
                echo "<span style='color: #0f0;'>✓ User created successfully!</span><br>";
                echo "✓ User ID: " . $userId . "<br>";
                echo "✓ Username: " . $username . "<br>";
                echo "✓ Password: " . $password . "<br>";
                
                // Create initial progress
                $progressStmt = $conn->prepare("INSERT INTO user_progress (user_id) VALUES (?)");
                $progressStmt->bind_param("i", $userId);
                
                if ($progressStmt->execute()) {
                    echo "✓ User progress initialized<br>";
                }
                
                $progressStmt->close();
            } else {
                echo "<span style='color: #f00;'>✗ Error creating user: " . $stmt->error . "</span><br>";
            }
        }
        
        echo "</div>";
        
        $stmt->close();
        $conn->close();
        
        echo "<hr>";
        echo "<a href='create_user.php' style='color: #fff; text-decoration: none; padding: 10px; border: 2px solid #fff; display: inline-block;'>← CREATE ANOTHER USER</a> ";
        echo "<a href='test_login.php' style='color: #fff; text-decoration: none; padding: 10px; border: 2px solid #fff; display: inline-block;'>TEST LOGIN →</a>";
        
    } catch (Exception $e) {
        echo "<div style='background: #f00; color: #fff; padding: 15px; margin: 10px 0;'>";
        echo "Error: " . $e->getMessage();
        echo "</div>";
    }
}
?>