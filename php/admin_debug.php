<?php
require 'config.php';

echo "<pre>";
echo "=== DATABASE DEBUG ===\n\n";

// Check connection
echo "1. Checking database connection...\n";
if ($conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME)) {
    echo "✓ Connection successful\n\n";
} else {
    echo "✗ Connection failed\n";
    exit;
}

// Check if admins table exists
echo "2. Checking admins table...\n";
$result = $conn->query("SHOW TABLES LIKE 'admins'");
if ($result && $result->num_rows > 0) {
    echo "✓ Admins table exists\n\n";
    
    // Check admin user
    echo "3. Checking admin user...\n";
    $result = $conn->query("SELECT id, username, password FROM admins");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "   - ID: " . $row['id'] . "\n";
            echo "   - Username: " . $row['username'] . "\n";
            echo "   - Password hash: " . substr($row['password'], 0, 20) . "...\n";
        }
        echo "\n✓ Admin user(s) found\n\n";
    } else {
        echo "✗ No admin users found!\n";
        echo "  Creating admin user...\n";
        
        $username = 'admin';
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admins (username, password, is_active) VALUES (?, ?, 1)");
        $stmt->bind_param("ss", $username, $password);
        
        if ($stmt->execute()) {
            echo "  ✓ Admin user created successfully!\n";
            echo "  - Username: admin\n";
            echo "  - Password: admin123\n\n";
        } else {
            echo "  ✗ Failed to create admin user: " . $stmt->error . "\n\n";
        }
        $stmt->close();
    }
} else {
    echo "✗ Admins table does not exist!\n";
    echo "  Please run admin_setup.php first\n\n";
}

// Check stories table
echo "4. Checking stories table...\n";
$result = $conn->query("SHOW TABLES LIKE 'stories'");
if ($result && $result->num_rows > 0) {
    echo "✓ Stories table exists\n";
} else {
    echo "✗ Stories table does not exist\n";
}

// Check characters table
echo "5. Checking characters table...\n";
$result = $conn->query("SHOW TABLES LIKE 'characters'");
if ($result && $result->num_rows > 0) {
    echo "✓ Characters table exists\n";
} else {
    echo "✗ Characters table does not exist\n";
}

echo "\n=== END DEBUG ===\n";
echo "</pre>";

$conn->close();
?>
