<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        overflow-x: hidden;
        position: relative;
    }

    .navbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background-color: #006400;
        color: white;
        padding: 10px 0;
        z-index: 1000;
    }

    .top-sidebar h2 {
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
        text-align: center;
        margin: 20px 0 0;
    }

    .top-sidebar span {
        font-weight: bold;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #333;
        background-color: rgb(255, 255, 255);
        width: 80%;
        height: 30px;
        border-radius: 20px;
        margin: 10px auto 20px;
    }

    .top-sidebar a {
        display: block;
        color: white;
        font-size: 1.1rem;
        text-decoration: none;
        padding: 10px 20px;
        transition: 0.3s;
        border-bottom: 1px solid #ccc;
    }

    .top-sidebar a:last-child {
        border-bottom: none;
    }

    .top-sidebar a:hover {
        background-color: #228B22;

    }

    .bottom-sidebar {
        display: flex;
    }

    .bottom-sidebar a {
        background-color: #f44336;
        width: 150px;
        margin: 10px auto;
        padding: 5px 10px;
        border-radius: 4px;
        color: white;
        text-decoration: none;
        text-align: center;
    }

    .content-wrapper {
        margin-left: 250px;
        width: calc(100% - 250px);
        padding: 20px;
    }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="top-sidebar">

            <h2>Admin Menu</h2>

            <?php if (isset($_SESSION['admin_username'])): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['admin_username']); ?></span>

            <a href="admin_dashboard.php">Dashboard</a>
            <a href="add_category.php">Add Category</a>
            <a href="edit_category.php">Edit categories</a>
            <a href="delete_category.php">Delete Category</a>
            <a href="view_users.php">Manage Users</a>
            <a href="view_ads.php">View & Delete Ads</a>
            <a href="admin_send_notification.php">Send Push Notifications</a>
            <a href="admin_manage_notifications.php">Manage Push Notifications</a>
            <a href="admin_manage_requests.php">Manage Requests</a>
            <a href="admin_approval.php">Admin Approval</a>
            <a href="seller_verify.php">Seller Verify</a>
            <a href="ads_report.php">Ad Reports</a>
        </div>
        <div class="bottom-sidebar">
            <a href="admin_logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
            <a href="admin_login.php" class="btn-login">Login</a>
            <a href="admin_register.php" class="btn-register">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="content-wrapper">
        <?php
        if (isset($content)) {
            echo $content;
        }
        ?>
    </div>
</body>

</html>