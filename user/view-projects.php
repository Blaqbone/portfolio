<?php 
// user/view-projects.php
require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Handle project deletion
if (isset($_POST['delete_project'])) {
    $projectId = $_POST['project_id'];
    
    // Get file path before deletion
    $stmt = $conn->prepare("SELECT file_path FROM projects WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $projectId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($project = $result->fetch_assoc()) {
        // Delete file if exists
        if ($project['file_path']) {
            @unlink(PROJECTS_UPLOAD_DIR . $project['file_path']);
        }
        
        // Delete project from database
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $projectId, $userId);
        
        if ($stmt->execute()) {
            logActivity($userId, 'delete_project', "Deleted project ID: $projectId");
            $_SESSION['success'] = "Project deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete project.";
        }
    }
}

// Fetch all projects for the user
$stmt = $conn->prepare("SELECT * FROM projects WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$projects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <div class="header-actions">
        <h2>My Projects</h2>
        <a href="add-project.php" class="btn">Add New Project</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($projects)): ?>
        <div class="no-projects">
            <p>You haven't created any projects yet.</p>
        </div>
    <?php else: ?>
        <div class="projects-grid">
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <p class="project-description">
                        <?php echo htmlspecialchars($project['description']); ?>
                    </p>
                    <div class="project-meta">
                        <span class="date">Created: <?php echo date('M j, Y', strtotime($project['created_at'])); ?></span>
                        <?php if ($project['file_path']): ?>
                            <span class="has-attachment">📎 Has attachment</span>
                        <?php endif; ?>
                    </div>
                    <div class="project-actions">
                        <a href="edit-project.php?id=<?php echo $project['id']; ?>" class="btn btn-edit">Edit</a>
                        <form method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this project?');">
                            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                            <button type="submit" name="delete_project" class="btn btn-delete">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.header-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.project-card {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.project-description {
    margin: 1rem 0;
    color: #666;
}

.project-meta {
    font-size: 0.9rem;
    color: #777;
    margin-bottom: 1rem;
}

.project-actions {
    display: flex;
    gap: 1rem;
}

.btn-edit {
    background: #4a90e2;
    color: white;
}

.btn-delete {
    background: #e74c3c;
    color: white;
}

.delete-form {
    display: inline;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.no-projects {
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 8px;
}
</style>

<?php include '../includes/footer.php'; ?>