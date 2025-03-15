<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include '../config.php';

$usercount = "SELECT COUNT(*) as total FROM users";
$users = $conn->query($usercount);
$users = $users->fetch_assoc();

$adcount = "SELECT COUNT(*) as total FROM ads";
$ads = $conn->query($adcount);
$ads = $ads->fetch_assoc();

$catcount = "SELECT COUNT(*) as total FROM categories";
$categories = $conn->query($catcount);
$categories = $categories->fetch_assoc();

$adreqcount = "SELECT COUNT(*) as total FROM plant_requests";
$adrequests = $conn->query($adreqcount);
$adrequests = $adrequests->fetch_assoc();

$notificationcount = "SELECT COUNT(DISTINCT message) AS total FROM notifications";
$notifications = $conn->query($notificationcount);
$notifications = $notifications->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    body {
        display: flex;
    }

    .sidebar {
        width: 200px;
        background-color: #333;
        color: white;
        height: 100vh;
        padding: 20px;
        position: fixed;
    }

    .sidebar a {
        display: block;
        padding: 10px;
        color: white;
        text-decoration: none;
    }

    .sidebar a:hover {
        background-color: #575757;
    }

    .content {
        margin-left: 220px;
        padding: 0px 20px;
        width: 100%;
    }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>Admin Menu</h2>
        <a href="add_category.php">Add Category</a>
        <a href="delete_category.php">Delete Category</a>
        <a href="view_users.php">Manage Users</a>
        <a href="view_ads.php">View & Delete Ads</a>
        <a href="admin_send_notification.php">Send Push Notifications</a>
        <a href="admin_manage_notifications.php">Delete Push Notifications</a>
        <a href="admin_manage_requests.php">Manage Requests</a>
        <a href="admin_approval.php">Admin Approval</a>
    </div>

    <div class="content">
        <?php include 'admin_navbar.php'; ?>
        <h1>Welcome to Admin Dashboard</h1>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message"><?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <div>
            <h2>Statistics</h2>
            <div>
                <h3>Total Users: <?= $users['total'] ?></h3>
                <h3>Total Ads: <?= $ads['total'] ?></h3>
                <h3>Total Categories: <?= $categories['total'] ?></h3>
                <h3>Total Ad Requests: <?= $adrequests['total'] ?></h3>
                <h3>Total Notifications: <?= $notifications['total'] ?></h3>
            </div>
        </div>

    </div>




</body>

</html>