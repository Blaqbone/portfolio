<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio System</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<style>
    .logo{
        height: 60px;
        width: 60px;
    }
    .logo-main{
        display: flex;
        justify-content: center;
    }
    .logo-text{
        display: flex;
        text-decoration: none;
        color: white;
        font-size: 30px;
        font-weight: 700;
        margin-top: 7px;
    }
    .logo-main a{
        text-decoration: none;
    }
</style>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo-main">
                <a href="../index.php"><img class="logo" src="../images/logo.png" alt="logo"></a>
                <a href="../index.php"><div class="logo-text">GOAT</div></a>
            </div>
            <div class="nav-links">
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo BASE_URL; ?>/user/dashboard.php">Dashboard</a>
                    <a href="<?php echo BASE_URL; ?>/user/view-notes.php">Notes</a>
                    <a href="<?php echo BASE_URL; ?>/user/view-projects.php">Projects</a>
                    <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login.php">Login</a>
                    <a href="<?php echo BASE_URL; ?>/register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</body>
</html>