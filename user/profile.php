<?php
// user/profile.php

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userDetails = getUserDetails($userId);
$userStats = getUserStats($userId);

// Handle profile update
if (isset($_POST['update_profile'])) {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    
    $errors = [];
    
    // Verify current password
    if (!empty($currentPassword)) {
        if (!password_verify($currentPassword, $userDetails['password'])) {
            $errors[] = "Current password is incorrect";
        } else if (!empty($newPassword)) {
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $userId);
            $stmt->execute();
        }
    }
    
    // Update profile if no errors
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
            header('Location: profile.php');
            exit();
        } else {
            $errors[] = "Failed to update profile";
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="profile-section">
        <h2>My Profile</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="card">
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-label">Total Notes:</span>
                    <span class="stat-value"><?php echo $userStats['total_notes']; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Total Projects:</span>
                    <span class="stat-value"><?php echo $userStats['total_projects']; ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Last Activity:</span>
                    <span class="stat-value">
                        <?php echo $userStats['last_activity'] ? formatDate($userStats['last_activity']) : 'No activity'; ?>
                    </span>
                </div>
            </div>
            
            <form method="POST" action="" class="profile-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userDetails['username']); ?>" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userDetails['email']); ?>" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="current_password">Current Password (required for password change)</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password (leave blank to keep current)</label>
                    <input type="password" id="new_password" name="new_password" class="form-control">
                </div>
                
                <button type="submit" name="update_profile" class="btn">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>