<?php
/**
 * UNDERTALE GAME - INITIAL SETUP
 * Jalankan file ini SEKALI untuk setup database dan create demo users
 * URL: http://localhost/undertale_game/php/setup.php
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'undertale_game');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup - UNDERTALE Game</title>
    <style>
        body {
            font-family: monospace;
            background: #000;
            color: #fff;
            padding: 30px;
            line-height: 1.8;
        }
        .box {
            background: #222;
            border: 3px solid #fff;
            padding: 20px;
            margin: 15px 0;
        }
        .success { color: #0f0; }
        .error { color: #f00; }
        .warning { color: #ff0; }
        h1 { color: #fff; text-align: center; margin-bottom: 30px; }
        h2 { color: #fff; margin-top: 20px; }
        button {
            background: #fff;
            color: #000;
            border: 3px solid #fff;
            padding: 15px 30px;
            font-family: monospace;
            font-size: 16px;
            cursor: pointer;
            margin: 10px 5px;
        }
        button:hover {
            background: #000;
            color: #fff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table td, table th {
            border: 1px solid #fff;
            padding: 10px;
            text-align: left;
        }
        table th {
            background: #333;
        }
        .code {
            background: #111;
            padding: 15px;
            border: 2px solid #0f0;
            margin: 10px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body>

<h1>★ UNDERTALE GAME - SETUP ★</h1>

<?php

// Step 1: Test Database Connection
echo "<div class='box'>";
echo "<h2>STEP 1: Database Connection Test</h2>";

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        echo "<p class='error'>✗ Connection FAILED: " . $conn->connect_error . "</p>";
        echo "<p class='warning'>Please check your database configuration in this file.</p>";
        exit;
    } else {
        echo "<p class='success'>✓ Database connection successful!</p>";
    }
    
    // Create database if not exists
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>✓ Database '" . DB_NAME . "' is ready</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    exit;
}

echo "</div>";

// Step 2: Create Tables
echo "<div class='box'>";
echo "<h2>STEP 2: Create Tables</h2>";

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login TIMESTAMP NULL,
        is_active TINYINT(1) DEFAULT 1,
        INDEX idx_username (username),
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>✓ Table 'users' created/verified</p>";
    } else {
        echo "<p class='error'>✗ Error creating users table: " . $conn->error . "</p>";
    }
    
    // User progress table
    $sql = "CREATE TABLE IF NOT EXISTS user_progress (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL UNIQUE,
        level INT DEFAULT 1,
        exp INT DEFAULT 0,
        gold INT DEFAULT 0,
        battles_won INT DEFAULT 0,
        battles_lost INT DEFAULT 0,
        pacifist_count INT DEFAULT 0,
        genocide_count INT DEFAULT 0,
        last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>✓ Table 'user_progress' created/verified</p>";
    }
    
    // Game scores table
    $sql = "CREATE TABLE IF NOT EXISTS game_scores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        score INT NOT NULL DEFAULT 0,
        turns_used INT NOT NULL DEFAULT 0,
        damage_dealt INT NOT NULL DEFAULT 0,
        hp_remaining INT NOT NULL DEFAULT 20,
        route_type VARCHAR(20) DEFAULT 'neutral',
        played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_id (user_id),
        INDEX idx_score (score)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p class='success'>✓ Table 'game_scores' created/verified</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "</div>";

// Step 3: Create Demo Users
echo "<div class='box'>";
echo "<h2>STEP 3: Create Demo Users</h2>";

$demoUsers = [
    ['username' => 'player1', 'email' => 'player1@undertale.com', 'password' => 'undertale123'],
    ['username' => 'frisk', 'email' => 'frisk@underground.com', 'password' => 'undertale123'],
    ['username' => 'sans', 'email' => 'sans@snowdin.com', 'password' => 'undertale123']
];

echo "<table>";
echo "<tr><th>Username</th><th>Email</th><th>Password</th><th>Status</th></tr>";

foreach ($demoUsers as $user) {
    $username = $user['username'];
    $email = $user['email'];
    $password = $user['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "<tr>";
    echo "<td>" . $username . "</td>";
    echo "<td>" . $email . "</td>";
    echo "<td>" . $password . "</td>";
    
    if ($result->num_rows > 0) {
        echo "<td class='warning'>Already exists</td>";
    } else {
        // Insert new user
        $insertStmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $username, $email, $hashedPassword);
        
        if ($insertStmt->execute()) {
            $userId = $conn->insert_id;
            
            // Create progress for user
            $progressStmt = $conn->prepare("INSERT INTO user_progress (user_id) VALUES (?)");
            $progressStmt->bind_param("i", $userId);
            $progressStmt->execute();
            
            echo "<td class='success'>✓ Created!</td>";
        } else {
            echo "<td class='error'>✗ Failed: " . $insertStmt->error . "</td>";
        }
        $insertStmt->close();
    }
    
    echo "</tr>";
    $stmt->close();
}

echo "</table>";
echo "</div>";

// Step 4: Verify Setup
echo "<div class='box'>";
echo "<h2>STEP 4: Verification</h2>";

$result = $conn->query("SELECT username, email, created_at FROM users");

if ($result && $result->num_rows > 0) {
    echo "<p class='success'>✓ Found " . $result->num_rows . " users in database</p>";
    echo "<table>";
    echo "<tr><th>Username</th><th>Email</th><th>Created At</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>✗ No users found!</p>";
}

echo "</div>";

// Step 5: Test Password Hash
echo "<div class='box'>";
echo "<h2>STEP 5: Password Hash Test</h2>";

$testPassword = "undertale123";
$testHash = password_hash($testPassword, PASSWORD_DEFAULT);

echo "<p>Original Password: <span class='warning'>" . $testPassword . "</span></p>";
echo "<p>Generated Hash: <span class='success'>" . $testHash . "</span></p>";
echo "<p>Hash Length: <span class='success'>" . strlen($testHash) . " characters</span> (should be 60)</p>";

$verify = password_verify($testPassword, $testHash);
echo "<p>Verification Test: " . ($verify ? "<span class='success'>✓ PASSED</span>" : "<span class='error'>✗ FAILED</span>") . "</p>";

echo "</div>";

$conn->close();
?>

<!-- Step 6: Next Steps -->
<div class='box'>
    <h2>STEP 6: Next Steps</h2>
    <ol style="line-height: 2;">
        <li>✓ Setup completed successfully!</li>
        <li>Create <strong>logs/</strong> folder in your project root with write permissions</li>
        <li>Test the login system: <a href="test_login.php" style="color: #0f0;">Test Login</a></li>
        <li>Go to login page: <a href="../login.html" style="color: #0f0;">Login Page</a></li>
        <li>Or go to game: <a href="../index.html" style="color: #0f0;">Play Game</a></li>
    </ol>
    
    <h3 style="margin-top: 20px;">Demo Login Credentials:</h3>
    <div class='code'>
Username: player1<br>
Password: undertale123<br>
<br>
Username: frisk<br>
Password: undertale123<br>
<br>
Username: sans<br>
Password: undertale123
    </div>
</div>

<div class='box'>
    <h2>🔧 Troubleshooting</h2>
    <ul style="line-height: 2;">
        <li>If login still fails, check browser console (F12) for errors</li>
        <li>Make sure <strong>php/login.php</strong> path is correct in login.html</li>
        <li>Check if sessions are enabled in php.ini</li>
        <li>Clear browser cache and cookies</li>
        <li>Run <a href="test_login.php" style="color: #0f0;">test_login.php</a> for detailed debugging</li>
    </ul>
</div>

<div style="text-align: center; margin: 30px 0;">
    <button onclick="window.location.href='test_login.php'">TEST LOGIN SYSTEM</button>
    <button onclick="window.location.href='create_user.php'">CREATE NEW USER</button>
    <button onclick="window.location.href='../login.html'">GO TO LOGIN PAGE</button>
</div>

</body>
</html>