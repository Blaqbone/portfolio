<?php
// logout.php

require_once 'includes/config.php';
require_once 'includes/db.php';

if (isLoggedIn()) {
    // Log the logout activity
    logActivity($_SESSION['user_id'], 'logout', 'User logged out');
    
    // Destroy all session data
    session_destroy();
    
    // Clear session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
}

// Redirect to login page
header('Location: login.php');
exit();
?>
