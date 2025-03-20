<?php
session_start();
include 'config.php';
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    exit();
}

$user_id = $_SESSION['user_id'];

// mark all unread notifications as read when the user views them
$updateSQL = "UPDATE notifications SET status = 'read' WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($updateSQL);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// fetch notifications for the logged-in user
$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

// count unread notifications for the user
$countSQL = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($countSQL);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$countResult = $stmt->get_result();
$row = $countResult->fetch_assoc();
$unread_count = $row['unread_count'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>My Notifications</title>
    <link rel="stylesheet" href="css/notifications.css">
</head>

<body>
    <h1>Notifications</h1>
    <div class="notification-container">
        <?php if (empty($notifications)): ?>
        <p>No notifications available.</p>
        <?php else: ?>
        <?php foreach ($notifications as $notif): ?>
        <div class="card" style="background: <?= ($notif['status'] == 'unread') ? '#fff8e1' : '#ffffff'; ?>">
            <div class="card-body">
                <p class='title'><?= htmlspecialchars($notif['message']); ?></p>
                <?php if (!empty($notif['link'])): ?>
                <a href="<?= htmlspecialchars($notif['link']); ?>" target="_blank">Visit</a>
                <?php endif; ?>
                <?php if (!empty($notif['image'])): ?>
                <img src="<?= htmlspecialchars($notif['image']); ?>" alt="Notification Image">
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <small><?= date('F j, Y \a\t g:i A', strtotime($notif['created_at'])); ?></small>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>

</html>

<?php
include 'footer.php';
?>