<?php
// admin/view-activities.php

require_once '../includes/config.php';
require_once '../includes/db.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get total activities count
$totalActivities = $conn->query("SELECT COUNT(*) as count FROM user_activities")->fetch_assoc()['count'];
$totalPages = ceil($totalActivities / $perPage);

// Get activities with user information
$activities = $conn->query("SELECT ua.*, u.username 
                          FROM user_activities ua 
                          JOIN users u ON ua.user_id = u.id 
                          ORDER BY ua.created_at DESC 
                          LIMIT $offset, $perPage")->fetch_all(MYSQLI_ASSOC);

include 'includes/admin-header.php';
?>

<div class="container">
    <h2>User Activities</h2>
    
    <div class="card">
        <div class="filters">
            <form method="GET" action="" class="filter-form">
                <div class="form-group">
                    <label for="activity_type">Activity Type</label>
                    <select name="activity_type" id="activity_type" class="form-control">
                        <option value="">All Activities</option>
                        <option value="login">Login</option>
                        <option value="add_note">Add Note</option>
                        <option value="add_project">Add Project</option>
                        <option value="delete_note">Delete Note</option>
                        <option value="delete_project">Delete Project</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date_range">Date Range</label>
                    <select name="date_range" id="date_range" class="form-control">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Apply Filters</button>
            </form>
        </div>
        
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Activity</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($activity['username']); ?></td>
                        <td><?php echo htmlspecialchars($activity['activity_type']); ?></td>
                        <td><?php echo htmlspecialchars($activity['description']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="btn btn-small <?php echo $page === $i ? 'btn-active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
