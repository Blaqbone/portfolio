<?php
// admin/manage-users.php

require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    
    // Don't allow deleting yourself
    if ($userId != $_SESSION['user_id']) {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Delete user's notes
            $conn->query("DELETE FROM notes WHERE user_id = $userId");
            
            // Delete user's projects
            $stmt = $conn->prepare("SELECT file_path FROM projects WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $projects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            // Delete project files
            foreach ($projects as $project) {
                if ($project['file_path']) {
                    @unlink(PROJECTS_UPLOAD_DIR . $project['file_path']);
                }
            }
            
            // Delete projects from database
            $conn->query("DELETE FROM projects WHERE user_id = $userId");
            
            // Delete user activities
            $conn->query("DELETE FROM user_activities WHERE user_id = $userId");
            
            // Finally, delete the user
            $conn->query("DELETE FROM users WHERE id = $userId");
            
            $conn->commit();
            $_SESSION['success'] = "User and all associated data deleted successfully!";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "Failed to delete user. Please try again.";
        }
    } else {
        $_SESSION['error'] = "You cannot delete your own account!";
    }
}

// Handle user status toggle
if (isset($_POST['toggle_status'])) {
    $userId = $_POST['user_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $userId);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "User status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update user status.";
    }
}

// Fetch all users except current admin
$users = $conn->query("SELECT * FROM users WHERE id != {$_SESSION['user_id']} ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-header.php';
?>

<div class="container">
    <h2>Manage Users</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <table class="users-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo ucfirst($user['role']); ?></td>
                        <td>
                        <form method="POST" action="" class="inline-form">
                            <input type="hidden" name="user_id" value="<?php echo isset($user['id']) ? $user['id'] : ''; ?>">
                            <input type="hidden" name="status" value="<?php echo isset($user['status']) && $user['status'] === 'active' ? 'inactive' : 'active'; ?>">
                            
                            <button type="submit" name="toggle_status" class="btn btn-small <?php echo isset($user['status']) && $user['status'] === 'active' ? 'btn-success' : 'btn-warning'; ?>">
                                <?php echo isset($user['status']) ? ucfirst($user['status']) : 'Unknown'; ?>
                            </button>
                        </form>

                        </td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="view-user-activity.php?id=<?php echo $user['id']; ?>" class="btn btn-small">View Activity</a>
                                <form method="POST" action="" class="inline-form">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user" class="btn btn-small btn-danger delete-btn">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
