<?php
// Admin Setup - Run this once to create admin tables and insert admin user

$message = '';
$error = '';

// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    $error = 'Database connection failed: ' . $conn->connect_error;
}

if (!empty($_POST) && isset($_POST['action']) && $_POST['action'] === 'setup' && !$error) {
    try {
        // Create admins table
        $sql_admins = "CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            is_active TINYINT(1) DEFAULT 1,
            INDEX idx_username (username)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        mysqli_query($conn, $sql_admins);
        
        // Create stories table
        $sql_stories = "CREATE TABLE IF NOT EXISTS stories (
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
        
        mysqli_query($conn, $sql_stories);
        
        // Create characters table
        $sql_characters = "CREATE TABLE IF NOT EXISTS characters (
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
        
        mysqli_query($conn, $sql_characters);
        
        // Add status column to user_progress if not exists
        $result = mysqli_query($conn, "SHOW COLUMNS FROM user_progress LIKE 'status'");
        if (mysqli_num_rows($result) == 0) {
            mysqli_query($conn, "ALTER TABLE user_progress ADD COLUMN status VARCHAR(200)");
        }
        
        // Check if admin user already exists
        $check_admin = mysqli_query($conn, "SELECT id FROM admins WHERE username = 'admin'");
        
        if (mysqli_num_rows($check_admin) == 0) {
            // Insert admin user - password: admin123
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (username, password, is_active) VALUES (?, ?, 1)");
            $stmt->bind_param("ss", $username, $password);
            $username = 'admin';
            $password = $admin_password;
            $stmt->execute();
            $message .= "✓ Admin user created (username: admin, password: admin123)<br>";
        } else {
            $message .= "✓ Admin user already exists<br>";
        }
        
        // Insert sample stories
        $insert_stories = "INSERT INTO stories (title, content, description, order_number, created_by) VALUES 
        ('Prologue: The Fall', 'You wake up in the ruins. The light from your phone guides you through the darkness.', 'The beginning of your journey', 1, 1),
        ('Encounter', 'You meet a small flower. It introduces itself as Flowey.', 'Your first encounter', 2, 1),
        ('The Ruins', 'You venture deeper into the Ruins. Ancient architecture surrounds you.', 'Exploring the ancient ruins', 3, 1)
        ON DUPLICATE KEY UPDATE updated_at=CURRENT_TIMESTAMP";
        
        mysqli_query($conn, $insert_stories);
        $message .= "✓ Sample stories inserted<br>";
        
        // Insert sample characters
        $insert_characters = "INSERT INTO characters (name, description, role, bio, created_by) VALUES 
        ('Frisk', 'The protagonist', 'Main Character', 'You are Frisk, a human child who has fallen into the Underground.', 1),
        ('Flowey', 'A small golden flower', 'Antagonist', 'A golden flower that greets you in the Ruins.', 1),
        ('Toriel', 'A majestic goat-like creature', 'Guardian', 'The caretaker of the Ruins.', 1),
        ('Sans', 'A skeleton wearing a blue hoodie', 'Ally', 'A comedic skeleton who appears throughout your journey.', 1),
        ('Papyrus', 'A tall skeleton with a deep voice', 'Ally', 'Sans brother, Papyrus is enthusiastic and energetic.', 1)
        ON DUPLICATE KEY UPDATE updated_at=CURRENT_TIMESTAMP";
        
        mysqli_query($conn, $insert_characters);
        $message .= "✓ Sample characters inserted<br>";
        $message .= "<br>✓ Admin setup completed successfully!<br>";
        $message .= "You can now login at <a href='admin_login.php'>admin_login.php</a>";
        
    } catch (Exception $e) {
        $error = "Setup failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup</title>
    <style>
        body {
            font-family: 'Press Start 2P', monospace;
            background: #000;
            color: #fff;
            padding: 40px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            border: 3px solid #fff;
            padding: 30px;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 30px;
        }
        .message {
            background: rgba(0, 255, 0, 0.2);
            border: 2px solid #0f0;
            color: #0f0;
            padding: 20px;
            margin: 20px 0;
            font-size: 0.8rem;
            line-height: 1.8;
            text-align: left;
        }
        .error {
            background: rgba(255, 0, 0, 0.2);
            border: 2px solid #f00;
            color: #f00;
            padding: 20px;
            margin: 20px 0;
            font-size: 0.8rem;
        }
        button {
            background: #fff;
            color: #000;
            border: 2px solid #fff;
            padding: 15px 30px;
            font-family: 'Press Start 2P', monospace;
            font-size: 0.8rem;
            cursor: pointer;
            margin: 20px 10px;
        }
        button:hover {
            background: #000;
            color: #fff;
        }
        a {
            color: #0f0;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>★ ADMIN SETUP ★</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php else: ?>
            <p style="margin-bottom: 30px; font-size: 0.9rem;">
                Click the button below to create admin tables and insert default admin user.<br>
                <strong>Username:</strong> admin<br>
                <strong>Password:</strong> admin123
            </p>
            <form method="POST">
                <input type="hidden" name="action" value="setup">
                <button type="submit">★ SETUP ADMIN ★</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
