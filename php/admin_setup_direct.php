<?php
// Admin Setup - Direct execution
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<pre>";
echo "=== ADMIN SETUP STARTED ===\n\n";

// 1. Create admins table
echo "1. Creating admins table...\n";
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "   ✓ Admins table created/already exists\n";
} else {
    echo "   ✗ Error: " . $conn->error . "\n";
}

// 2. Create stories table
echo "\n2. Creating stories table...\n";
$sql = "CREATE TABLE IF NOT EXISTS stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    description TEXT,
    order_number INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_order (order_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "   ✓ Stories table created/already exists\n";
} else {
    echo "   ✗ Error: " . $conn->error . "\n";
}

// 3. Create characters table
echo "\n3. Creating characters table...\n";
$sql = "CREATE TABLE IF NOT EXISTS characters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    role VARCHAR(50),
    image_url VARCHAR(255),
    bio LONGTEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "   ✓ Characters table created/already exists\n";
} else {
    echo "   ✗ Error: " . $conn->error . "\n";
}

// 4. Add status column to user_progress if not exists
echo "\n4. Adding status column to user_progress...\n";
$result = $conn->query("SHOW COLUMNS FROM user_progress LIKE 'status'");
if ($result && $result->num_rows == 0) {
    if ($conn->query("ALTER TABLE user_progress ADD COLUMN status VARCHAR(200)") === TRUE) {
        echo "   ✓ Status column added\n";
    } else {
        echo "   ✗ Error: " . $conn->error . "\n";
    }
} else {
    echo "   ✓ Status column already exists\n";
}

// 5. Check if admin exists
echo "\n5. Checking admin user...\n";
$result = $conn->query("SELECT id FROM admins WHERE username = 'admin'");

if ($result && $result->num_rows > 0) {
    echo "   ✓ Admin user already exists\n";
} else {
    echo "   Creating admin user (admin/admin123)...\n";
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO admins (username, password, is_active) VALUES (?, ?, 1)");
    if (!$stmt) {
        echo "   ✗ Prepare failed: " . $conn->error . "\n";
    } else {
        $stmt->bind_param("ss", $username, $password);
        $username = 'admin';
        $password = $admin_password;
        
        if ($stmt->execute()) {
            echo "   ✓ Admin user created successfully!\n";
            echo "   Username: admin\n";
            echo "   Password: admin123\n";
        } else {
            echo "   ✗ Error: " . $stmt->error . "\n";
        }
        $stmt->close();
    }
}

// 6. Insert sample stories
echo "\n6. Inserting sample stories...\n";
$stories = [
    ['Prologue: The Fall', 'You wake up in the ruins. The light from your phone guides you through the darkness.', 'The beginning of your journey', 1],
    ['Encounter', 'You meet a small flower. It introduces itself as Flowey.', 'Your first encounter', 2],
    ['The Ruins', 'You venture deeper into the Ruins. Ancient architecture surrounds you.', 'Exploring the ancient ruins', 3]
];

foreach ($stories as $story) {
    $stmt = $conn->prepare("INSERT INTO stories (title, content, description, order_number, created_by) VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE updated_at=CURRENT_TIMESTAMP");
    if ($stmt) {
        $stmt->bind_param("sssi", $story[0], $story[1], $story[2], $story[3]);
        $stmt->execute();
        $stmt->close();
    }
}
echo "   ✓ Sample stories inserted\n";

// 7. Insert sample characters
echo "\n7. Inserting sample characters...\n";
$characters = [
    ['Frisk', 'The protagonist', 'Main Character', 'You are Frisk, a human child who has fallen into the Underground.'],
    ['Flowey', 'A small golden flower', 'Antagonist', 'A golden flower that greets you in the Ruins.'],
    ['Toriel', 'A majestic goat-like creature', 'Guardian', 'The caretaker of the Ruins.'],
    ['Sans', 'A skeleton wearing a blue hoodie', 'Ally', 'A comedic skeleton who appears throughout your journey.'],
    ['Papyrus', 'A tall skeleton with a deep voice', 'Ally', 'Sans brother, Papyrus is enthusiastic and energetic.']
];

foreach ($characters as $char) {
    $stmt = $conn->prepare("INSERT INTO characters (name, description, role, bio, created_by) VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE updated_at=CURRENT_TIMESTAMP");
    if ($stmt) {
        $stmt->bind_param("ssss", $char[0], $char[1], $char[2], $char[3]);
        $stmt->execute();
        $stmt->close();
    }
}
echo "   ✓ Sample characters inserted\n";

echo "\n=== SETUP COMPLETED SUCCESSFULLY ===\n\n";
echo "Next steps:\n";
echo "1. Go to Admin Login: http://localhost/gameundertale/php/admin_login.php\n";
echo "2. Username: admin\n";
echo "3. Password: admin123\n";
echo "\n</pre>";

$conn->close();
?>
