<?php
$dir = __DIR__ . '/assets/uploads/music/';
if (is_dir($dir)) {
    echo "Directory exists.\n";
    $files = scandir($dir);
    print_r($files);
} else {
    echo "Directory does NOT exist.\n";
}
?>
