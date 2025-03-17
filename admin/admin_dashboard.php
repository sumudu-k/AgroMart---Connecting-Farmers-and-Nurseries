<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}


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


ob_start();
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    .container {
        max-width: 100%;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        font-size: 28px;
        color: #333;
        margin-bottom: 20px;
    }

    stats-container h2 {
        font-size: 24px;
        color: #333;
        margin-bottom: 15px;
    }

    .stats {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: space-around;
    }

    .stat-item {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        flex: 1;
        min-width: 200px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .stat-item h3 {
        font-size: 18px;
        color: #555;
        margin: 0;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 20px;
        text-align: center;
    }

    @media screen and (max-width: 768px) {
        .container {
            padding: 15px;
        }

        h1 {
            font-size: 24px;
        }

        h2 {
            font-size: 20px;
        }

        .stat-item {
            min-width: 100%;
        }
    }
</style>

<div class="container">
    <h1>Welcome to Admin Dashboard</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message"><?= htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message"><?= htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="stats-container">
        <h2>Statistics</h2>
        <div class="stats">
            <div class="stat-item">
                <h3>Total Users: <?= htmlspecialchars($users['total']); ?></h3>
            </div>
            <div class="stat-item">
                <h3>Total Ads: <?= htmlspecialchars($ads['total']); ?></h3>
            </div>
            <div class="stat-item">
                <h3>Total Categories: <?= htmlspecialchars($categories['total']); ?></h3>
            </div>
            <div class="stat-item">
                <h3>Total Ad Requests: <?= htmlspecialchars($adrequests['total']); ?></h3>
            </div>
            <div class="stat-item">
                <h3>Total Notifications: <?= htmlspecialchars($notifications['total']); ?></h3>
            </div>
        </div>
    </div>
</div>

<?php

$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>