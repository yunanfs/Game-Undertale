<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

$conn = new mysqli($host, $user, $pass, $db);

echo "<pre>";
echo "=== ADMIN LOGIN TEST ===\n\n";

// Test 1: Connection
echo "1. Database connection: ";
if ($conn->connect_error) {
    echo "FAILED\n";
    echo "Error: " . $conn->connect_error . "\n\n";
    exit;
} else {
    echo "OK\n\n";
}

// Test 2: Check if admin user exists
echo "2. Checking admin user...\n";
$result = $conn->query("SELECT id, username, password FROM admins WHERE username = 'admin'");

if ($result && $result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    echo "   Admin found!\n";
    echo "   ID: " . $admin['id'] . "\n";
    echo "   Username: " . $admin['username'] . "\n";
    echo "   Password hash: " . substr($admin['password'], 0, 30) . "...\n\n";
    
    // Test 3: Test password verification
    echo "3. Testing password verification...\n";
    $test_password = 'admin123';
    
    if (password_verify($test_password, $admin['password'])) {
        echo "   ✓ Password verification PASSED!\n";
        echo "   Password 'admin123' is CORRECT\n\n";
    } else {
        echo "   ✗ Password verification FAILED!\n";
        echo "   Password 'admin123' is INCORRECT\n\n";
    }
    
    // Test 4: Test prepared statement (like in admin_login.php)
    echo "4. Testing prepared statement (like login process)...\n";
    $username = 'admin';
    $password = 'admin123';
    
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? AND is_active = 1");
    if (!$stmt) {
        echo "   ✗ Prepare failed: " . $conn->error . "\n\n";
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $login_result = $stmt->get_result();
        
        if ($login_result->num_rows === 1) {
            $login_admin = $login_result->fetch_assoc();
            echo "   ✓ Prepared statement found admin\n";
            
            if (password_verify($password, $login_admin['password'])) {
                echo "   ✓ Password matches!\n";
                echo "   ✓ LOGIN SUCCESSFUL!\n\n";
                
                // Simulate session
                echo "5. Session simulation:\n";
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $login_admin['id'];
                $_SESSION['admin_username'] = $login_admin['username'];
                
                echo "   Session set:\n";
                echo "   - admin_logged_in: " . ($_SESSION['admin_logged_in'] ? 'true' : 'false') . "\n";
                echo "   - admin_id: " . $_SESSION['admin_id'] . "\n";
                echo "   - admin_username: " . $_SESSION['admin_username'] . "\n";
            } else {
                echo "   ✗ Password does not match!\n";
            }
        } else {
            echo "   ✗ Admin not found or not active\n";
        }
        $stmt->close();
    }
    
} else {
    echo "   ✗ Admin user NOT found!\n";
    echo "   Please run setup first\n";
}

echo "\n=== END TEST ===\n";
echo "</pre>";

$conn->close();
?>
