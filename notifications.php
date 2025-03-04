<?php
session_start();
include 'config.php';
include 'navbar.php';
include 'alertFunction.php';

if (!isset($_SESSION['user_id'])) {
    showAlert('Please log in to see notifications.', 'error', '#ff0000', 'login.php');
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
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .container {
        width: 75%;
        max-width: 1200px;
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
        font-size: 2rem;
        font-weight: bold;
    }

    .badge {
        background: red;
        color: white;
        padding: 5px 10px;
        font-size: 14px;
        border-radius: 50%;
        font-weight: bold;
    }

    .card {
        background: rgb(209, 46, 46);
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(228, 228, 228, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-body {
        padding: 20px;
        background-color: rgb(243, 249, 255);
    }

    .card-body p {
        margin: 0 0 10px;
        font-size: 1rem;
        line-height: 1.6;
    }

    .card-body a {
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }

    .card-body a:hover {
        text-decoration: underline;
    }

    .card-body img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        margin-top: 15px;
        object-fit: cover;
    }

    .card-footer {
        background: rgb(233, 233, 233);
        padding: 10px 20px;
        text-align: right;
        font-size: 0.9rem;
        color: #666;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .card-footer small {
        display: inline-block;
        font-style: italic;
        font-size: 0.8rem;
        font-weight: bold;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Notifications
            <?php if ($unread_count > 0): ?>
            <span class="badge"><?= $unread_count ?></span>
            <?php endif; ?>
        </h2>
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