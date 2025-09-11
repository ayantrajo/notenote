<?php
// Database connection
$servername = "localhost"; 
$username = "root"; // Change if you set a MySQL username
$password = "";    // Change if you set a password
$dbname = "noteit_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if (empty($title) || empty($content)) {
    echo "error: title or content missing";
    exit;
}

// Default values
$status = "active";
$user_id = 1;

$sql = "INSERT INTO notes (title, content, status, user_id) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $title, $content, $status, $user_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
