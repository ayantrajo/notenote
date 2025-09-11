<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO notes (title, content, status, date_created, user_id) VALUES (?, ?, 'active', NOW(), 1)");
        $stmt->execute([$title, $content]);
        header("Location: index.php");
        exit;
    } else {
        echo "Title and Content are required!";
    }
}
?>

<form method="POST">
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="content" placeholder="Write your note..." required></textarea>
    <button type="submit">Save</button>
</form>
