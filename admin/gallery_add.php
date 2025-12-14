<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../php/admin_login.php');
    exit;
}

require_once '../php/config.php';
global $conn;

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $image_url = '';

    // Handle File Upload or URL/Emoji
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $target_dir = "../uploads/"; // Make sure this exists
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_name = time() . '_' . basename($_FILES["image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
            $image_url = 'uploads/' . $file_name; // Relative for DB
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    } else {
        // Fallback to text input (URL or Emoji)
        $image_url = $conn->real_escape_string($_POST['image_text']);
    }

    if (!$error && $title && $image_url) {
        $sql = "INSERT INTO gallery (title, description, image_url) VALUES ('$title', '$description', '$image_url')";
        if ($conn->query($sql) === TRUE) {
            header("Location: gallery.php");
            exit;
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else if (!$error) {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Gallery Item - UNDERTALE Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #000; color: #fff; padding: 20px; font-family: 'Press Start 2P', monospace; padding-top: 80px; }
        .container { max-width: 600px; margin: 0 auto; border: 4px solid #fff; padding: 40px; }
        h1 { text-align: center; margin-bottom: 30px; font-size: 1.5rem; text-transform: uppercase; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 10px; color: #aaa; font-size: 0.8rem; }
        input[type="text"], textarea, input[type="file"] {
            width: 100%; padding: 10px; background: #000; border: 2px solid #fff; color: #fff;
            font-family: inherit; box-sizing: border-box;
        }
        button {
            width: 100%; background: #0f0; color: #000; padding: 15px; border: none;
            font-family: inherit; cursor: pointer; font-size: 1rem; margin-top: 20px;
        }
        button:hover { background: #fff; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #aaa; text-decoration: none; font-size: 0.7rem; }
        .back-link:hover { color: #fff; }
        .error { color: #f00; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>ADD GALLERY ITEM</h1>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>TITLE</label>
                <input type="text" name="title" required>
            </div>
            
            <div class="form-group">
                <label>DESCRIPTION</label>
                <textarea name="description" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label>IMAGE (UPLOAD)</label>
                <input type="file" name="image_file" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>OR IMAGE TEXT/URL/EMOJI</label>
                <input type="text" name="image_text" placeholder="e.g. 🏰 or https://...">
            </div>
            
            <button type="submit">SAVE ITEM</button>
            <a href="gallery.php" class="back-link">CANCEL</a>
        </form>
    </div>
</body>
</html>
