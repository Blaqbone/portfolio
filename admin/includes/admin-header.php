<?php
// includes/admin-header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Portfolio System</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
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
<body class="admin-panel">
    <nav class="navbar admin-navbar">
        <div class="container">
            <div class="logo-main">
                <a href="../index.php"><img class="logo" src="../images/logo.png" alt="logo"></a>
                <a href="index.php"><div class="logo-text">GOAT</div></a>
            </div>
            <div class="nav-links">
                <a href="<?php echo BASE_URL; ?>/admin/dashboard.php">Dashboard</a>
                <a href="<?php echo BASE_URL; ?>/admin/manage-users.php">Users</a>
                <a href="<?php echo BASE_URL; ?>/admin/view-activities.php">Activities</a>
                <a href="<?php echo BASE_URL; ?>/logout.php">Logout</a>
            </div>
        </div>
    </nav>
</body>
</html>