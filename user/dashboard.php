<?php
// user/dashboard.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


require_once '../includes/config.php';
require_once '../includes/db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// Get user's notes and projects
$userId = $_SESSION['user_id'];

$notesQuery = "SELECT * FROM notes WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($notesQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$notes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$projectsQuery = "SELECT * FROM projects WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($projectsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$projects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    
    <div class="dashboard-stats">
        <div class="card">
            <h3>Quick Actions</h3>
            <div class="button-group">
                <a href="add-note.php" class="btn">Add New Note</a>
                <a href="add-project.php" class="btn">Add New Project</a>
            </div>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="grid">
            <div class="card">
                <h3>Recent Notes</h3>
                <?php if (empty($notes)): ?>
                    <p>No notes found.</p>
                <?php else: ?>
                    <ul class="list">
                        <?php foreach ($notes as $note): ?>
                            <li>
                                <h4><?php echo htmlspecialchars($note['title']); ?></h4>
                                <p class="date"><?php echo date('M d, Y', strtotime($note['created_at'])); ?></p>
                                <div class="actions">
                                    <a href="view-notes.php?id=<?php echo $note['id']; ?>" class="btn btn-small">View</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="view-notes.php" class="btn btn-link">View All Notes</a>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3>Recent Projects</h3>
                <?php if (empty($projects)): ?>
                    <p>No projects found.</p>
                <?php else: ?>
                    <ul class="list">
                        <?php foreach ($projects as $project): ?>
                            <li>
                                <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                                <p class="date"><?php echo date('M d, Y', strtotime($project['created_at'])); ?></p>
                                <div class="actions">
                                    <a href="view-projects.php?id=<?php echo $project['id']; ?>" class="btn btn-small">View</a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="view-projects.php" class="btn btn-link">View All Projects</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
