<?php
session_start();
include '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

//fetch statistics
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
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: "Poppins", Arial, sans-serif;
}

body {
    color: #333;
    min-height: 100vh;
    position: relative;
    display: flex;
    background-color: #f4f4f4;
}

body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url("../images/B1.jpg");
    background-size: cover;
    opacity: 0.2;
}

.dashdoard-container {
    max-width: 90%;
    margin: 20px auto;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    z-index: 1;
}

.dashdoard-container h1 {
    text-align: center;
    font-size: 2rem;
    color: #333;
    margin-bottom: 15px;
    padding: 10px 0;
    border-radius: 5px;
}

.stats-container {
    padding: 20px 0;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.stats-container h2 {
    font-size: 1.5rem;
    font-weight: 600;
    text-align: center;
    color: #333;
    margin: 0 0 30px;
    background-color: rgba(169, 230, 169, 0.45);
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stats {
    display: flex;
    gap: 20px;
    justify-content: center;
    align-items: stretch;
    flex-wrap: wrap;
}

.stat-item {
    background-color: #fff;
    padding: 15px;
    border-radius: 8px;
    width: 18%;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.stat-item h3 {
    font-size: 1.1rem;
    line-height: 1.5;
    color: #555;
    margin: 0;
    font-weight: bold;
}
</style>

<div class="dashdoard-container">
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
                <h3>Total Users: <br><?= htmlspecialchars($users['total']); ?></h3>
            </div>
            <div class="stat-item">
                <h3>Total Ads: <br><?= htmlspecialchars($ads['total']); ?></h3>
            </div>
            <div class="stat-item">
                <h3>Total Categories: <br><?= htmlspecialchars($categories['total']); ?></h3>
            </div>
            <div class="stat-item">
                <h3>Total Ad Requests: <br><?= htmlspecialchars($adrequests['total']); ?></h3>
            </div>
            <div class="stat-item">
                <h3>Total Notifications: <br><?= htmlspecialchars($notifications['total']); ?></h3>
            </div>
        </div>
    </div>
</div>

<?php
//capture the content and include the layout
$content = ob_get_clean();
include '../admin/admin_navbar.php';
?>