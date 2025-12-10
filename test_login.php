<?php
/**
 * Script untuk test login system
 * Jalankan file ini di browser: http://localhost/undertale_game/php/test_login.php
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
    <title>Test Login - UNDERTALE</title>
    <style>
        body {
            font-family: monospace;
            background: #000;
            color: #fff;
            padding: 20px;
        }
        .test-box {
            background: #222;
            padding: 20px;
            margin: 10px 0;
            border: 2px solid #fff;
        }
        .success {
            background: #0f0;
            color: #000;
            padding: 10px;
            margin: 5px 0;
        }
        .error {
            background: #f00;
            color: #fff;
            padding: 10px;
            margin: 5px 0;
        }
        input, button {
            padding: 10px;
            margin: 5px 0;
            width: 300px;
            display: block;
            font-family: monospace;
            font-size: 14px;
        }
        button {
            background: #fff;
            color: #000;
            border: 2px solid #fff;
            cursor: pointer;
        }
        button:hover {
            background: #000;
            color: #fff;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px 0;
        }
        table td, table th {
            border: 1px solid #fff;
            padding: 8px;
            text-align: left;
        }
        table th {
            background: #333;
        }
    </style>
</head>
<body>
    <h2>★ TEST LOGIN SYSTEM ★</h2>

    <?php
    // Test 1: Database Connection
    echo "<div class='test-box'>";
    echo "<h3>TEST 1: Database Connection</h3>";
    
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            echo "<div class='error'>✗ Connection Failed: " . $conn->connect_error . "</div>";
        } else {
            echo "<div class='success'>✓ Database Connected Successfully</div>";
            echo "Host: " . DB_HOST . "<br>";
            echo "Database: " . DB_NAME . "<br>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Exception: " . $e->getMessage() . "</div>";
    }
    echo "</div>";

    // Test 2: Check Users Table
    if (isset($conn) && !$conn->connect_error) {
        echo "<div class='test-box'>";
        echo "<h3>TEST 2: Users in Database</h3>";
        
        $result = $conn->query("SELECT id, username, email, created_at FROM users ORDER BY id");
        
        if ($result && $result->num_rows > 0) {
            echo "<div class='success'>✓ Found " . $result->num_rows . " users</div>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created At</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='error'>✗ No users found in database</div>";
            echo "<a href='create_user.php' style='color: #0f0;'>→ Create User First</a>";
        }
        echo "</div>";

        // Test 3: Test Login Form
        echo "<div class='test-box'>";
        echo "<h3>TEST 3: Try Login</h3>";
        ?>
        
        <form id="testLoginForm">
            <label>Username:</label>
            <input type="text" id="test_username" name="username" value="player1" required>
            
            <label>Password:</label>
            <input type="text" id="test_password" name="password" value="undertale123" required>
            
            <button type="submit">TEST LOGIN</button>
        </form>
        
        <div id="loginResult" style="margin-top: 20px;"></div>

        <script>
        document.getElementById('testLoginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('test_username').value;
            const password = document.getElementById('test_password').value;
            const resultDiv = document.getElementById('loginResult');
            
            resultDiv.innerHTML = '<div style="background: #ff0; color: #000; padding: 10px;">⏳ Testing login...</div>';
            
            fetch('login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="success">
                            ✓ LOGIN SUCCESSFUL!<br>
                            User ID: ${data.user.id}<br>
                            Username: ${data.user.username}<br>
                            Message: ${data.message}
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            ✗ LOGIN FAILED!<br>
                            Message: ${data.message}<br>
                            ${data.debug ? 'Debug: ' + data.debug : ''}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = `
                    <div class="error">
                        ✗ FETCH ERROR!<br>
                        ${error.message}
                    </div>
                `;
            });
        });
        </script>

        <?php
        echo "</div>";

        // Test 4: Check Password Hashes
        echo "<div class='test-box'>";
        echo "<h3>TEST 4: Password Hash Check</h3>";
        
        $result = $conn->query("SELECT username, password FROM users LIMIT 3");
        
        if ($result && $result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Username</th><th>Password Hash (First 50 chars)</th><th>Hash Length</th></tr>";
            
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['username'] . "</td>";
                echo "<td>" . substr($row['password'], 0, 50) . "...</td>";
                echo "<td>" . strlen($row['password']) . " chars</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            echo "<p style='color: #0f0;'>✓ Password hashes look correct (should be 60 chars for bcrypt)</p>";
        }
        echo "</div>";

        $conn->close();
    }
    ?>

    <hr>
    <div style="margin-top: 20px;">
        <a href="create_user.php" style="color: #fff; text-decoration: none; padding: 10px; border: 2px solid #fff; display: inline-block;">← CREATE USER</a>
        <a href="../login.html" style="color: #fff; text-decoration: none; padding: 10px; border: 2px solid #fff; display: inline-block;">GO TO LOGIN PAGE →</a>
    </div>

    <div class='test-box' style='margin-top: 20px;'>
        <h3>📝 DEBUGGING TIPS:</h3>
        <ul style="line-height: 1.8;">
            <li>Check if <strong>logs/debug.log</strong> file exists and has write permissions</li>
            <li>Make sure <strong>session_start()</strong> is called before any output</li>
            <li>Verify password hash length is exactly <strong>60 characters</strong></li>
            <li>Check browser console (F12) for JavaScript errors</li>
            <li>Test with browser in <strong>Incognito mode</strong> (no cache)</li>
            <li>Make sure <strong>php/login.php</strong> path is correct</li>
        </ul>
    </div>

</body>
</html>