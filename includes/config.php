<?php
// includes/config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'portfolio_db');

// Base URL
define('BASE_URL', 'http://localhost/portfolio');

// Upload directories
define('NOTES_UPLOAD_DIR', '../assets/uploads/notes/');
define('PROJECTS_UPLOAD_DIR', '../assets/uploads/projects/');

// Session configuration
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to log user activity
function logActivity($userId, $activityType, $description) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO user_activities (user_id, activity_type, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $activityType, $description);
    $stmt->execute();
}
?>
