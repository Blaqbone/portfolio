<?php
// index.php

require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Redirect logged-in users to their dashboard
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit();
}

include 'includes/header.php';
?>

<div class="container">
    <div class="hero-section">
        <h1>Welcome to Portfolio System</h1>
        <p class="lead">Manage your notes and projects in one place</p>
        
        <div class="cta-buttons">
            <a href="login.php" class="btn btn-primary">Login</a>
            <a href="register.php" class="btn btn-secondary">Register</a>
        </div>
    </div>
    
    <div class="features-section">
        <h2>Features</h2>
        
        <div class="features-grid">
            <div class="feature-card">
                <h3>Note Management</h3>
                <p>Create, organize, and manage your notes efficiently</p>
            </div>
            
            <div class="feature-card">
                <h3>Project Portfolio</h3>
                <p>Showcase your projects with detailed descriptions and files</p>
            </div>
            
            <div class="feature-card">
                <h3>Secure Storage</h3>
                <p>Your data is securely stored and backed up regularly</p>
            </div>
            
            <div class="feature-card">
                <h3>Easy Sharing</h3>
                <p>Share your work with others when needed</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>