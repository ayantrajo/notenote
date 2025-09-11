<?php
require 'config.php';

$stmt = $conn->query("SELECT * FROM notes ORDER BY date_created DESC");
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note It!</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* your existing CSS here */
    </style>
</head>
<body>
    <div class="sidebar">
        <h1><span>Note</span>It!</h1>
        <ul>
            <li><a href="#"><i class="fas fa-sticky-note"></i>All Notes</a></li>
            <li><a href="#"><i class="fas fa-heart"></i>Favorites</a></li>
            <li><a href="#"><i class="fas fa-archive"></i>Archives</a></li>
            <li><a href="#"><i class="fas fa-power-off"></i>Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <header>
            <h2>All Notes</h2>
            <div class="search-bar">
                <input type="text" placeholder="Search" />
                <button class="add-note-btn" onclick="window.location.href='add_note.php'">+ Add Notes</button>
            </div>
        </header>

        <div class="notes-grid">
            <?php foreach ($notes as $note): ?>
                <div class="note-card">
                    <h3><?= htmlspecialchars($note['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                    <div class="note-footer">
                        <span class="date"><?= date('M d, Y', strtotime($note['date_created'])) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
