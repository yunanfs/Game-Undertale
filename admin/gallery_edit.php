<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../php/admin_login.php');
    exit;
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'undertale_game';
$conn = new mysqli($host, $user, $pass, $db);

$id = $_GET['id'] ?? 0;
$item = null;

if ($id) {
    $result = $conn->query("SELECT * FROM gallery WHERE id = $id");
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
    } else {
        die("Item not found");
    }
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $image_url = $item['image_url']; // Default keep old

    // Handle File Upload or URL/Emoji
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_name = time() . '_' . basename($_FILES["image_file"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
            $image_url = 'uploads/' . $file_name; 
        }
    } else if (!empty($_POST['image_text'])) {
        $image_url = $conn->real_escape_string($_POST['image_text']);
    }

    $sql = "UPDATE gallery SET title='$title', description='$description', image_url='$image_url' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header("Location: gallery.php");
        exit;
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Gallery Item - UNDERTALE Admin</title>
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
        .preview { margin-top: 10px; border: 2px solid #fff; padding: 10px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>EDIT ITEM</h1>
        <?php if($error) echo "<p style='color:red;text-align:center;'>$error</p>"; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>TITLE</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>DESCRIPTION</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>CURRENT IMAGE</label>
                <div class="preview">
                    <?php 
                    if (mb_strlen($item['image_url']) < 10 && !strpos($item['image_url'], '/')) echo $item['image_url'];
                    else echo '<img src="../'.$item['image_url'].'" style="max-width:100px;">';
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label>CHANGE IMAGE (UPLOAD)</label>
                <input type="file" name="image_file" accept="image/*">
            </div>
            
            <div class="form-group">
                <label>OR IMAGE TEXT/URL/EMOJI</label>
                <input type="text" name="image_text" value="<?php echo htmlspecialchars($item['image_url']); ?>">
            </div>
            
            <button type="submit">UPDATE ITEM</button>
            <a href="gallery.php" class="back-link">CANCEL</a>
        </form>
    </div>
</body>
</html>
