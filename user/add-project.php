<?php
// user/add-project.php

require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

if (isset($_POST['add_project'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $userId = $_SESSION['user_id'];
    
    // Handle file upload
    $filePath = null;
    if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] === 0) {
        $fileName = time() . '_' . $_FILES['project_file']['name'];
        $targetPath = PROJECTS_UPLOAD_DIR . $fileName;
        
        if (move_uploaded_file($_FILES['project_file']['tmp_name'], $targetPath)) {
            $filePath = $fileName;
        } else {
            $error = "Failed to upload file.";
        }
    }
    
    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO projects (user_id, title, description, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userId, $title, $description, $filePath);
        
        if ($stmt->execute()) {
            logActivity($userId, 'add_project', "Added new project: $title");
            $_SESSION['success'] = "Project added successfully!";
            header('Location: view-projects.php');
            exit();
        } else {
            $error = "Failed to add project. Please try again.";
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <h2>Add New Project</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Project Title</label>
            <input type="text" id="title" name="title" required class="form-control">
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" required class="form-control" rows="5"></textarea>
        </div>
        
        <div class="form-group">
            <label for="project_file">Project File (Optional)</label>
            <input type="file" id="project_file" name="project_file" class="file-input">
            <div class="file-name"></div>
        </div>
        
        <button type="submit" name="add_project" class="btn">Add Project</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>