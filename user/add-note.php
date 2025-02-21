<?php
// user/add-note.php

require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

if (isset($_POST['add_note'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $title, $content);
    
    if ($stmt->execute()) {
        logActivity($userId, 'add_note', "Added new note: $title");
        $_SESSION['success'] = "Note added successfully!";
        header('Location: view-notes.php');
        exit();
    } else {
        $error = "Failed to add note. Please try again.";
    }
}

include '../includes/header.php';
?>

<div class="container">
    <h2>Add New Note</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" required class="form-control" rows="10"></textarea>
        </div>
        
        <button type="submit" name="add_note" class="btn">Add Note</button>
    </form>
</div>