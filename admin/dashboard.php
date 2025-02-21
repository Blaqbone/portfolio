<?php
// admin/dashboard.php

require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Get statistics
$stats = [
    'total_users' => $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'],
    'total_notes' => $conn->query("SELECT COUNT(*) as count FROM notes")->fetch_assoc()['count'],
    'total_projects' => $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'],
    'recent_activities' => $conn->query("SELECT ua.*, u.username FROM user_activities ua 
                                       JOIN users u ON ua.user_id = u.id 
                                       ORDER BY ua.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC)
];

include 'includes/admin-header.php';
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    
    <div class="stats-grid">
        <div class="card stat-card">
            <h3>Total Users</h3>
            <p class="stat-number"><?php echo $stats['total_users']; ?></p>
            <a href="manage-users.php" class="btn btn-small">Manage Users</a>
        </div>
        
        <div class="card stat-card">
            <h3>Total Notes</h3>
            <p class="stat-number"><?php echo $stats['total_notes']; ?></p>
        </div>
        
        <div class="card stat-card">
            <h3>Total Projects</h3>
            <p class="stat-number"><?php echo $stats['total_projects']; ?></p>
        </div>
    </div>
    
    <div class="card">
        <h3>Recent Activities</h3>
        <table class="activity-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Activity</th>
                    <th>Description</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent_activities'] as $activity): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($activity['username']); ?></td>
                        <td><?php echo htmlspecialchars($activity['activity_type']); ?></td>
                        <td><?php echo htmlspecialchars($activity['description']); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="view-activities.php" class="btn btn-link">View All Activities</a>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>