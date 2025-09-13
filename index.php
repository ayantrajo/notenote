<?php

require_once 'config.php';
$user_id = 1; // Default user for now

// Handle actions (Create, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add note
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        if ($title && $content) {
            $stmt = $conn->prepare("INSERT INTO notes (title, content, status, date_created, user_id) VALUES (?, ?, 'normal', NOW(), ?)");
            $stmt->execute([$title, $content, $user_id]);
        }
    }
    // Edit note
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        $id = $_POST['id'] ?? '';
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        if ($id && $title && $content) {
            $stmt = $conn->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $content, $id, $user_id]);
        }
    }
    // Set as favorite
    if (isset($_POST['action']) && $_POST['action'] === 'favorite') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $conn->prepare("UPDATE notes SET status = 'Favorite' WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
        }
    }
    // Archive
    if (isset($_POST['action']) && $_POST['action'] === 'archive') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $conn->prepare("UPDATE notes SET status = 'Archived' WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
        }
    }
    // Delete
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $user_id]);
        }
    }
    // Redirect to prevent form resubmission
    header("Location: index.php" . (isset($_GET['filter']) ? "?filter=" . $_GET['filter'] : ""));
    exit;
}

// Get current filter
$filter = $_GET['filter'] ?? 'all';
$section_title = "All Notes";
$title_color = "#222";

if ($filter === 'favorite') {
    $section_title = "★ Favorites";
    $title_color = "#06b399";
} elseif ($filter === 'archived') {
    $section_title = "🗄️ Archives";
    $title_color = "#ff9800";
}

// Fetch notes based on filter
$sql = "SELECT * FROM notes WHERE user_id = ?";
if ($filter === 'favorite') {
    $sql .= " AND status = 'Favorite'";
} elseif ($filter === 'archived') {
    $sql .= " AND status = 'Archived'";
}
$sql .= " ORDER BY date_created DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoteIt_Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="theme-color" content="#06b399">
    
    <style>
        /* === General Styles === */
        body.admin-body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            display: flex;
            min-height: 100vh;
        }

        .container {
            display: flex;
            width: 100%;
        }

        /* === Sidebar === */
        .sidebar {
            width: 240px;
            background-color: #fff;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .logo {
            font-size: 1.6rem;
            font-weight: 700;
            color: #06b399;
            margin-bottom: 30px;
        }

        .logo span.exclamation {
            color: #ff5722;
        }

        .nav-admin {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .nav-item {
            display: block;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            color: #444;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-item:hover,
        .nav-item.active {
            background: #06b399;
            color: white;
        }

        .user-info {
            margin-top: auto;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 8px;
            text-align: center;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: #ccc;
            border-radius: 50%;
            margin: 10px auto 0;
        }

        /* === Main Content === */
        .main-content {
            flex: 1;
            padding: 25px;
            display: flex;
            flex-direction: column;
        }

        .headerA {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 1.6rem;
            font-weight: 600;
        }

        .search-container {
            display: flex;
            gap: 10px;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            width: 200px;
        }

        .add-note-btn {
            background-color: #06b399;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .add-note-btn:hover {
            background-color: #049b84;
        }

        /* === Notes Grid === */
        .note-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.05);
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.2s ease;
        }

        .note-card:hover {
            transform: scale(1.01);
        }

        .note-title {
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .note-content {
            margin-top: 10px;
            color: #555;
            font-size: 0.95rem;
        }

        .note-footer {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: #888;
        }

        .status-badge {
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
        }

        .status-badge.favorite {
            background: #e0f7f4;
            color: #06b399;
        }

        .status-badge.archived {
            background: #fff4e0;
            color: #ff9800;
        }

        /* === Dropdown Menu === */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dots-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 25px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            min-width: 160px;
            z-index: 10;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-btn {
            display: block;
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .dropdown-btn:hover {
            background: #f2f2f2;
        }

        /* === Modal Styles === */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 12px;
            width: 400px;
            max-width: 90%;
        }

        .close-modal {
            float: right;
            font-size: 1.4rem;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        input[type="text"],
        textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-save {
            background: #06b399;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            width: 100%;
        }

        .btn-save:hover {
            background: #049b84;
        }

        /* === Empty Notes Placeholder === */
        .no-notes {
            text-align: center;
            margin-top: 30px;
            color: #888;
            font-style: italic;
        }
    </style>
</head>

<body class="admin-body">
    <!-- rest of your HTML stays the same -->
